<?php
namespace App\Controllers;

defined('ABSPATH') || exit;

class PatientEncounterSummaryController
{
    /**
     * Renderiza un shell HTML ligero. Los datos se cargan por AJAX
     * desde la ruta existente 'patient_encounter_details' para no repetir lógica.
     */
    public function show()
    {
        $encounterId = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($encounterId <= 0) {
            wp_die(__('Invalid encounter id','kc-lang'));
        }

        // Variables mínimas para la vista
        $data = [
            'encounter_id' => $encounterId,
            'ajax_url'     => admin_url('admin-ajax.php'),
            'assets_url'   => trailingslashit(plugin_dir_url(dirname(__DIR__))) . 'assets/',
        ];

        // Carga vista
        // Nota: usamos una vista propia que se renderiza sin depender del SPA.
        include dirname(__DIR__) . '/views/encounter/summary.php';
        exit;
    }
}
