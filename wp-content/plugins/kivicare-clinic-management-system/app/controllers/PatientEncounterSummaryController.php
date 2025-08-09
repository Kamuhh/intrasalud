<?php
namespace App\controllers;
defined('ABSPATH') || exit;

class PatientEncounterSummaryController
{
    // Shell HTML para Thickbox; los datos se cargan vía ajax_get (core)
    public function show()
    {
        $encounterId = isset($_GET['encounter_id']) ? intval($_GET['encounter_id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);
        if ($encounterId <= 0) {
            wp_die(__('Invalid encounter id','kc-lang'));
        }
        nocache_headers();
        header('Content-Type: text/html; charset=' . get_bloginfo('charset'));

        $ajaxUrl = admin_url('admin-ajax.php');
        $encId   = (int) $encounterId;
        include dirname(__DIR__) . '/views/encounter/summary.php';
        exit;
    }
}
