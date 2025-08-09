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

add_action('admin_enqueue_scripts', function () {
    // Load assets only in KiviCare dashboard
    $is_kivicare_dashboard = isset($_GET['page']) && $_GET['page'] === 'dashboard';
    if (!$is_kivicare_dashboard) {
        return;
    }

    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');

    wp_enqueue_script(
        'kivicare-custom',
        KIVI_CARE_DIR_URI . 'assets/js/custom.js',
        array('jquery', 'thickbox'),
        '3.6.11.3',
        true
    );
    wp_localize_script(
        'kivicare-custom',
        'request_data',
        array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'get_nonce' => wp_create_nonce('ajax_get'),
        )
    );
}, 20);
