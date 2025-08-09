<?php
/**
 * Plugin Name: KiviCare - Clinic & Patient Management System (EHR)
 * Plugin URI: https://iqonic.design
 * Description: KiviCare is an impressive clinic and patient management plugin (EHR).
 * Version:3.6.11
 * Author: iqonic
 * Text Domain: kc-lang
 * Domain Path: /languages
 * Author URI: http://iqonic.design/
 **/
use App\baseClasses\KCActivate;
use App\baseClasses\KCDeactivate;
defined( 'ABSPATH' ) or die( 'Something went wrong' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
} else {
	die( 'Something went wrong' );
}

if (!defined('KIVI_CARE_DIR'))
{
	define('KIVI_CARE_DIR', plugin_dir_path(__FILE__));
}

if (!defined('KIVI_CARE_DIR_URI'))
{
	define('KIVI_CARE_DIR_URI', plugin_dir_url(__FILE__));
}

if (!defined('KIVI_CARE_BASE_NAME'))
{
    define('KIVI_CARE_BASE_NAME', plugin_basename(__FILE__));
}

if (!defined('KIVI_CARE_NAMESPACE'))
{
	define('KIVI_CARE_NAMESPACE', "kivi-care");
}

if (!defined('KIVI_CARE_PREFIX'))
{
	define('KIVI_CARE_PREFIX', "kiviCare_");
}

if (!defined('KIVI_CARE_VERSION'))
{
    define('KIVI_CARE_VERSION', "3.6.11");
}

/**
 * The code that runs during plugin activation
 */
register_activation_hook( __FILE__, [ KCActivate::class, 'activate'] );

/**
 * The code that runs during plugin deactivation
 */
register_deactivation_hook( __FILE__, [KCDeactivate::class, 'deActivate'] );

( new KCActivate )->init();

( new KCDeactivate() );

/**
 * Inyecta variables globales para el SPA lo más temprano posible.
 * No depende de handles registrados.
 */
add_action('admin_head', function () {
    if (!defined('KIVICARE_PLUGIN_URL')) {
        define('KIVICARE_PLUGIN_URL', plugin_dir_url(__FILE__));
    }
    if (!defined('KIVICARE_ASSETS_URL')) {
        define('KIVICARE_ASSETS_URL', KIVICARE_PLUGIN_URL . 'assets/');
    }
    $admin_base = admin_url(); // termina en /wp-admin/

    ?>
    <script>
      // Bases que usará el bundle y nuestro parche JS
      window.kc_admin_url  = <?php echo wp_json_encode($admin_base); ?>;
      window.kc_plugin_url = <?php echo wp_json_encode(KIVICARE_PLUGIN_URL); ?>;
      window.kc_assets_url = <?php echo wp_json_encode(KIVICARE_ASSETS_URL); ?>;
      window.kc_ajax_url   = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
    </script>
    <?php
}, 1); // prioridad baja = muy temprano

add_action('admin_enqueue_scripts', function () {
    // Solo en la SPA de KiviCare
    if (!isset($_GET['page']) || $_GET['page'] !== 'dashboard') {
        return;
    }

    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    $deps = ['jquery', 'thickbox'];
    if (wp_script_is('kc_custom', 'registered') || wp_script_is('kc_custom', 'enqueued')) {
        $deps[] = 'kc_custom'; // asegurar orden
    }

    $file    = KIVI_CARE_DIR . 'assets/js/custom.js';
    $version = file_exists($file) ? filemtime($file) : '3.6.11.4';

    wp_enqueue_script(
        'kivicare-custom',
        KIVI_CARE_DIR_URI . 'assets/js/custom.js',
        $deps,
        $version,
        true
    );

    wp_localize_script(
        'kivicare-custom',
        'request_data',
        [
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'get_nonce' => wp_create_nonce('ajax_get'),
        ]
    );
}, 99); // prioridad ALTA para ir después del core

/**
 * Opcional: quitar páginas de soporte/solicitud si existieran en el menú WP clásico.
 */
add_action('admin_menu', function () {
    @remove_menu_page('kc-support');
    @remove_menu_page('kc-request-feature');
    @remove_submenu_page('kivicare', 'kc-support');
    @remove_submenu_page('kivicare', 'kc-request-feature');
}, 999);
