<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
if ( !class_exists( 'Kleverlist_Mailchimp_WC' ) ) {
    class Kleverlist_Mailchimp_WC {
        private $plugin_name;

        private $version;

        private $screen_ids;

        private $pro_featured_icon;

        private $privacy_consent = [];

        private $privacy_consent_toggle = null;

        private $privacy_checkbox = false;

        private $privacy_consent_input_text = null;

        private $mailchimp_apikey = null;

        private $mailchimp_apiurl = null;

        private $mailchimp_list_id = null;

        protected $required_plugins = [];

        private $extra_tablenav_added = false;

        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->pro_featured_icon = '<div class="wc-pro-featured-icon"><img src="' . esc_url( KLEVERLIST_PLUGIN_ADMIN_DIR_URL ) . '/images/pro_featured.png"></div>';
            if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_MAILCHIMP && !empty( get_option( 'kleverlist_mailchimp_user_audience' ) ) ) {
                add_action( 'woocommerce_order_status_processing', array($this, 'kleverlist_send_order_data_to_mailchimp_on_wc_order_processing') );
                add_action(
                    'woocommerce_order_status_completed',
                    array($this, 'kleverlist_send_order_data_to_mailchimp_on_wc_order_completed'),
                    10,
                    1
                );
                add_filter(
                    'woocommerce_product_data_tabs',
                    array($this, 'kleverlist_mailchimp_custom_product_tab'),
                    10,
                    1
                );
                add_action( 'woocommerce_product_data_panels', array($this, 'kleverlist_mailchimp_wc_custom_product_panels') );
                add_action( 'woocommerce_process_product_meta', array($this, 'kleverlist_mailchimp_wc_custom_product_save_fields') );
                add_action( 'woocommerce_product_options_general_product_data', array($this, 'kleverlist_mailchimp_add_product_nonce_field') );
                add_action(
                    "manage_posts_extra_tablenav",
                    array($this, "kleverlist_mailchimp_execute_extra_tablenav"),
                    10,
                    1
                );
                // Hook into the Freemius initialization process
                if ( function_exists( 'kleverlist_fs' ) ) {
                }
            }
        }

        public function kleverlist_mailchimp_execute_extra_tablenav( $which ) {
            if ( !$this->extra_tablenav_added ) {
                if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
                    include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-mailchimp-bulk-products-settings.php';
                }
                $this->extra_tablenav_added = true;
            }
        }

        public function kleverlist_mailchimp_add_product_nonce_field() {
            wp_nonce_field( 'kleverlist_mailchimp_product_meta', 'kleverlist_mailchimp_product_nonce' );
        }

        public function kleverlist_mailchimp_custom_product_tab( $tabs ) {
            $tabs['kleverlist_wc_custom_tab'] = array(
                'label'    => __( 'KleverList', 'kleverlist' ),
                'target'   => 'kleverlist_mailchimp_wc_custom_product_panels',
                'priority' => 10,
                'class'    => array('show_if_kleverlist_mailchimp'),
            );
            return $tabs;
        }

        public function kleverlist_mailchimp_wc_custom_product_panels() {
            echo '<div id="kleverlist_mailchimp_wc_custom_product_panels" class="panel woocommerce_options_panel hidden">';
            /******** WC Order Processing ********/
            echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Processing', 'kleverlist' ) . '</h2>';
            $audience_arr = get_option( 'kleverlist_mailchimp_audience_lists' );
            /******** Subscribe list when order processing ********/
            $order_processing_description = esc_html__( "If enabled, you can subscribe the customer to a  audience on “order processing”", "kleverlist" );
            $processing_mc_special_product = get_post_meta( get_the_ID(), '_order_processing_mc_special_product', true );
            $is_active_all_products = get_option( 'kleverlist_mailchimp_global_active_all_products' );
            $is_order_processing_action = get_option( 'kleverlist_mailchimp_global_active_all_order_action' );
            $order_processing_checkbox_value = ( empty( $processing_mc_special_product ) && $is_active_all_products === '1' && $is_order_processing_action === 'order_processing' ? 'yes' : $processing_mc_special_product );
            woocommerce_wp_checkbox( array(
                'id'            => 'mc_spi_order_processing',
                'value'         => esc_attr( $order_processing_checkbox_value ),
                'label'         => __( 'Subscribe to an Audience', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_special_product",
                'description'   => $order_processing_description,
            ) );
            woocommerce_wp_text_input( array(
                'id'                => 'order_processing_mc_special_product_list',
                'label'             => __( 'Audience:', 'kleverlist' ),
                'wrapper_class'     => 'hidden',
                'required'          => true,
                'value'             => ( !empty( $audience_arr ) ? reset( $audience_arr ) : '' ),
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                ),
            ) );
            $order_processing_dropdown_description = esc_html__( "The customer will be added to the selected Audience on “Order processing”", "kleverlist" );
            echo '<p class="hidden order_processing_mc_special_product_list_field" style="margin-left:150px;"> ' . $order_processing_dropdown_description . '</p>';
            /******** Subscribe list when order processing ********/
            /******** WC Order Processing ********/
            /******** WC Order Completed ********/
            echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Complete', 'kleverlist' ) . '</h2>';
            /******** Subscribe list when order completed ********/
            $order_completed_description = esc_html__( "If enabled, you can subscribe the customer to a  audience on “order completed” ", "kleverlist" );
            $completed_mc_special_product = get_post_meta( get_the_ID(), '_order_completed_mc_special_product', true );
            $is_active_all_products = get_option( 'kleverlist_mailchimp_global_active_all_products' );
            $is_order_complete_action = get_option( 'kleverlist_mailchimp_global_active_all_order_action' );
            $order_completed_checkbox_value = ( empty( $completed_mc_special_product ) && $is_active_all_products === '1' && $is_order_complete_action === 'order_completed' ? 'yes' : $completed_mc_special_product );
            woocommerce_wp_checkbox( array(
                'id'            => 'mc_spi_order_completed',
                'value'         => esc_attr( $order_completed_checkbox_value ),
                'label'         => __( 'Subscribe to an Audience', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_special_product",
                'description'   => $order_completed_description,
            ) );
            woocommerce_wp_text_input( array(
                'id'                => 'order_completed_mc_special_product_list',
                'label'             => __( 'Audience:', 'kleverlist' ),
                'wrapper_class'     => 'hidden',
                'required'          => true,
                'value'             => ( !empty( $audience_arr ) ? reset( $audience_arr ) : '' ),
                'custom_attributes' => array(
                    'disabled' => 'disabled',
                ),
            ) );
            $dropdown_description = esc_html__( "The customer will be added to the selected Audience on “Order complete”", "kleverlist" );
            echo '<p class="hidden order_completed_mc_special_product_list_field" style="margin-left:150px;"> ' . $dropdown_description . '</p>';
            /******** Subscribe list when order completed ********/
            echo '</div>';
            if ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ) {
                include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
            }
        }

        public function kleverlist_mailchimp_wc_custom_product_save_fields( $id ) {
            // Verify the nonce
            if ( !isset( $_POST['kleverlist_mailchimp_product_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kleverlist_mailchimp_product_nonce'] ) ), 'kleverlist_mailchimp_product_meta' ) ) {
                return;
            }
            if ( !current_user_can( 'manage_options' ) ) {
                return;
            }
            /******** Subscribe list when the order processing ********/
            $mc_spi_order_processing = ( isset( $_POST['mc_spi_order_processing'] ) && 'yes' === $_POST['mc_spi_order_processing'] ? 'yes' : 'no' );
            update_post_meta( $id, '_order_processing_mc_special_product', sanitize_text_field( $mc_spi_order_processing ) );
            if ( isset( $_POST['order_processing_mc_special_product_list'] ) && !empty( $_POST['order_processing_mc_special_product_list'] ) ) {
                $order_processing_mc_special_product_list = sanitize_text_field( $_POST['order_processing_mc_special_product_list'] );
                update_post_meta( $id, '_order_processing_mc_special_product_list', $order_processing_mc_special_product_list );
            }
            /******** Subscribe list when the order processing ********/
            /******** Subscribe list when the order completed ********/
            $mc_spi_order_completed = ( isset( $_POST['mc_spi_order_completed'] ) && 'yes' === $_POST['mc_spi_order_completed'] ? 'yes' : 'no' );
            update_post_meta( $id, '_order_completed_mc_special_product', sanitize_text_field( $mc_spi_order_completed ) );
            if ( isset( $_POST['order_completed_mc_special_product_list'] ) && !empty( $_POST['order_completed_mc_special_product_list'] ) ) {
                $order_completed_mc_special_product_list = sanitize_text_field( $_POST['order_completed_mc_special_product_list'] );
                update_post_meta( $id, '_order_completed_mc_special_product_list', $order_completed_mc_special_product_list );
            }
            /******** Subscribe list when the order completed ********/
        }

        public function kleverlist_send_order_data_to_mailchimp_on_wc_order_processing( $order_id ) {
            if ( !$order_id ) {
                return;
            }
            $privacy_checkbox = get_post_meta( $order_id, 'kleverlist_mailchimp_privacy_checkbox', true );
            if ( get_option( 'kleverlist_mailchimp_user_email' ) !== 'yes' ) {
                return;
            }
            if ( get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_MAILCHIMP ) {
                return;
            }
            if ( get_post_meta( $order_id, '_kleverlist_mc_order_processing', true ) ) {
                return;
            }
            if ( empty( get_option( 'kleverlist_mailchimp_user_audience' ) ) ) {
                return;
            } else {
                $this->mailchimp_list_id = get_option( 'kleverlist_mailchimp_user_audience' );
            }
            if ( empty( get_option( 'kleverlist_mailchimp_apikey' ) ) ) {
                return;
            } else {
                $this->mailchimp_apikey = get_option( 'kleverlist_mailchimp_apikey' );
            }
            if ( empty( get_option( 'kleverlist_mailchimp_apiurl' ) ) ) {
                return;
            } else {
                $this->mailchimp_apiurl = get_option( 'kleverlist_mailchimp_apiurl' );
            }
            if ( function_exists( 'kleverlist_fs' ) ) {
            }
            self::kleverlist_send_order_data_to_mailchimp_api_on_wc_order_processing(
                $order_id,
                $this->mailchimp_apikey,
                $this->mailchimp_apiurl,
                $this->mailchimp_list_id
            );
        }

        public static function kleverlistGetProductTagsByIDs( $product_id, $order_action ) {
            $product_tags = array();
            // Ensure the WooCommerce functions are available
            if ( function_exists( 'wc_get_product' ) ) {
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $tags = $product->get_tag_ids();
                    if ( !empty( $tags ) ) {
                        foreach ( $tags as $tag_id ) {
                            $tag = get_term( $tag_id, 'product_tag' );
                            if ( $tag && !in_array( $tag->name, $product_tags ) ) {
                                $product_tags[] = $tag->name;
                            }
                        }
                    }
                }
            }
            return $product_tags;
        }

        // Function to send order data to Mailchimp
        public static function kleverlist_send_order_data_to_mailchimp_api_on_wc_order_processing(
            $order_id,
            $mailchimp_apikey,
            $mailchimp_apiurl,
            $mailchimp_list_id
        ) {
            if ( get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_MAILCHIMP ) {
                return;
            }
            if ( is_null( $mailchimp_apikey ) || is_null( $mailchimp_apiurl ) || is_null( $mailchimp_list_id ) ) {
                return;
            }
            // Allow code execution only once
            if ( !get_post_meta( $order_id, '_kleverlist_mc_order_processing', true ) ) {
                // Get an instance of the WC_Order object
                $order = wc_get_order( $order_id );
                // Get an order items
                $items = $order->get_items();
                $audience_id = null;
                $unsubscribe_audience_ids = [];
                $tagsToStore = [];
                $orderProcessingTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    $product_variation_id = $item->get_variation_id();
                    // Individual product list checkbox is checked and list is assigned (wither default/any other )
                    $pro_spi = get_post_meta( $product_id, '_order_processing_mc_special_product', true );
                    $pro_spl = get_post_meta( $product_id, '_order_processing_mc_special_product_list', true );
                    if ( $pro_spi !== 'yes' ) {
                        continue;
                    } else {
                        $audience_id = $mailchimp_list_id;
                    }
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_mailchimp_order_processing' ) ) {
                        // Check if 'order processing' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_PROCESSING_TAG, $orderProcessingTags ) ) {
                            $orderProcessingTags[] = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                        }
                    }
                }
                // Get the Customer ID (User ID)
                $customer_id = $order->get_customer_id();
                // Or $order->get_user_id();
                // Get the WP_User Object instance
                $user = $order->get_user();
                // Username
                $username = null;
                if ( $user ) {
                    // Username
                    $username = $user->user_login;
                }
                // Get the Customer billing email
                $billing_email = $order->get_billing_email();
                // Get order creation date
                $order_date = wc_format_datetime( $order->get_date_created() );
                // Customer billing information details
                $firstname = sanitize_text_field( $order->get_billing_first_name() );
                $lastname = sanitize_text_field( $order->get_billing_last_name() );
                $phone = sanitize_text_field( $order->get_billing_phone() );
                $company = sanitize_text_field( $order->get_billing_company() );
                $address_1 = sanitize_text_field( $order->get_billing_address_1() );
                $address_2 = sanitize_text_field( $order->get_billing_address_2() );
                $city = sanitize_text_field( $order->get_billing_city() );
                $state = sanitize_text_field( $order->get_billing_state() );
                $postcode = sanitize_text_field( $order->get_billing_postcode() );
                $country = sanitize_text_field( $order->get_billing_country() );
                // Check if the billing email exists
                if ( $billing_email ) {
                    if ( !empty( $audience_id ) && !is_null( $audience_id ) ) {
                        $url = "{$mailchimp_apiurl}lists/{$audience_id}/members";
                        $data = array(
                            'email_address' => $billing_email,
                            'status'        => 'subscribed',
                            'merge_fields'  => array(
                                'FNAME'    => ( '1' === get_option( 'kleverlist_mailchimp_firstname' ) ? $firstname : '' ),
                                'LNAME'    => ( '1' === get_option( 'kleverlist_mailchimp_lastname' ) ? $lastname : '' ),
                                'USERNAME' => ( '1' === get_option( 'kleverlist_mailchimp_username' ) ? $username : '' ),
                            ),
                            'tags'          => $orderProcessingTags,
                        );
                        // wc_get_logger()->debug('Order Processing Data Array: ' . print_r($data, true), array('source' => 'kleverlist'));
                        // Check if the contact already exists in Mailchimp
                        $existing_member_url = "{$url}/" . md5( strtolower( $billing_email ) );
                        $existing_member_args = array(
                            'method'  => 'GET',
                            'headers' => array(
                                'Authorization' => 'apikey ' . $mailchimp_apikey,
                                'Content-Type'  => 'application/json',
                            ),
                        );
                        $existing_member_response = wp_remote_get( $existing_member_url, $existing_member_args );
                        if ( !is_wp_error( $existing_member_response ) && wp_remote_retrieve_response_code( $existing_member_response ) === 200 ) {
                            // Check the existing member status
                            $existing_member_data = json_decode( wp_remote_retrieve_body( $existing_member_response ), true );
                            $existing_member_status = ( isset( $existing_member_data['status'] ) ? $existing_member_data['status'] : '' );
                            $resubscribe_order_action_option = get_option( 'kleverlist_mailchimp_global_resubscribe_order_action_option' );
                            if ( $existing_member_status === 'subscribed' ) {
                                // Update existing contact
                                $args = array(
                                    'method'  => 'PATCH',
                                    'headers' => array(
                                        'Authorization' => 'apikey ' . $mailchimp_apikey,
                                        'Content-Type'  => 'application/json',
                                    ),
                                    'body'    => json_encode( $data ),
                                );
                                $update_response = wp_remote_request( $existing_member_url, $args );
                                $response_json = wp_remote_retrieve_body( $update_response );
                                if ( !is_wp_error( $update_response ) && wp_remote_retrieve_response_code( $update_response ) === 200 ) {
                                    wc_get_logger()->debug( 'Order Processing Contact Updated Successfully', array(
                                        'source' => 'kleverlist',
                                    ) );
                                }
                            } elseif ( $existing_member_status === 'unsubscribed' && '1' === get_option( 'kleverlist_mailchimp_global_resubscribe' ) && $resubscribe_order_action_option === 'kleverlist_mailchimp_global_resubscribe_order_on_processing' ) {
                                // Resubscribe existing contact
                                $args = array(
                                    'method'  => 'PATCH',
                                    'headers' => array(
                                        'Authorization' => 'apikey ' . $mailchimp_apikey,
                                        'Content-Type'  => 'application/json',
                                    ),
                                    'body'    => json_encode( $data ),
                                );
                                $resubscribe_response = wp_remote_request( $existing_member_url, $args );
                                if ( !is_wp_error( $resubscribe_response ) && wp_remote_retrieve_response_code( $resubscribe_response ) === 200 ) {
                                    wc_get_logger()->debug( 'Order Processing Data Contact Resubscribed Successfully', array(
                                        'source' => 'kleverlist',
                                    ) );
                                } else {
                                    wc_get_logger()->debug( 'Order Processing Failed to Resubscribe the Contact', array(
                                        'source' => 'kleverlist',
                                    ) );
                                }
                            }
                        } else {
                            // Create new contact for Mailchimp
                            $args = array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Authorization' => 'apikey ' . $mailchimp_apikey,
                                    'Content-Type'  => 'application/json',
                                ),
                                'body'    => json_encode( $data ),
                            );
                            $response = wp_remote_post( $url, $args );
                            $response_json = wp_remote_retrieve_body( $response );
                            if ( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                                wc_get_logger()->debug( 'Order Processing New Contact Subscribe', array(
                                    'source' => 'kleverlist',
                                ) );
                            }
                        }
                    }
                }
                $order->update_meta_data( '_kleverlist_mc_order_processing', true );
                $order->save();
                // Save
                delete_option( '_klerverlist_mailchimp_order_processing_tags' );
            }
        }

        public function kleverlist_send_order_data_to_mailchimp_on_wc_order_completed( $order_id ) {
            $privacy_checkbox = get_post_meta( $order_id, 'kleverlist_mailchimp_privacy_checkbox', true );
            if ( !$order_id ) {
                return;
            }
            if ( get_option( 'kleverlist_mailchimp_user_email' ) !== 'yes' ) {
                return;
            }
            if ( get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_MAILCHIMP ) {
                return;
            }
            if ( get_post_meta( $order_id, '_kleverlist_mc_order_completed', true ) ) {
                return;
            }
            if ( empty( get_option( 'kleverlist_mailchimp_user_audience' ) ) ) {
                return;
            } else {
                $this->mailchimp_list_id = get_option( 'kleverlist_mailchimp_user_audience' );
            }
            if ( empty( get_option( 'kleverlist_mailchimp_apikey' ) ) ) {
                return;
            } else {
                $this->mailchimp_apikey = get_option( 'kleverlist_mailchimp_apikey' );
            }
            if ( empty( get_option( 'kleverlist_mailchimp_apiurl' ) ) ) {
                return;
            } else {
                $this->mailchimp_apiurl = get_option( 'kleverlist_mailchimp_apiurl' );
            }
            if ( function_exists( 'kleverlist_fs' ) ) {
            }
            self::kleverlist_send_order_data_to_mailchimp_api_on_wc_order_completed(
                $order_id,
                $this->mailchimp_apikey,
                $this->mailchimp_apiurl,
                $this->mailchimp_list_id
            );
        }

        // Function to send order data to Mailchimp on order completed
        public static function kleverlist_send_order_data_to_mailchimp_api_on_wc_order_completed(
            $order_id,
            $mailchimp_apikey,
            $mailchimp_apiurl,
            $mailchimp_list_id
        ) {
            if ( get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_MAILCHIMP ) {
                return;
            }
            if ( is_null( $mailchimp_apikey ) || is_null( $mailchimp_apiurl ) || is_null( $mailchimp_list_id ) ) {
                return;
            }
            // Allow code execution only once
            if ( !get_post_meta( $order_id, '_kleverlist_mc_order_completed', true ) ) {
                // Get an instance of the WC_Order object
                $order = wc_get_order( $order_id );
                // Get an order items
                $items = $order->get_items();
                $audience_ids = [];
                $audience_id = null;
                $unsubscribe_audience_ids = [];
                $tagsToStore = [];
                $orderCompletedTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    $product_variation_id = $item->get_variation_id();
                    // Individual product list checkbox is checked and list is assigned (wither default/any other )
                    $pro_spi = get_post_meta( $product_id, '_order_completed_mc_special_product', true );
                    $pro_spl = get_post_meta( $product_id, '_order_completed_mc_special_product_list', true );
                    if ( $pro_spi !== 'yes' ) {
                        continue;
                    } else {
                        $audience_id = $mailchimp_list_id;
                    }
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_mailchimp_order_completed' ) ) {
                        // Check if 'order complete' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_COMPLETED_TAG, $orderCompletedTags ) ) {
                            $orderCompletedTags[] = KLEVERLIST_DEFAULT_COMPLETED_TAG;
                        }
                    }
                }
                // Get the Customer ID (User ID)
                $customer_id = $order->get_customer_id();
                // Or $order->get_user_id();
                // Get the WP_User Object instance
                $user = $order->get_user();
                // Username
                $username = null;
                if ( $user ) {
                    // Username
                    $username = $user->user_login;
                }
                // Get the Customer billing email
                $billing_email = $order->get_billing_email();
                // Get order creation date
                $order_date = wc_format_datetime( $order->get_date_created() );
                // Customer billing information details
                $firstname = sanitize_text_field( $order->get_billing_first_name() );
                $lastname = sanitize_text_field( $order->get_billing_last_name() );
                $phone = sanitize_text_field( $order->get_billing_phone() );
                $company = sanitize_text_field( $order->get_billing_company() );
                $address_1 = sanitize_text_field( $order->get_billing_address_1() );
                $address_2 = sanitize_text_field( $order->get_billing_address_2() );
                $city = sanitize_text_field( $order->get_billing_city() );
                $state = sanitize_text_field( $order->get_billing_state() );
                $postcode = sanitize_text_field( $order->get_billing_postcode() );
                $country = sanitize_text_field( $order->get_billing_country() );
                // Check if the billing email exists
                if ( $billing_email ) {
                    if ( !empty( $audience_id ) && !is_null( $audience_id ) ) {
                        $url = "{$mailchimp_apiurl}lists/{$audience_id}/members";
                        $data = array(
                            'email_address' => $billing_email,
                            'status'        => 'subscribed',
                            'merge_fields'  => array(
                                'FNAME'    => ( '1' === get_option( 'kleverlist_mailchimp_firstname' ) ? $firstname : '' ),
                                'LNAME'    => ( '1' === get_option( 'kleverlist_mailchimp_lastname' ) ? $lastname : '' ),
                                'USERNAME' => ( '1' === get_option( 'kleverlist_mailchimp_username' ) ? $username : '' ),
                            ),
                            'tags'          => $orderCompletedTags,
                        );
                        // wc_get_logger()->debug('Order Completed Data Array: ' . print_r($data, true), array('source' => 'kleverlist'));
                        // Check if the contact already exists in Mailchimp
                        $existing_member_url = "{$url}/" . md5( strtolower( $billing_email ) );
                        $existing_member_args = array(
                            'method'  => 'GET',
                            'headers' => array(
                                'Authorization' => 'apikey ' . $mailchimp_apikey,
                                'Content-Type'  => 'application/json',
                            ),
                        );
                        $existing_member_response = wp_remote_get( $existing_member_url, $existing_member_args );
                        if ( !is_wp_error( $existing_member_response ) && wp_remote_retrieve_response_code( $existing_member_response ) === 200 ) {
                            // Check the existing member status
                            $existing_member_data = json_decode( wp_remote_retrieve_body( $existing_member_response ), true );
                            $existing_member_status = ( isset( $existing_member_data['status'] ) ? $existing_member_data['status'] : '' );
                            $resubscribe_order_action_option = get_option( 'kleverlist_mailchimp_global_resubscribe_order_action_option' );
                            if ( $existing_member_status === 'subscribed' ) {
                                // Update existing contact
                                $args = array(
                                    'method'  => 'PATCH',
                                    'headers' => array(
                                        'Authorization' => 'apikey ' . $mailchimp_apikey,
                                        'Content-Type'  => 'application/json',
                                    ),
                                    'body'    => json_encode( $data ),
                                );
                                $update_response = wp_remote_request( $existing_member_url, $args );
                                $response_json = wp_remote_retrieve_body( $update_response );
                                if ( !is_wp_error( $update_response ) && wp_remote_retrieve_response_code( $update_response ) === 200 ) {
                                    // Contact update successful
                                    wc_get_logger()->debug( 'Order Completed Contact Update Successfully', array(
                                        'source' => 'kleverlist',
                                    ) );
                                }
                            } elseif ( $existing_member_status === 'unsubscribed' && '1' === get_option( 'kleverlist_mailchimp_global_resubscribe' ) && $resubscribe_order_action_option === 'kleverlist_mailchimp_global_resubscribe_order_on_complete' ) {
                                // Resubscribe existing contact
                                $args = array(
                                    'method'  => 'PATCH',
                                    'headers' => array(
                                        'Authorization' => 'apikey ' . $mailchimp_apikey,
                                        'Content-Type'  => 'application/json',
                                    ),
                                    'body'    => json_encode( $data ),
                                );
                                $resubscribe_response = wp_remote_request( $existing_member_url, $args );
                                if ( !is_wp_error( $resubscribe_response ) && wp_remote_retrieve_response_code( $resubscribe_response ) === 200 ) {
                                    wc_get_logger()->debug( 'Order Completed Contact resubscribed Successfully', array(
                                        'source' => 'kleverlist',
                                    ) );
                                } else {
                                    wc_get_logger()->debug( 'Order Completed Failed to Resubscribe the Contact', array(
                                        'source' => 'kleverlist',
                                    ) );
                                }
                            }
                        } else {
                            // Create new contact for Mailchimp
                            $args = array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Authorization' => 'apikey ' . $mailchimp_apikey,
                                    'Content-Type'  => 'application/json',
                                ),
                                'body'    => json_encode( $data ),
                            );
                            $response = wp_remote_post( $url, $args );
                            $response_json = wp_remote_retrieve_body( $response );
                            if ( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) === 200 ) {
                                // Mailchimp request successful
                                wc_get_logger()->debug( 'Order Completed New Contact Created', array(
                                    'source' => 'kleverlist',
                                ) );
                            }
                        }
                    }
                }
                $order->update_meta_data( '_kleverlist_mc_order_completed', true );
                $order->save();
                // Save
                delete_option( '_klerverlist_mailchimp_order_completed_tags' );
                if ( '1' === get_option( 'kleverlist_mailchimp_remove_order_processing_tag' ) && '1' === get_option( 'kleverlist_mailchimp_order_completed' ) ) {
                    $tags = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                    self::kleverlist_mailchimp_update_member_tags__premium_only(
                        $mailchimp_apikey,
                        $mailchimp_list_id,
                        $billing_email,
                        $tags
                    );
                }
            }
        }

        // Extract the server prefix from the API key
        public static function kleverlist_get_mailchimp_server( $apiKey ) {
            if ( is_null( $apiKey ) || empty( $apiKey ) ) {
                return;
            }
            $server = explode( '-', $apiKey )[1];
            return $server;
        }

        public function mailchimp_wc_enqueue_styles() {
            wp_enqueue_style(
                $this->plugin_name . '_mc',
                KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'css/kleverlist-mailchimp-wc-admin.css',
                array(),
                $this->version,
                'all'
            );
        }

        public function mailchimp_wc_enqueue_scripts() {
            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_enqueue_script(
                $this->plugin_name . '_mc',
                plugin_dir_url( __FILE__ ) . 'js/kleverlist-mailchimp-wc-admin.js',
                array('jquery'),
                $this->version,
                false
            );
            // kleverlist plugin object
            $is_kleverlist_premium_type = null;
            wp_localize_script( $this->plugin_name, 'kleverlist_mcwc_object', array(
                'ajax_url'              => esc_url( admin_url( 'admin-ajax.php' ) ),
                'admin_url'             => esc_url( admin_url() ),
                'nonce'                 => wp_create_nonce( 'kleverlist_mcwc_nonce' ),
                'is_kleverlist_premium' => esc_attr( $is_kleverlist_premium_type ),
            ) );
        }

    }

}