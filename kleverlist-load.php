<?php

require 'vendor/autoload.php';
// Load the HTTP client library
use GuzzleHttp\Client;
use MailchimpMarketing\ApiClient;
$plugin_class = 'kleverlist-free-plan';
$plugin_plan = 'kleverlist-free';
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    if ( !function_exists( 'kleverlist_fs' ) ) {
        // Create a helper function for easy SDK access.
        function kleverlist_fs() {
            global $kleverlist_fs;
            if ( !isset( $kleverlist_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $kleverlist_fs = fs_dynamic_init( array(
                    'id'              => '12489',
                    'slug'            => 'kleverlist',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_3134d843e3c025c47ba22a04587bd',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Pro',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                        'slug'    => 'kleverlist',
                        'support' => false,
                    ),
                    'is_live'         => true,
                ) );
            }
            return $kleverlist_fs;
        }

        // Init Freemius.
        kleverlist_fs();
        // Signal that SDK was initiated.
        do_action( 'kleverlist_fs_loaded' );
    }
}
define( 'KLEVERLIST_VERSION', '1.0.0' );
define( 'KLEVERLIST_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'KLEVERLIST_ROOT_DIR_ADMIN', dirname( __FILE__ ) . '/admin' );
define( 'KLEVERLIST_PLUGIN_PUBLIC_DIR_URL', plugin_dir_url( __FILE__ ) . '/public' );
define( 'KLEVERLIST_PLUGIN_ADMIN_DIR_URL', plugin_dir_url( __FILE__ ) . 'admin/' );
define( 'KLEVERLIST_SERVICE_VERIFIED', 'verified' );
define( 'KLEVERLIST_SERVICE_MAILCHIMP', 'mailchimp' );
define( 'KLEVERLIST_SERVICE_SENDY', 'sendy' );
define( 'KLEVERLIST_SERVICE_AWEBER', 'aweber' );
define( 'KLEVERLIST_DEFAULT_PROCESSING_TAG', 'order processing' );
define( 'KLEVERLIST_DEFAULT_COMPLETED_TAG', 'order complete' );
define( 'KLEVERLIST_PLUGIN_CLASS', $plugin_class );
define( 'KLEVERLIST_PLUGIN_PLAN', $plugin_plan );
/**
 * Main plugin file.
 */
const KLEVERLIST_PLUGIN_FILE = __FILE__;
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kleverlist-activator.php
 */
function kleverlist_activate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-kleverlist-activator.php';
    Kleverlist_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kleverlist-deactivator.php
 */
function kleverlist_deactivate() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-kleverlist-deactivator.php';
    Kleverlist_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'kleverlist_activate' );
register_deactivation_hook( __FILE__, 'kleverlist_deactivate' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kleverlist.php';
/**
 * The core plugin file that is used to delete plugin data when uninstall plugin
*/
require plugin_dir_path( __FILE__ ) . 'includes/kleverlist-cleanup.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function kleverlist_run() {
    $plugin = new Kleverlist();
    $plugin->run();
}

kleverlist_run();