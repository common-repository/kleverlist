<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
class Kleverlist_Admin {
    private $plugin_name;

    private $version;

    private $screen_ids;

    private $plugin_slug;

    protected $required_plugins = [];

    public function __construct( $plugin_name, $version ) {
        $this->required_plugins = [[
            'plugin' => 'woocommerce/woocommerce.php',
            'name'   => 'WooCommerce',
            'slug'   => 'woocommerce',
            'class'  => 'WooCommerce',
            'active' => false,
        ]];
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->plugin_slug = 'kleverlist';
        /** Check Plugin Requirement **/
        add_action( 'admin_init', array($this, 'kleverlist_plugin_requirements'), 1 );
        /** Add Screen Filter for plugin screen **/
        add_filter( 'kleverlist_get_screen_ids', array($this, 'get_screen_ids'), 10 );
        /** Add Admin Menu Page **/
        add_action( 'admin_menu', array($this, 'kleverlist_register_settings_page') );
        add_action( 'admin_menu', array($this, 'kleverlist_modify_menu_items') );
        /** API Settings Call **/
        add_action( 'wp_ajax_kleverlist_sendy_settings', array($this, 'kleverlist_sendy_settings_handle') );
        /** Brand Settings Call **/
        add_action( 'wp_ajax_kleverlist_generate_lists', array($this, 'kleverlist_generate_lists_handle') );
        /** Mapping Settings Call **/
        add_action( 'wp_ajax_kleverlist_mapping_settings', array($this, 'kleverlist_mapping_settings_handle') );
        /** Remove API Settings Call **/
        add_action( 'wp_ajax_kleverlist_remove_api_info', array($this, 'kleverlist_remove_api_info_handle') );
        ///* Create Custom Endpoint */
        add_action( 'rest_api_init', array($this, 'create_custon_endpoint') );
    }

    function create_custon_endpoint() {
        register_rest_route( 'wp/v2', '/authenticate', array(
            'methods'             => 'POST',
            'callback'            => array($this, 'get_response'),
            'permission_callback' => '__return_true',
        ) );
    }

    // Handle authentication request
    function get_response( $request ) {
        // Get site URL from the request body
        $params = $request->get_params();
        $requested_site_url = ( isset( $params['site_url'] ) ? $params['site_url'] : '' );
        // Get current site URL
        $current_site_url = get_site_url();
        // Check if requested site URL matches the current site URL
        if ( $requested_site_url === $current_site_url ) {
            // Site URL matches, proceed with authentication
            $client_id = 'RLcEH3FJqTEXmY4GOc0M2HpPbf5qxhUl';
            $client_secret = 'PDPZA3J4jF93CNdfcnMLxBvlvf0pPy7t';
            // Return client_id and client_secret
            return rest_ensure_response( array(
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
            ) );
        } else {
            // Site URL does not match, return error
            return new WP_Error('url_mismatch_error', 'Site URL does not match.', array(
                'status' => 401,
            ));
        }
    }

    public function kleverlist_plugin_requirements() {
        if ( !$this->kleverlist_requirements_met() ) {
            add_action( 'admin_notices', [$this, 'kleverlist_show_plugin_not_found_notice'] );
            if ( is_plugin_active( plugin_basename( constant( 'KLEVERLIST_PLUGIN_FILE' ) ) ) ) {
                deactivate_plugins( plugin_basename( constant( 'KLEVERLIST_PLUGIN_FILE' ) ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset($_GET['activate']);
                }
                add_action( 'admin_notices', [$this, 'kleverlist_show_deactivate_notice'] );
            }
        }
    }

    /** Show required plugins not found message. **/
    public function kleverlist_show_plugin_not_found_notice() {
        $message = esc_html__( 'Kleverlist plugin requires WooCommerce to be installed and activated.', 'kleverlist' );
        $this->admin_notice( $message, 'notice notice-error is-dismissible' );
    }

    /** Show a notice to inform the user that the plugin has been deactivated. **/
    public function kleverlist_show_deactivate_notice() {
        $this->admin_notice( __( 'Kleverlist plugin has been deactivated.', 'kleverlist' ), 'notice notice-info is-dismissible' );
    }

    /** Check if plugin requirements met. **/
    private function kleverlist_requirements_met() {
        $all_active = true;
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        foreach ( $this->required_plugins as $key => $required_plugin ) {
            if ( is_plugin_active( $required_plugin['plugin'] ) ) {
                $this->required_plugins[$key]['active'] = true;
            } else {
                $all_active = false;
            }
        }
        return $all_active;
    }

    private function admin_notice( $message, $class ) {
        ?>
        <div class="<?php 
        echo esc_attr( $class );
        ?>">
            <p>
                <?php 
        echo wp_kses_post( $message );
        ?>
            </p>
        </div>
        <?php 
    }

    /**
     * Generate List Settings Callback
     */
    public function kleverlist_generate_lists_handle() {
        $response_arr = array();
        if ( isset( $_REQUEST['brand_id'] ) && !empty( $_REQUEST['brand_id'] ) && isset( $_POST['_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce'] ) ), 'kleverlist_ajax_nonce' ) ) {
            $brand_id = sanitize_text_field( $_REQUEST['brand_id'] );
            $kleverlist_service_settings = get_option( 'kleverlist_service_settings' );
            if ( !empty( $kleverlist_service_settings ) && $kleverlist_service_settings['service_verified'] === KLEVERLIST_SERVICE_VERIFIED ) {
                $api_url = esc_url_raw( $kleverlist_service_settings['service_domain_name'] );
                $api_key = sanitize_text_field( $kleverlist_service_settings['service_api_key'] );
                $postdata = array(
                    'api_key'        => $api_key,
                    'brand_id'       => $brand_id,
                    'include_hidden' => 'yes',
                );
                $response = wp_remote_post( $api_url . '/api/lists/get-lists.php', array(
                    'method'  => 'POST',
                    'headers' => array(
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ),
                    'body'    => $postdata,
                ) );
                if ( is_wp_error( $response ) ) {
                    // Handle errors if any.
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Error occurred while making the request.', 'kleverlist' ),
                    );
                } else {
                    $response_code = wp_remote_retrieve_response_code( $response );
                    $response_body = wp_remote_retrieve_body( $response );
                    $decoded_response = json_decode( $response_body );
                    // Decoded response as object
                    if ( $response_code === 200 && json_last_error() === JSON_ERROR_NONE && is_object( $decoded_response ) ) {
                        // Success case
                        $lists_option = array(
                            'sendy_api_brand_id' => $brand_id,
                            'sendy_api_lists'    => $decoded_response,
                        );
                        update_option( 'kleverlist_sendy_lists', $lists_option );
                        $response_arr = array(
                            'status'  => 1,
                            'message' => __( 'Load Lists Successfully', 'kleverlist' ),
                        );
                    } else {
                        // Error case
                        $response_arr = array(
                            'status'  => 0,
                            'message' => __( 'Please verify API details', 'kleverlist' ),
                        );
                    }
                }
            }
        } else {
            // Invalid or missing parameters
            if ( isset( $_REQUEST['brand_id'] ) && empty( $_REQUEST['brand_id'] ) ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Please choose a brand.', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong. Please try again later.', 'kleverlist' ),
                );
            }
        }
        wp_send_json( $response_arr );
        die;
    }

    /**
     * Dashboard Settings Callback
     */
    public function kleverlist_sendy_settings_handle() {
        $response_arr = array();
        if ( isset( $_REQUEST['domain_name'] ) && !empty( $_REQUEST['domain_name'] ) && get_option( 'kleverlist_service_type' ) !== 'mailchimp' && isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'kleverlist_ajax_nonce' ) ) {
            $api_url = sanitize_text_field( $_REQUEST['domain_name'] );
            $api_key = sanitize_text_field( $_REQUEST['api_key'] );
            $service_name = sanitize_text_field( $_REQUEST['service_name'] );
            if ( str_contains( $api_url, "https://" ) ) {
                $api_url = $api_url;
            } elseif ( str_contains( $api_url, "http://" ) ) {
                $api_url = str_replace( "http://", "https://", $api_url );
            } else {
                $api_url = "https://" . $api_url;
            }
            $postdata = array(
                'api_key' => $api_key,
            );
            $response = wp_remote_post( $api_url . '/api/brands/get-brands.php', array(
                'method'      => 'POST',
                'headers'     => array(
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ),
                'body'        => $postdata,
                'data_format' => 'body',
            ) );
            if ( !is_wp_error( $response ) ) {
                $result = $response['body'];
                json_decode( $result );
                switch ( json_last_error() ) {
                    case JSON_ERROR_NONE:
                        $response_arr = array(
                            'status'  => 1,
                            'message' => __( 'Verified Successfully', 'kleverlist' ),
                        );
                        $is_service_verified = KLEVERLIST_SERVICE_VERIFIED;
                        $is_service_type = $service_name;
                        update_option( 'kleverlist_sendy_brands', json_decode( $result ) );
                        break;
                    case JSON_ERROR_SYNTAX:
                        $response_arr = array(
                            'status'  => 0,
                            'message' => __( 'Invalid website domain name', 'kleverlist' ),
                        );
                        $is_service_type = $service_name;
                        $is_service_verified = 'no';
                        break;
                    default:
                        $response_arr = array(
                            'status'  => 0,
                            'message' => __( 'Please enter proper details', 'kleverlist' ),
                        );
                        break;
                }
                $option_array = [];
                $option_array['service_verified'] = $is_service_verified;
                $option_array['service_type'] = $is_service_type;
                $option_array['service_api_key'] = $api_key;
                $option_array['service_domain_name'] = $api_url;
                update_option( 'kleverlist_service_settings', $option_array );
                update_option( 'kleverlist_service_type', $is_service_type );
            } else {
                // Handle error from wp_remote_post
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Please enter proper details', 'kleverlist' ),
                );
            }
        } else {
            if ( get_option( 'kleverlist_service_type' ) === 'mailchimp' ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Cannot activate Sendy integration while Mailchimp integration is active.', 'kleverlist' ),
                );
            } elseif ( isset( $_REQUEST['api_key'] ) && empty( $_REQUEST['api_key'] ) || isset( $_REQUEST['domain_name'] ) && empty( $_REQUEST['domain_name'] ) ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'All Input fields required', 'kleverlist' ),
                );
            } elseif ( isset( $_REQUEST['api_key'] ) && empty( $_REQUEST['api_key'] ) ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'API Key required', 'kleverlist' ),
                );
            } elseif ( isset( $_REQUEST['domain_name'] ) && empty( $_REQUEST['domain_name'] ) ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Domain name required', 'kleverlist' ),
                );
            }
        }
        wp_send_json( $response_arr );
        die;
    }

    /**
     * Mapping Settings Callback
     */
    public function kleverlist_mapping_settings_handle() {
        $response_arr = array();
        if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_email_allowed'] ) && $_REQUEST['kleverlist_sendy_mapping_user_email_allowed'] === 'yes' && isset( $_POST['_nonce_'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_nonce_'] ) ), 'kleverlist_ajax_nonce' ) ) {
            $user_email_allowed = sanitize_text_field( $_REQUEST['kleverlist_sendy_mapping_user_email_allowed'] );
            update_option( 'kleverlist_sendy_mapping_user_email_allowed', $user_email_allowed );
            // User Full Name
            if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_fullname'] ) && $_POST["kleverlist_sendy_mapping_user_fullname"] != '' ) {
                $fullname = sanitize_text_field( $_REQUEST['kleverlist_sendy_mapping_user_fullname'] );
                update_option( 'kleverlist_sendy_mapping_user_fullname', $fullname );
            }
            // User First Name
            if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_firstname'] ) && $_POST["kleverlist_sendy_mapping_user_firstname"] != '' ) {
                $firstname = sanitize_text_field( $_REQUEST['kleverlist_sendy_mapping_user_firstname'] );
                update_option( 'kleverlist_sendy_mapping_user_firstname', $firstname );
            }
            // User Last Name
            if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_lastname'] ) && $_POST["kleverlist_sendy_mapping_user_lastname"] != '' ) {
                $lastname = sanitize_text_field( $_REQUEST['kleverlist_sendy_mapping_user_lastname'] );
                update_option( 'kleverlist_sendy_mapping_user_lastname', $lastname );
            }
            // User Username
            if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_username'] ) && $_POST["kleverlist_sendy_mapping_user_username"] != '' ) {
                $username = sanitize_text_field( $_REQUEST['kleverlist_sendy_mapping_user_username'] );
                update_option( 'kleverlist_sendy_mapping_user_username', $username );
            }
            $response_arr = array(
                'status'  => 1,
                'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
            );
        } else {
            if ( isset( $_REQUEST['kleverlist_sendy_mapping_user_email_allowed'] ) && $_REQUEST['kleverlist_sendy_mapping_user_email_allowed'] === 'no' ) {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Email is required', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong, Please try again later', 'kleverlist' ),
                );
            }
        }
        wp_send_json( $response_arr );
        die;
    }

    /**
     * Remove API Settings Callback
     */
    public function kleverlist_remove_api_info_handle() {
        $response_arr = array();
        if ( isset( $_POST['__nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['__nonce'] ) ), 'kleverlist_ajax_nonce' ) ) {
            // API Configuration details
            delete_option( 'kleverlist_service_settings' );
            // Remove brand details
            delete_option( 'kleverlist_sendy_brands' );
            // Remove list details
            delete_option( 'kleverlist_sendy_lists' );
            // Remove mapping settings
            delete_option( 'kleverlist_mapping_settings' );
            // Remove Mailchimp API Key
            delete_option( 'kleverlist_mailchimp_apikey' );
            // Remove Mailchimp API URL
            delete_option( 'kleverlist_mailchimp_apiurl' );
            // Remove Mailchimp Service Type
            delete_option( 'kleverlist_service_type' );
            // Remove Mailchimp Audience Lists
            delete_option( 'kleverlist_mailchimp_audience_lists' );
            // Remove Mailchimp User Audience
            delete_option( 'kleverlist_mailchimp_user_audience' );
            // Remove Mailchimp Migration Option
            delete_option( 'klerverlist_mailchimp_migration_allow' );
            // Remove Aweber Migration Option
            delete_option( 'klerverlist_aweber_migration_allow' );
            // Remove Sendy Migration Option
            delete_option( 'klerverlist_sendy_migration_allow' );
            // Remove AWeber Token Data
            delete_option( 'kleverlist_aweber_tokenData' );
            // Remove AWeber Auth Code
            delete_option( 'kleverlist_aweber_auth_code' );
            // Remove AWeber Account ID
            delete_option( 'kleverlist_aweber_accounts_id' );
            // Remove AWeber Account List Data
            delete_option( 'kleverlist_aweber_account_lists_data' );
            // Remove AWeber User Selected Account ID
            delete_option( 'kleverlist_aweber_user_selected_account_id' );
            // Remove AWeber User Global Account ID
            delete_option( 'kleverlist_aweber_global_account_id' );
            $redirect_uri = admin_url( 'admin.php?page=kleverlist-integrations' );
            $response_arr = array(
                'status'       => 1,
                'redirect_uri' => $redirect_uri,
                'message'      => __( 'API Info removed successfully', 'kleverlist' ),
            );
            wp_send_json( $response_arr );
            die;
        }
    }

    /**
     * Hide API Info
     */
    public static function hide_input_character( $input_char, $visible_chars = 3 ) {
        $length = strlen( $input_char );
        // Input character lenght count
        $hidden_chars = $length - $visible_chars;
        // Number of characters to hide
        $hidden_part = str_repeat( '*', $hidden_chars );
        // Create the hidden part with asterisks
        $result = substr( $input_char, 0, $visible_chars ) . $hidden_part;
        return $result;
    }

    public function kleverlist_admin_notice() {
        $message = $this->get_woocommerce_required_message();
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
    }

    private function get_woocommerce_required_message() {
        $message = sprintf( esc_html__( 'WooCommerce is required for the %s plugin.', 'kleverlist' ), esc_html( $this->plugin_name ) );
        return $message;
    }

    /**
     * Add plugin screen function
     */
    public function get_screen_ids( $screen_ids ) {
        $screen_ids[] = 'toplevel_page_' . $this->plugin_name;
        $screen_ids[] = 'toplevel_page_' . $this->plugin_name . '-premium';
        $screen_ids[] = 'kleverlist_page_kleverlist-integrations';
        $screen_ids[] = 'kleverlist_page_kleverlist-mapping';
        $screen_ids[] = 'kleverlist_page_kleverlist-migration';
        $screen_ids[] = 'kleverlist_page_kleverlist-tags';
        $screen_ids[] = 'kleverlist_page_kleverlist-global-settings';
        $screen_ids[] = 'kleverlist_page_kleverlist-account';
        $screen_ids[] = 'kleverlist_page_kleverlist-affiliation';
        $screen_ids[] = 'kleverlist_page_kleverlist-contact';
        return $screen_ids;
    }

    /**
     * Register a custom menu page.
     */
    public function kleverlist_register_settings_page() {
        // Top Level menu
        add_menu_page(
            __( 'KleverList', 'kleverlist' ),
            #page_title
            'KleverList',
            #menu_title
            'manage_options',
            #caapability
            $this->plugin_slug,
            #menu_slug
            array($this, 'kleverlist_quick_start_page'),
            #callback
            'dashicons-buddicons-pm',
            #icon_url
            58
        );
        // Quick Start Submenu
        add_submenu_page(
            'kleverlist',
            #parent_slug
            __( 'Quick Start', 'kleverlist' ),
            #submenu_page_title
            __( 'Quick Start', 'kleverlist' ),
            #submenu_title
            'manage_options',
            #caapability
            $this->plugin_slug,
            #submenu_slug
            [$this, 'kleverlist_quick_start_page']
        );
        // Integrations Submenu
        add_submenu_page(
            $this->plugin_slug,
            #parent_slug
            __( 'Integrations', 'kleverlist' ),
            #submenu_page_title
            __( 'Integrations', 'kleverlist' ),
            #submenu_title
            'manage_options',
            #caapability
            'kleverlist-integrations',
            #submenu_slug
            [$this, 'kleverlist_integrations_settings_page']
        );
        // Mapping Submenu
        add_submenu_page(
            $this->plugin_slug,
            #parent_slug
            __( 'Mapping', 'kleverlist' ),
            #submenu_page_title
            __( 'Mapping', 'kleverlist' ),
            #submenu_title
            'manage_options',
            #caapability
            'kleverlist-mapping',
            #submenu_slug
            [$this, 'kleverlist_mapping_submenu_page']
        );
        if ( function_exists( 'kleverlist_fs' ) ) {
        }
        $service_type = get_option( 'kleverlist_service_type' );
        if ( $service_type === KLEVERLIST_SERVICE_MAILCHIMP || $service_type === KLEVERLIST_SERVICE_SENDY || $service_type === KLEVERLIST_SERVICE_AWEBER ) {
            switch ( $service_type ) {
                case KLEVERLIST_SERVICE_MAILCHIMP:
                    $callback_function = 'kleverlist_mailchimp_tags_submenu_page';
                    break;
                case KLEVERLIST_SERVICE_AWEBER:
                    $callback_function = 'kleverlist_aweber_tags_submenu_page';
                    break;
                default:
                    // Handle default case or unknown service type
                    $callback_function = 'kleverlist_sendy_tags_submenu_page';
            }
            add_submenu_page(
                $this->plugin_slug,
                // parent_slug
                __( 'Tags', 'kleverlist' ),
                // submenu_page_title
                __( 'Tags', 'kleverlist' ),
                // submenu_title
                'manage_options',
                // capability
                'kleverlist-tags',
                // submenu_slug
                [$this, $callback_function]
            );
        }
        // Global Settings Submenu
        add_submenu_page(
            $this->plugin_slug,
            #parent_slug
            __( 'Settings', 'kleverlist' ),
            #submenu_page_title
            __( 'Settings', 'kleverlist' ),
            #submenu_title
            'manage_options',
            #caapability
            'kleverlist-global-settings',
            #submenu_slug
            [$this, 'kleverlist_global_settings_submenu_page']
        );
    }

    public function kleverlist_modify_menu_items() {
        global $submenu;
        // Add a specific class to the 'Mapping' and 'Tags' menu items
        if ( isset( $submenu['kleverlist'] ) ) {
            foreach ( $submenu['kleverlist'] as &$submenu_item ) {
                if ( isset( $submenu_item[2] ) && ($submenu_item[2] === 'kleverlist-mapping' || $submenu_item[2] === 'kleverlist-tags') ) {
                    $submenu_item[] = 'kleverlist-menu-item-hidden';
                }
            }
        }
    }

    /**
     * Quick Start Callback Function
     */
    public function kleverlist_quick_start_page() {
        include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-quick-start.php';
    }

    /**
     * Integrations Menu Callback Function
     */
    public function kleverlist_integrations_settings_page() {
        include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-integrations.php';
    }

    /**
     * Mapping Submenu Callback Function
     */
    public function kleverlist_mapping_submenu_page() {
        include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-mapping.php';
    }

    /**
     * Mapping Submenu Callback Function
     */
    public function kleverlist_migration_submenu_page() {
        include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-migration.php';
    }

    /**
     * Mailchimp Tags Management Submenu Callback Function
     */
    public function kleverlist_mailchimp_tags_submenu_page() {
        if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_MAILCHIMP ) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-mailchimp-tag-management.php';
        }
    }

    /**
     * Sendy Tags Management Submenu Callback Function
     */
    public function kleverlist_sendy_tags_submenu_page() {
        if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_SENDY ) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-sendy-tag-management.php';
        }
    }

    /**
     * Aweber Tags Management Submenu Callback Function
     */
    public function kleverlist_aweber_tags_submenu_page() {
        if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_AWEBER ) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-aweber-tag-management.php';
        }
    }

    /**
     * Global Settings Submenu Callback Function
     */
    public function kleverlist_global_settings_submenu_page() {
        include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-global-settings.php';
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        $this->screen_ids = apply_filters( 'kleverlist_get_screen_ids', $this->screen_ids );
        if ( in_array( get_current_screen()->id, $this->screen_ids ) ) {
            wp_enqueue_style(
                $this->plugin_name,
                KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'css/kleverlist-admin.css',
                array(),
                $this->version,
                'all'
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $this->screen_ids = apply_filters( 'kleverlist_get_screen_ids', $this->screen_ids );
        if ( in_array( get_current_screen()->id, $this->screen_ids ) ) {
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/kleverlist-admin.js',
                array('jquery'),
                $this->version,
                false
            );
            wp_enqueue_script(
                'global',
                plugin_dir_url( __FILE__ ) . 'js/kleverlist-global.js',
                array('jquery'),
                $this->version,
                false
            );
            // kleverlist plugin object
            $is_kleverlist_premium_type = null;
            $totalCustomers = 0;
            wp_localize_script( $this->plugin_name, 'kleverlist_object', array(
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'admin_url'             => admin_url(),
                'totalCustomers'        => $totalCustomers,
                'nonce'                 => wp_create_nonce( 'kleverlist_ajax_nonce' ),
                'is_kleverlist_premium' => $is_kleverlist_premium_type,
            ) );
        }
    }

}
