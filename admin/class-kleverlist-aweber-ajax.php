<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
if ( !class_exists( 'Kleverlist_AWeber_Ajax' ) ) {
    class Kleverlist_AWeber_Ajax {
        protected $required_plugins = [];

        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            /** AWeber Choose List Action Hook **/
            add_action( 'wp_ajax_kleverlist_aweber_choose_list', array($this, 'kleverlist_aweber_choose_list_handle') );
            /** AWeber Global Settings Hook **/
            add_action( 'wp_ajax_kleverlist_aweber_global_settings', array($this, 'kleverlist_aweber_global_settings_handle') );
            /** AWeber Mapping Settings Call **/
            add_action( 'wp_ajax_kleverlist_aweber_mapping_settings', array($this, 'kleverlist_aweber_mapping_settings_handle') );
        }

        /**
         *
         * AWeber Choose List Callback
         **/
        public function kleverlist_aweber_choose_list_handle() {
            $response_arr = array();
            $valid_request = isset( $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'kleverlist_aweber_nonce' ) && isset( $_REQUEST['aweber_account_id'] ) && !empty( $_REQUEST['aweber_account_id'] );
            if ( $valid_request ) {
                if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_AWEBER ) {
                    $aweber_account_id = sanitize_text_field( $_POST['aweber_account_id'] );
                    update_option( 'kleverlist_aweber_user_selected_account_id', $aweber_account_id );
                    $response_arr = array(
                        'status'  => 1,
                        'message' => __( 'Load Audience Successfully', 'kleverlist' ),
                    );
                }
                $response_arr = array(
                    'status'  => 1,
                    'message' => __( 'Setting Saved Successfully', 'kleverlist' ),
                );
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something went wrong', 'kleverlist' ),
                );
            }
            wp_send_json( $response_arr );
            wp_die();
        }

        /**
         *AWeber Choose List Callback
         **/
        public function kleverlist_aweber_global_settings_handle() {
            $response_arr = array();
            $response_status = true;
            $status = null;
            $message = null;
            if ( isset( $_REQUEST['aweber_account_id'] ) && !empty( $_REQUEST['aweber_account_id'] ) && isset( $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'kleverlist_aweber_nonce' ) ) {
                if ( $response_status ) {
                    $aweber_account_id = sanitize_text_field( $_REQUEST['aweber_account_id'] );
                    update_option( 'kleverlist_aweber_global_account_id', $aweber_account_id );
                    // User Resubscribe
                    if ( isset( $_REQUEST['user_resubscribe'] ) && $_POST["user_resubscribe"] != '' ) {
                        $resubscribe = sanitize_text_field( $_REQUEST['user_resubscribe'] );
                        update_option( 'kleverlist_aweber_global_resubscribe', $resubscribe );
                        if ( isset( $_REQUEST['resubscribe_order_action'] ) && $_POST["resubscribe_order_action"] != '' ) {
                            $resubscribe_order_action = sanitize_text_field( $_REQUEST['resubscribe_order_action'] );
                            update_option( 'kleverlist_aweber_global_resubscribe_order_action_option', $resubscribe_order_action );
                        }
                    }
                    // User Active All Products
                    if ( isset( $_REQUEST['active_all_products'] ) && $_POST["active_all_products"] != '' ) {
                        $all_products = sanitize_text_field( $_REQUEST['active_all_products'] );
                        update_option( 'kleverlist_aweber_global_active_all_products', $all_products );
                        if ( $all_products === '1' ) {
                            $active_all_action = sanitize_text_field( $_REQUEST['active_all_action'] );
                            update_option( 'kleverlist_aweber_global_active_all_order_action', $active_all_action );
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
                if ( isset( $_REQUEST['aweber_account_id'] ) && empty( $_REQUEST['aweber_account_id'] ) ) {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Please choose your list', 'kleverlist' ),
                    );
                }
            }
            wp_send_json( $response_arr );
            die;
        }

        /**
         * AWeber Mapping Settings Callback
         */
        public function kleverlist_aweber_mapping_settings_handle() {
            $response_arr = array();
            if ( isset( $_POST['user_email'] ) && !empty( $_POST['user_email'] ) && isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'kleverlist_aweber_nonce' ) ) {
                $user_email = sanitize_text_field( $_POST['user_email'] );
                update_option( 'kleverlist_aweber_user_email', $user_email );
                // User firstname
                if ( isset( $_POST['firstname'] ) && $_POST["firstname"] != '' ) {
                    $firstname = sanitize_text_field( $_POST['firstname'] );
                    update_option( 'kleverlist_aweber_firstname', $firstname );
                }
                // User lastname
                if ( isset( $_POST['lastname'] ) && $_POST["lastname"] != '' ) {
                    $lastname = sanitize_text_field( $_POST['lastname'] );
                    update_option( 'kleverlist_aweber_lastname', $lastname );
                }
                // Username
                if ( isset( $_POST['username'] ) && $_POST["username"] != '' ) {
                    $username = sanitize_text_field( $_POST['username'] );
                    update_option( 'kleverlist_aweber_username', $username );
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

    }

}