<?php
namespace App\controllers;

defined('ABSPATH') || exit;

class PatientEncounterSummaryController
{
    /**
     * Entrega HTML ligero para Thickbox. Los datos se obtienen por AJAX
     * usando el router del core (action=ajax_get).
     */
    public function show()
    {
        $encounterId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($encounterId <= 0) {
            wp_die(__('Invalid encounter id','kc-lang'));
        }

        // Cabeceras mínimas
        nocache_headers();
        header('Content-Type: text/html; charset=' . get_bloginfo('charset'));

        $data = [
            'encounter_id' => $encounterId,
            // admin-ajax del dashboard
            'ajax_url'     => admin_url('admin-ajax.php'),
        ];

        include dirname(__DIR__) . '/views/encounter/summary.php';
        exit;
    }
}
