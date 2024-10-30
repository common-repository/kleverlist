<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
if ( !class_exists( 'Kleverlist_Ajax' ) ) {
    class Kleverlist_Ajax {
        private $plugin_name;

        private $version;

        private $screen_ids;

        protected $required_plugins = [];

        public static $aweberClientId = 'JU5i6Kny0d3fuHclYDmmVkUOFFcOGATy';

        public static $tokenBaseURL = 'https://auth.aweber.com/oauth2/token';

        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            /** Global Settings Call **/
            add_action( 'wp_ajax_kleverlist_global_settings', array($this, 'kleverlist_global_settings_handle') );
            /** Mailchimp API Settings Call **/
            add_action( 'wp_ajax_kleverlist_mailchimp_setting', array($this, 'kleverlist_mailchimp_setting_handle') );
            /** Mailchimp Load Audience Call **/
            add_action( 'wp_ajax_kleverlist_load_mailchimp_audience', array($this, 'kleverlist_load_mailchimp_audience_handle') );
            /** Mapping Settings Call **/
            add_action( 'wp_ajax_kleverlist_mailchimp_mapping_settings', array($this, 'kleverlist_mailchimp_mapping_settings_handle') );
            /** Mailchimp Tag Settings Call **/
            add_action( 'wp_ajax_kleverlist_mailchimp_tags_settings', array($this, 'kleverlist_mailchimp_tags_settings_handle') );
            /** Aweber Tag Settings Call **/
            add_action( 'wp_ajax_kleverlist_aweber_tags_settings', array($this, 'kleverlist_aweber_tags_settings_handle') );
            /** Sendy Tag Settings Call **/
            add_action( 'wp_ajax_kleverlist_sendy_tags_settings', array($this, 'kleverlist_sendy_tags_settings_handle') );
            /** Global Settings Call **/
            add_action( 'wp_ajax_kleverlist_mailchimp_global_settings', array($this, 'kleverlist_mailchimp_global_settings_handle') );
            // This hook handles bulk product assignment for Sendy, exclusively for premium users.
            add_action( 'wp_ajax_kleverlist_sendy_bulk_list_settings', array($this, 'kleverlist_sendy_bulk_list_settings') );
            // This hook handles bulk product assignment for MailChimp, exclusively for premium users.
            add_action( 'wp_ajax_kleverlist_mailchimp_bulk_list_settings', array($this, 'kleverlist_mailchimp_bulk_list_settings') );
            // This hook handles bulk product assignment for Aweber, exclusively for premium users.
            add_action( 'wp_ajax_kleverlist_aweber_bulk_list_settings', array($this, 'kleverlist_aweber_bulk_list_settings') );
            if ( function_exists( 'kleverlist_fs' ) ) {
            }
        }

        /**
         * Global Settings Callback
         */
        public function kleverlist_global_settings_handle() {
            $response_arr = array();
            $response_status = true;
            $status = null;
            $message = null;
            if ( isset( $_POST['sendy_list_id'] ) && !empty( $_POST['sendy_list_id'] ) && isset( $_POST['global_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['global_nonce'] ) ), 'kleverlist_ajax_nonce' ) ) {
                if ( $response_status ) {
                    $sendy_list_id = sanitize_text_field( $_POST['sendy_list_id'] );
                    update_option( 'kleverlist_global_sendy_list_id', $sendy_list_id );
                    // User Resubscribe
                    if ( isset( $_POST['user_resubscribe'] ) && $_POST["user_resubscribe"] != '' ) {
                        $resubscribe = sanitize_text_field( $_POST['user_resubscribe'] );
                        update_option( 'kleverlist_global_resubscribe', $resubscribe );
                        if ( isset( $_POST['resubscribe_order_action'] ) && $_POST["resubscribe_order_action"] != '' ) {
                            $resubscribe_order_action = sanitize_text_field( $_POST['resubscribe_order_action'] );
                            update_option( 'kleverlist_sendy_global_resubscribe_order_action_option', $resubscribe_order_action );
                        }
                    }
                    // Sendy 1-Click Activation
                    if ( isset( $_POST['active_all_products'] ) && $_POST["active_all_products"] != '' ) {
                        $all_products = sanitize_text_field( $_POST['active_all_products'] );
                        update_option( 'kleverlist_sendy_global_active_all_products', $all_products );
                        if ( isset( $_POST['active_all_on_order_processing'] ) && $_POST["active_all_on_order_processing"] != '' ) {
                            $active_all_on_order_processing = sanitize_text_field( $_POST['active_all_on_order_processing'] );
                            update_option( 'kleverlist_sendy_global_active_all_order_processing_action', $active_all_on_order_processing );
                        }
                        if ( isset( $_POST['active_all_on_order_complete'] ) && $_POST["active_all_on_order_complete"] != '' ) {
                            $active_all_on_order_complete = sanitize_text_field( $_POST['active_all_on_order_complete'] );
                            update_option( 'kleverlist_sendy_global_active_all_order_complete_action', $active_all_on_order_complete );
                        }
                    }
                    $status = 1;
                    $message = __( 'Setting Saved Successfully', 'kleverlist' );
                }
                $response_arr = array(
                    'status'  => $status,
                    'message' => $message,
                );
            } else {
                if ( isset( $_POST['sendy_list_id'] ) && empty( $_POST['sendy_list_id'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please Choose your default list', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Mailchimp API Settings Callback
         */
        public function kleverlist_mailchimp_setting_handle() {
            $response_arr = array();
            $list_data = array();
            if ( isset( $_REQUEST['apikey'] ) && !empty( $_REQUEST['apikey'] ) && isset( $_REQUEST['apiurl'] ) && !empty( $_REQUEST['apiurl'] ) && isset( $_REQUEST['service_name'] ) && !empty( $_REQUEST['service_name'] ) && get_option( 'kleverlist_service_type' ) !== 'sendy' && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                $api_key = sanitize_text_field( $_REQUEST['apikey'] );
                $api_url = sanitize_text_field( $_REQUEST['apiurl'] );
                $service_name = sanitize_text_field( $_REQUEST['service_name'] );
                $lists = Kleverlist_Ajax::get_mailchimp_lists( $api_key, $api_url );
                // Access the lists
                if ( $lists ) {
                    foreach ( $lists as $list ) {
                        $list_id = sanitize_text_field( $list['id'] );
                        $list_name = sanitize_text_field( $list['name'] );
                        $list_data[$list_id] = $list_name;
                    }
                    // Save the list data in options
                    update_option( 'kleverlist_mailchimp_audience_lists', $list_data );
                    // Save service type in options
                    update_option( 'kleverlist_service_type', $service_name );
                    // Save API Key in options
                    update_option( 'kleverlist_mailchimp_apikey', $api_key );
                    // Save API URL in options
                    update_option( 'kleverlist_mailchimp_apiurl', $api_url );
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Verified Successfully', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please enter valid api key and api url', 'kleverlist' ),
                    );
                }
            } else {
                if ( get_option( 'kleverlist_service_type' ) === 'sendy' ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Cannot activate Mailchimp integration while Sendy integration is active.', 'kleverlist' ),
                    );
                } elseif ( isset( $_REQUEST['apikey'] ) && empty( $_REQUEST['apikey'] ) && isset( $_REQUEST['apiurl'] ) && empty( $_REQUEST['apiurl'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please enter api key and api url', 'kleverlist' ),
                    );
                } elseif ( isset( $_REQUEST['apikey'] ) && empty( $_REQUEST['apikey'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please enter api key', 'kleverlist' ),
                    );
                } elseif ( isset( $_REQUEST['apiurl'] ) && empty( $_REQUEST['apiurl'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please enter api url', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Something went wrong', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            wp_die();
        }

        /**
         * Get Mailchimp Lists
         */
        public static function get_mailchimp_lists( $api_key, $api_url ) {
            // To check if a URL has an ending slash
            if ( substr( $api_url, -1 ) !== '/' ) {
                $api_url .= '/lists/';
            } else {
                $api_url .= 'lists/';
            }
            $args = array(
                'headers' => array(
                    'Authorization' => 'apikey ' . $api_key,
                ),
            );
            $response = wp_remote_get( $api_url, $args );
            if ( is_wp_error( $response ) ) {
                return false;
            } else {
                $response_code = wp_remote_retrieve_response_code( $response );
                if ( $response_code === 200 ) {
                    $body = wp_remote_retrieve_body( $response );
                    // Check if $body is not null before attempting to decode it
                    if ( $body !== null ) {
                        $lists = json_decode( $body, true );
                        // Make sure $lists is an array before accessing its elements
                        if ( is_array( $lists ) && isset( $lists['lists'] ) ) {
                            return $lists['lists'];
                        } else {
                            // Handle the case where 'lists' key is not present in the decoded JSON
                            return null;
                            // Or some other default value or error handling
                        }
                    } else {
                        // Handle the case where $body is null or empty
                        return null;
                        // Or some other default value or error handling
                    }
                } else {
                    // Handle the case where $body is null or empty
                    return null;
                    // Or some other default value or error handling
                }
            }
        }

        /**
         * Mailchimp Load Audience Callback
         */
        public function kleverlist_load_mailchimp_audience_handle() {
            $response_arr = array();
            if ( isset( $_REQUEST['user_audience'] ) && !empty( $_REQUEST['user_audience'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_MAILCHIMP ) {
                    $user_audience = sanitize_text_field( $_REQUEST['user_audience'] );
                    update_option( 'kleverlist_mailchimp_user_audience', $user_audience );
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Load Audience Successfully', 'kleverlist' ),
                    );
                }
            } else {
                if ( isset( $_REQUEST['user_audience'] ) && empty( $_REQUEST['user_audience'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please choose audience', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Something wrong, Please try again later', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Mapping Settings Callback
         */
        public function kleverlist_mailchimp_mapping_settings_handle() {
            $response_arr = array();
            if ( isset( $_POST['user_email'] ) && !empty( $_POST['user_email'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                $user_email = sanitize_text_field( $_POST['user_email'] );
                update_option( 'kleverlist_mailchimp_user_email', $user_email );
                // User firstname
                if ( isset( $_POST['firstname'] ) && $_POST["firstname"] != '' ) {
                    $firstname = sanitize_text_field( $_POST['firstname'] );
                    update_option( 'kleverlist_mailchimp_firstname', $firstname );
                }
                // User lastname
                if ( isset( $_POST['lastname'] ) && $_POST["lastname"] != '' ) {
                    $lastname = sanitize_text_field( $_POST['lastname'] );
                    update_option( 'kleverlist_mailchimp_lastname', $lastname );
                }
                // Username
                if ( isset( $_POST['username'] ) && $_POST["username"] != '' ) {
                    $username = sanitize_text_field( $_POST['username'] );
                    update_option( 'kleverlist_mailchimp_username', $username );
                }
                $response_arr = array(
                    'status'  => 1,
                    'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
                );
            } else {
                if ( isset( $_REQUEST['user_email'] ) && $_REQUEST['user_email'] === 'no' ) {
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
         * Sendy Tag Settings Callback
         */
        public function kleverlist_sendy_tags_settings_handle() {
            $response_arr = array();
            if ( isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                // Order Processing
                if ( isset( $_POST['order_processing'] ) && $_POST["order_processing"] != '' ) {
                    $order_processing = sanitize_text_field( $_POST['order_processing'] );
                    update_option( 'kleverlist_sendy_order_processing_tag', $order_processing );
                }
                // Order Completed
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' ) {
                    $order_completed = sanitize_text_field( $_POST['order_completed'] );
                    update_option( 'kleverlist_sendy_order_completed_tag', $order_completed );
                }
                // Remove Order Processing Tag
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' && isset( $_POST['remove_order_processing_tag'] ) && $_POST["remove_order_processing_tag"] != '' ) {
                    $remove_order_processing_tag = sanitize_text_field( $_POST['remove_order_processing_tag'] );
                    update_option( 'kleverlist_sendy_remove_order_processing_tag', $remove_order_processing_tag );
                }
                $response_arr = array(
                    'status'  => 1,
                    'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong, Please try again later', 'kleverlist' ),
                );
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Mapping Tag Settings Callback
         */
        public function kleverlist_mailchimp_tags_settings_handle() {
            $response_arr = array();
            if ( isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                // Order Processing
                if ( isset( $_POST['order_processing'] ) && $_POST["order_processing"] != '' ) {
                    $order_processing = sanitize_text_field( $_POST['order_processing'] );
                    update_option( 'kleverlist_mailchimp_order_processing', $order_processing );
                }
                // Order Completed
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' ) {
                    $order_completed = sanitize_text_field( $_POST['order_completed'] );
                    update_option( 'kleverlist_mailchimp_order_completed', $order_completed );
                }
                // Remove Order Processing Tag
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' && isset( $_POST['remove_order_processing_tag'] ) && $_POST["remove_order_processing_tag"] != '' ) {
                    $remove_order_processing_tag = sanitize_text_field( $_POST['remove_order_processing_tag'] );
                    update_option( 'kleverlist_mailchimp_remove_order_processing_tag', $remove_order_processing_tag );
                }
                $response_arr = array(
                    'status'  => 1,
                    'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong, Please try again later', 'kleverlist' ),
                );
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Mailchimp Global Settings Callback
         */
        public function kleverlist_mailchimp_global_settings_handle() {
            $response_arr = array();
            $response_status = true;
            $status = null;
            $message = null;
            if ( isset( $_REQUEST['audience_id'] ) && !empty( $_REQUEST['audience_id'] ) && isset( $_REQUEST['global_mc_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['global_mc_nonce'] ) ), 'kleverlist_ajax_nonce' ) ) {
                if ( $response_status ) {
                    $audience_id = sanitize_text_field( $_REQUEST['audience_id'] );
                    update_option( 'kleverlist_mailchimp_global_audience_id', $audience_id );
                    // User Resubscribe
                    if ( isset( $_REQUEST['user_resubscribe'] ) && $_POST["user_resubscribe"] != '' ) {
                        $resubscribe = sanitize_text_field( $_REQUEST['user_resubscribe'] );
                        update_option( 'kleverlist_mailchimp_global_resubscribe', $resubscribe );
                        if ( isset( $_REQUEST['resubscribe_order_action'] ) && $_POST["resubscribe_order_action"] != '' ) {
                            $resubscribe_order_action = sanitize_text_field( $_REQUEST['resubscribe_order_action'] );
                            update_option( 'kleverlist_mailchimp_global_resubscribe_order_action_option', $resubscribe_order_action );
                        }
                    }
                    // User Active All Products
                    if ( isset( $_REQUEST['active_all_products'] ) && $_POST["active_all_products"] != '' ) {
                        $all_products = sanitize_text_field( $_REQUEST['active_all_products'] );
                        update_option( 'kleverlist_mailchimp_global_active_all_products', $all_products );
                        if ( $all_products === '1' ) {
                            $active_all_action = sanitize_text_field( $_REQUEST['active_all_action'] );
                            update_option( 'kleverlist_mailchimp_global_active_all_order_action', $active_all_action );
                        }
                    }
                    // Activity Insight
                    /*if (isset($_REQUEST['activity_insights']) && $_POST["activity_insights"] !=''
                                        ) {
                                            $activity_insights = sanitize_text_field($_REQUEST['activity_insights']);
                                            update_option('kleverlist_mailchimp_global_activity_insights', $activity_insights);
                    
                                            if (isset($_REQUEST['activity_insights_action']) && $_POST["activity_insights_action"] !='') {
                                                $activity_insights_action = sanitize_text_field($_REQUEST['activity_insights_action']);
                                                update_option('kleverlist_mailchimp_global_activity_insights_order_action_option', $activity_insights_action);
                                            }
                                        }*/
                    $status = 1;
                    $message = __( 'Setting Saved Successfully', 'kleverlist' );
                }
                $response_arr = array(
                    'status'  => $status,
                    'message' => $message,
                );
            } else {
                if ( isset( $_REQUEST['audience_id'] ) && empty( $_REQUEST['audience_id'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Audience is required', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Sendy Product Bulk List Assign Callback
         */
        public function kleverlist_sendy_bulk_list_settings() {
            $response_arr = array();
            // Check if the request is valid with nonce verification
            $valid_request = isset( $_POST['kleverlist_sendy_bulk_choosen_list'] ) && !empty( $_POST['kleverlist_sendy_bulk_choosen_list'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' );
            $processing_checkbox = isset( $_POST['kleverlist_sendy_bulk_list_order_processing_checkbox'] ) && $_POST['kleverlist_sendy_bulk_list_order_processing_checkbox'] === '1';
            $completed_checkbox = isset( $_POST['kleverlist_sendy_bulk_list_order_completed_checkbox'] ) && $_POST['kleverlist_sendy_bulk_list_order_completed_checkbox'] === '1';
            $radio_action = isset( $_POST['kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio'] ) && !empty( $_POST['kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio'] );
            if ( $valid_request && ($processing_checkbox || $completed_checkbox) && $radio_action ) {
                $product_ids = ( isset( $_POST['ids'] ) ? $_POST['ids'] : array() );
                $radio_action = sanitize_text_field( $_POST['kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio'] );
                if ( !empty( $product_ids ) ) {
                    foreach ( $product_ids as $product_id ) {
                        ######### Subscribe Product Action #########
                        if ( $processing_checkbox && $radio_action === 'subscribe' ) {
                            update_post_meta( $product_id, '_order_processing_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_sendy_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_sendy_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_order_processing_special_product_list', $selected_list );
                        }
                        if ( $completed_checkbox && $radio_action === 'subscribe' ) {
                            update_post_meta( $product_id, '_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_sendy_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_sendy_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_special_product_list', $selected_list );
                        }
                        ######### Subscribe Product Action #########
                        ######### Unsubscribe Product Action #########
                        if ( $processing_checkbox && $radio_action === 'unsubscribe' ) {
                            update_post_meta( $product_id, '_order_processing_unsubscribe_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_sendy_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_sendy_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_order_processing_unsubscribe_product_list', $selected_list );
                        }
                        if ( $completed_checkbox && $radio_action === 'unsubscribe' ) {
                            update_post_meta( $product_id, '_unsubscribe_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_sendy_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_sendy_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_unsubscribe_product_list', $selected_list );
                        }
                        ######### Unsubscribe Product Action #########
                    }
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Settings saved successfully', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select products', 'kleverlist' ),
                    );
                }
            } else {
                if ( isset( $_REQUEST['kleverlist_sendy_bulk_choosen_list'] ) && empty( $_REQUEST['kleverlist_sendy_bulk_choosen_list'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please choose list', 'kleverlist' ),
                    );
                } elseif ( !$processing_checkbox && !$completed_checkbox ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select order action', 'kleverlist' ),
                    );
                } elseif ( empty( $radio_action ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please check subscribe or unsubscribe radio button', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * MailChimp Product Bulk List Assign Callback
         */
        public function kleverlist_mailchimp_bulk_list_settings() {
            $response_arr = array();
            $radio_action = '';
            // Check if the request is valid with nonce verification
            $valid_request = isset( $_REQUEST['kleverlist_mailchimp_bulk_choosen_audience'] ) && !empty( $_REQUEST['kleverlist_mailchimp_bulk_choosen_audience'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_mcwc_nonce' );
            $processing_checkbox = isset( $_REQUEST['kleverlist_mailchimp_bulk_list_order_processing_checkbox'] ) && $_REQUEST['kleverlist_mailchimp_bulk_list_order_processing_checkbox'] === '1';
            $completed_checkbox = isset( $_REQUEST['kleverlist_mailchimp_bulk_list_order_completed_checkbox'] ) && $_REQUEST['kleverlist_mailchimp_bulk_list_order_completed_checkbox'] === '1';
            if ( $valid_request && ($processing_checkbox || $completed_checkbox) ) {
                $product_ids = ( isset( $_POST['ids'] ) ? $_POST['ids'] : array() );
                if ( !empty( $product_ids ) ) {
                    foreach ( $product_ids as $product_id ) {
                        if ( $processing_checkbox ) {
                            update_post_meta( $product_id, '_order_processing_mc_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_mailchimp_bulk_choosen_audience'] ) ? sanitize_text_field( $_POST['kleverlist_mailchimp_bulk_choosen_audience'] ) : '' );
                            update_post_meta( $product_id, '_order_processing_mc_special_product_list', $selected_list );
                        }
                        if ( $completed_checkbox ) {
                            update_post_meta( $product_id, '_order_completed_mc_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_mailchimp_bulk_choosen_audience'] ) ? sanitize_text_field( $_POST['kleverlist_mailchimp_bulk_choosen_audience'] ) : '' );
                            update_post_meta( $product_id, '_order_completed_mc_special_product_list', $selected_list );
                        }
                    }
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Settings saved successfully', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select products', 'kleverlist' ),
                    );
                }
            } else {
                if ( isset( $_REQUEST['kleverlist_mailchimp_bulk_choosen_audience'] ) && empty( $_REQUEST['kleverlist_mailchimp_bulk_choosen_audience'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please choose audience', 'kleverlist' ),
                    );
                } elseif ( !$processing_checkbox && !$completed_checkbox ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select order action', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            wp_die();
        }

        /**
         * Aweber Tag Settings Callback
         */
        public function kleverlist_aweber_tags_settings_handle() {
            $response_arr = array();
            if ( isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_ajax_nonce' ) ) {
                // Order Processing
                if ( isset( $_POST['order_processing'] ) && $_POST["order_processing"] != '' ) {
                    $order_processing = sanitize_text_field( $_POST['order_processing'] );
                    update_option( 'kleverlist_aweber_order_processing_tag', $order_processing );
                }
                // Order Completed
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' ) {
                    $order_completed = sanitize_text_field( $_POST['order_completed'] );
                    update_option( 'kleverlist_aweber_order_completed_tag', $order_completed );
                }
                // Remove Order Processing Tag
                if ( isset( $_POST['order_completed'] ) && $_POST["order_completed"] != '' && isset( $_POST['remove_order_processing_tag'] ) && $_POST["remove_order_processing_tag"] != '' ) {
                    $remove_order_processing_tag = sanitize_text_field( $_POST['remove_order_processing_tag'] );
                    update_option( 'kleverlist_aweber_remove_order_processing_tag', $remove_order_processing_tag );
                }
                $response_arr = array(
                    'status'  => 1,
                    'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong, Please try again later', 'kleverlist' ),
                );
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * Aweber Product Bulk List Assign Callback
         */
        public function kleverlist_aweber_bulk_list_settings() {
            $response_arr = array();
            $radio_action = '';
            // Check if the request is valid with nonce verification
            $valid_request = isset( $_REQUEST['kleverlist_aweber_bulk_choosen_list'] ) && !empty( $_REQUEST['kleverlist_aweber_bulk_choosen_list'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_aweber_nonce' );
            $processing_checkbox = isset( $_REQUEST['kleverlist_aweber_bulk_list_order_processing_checkbox'] ) && $_REQUEST['kleverlist_aweber_bulk_list_order_processing_checkbox'] === '1';
            $completed_checkbox = isset( $_REQUEST['kleverlist_aweber_bulk_list_order_completed_checkbox'] ) && $_REQUEST['kleverlist_aweber_bulk_list_order_completed_checkbox'] === '1';
            if ( $valid_request && ($processing_checkbox || $completed_checkbox) ) {
                $product_ids = ( isset( $_POST['ids'] ) ? $_POST['ids'] : array() );
                if ( !empty( $product_ids ) ) {
                    foreach ( $product_ids as $product_id ) {
                        if ( $processing_checkbox ) {
                            update_post_meta( $product_id, '_order_processing_aweber_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_aweber_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_aweber_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_order_processing_aweber_special_product_list', $selected_list );
                        }
                        if ( $completed_checkbox ) {
                            update_post_meta( $product_id, '_order_completed_aweber_special_product', 'yes' );
                            $selected_list = ( isset( $_POST['kleverlist_aweber_bulk_choosen_list'] ) ? sanitize_text_field( $_POST['kleverlist_aweber_bulk_choosen_list'] ) : '' );
                            update_post_meta( $product_id, '_order_completed_aweber_special_product_list', $selected_list );
                        }
                    }
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Settings saved successfully', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select products', 'kleverlist' ),
                    );
                }
            } else {
                if ( isset( $_REQUEST['kleverlist_aweber_bulk_choosen_list'] ) && empty( $_REQUEST['kleverlist_aweber_bulk_choosen_list'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please choose list', 'kleverlist' ),
                    );
                } elseif ( !$processing_checkbox && !$completed_checkbox ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please select order action', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            wp_die();
        }

    }

}