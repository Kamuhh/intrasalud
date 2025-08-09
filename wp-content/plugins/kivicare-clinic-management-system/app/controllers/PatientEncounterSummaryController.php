<?php
namespace App\Controllers;

use Dompdf\Dompdf;

class PatientEncounterSummaryController {
    public function handle() {
        check_ajax_referer('ajax_get', '_ajax_nonce');

        $encounter_id = intval($_GET['encounter_id'] ?? 0);
        $type = sanitize_text_field($_GET['type'] ?? 'html');

        if (!$encounter_id) {
            wp_send_json(['status'=>false,'message'=>'encounter_id requerido'],400);
        }

        $data = $this->getEncounterData($encounter_id);
        if (!$data) {
            wp_send_json(['status'=>false,'message'=>'Encuentro no encontrado'],404);
        }

        $html = $this->renderHtml($data);

        if ($type === 'html') {
            wp_send_json(['status'=>true,'data'=>$html,'message'=>'ok']);
        }

        if ($type === 'sendEmail') {
            $sent = $this->sendEmail($data, $html);
            wp_send_json(['status'=>$sent,'message'=>$sent ? 'Correo enviado' : 'Fallo al enviar']);
        }

        if ($type === 'pdf') {
            $url = $this->generatePdf($encounter_id, $html);
            if ($url) {
                wp_send_json(['status'=>true,'file_url'=>$url]);
            }
            wp_send_json(['status'=>false,'message'=>'No se pudo generar PDF'],500);
        }

        wp_send_json(['status'=>false,'message'=>'type inválido'],400);
    }

    private function getEncounterData($id) {
        global $wpdb;
        $encounters = $wpdb->prefix . 'kc_patient_encounters';
        $clinics = $wpdb->prefix . 'kc_clinics';
        $users = $wpdb->base_prefix . 'users';

        $row = $wpdb->get_row($wpdb->prepare("SELECT e.*, d.display_name AS doctor_name, c.name AS clinic_name, p.display_name AS patient_name, p.user_email AS patient_email FROM {$encounters} e LEFT JOIN {$users} d ON e.doctor_id=d.ID LEFT JOIN {$users} p ON e.patient_id=p.ID LEFT JOIN {$clinics} c ON e.clinic_id=c.id WHERE e.id=%d", $id));
        if (!$row) { return null; }

        $patient_meta = json_decode(get_user_meta((int)$row->patient_id, 'basic_data', true));
        $ci = get_user_meta((int)$row->patient_id, 'patient_unique_id', true);

        $diagnosticos = [];
        if (!empty($row->diagnosis)) {
            $dxData = json_decode($row->diagnosis, true);
            if (is_array($dxData)) {
                foreach ($dxData as $dx) {
                    if (is_array($dx)) {
                        $diagnosticos[] = ['codigo'=>$dx['code'] ?? '', 'nombre'=>$dx['name'] ?? ($dx['description'] ?? '')];
                    } else {
                        $diagnosticos[] = ['codigo'=>'','nombre'=>$dx];
                    }
                }
            } else {
                $diagnosticos[] = ['codigo'=>'','nombre'=>$row->diagnosis];
            }
        }

        $ordenes = [];
        if (!empty($row->observations)) {
            $obs = json_decode($row->observations, true);
            if (is_array($obs)) {
                $ordenes = array_filter(array_map('trim', $obs));
            } else {
                $ordenes = array_filter(array_map('trim', explode("\n", $row->observations)));
            }
        }

        $indicaciones = [];
        if (!empty($row->notes)) {
            $notes = json_decode($row->notes, true);
            if (is_array($notes)) {
                $indicaciones = array_filter(array_map('trim', $notes));
            } else {
                $indicaciones = array_filter(array_map('trim', explode("\n", $row->notes)));
            }
        }

        return [
            'paciente_nombre' => $row->patient_name,
            'paciente_ci' => $ci,
            'paciente_email' => $row->patient_email,
            'doctor_nombre' => $row->doctor_name,
            'clinica_nombre' => $row->clinic_name,
            'consulta_fecha' => $row->encounter_date,
            'diagnosticos' => $diagnosticos,
            'ordenes' => $ordenes,
            'indicaciones' => $indicaciones,
        ];
    }

    private function renderHtml($d) {
        ob_start(); ?>
        <div style="font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;">
            <table style="width:100%;margin-bottom:16px;">
                <tr>
                    <td>
                        <h2 style="margin:0;">Resumen de atención</h2>
                        <div style="color:#666;font-size:12px;">Fecha: <?= esc_html($d['consulta_fecha']); ?></div>
                    </td>
                    <td style="text-align:right;">
                        <strong>Paciente:</strong> <?= esc_html($d['paciente_nombre']); ?><br>
                        <strong>CI:</strong> <?= esc_html($d['paciente_ci']); ?><br>
                        <strong>Correo:</strong> <?= esc_html($d['paciente_email']); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:right;color:#666;font-size:12px;">
                        <strong>Médico:</strong> <?= esc_html($d['doctor_nombre']); ?> —
                        <strong>Clínica:</strong> <?= esc_html($d['clinica_nombre']); ?>
                    </td>
                </tr>
            </table>

            <h3 style="margin:12px 0;">Diagnóstico(s)</h3>
            <ul>
                <?php foreach ($d['diagnosticos'] as $dx): ?>
                    <li><?= esc_html(trim($dx['codigo'] . ' ' . $dx['nombre'])); ?></li>
                <?php endforeach; ?>
            </ul>

            <h3 style="margin:12px 0;">Órdenes clínicas</h3>
            <?php if (!empty($d['ordenes'])): ?>
                <ul>
                    <?php foreach ($d['ordenes'] as $o): ?>
                        <li><?= esc_html($o); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div style="color:#999;">No se encontraron registros</div>
            <?php endif; ?>

            <h3 style="margin:12px 0;">Indicaciones</h3>
            <?php if (!empty($d['indicaciones'])): ?>
                <ul>
                    <?php foreach ($d['indicaciones'] as $i): ?>
                        <li><?= esc_html($i); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div style="color:#999;">No se encontraron registros</div>
            <?php endif; ?>
        </div>
        <?php return ob_get_clean();
    }

    private function sendEmail($d, $html) {
        $to = $d['paciente_email'];
        if (!$to) { return false; }
        $subject = 'Resumen de atención';
        $headers = ['Content-Type: text/html; charset=UTF-8'];
        return wp_mail($to, $subject, $html, $headers);
    }

    private function generatePdf($encounter_id, $html) {
        $dompdf = new Dompdf();
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();
        $output = $dompdf->output();

        $upload = wp_upload_dir();
        $dir = trailingslashit($upload['basedir']) . 'kivicare/encounters';
        wp_mkdir_p($dir);
        $file = $dir . '/' . $encounter_id . '.pdf';
        file_put_contents($file, $output);
        return trailingslashit($upload['baseurl']) . 'kivicare/encounters/' . $encounter_id . '.pdf';
    }
}
