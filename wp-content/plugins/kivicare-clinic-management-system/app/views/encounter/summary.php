<?php
/**
 * Vista: Resumen de consulta
 * Usa la misma tipografía/estructura responsive que Detalles de la factura.
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> >
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_e('Resumen de consulta','kc-lang'); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url( KIVI_CARE_DIR_URI . 'assets/css/print.css' ); ?>">
    <style>
        /* Ajustes mínimos para tarjetas y tablas */
        .kc-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:16px}
        .kc-row{display:flex;flex-wrap:wrap;gap:16px}
        .kc-col{flex:1 1 260px}
        .kc-title{font-size:18px;margin:0 0 8px 0}
        .kc-muted{color:#6b7280}
        .kc-table{width:100%;border-collapse:collapse}
        .kc-table th,.kc-table td{border-bottom:1px solid #e5e7eb;padding:8px 12px;text-align:left;vertical-align:top}
        .kc-badge{display:inline-block;padding:2px 8px;border-radius:9999px;background:#eef2ff}
        .kc-right{text-align:right}
        .kc-head{display:flex;align-items:center;gap:16px}
        .kc-logo{height:40px}
        @media print { .no-print{display:none !important} body{background:#fff} }
    </style>
</head>
<body>
<div class="kc-card">
    <div class="kc-head">
        <?php if (!empty($logo_url)): ?>
            <img class="kc-logo" src="<?php echo esc_url($logo_url); ?>" alt="logo">
        <?php endif; ?>
        <div>
            <h2 class="kc-title"><?php esc_html_e('Resumen de consulta','kc-lang'); ?></h2>
            <div class="kc-muted"><?php echo esc_html( $print_date ); ?></div>
        </div>
    </div>
</div>

<div class="kc-row">
    <div class="kc-col">
        <div class="kc-card">
            <h3 class="kc-title"><?php esc_html_e('Paciente','kc-lang'); ?></h3>
            <div><?php echo esc_html($patient->name ?? ''); ?></div>
            <div class="kc-muted"><?php echo esc_html($patient->email ?? ''); ?></div>
            <div class="kc-muted"><?php echo esc_html($patient->phone_no ?? ''); ?></div>
        </div>
    </div>
    <div class="kc-col">
        <div class="kc-card">
            <h3 class="kc-title"><?php esc_html_e('Doctor','kc-lang'); ?></h3>
            <div><?php echo esc_html($doctor->name ?? ''); ?></div>
            <div class="kc-muted"><?php echo esc_html($doctor->speciality ?? ''); ?></div>
            <div class="kc-muted"><?php echo esc_html($clinic->name ?? ''); ?></div>
        </div>
    </div>
    <div class="kc-col">
        <div class="kc-card">
            <h3 class="kc-title"><?php esc_html_e('Consulta','kc-lang'); ?></h3>
            <div><?php esc_html_e('ID','kc-lang'); ?>: <?php echo esc_html($encounter->id ?? ''); ?></div>
            <div><?php esc_html_e('Fecha','kc-lang'); ?>: <?php echo esc_html($encounter->date ?? ''); ?></div>
            <div><?php esc_html_e('Estado','kc-lang'); ?>:
                <span class="kc-badge"><?php echo esc_html($encounter->status ?? ''); ?></span>
            </div>
        </div>
    </div>
</div>

<div class="kc-card">
    <h3 class="kc-title"><?php esc_html_e('Signos vitales','kc-lang'); ?></h3>
    <table class="kc-table">
        <tbody>
        <?php if (!empty($vitals)): foreach ($vitals as $k=>$v): ?>
            <tr><th><?php echo esc_html($k); ?></th><td><?php echo esc_html($v); ?></td></tr>
        <?php endforeach; else: ?>
            <tr><td colspan="2" class="kc-muted"><?php esc_html_e('Sin registros','kc-lang'); ?></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="kc-row">
    <div class="kc-col">
        <div class="kc-card">
            <h3 class="kc-title"><?php esc_html_e('Diagnóstico','kc-lang'); ?></h3>
            <div><?php echo !empty($diagnosis) ? wp_kses_post(nl2br($diagnosis)) : '<span class="kc-muted">'.esc_html__('Sin datos','kc-lang').'</span>'; ?></div>
        </div>
    </div>
    <div class="kc-col">
        <div class="kc-card">
            <h3 class="kc-title"><?php esc_html_e('Notas','kc-lang'); ?></h3>
            <div><?php echo !empty($notes) ? wp_kses_post(nl2br($notes)) : '<span class="kc-muted">'.esc_html__('Sin notas','kc-lang').'</span>'; ?></div>
        </div>
    </div>
</div>

<div class="kc-card">
    <h3 class="kc-title"><?php esc_html_e('Prescripción','kc-lang'); ?></h3>
    <table class="kc-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Medicamento','kc-lang'); ?></th>
                <th><?php esc_html_e('Dosis','kc-lang'); ?></th>
                <th><?php esc_html_e('Frecuencia','kc-lang'); ?></th>
                <th><?php esc_html_e('Duración','kc-lang'); ?></th>
                <th><?php esc_html_e('Notas','kc-lang'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($prescription)): foreach ($prescription as $row): ?>
            <tr>
                <td><?php echo esc_html($row->medicine ?? ''); ?></td>
                <td><?php echo esc_html($row->dose ?? ''); ?></td>
                <td><?php echo esc_html($row->frequency ?? ''); ?></td>
                <td><?php echo esc_html($row->duration ?? ''); ?></td>
                <td><?php echo esc_html($row->note ?? ''); ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="5" class="kc-muted"><?php esc_html_e('Sin prescripción','kc-lang'); ?></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="kc-card">
    <h3 class="kc-title"><?php esc_html_e('Servicios asociados','kc-lang'); ?></h3>
    <table class="kc-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Servicio','kc-lang'); ?></th>
                <th class="kc-right"><?php esc_html_e('Monto','kc-lang'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        if (!empty($services)):
            foreach ($services as $srv):
                $name  = $srv->name ?? '';
                $price = floatval($srv->price ?? 0);
                $total += $price;
        ?>
            <tr>
                <td><?php echo esc_html($name); ?></td>
                <td class="kc-right"><?php echo esc_html(number_format($price, 2)); ?></td>
            </tr>
        <?php endforeach; else: ?>
            <tr><td colspan="2" class="kc-muted"><?php esc_html_e('Sin servicios','kc-lang'); ?></td></tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th><?php esc_html_e('Total','kc-lang'); ?></th>
                <th class="kc-right"><?php echo esc_html(number_format($total, 2)); ?></th>
            </tr>
        </tfoot>
    </table>
</div>

<div class="no-print kc-card kc-right">
    <button onclick="window.print()"><?php esc_html_e('Imprimir','kc-lang'); ?></button>
</div>
</body>
</html>
