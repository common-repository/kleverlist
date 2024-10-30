<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
if ( !class_exists( 'Kleverlist_WC' ) ) {
    class Kleverlist_WC {
        private $extra_tablenav_added = false;

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

        public function __construct( $plugin_name, $version ) {
            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->pro_featured_icon = '<div class="wc-pro-featured-icon"><img src="' . esc_url( KLEVERLIST_PLUGIN_ADMIN_DIR_URL ) . '/images/pro_featured.png"></div>';
            $sendy_lists = get_option( 'kleverlist_sendy_lists', '' );
            if ( !empty( $sendy_lists ) ) {
                add_action(
                    'woocommerce_order_status_processing',
                    array($this, 'kleverlist_get_customer_details_wc_order_processing'),
                    10,
                    1
                );
                add_action(
                    'woocommerce_order_status_completed',
                    array($this, 'kleverlist_get_customer_details_wc_order_completed'),
                    10,
                    1
                );
                add_filter(
                    'woocommerce_product_data_tabs',
                    array($this, 'kleverlist_custom_product_tab'),
                    10,
                    1
                );
                add_action( 'woocommerce_product_data_panels', array($this, 'kleverlist_wc_custom_product_panels') );
                add_action( 'woocommerce_process_product_meta', array($this, 'kleverlist_wc_custom_product_save_fields') );
                add_action( 'woocommerce_product_options_general_product_data', array($this, 'kleverlist_sendy_add_product_nonce_field') );
                add_action(
                    "manage_posts_extra_tablenav",
                    array($this, "kleverlist_sendy_execute_extra_tablenav"),
                    10,
                    1
                );
            }
        }

        public function kleverlist_sendy_execute_extra_tablenav( $which ) {
            if ( !$this->extra_tablenav_added ) {
                if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
                    include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-sendy-bulk-products-settings.php';
                }
                // Set flag to true after adding extra tablenav
                $this->extra_tablenav_added = true;
            }
        }

        public function kleverlist_sendy_add_product_nonce_field() {
            wp_nonce_field( 'kleverlist_sendy_product_meta', 'kleverlist_sendy_product_nonce' );
        }

        public function kleverlist_custom_product_tab( $tabs ) {
            $tabs['kleverlist_wc_custom_tab'] = array(
                'label'    => __( 'KleverList', 'kleverlist' ),
                'target'   => 'kleverlist_wc_custom_product_panels',
                'priority' => 10,
                'class'    => array('show_if_sendy'),
            );
            return $tabs;
        }

        public function kleverlist_wc_custom_product_panels() {
            echo '<div id="kleverlist_wc_custom_product_panels" class="panel woocommerce_options_panel hidden">';
            /******** WC Order Processing ********/
            echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Processing', 'kleverlist' ) . '</h2>';
            /******** Subscribe list when order processing ********/
            $order_processing_description = esc_html__( "If enabled, you can subscribe the customer to a list on “order processing”", "kleverlist" );
            $order_processing_special_product = get_post_meta( get_the_ID(), '_order_processing_special_product', true );
            $is_active_all_products = get_option( 'kleverlist_sendy_global_active_all_products' );
            $is_global_active_order_processing = get_option( 'kleverlist_sendy_global_active_all_order_processing_action' );
            $order_processing_checkbox_value = ( $order_processing_special_product === '' && $is_active_all_products === '1' && $is_global_active_order_processing === 'yes' ? 'yes' : $order_processing_special_product );
            woocommerce_wp_checkbox( array(
                'id'            => 'spi_order_processing',
                'value'         => esc_attr( $order_processing_checkbox_value ),
                'label'         => __( 'Subscribe to a list', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_special_product",
                'description'   => $order_processing_description,
            ) );
            $order_processing_sendy_lists = get_option( 'kleverlist_sendy_lists', '' );
            if ( !empty( $order_processing_sendy_lists ) ) {
                foreach ( $order_processing_sendy_lists['sendy_api_lists'] as $key => $process_list ) {
                    $order_processing_subscribe_options[$process_list->id] = $process_list->name;
                }
            }
            $order_processing_dropdown_tooltip = esc_html__( "Choose your list from the dropdown or keep the default one specified in the “Settings” section", "kleverlist" );
            woocommerce_wp_select( array(
                'id'            => 'order_processing_special_product_list',
                'label'         => __( 'Choose List', 'kleverlist' ),
                'options'       => $order_processing_subscribe_options,
                'wrapper_class' => 'hidden',
                'desc_tip'      => true,
                'required'      => true,
                'description'   => $order_processing_dropdown_tooltip,
                'value'         => esc_attr( get_post_meta( get_the_ID(), '_order_processing_special_product_list', true ) ),
            ) );
            $order_processing_dropdown_description = esc_html__( "The customer will be added to the selected list on “Order processing”", "kleverlist" );
            echo '<p class="hidden order_processing_special_product_list_field" style="margin-left:150px;"> ' . $order_processing_dropdown_description . '</p>';
            /******** Subscribe list when order processing ********/
            /******** Unsubscribe from a list on Order Processing ********/
            echo '<div class="kleverlist-pro-featured-unsubscribe-order-processing ' . esc_attr( KLEVERLIST_PLUGIN_CLASS ) . '">';
            /******** Unsubscribe List Pro Featured when order completed ********/
            $order_processing_unsubscribe_checkbox_description = esc_html__( "If enabled, you can unsubscribe the customer from a list on “order processing”", "kleverlist" );
            $order_processing_unsubscribe_checkbox_value = get_post_meta( get_the_ID(), '_order_processing_unsubscribe_product', true );
            woocommerce_wp_checkbox( array(
                'id'            => 'order_processing_unsubscribe_product',
                'value'         => $order_processing_unsubscribe_checkbox_value,
                'label'         => __( 'Unsubscribe from a list', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_unsubscribe_product",
                'description'   => $order_processing_unsubscribe_checkbox_description,
            ) );
            $order_processing_unsubscribe_options = [];
            $order_processing_sendy_lists = get_option( 'kleverlist_sendy_lists', '' );
            if ( !empty( $order_processing_sendy_lists ) ) {
                $order_processing_unsubscribe_options[''] = __( 'Select a list', 'kleverlist' );
                foreach ( $order_processing_sendy_lists['sendy_api_lists'] as $key => $op_list ) {
                    $order_processing_unsubscribe_options[$op_list->id] = $op_list->name;
                }
            }
            $order_processing_unsubscribe_dropdown_tooltip = esc_html__( "Choose your list from the dropdown or keep the default one specified in the “Settings” section", "kleverlist" );
            woocommerce_wp_select( array(
                'id'            => 'order_processing_unsubscribe_product_list',
                'label'         => __( 'Choose a list', 'kleverlist' ),
                'options'       => $order_processing_unsubscribe_options,
                'wrapper_class' => 'hidden',
                'desc_tip'      => true,
                'required'      => false,
                'description'   => $order_processing_unsubscribe_dropdown_tooltip,
                'value'         => esc_attr( get_post_meta( get_the_ID(), '_order_processing_unsubscribe_product_list', true ) ),
            ) );
            echo ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ? $this->pro_featured_icon ?? '' : '' );
            $unsubscribe_dropdown_description = esc_html__( "The customer will be unsubscribed from the selected list on “Order processing”. To ensure proper functionality of this feature, please verify that your email marketing platform does not have any global settings that unsubscribe users from all lists.", "kleverlist" );
            echo '<p class="hidden order_processing_unsubscribe_product_list_field" style="margin-left:150px;"> ' . $unsubscribe_dropdown_description . '</p>';
            /******** Unsubscribe from a list on Order Processing ********/
            echo '</div>';
            /******** WC Order Processing ********/
            /******** WC Order Completed ********/
            echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Complete', 'kleverlist' ) . '</h2>';
            /******** Subscribe list when order completed ********/
            $assign_product = get_post_meta( get_the_ID(), '_special_product', true );
            $description = " ";
            if ( $assign_product === "yes" ) {
                $description = esc_html__( "If enabled, you can subscribe the customer to a list on “order complete”", "kleverlist" );
            } else {
                $description = esc_html__( "If enabled, you can subscribe the customer to a list on “order complete”", "kleverlist" );
            }
            $checkbox_value = get_post_meta( get_the_ID(), '_special_product', true );
            $is_active_all_products = get_option( 'kleverlist_sendy_global_active_all_products' );
            $is_global_active_order_complete = get_option( 'kleverlist_sendy_global_active_all_order_complete_action' );
            $checkbox_value = ( $checkbox_value === '' && $is_active_all_products === '1' && $is_global_active_order_complete === 'yes' ? 'yes' : $checkbox_value );
            woocommerce_wp_checkbox( array(
                'id'            => 'spi',
                'value'         => esc_attr( $checkbox_value ),
                'label'         => __( 'Subscribe to a list', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_special_product",
                'description'   => $description,
            ) );
            $sendy_lists = get_option( 'kleverlist_sendy_lists', '' );
            if ( !empty( $sendy_lists ) ) {
                foreach ( $sendy_lists['sendy_api_lists'] as $key => $list ) {
                    $options[esc_attr( $list->id )] = esc_html( $list->name );
                }
            }
            $dropdown_tooltip = esc_html__( "Choose your list from the dropdown or keep the default one specified in the “Settings” section", "kleverlist" );
            woocommerce_wp_select( array(
                'id'            => 'special_product_list',
                'label'         => __( 'Choose List', 'kleverlist' ),
                'options'       => $options,
                'wrapper_class' => 'hidden',
                'desc_tip'      => true,
                'required'      => true,
                'description'   => $dropdown_tooltip,
                'value'         => esc_attr( get_post_meta( get_the_ID(), '_special_product_list', true ) ),
            ) );
            $dropdown_description = esc_html__( "The customer will be added to the selected list on “Order complete”", "kleverlist" );
            echo '<p class="hidden special_product_list_field" style="margin-left:150px;"> ' . $dropdown_description . '</p>';
            /******** Subscribe list when order completed ********/
            /******** Unsubscribe List Pro Featured when order completed ********/
            echo '<div class="kleverlist-pro-featured-unsubscribe ' . esc_attr( KLEVERLIST_PLUGIN_CLASS ) . '">';
            $unsubscribe_checkbox_description = esc_html__( "If enabled, you can unsubscribe the customer from a list on “order complete”", "kleverlist" );
            $unsubscribe_checkbox_value = get_post_meta( get_the_ID(), '_unsubscribe_product', true );
            woocommerce_wp_checkbox( array(
                'id'            => 'unsubscribe_product',
                'value'         => esc_attr( $unsubscribe_checkbox_value ),
                'label'         => __( 'Unsubscribe from a list', 'kleverlist' ),
                'desc_tip'      => true,
                'wrapper_class' => "kleverlist_unsubscribe_product",
                'description'   => $unsubscribe_checkbox_description,
            ) );
            $unsubscribe_options = [];
            $sendy_lists = get_option( 'kleverlist_sendy_lists', '' );
            if ( !empty( $sendy_lists ) ) {
                foreach ( $sendy_lists['sendy_api_lists'] as $key => $list ) {
                    $unsubscribe_options[esc_attr( $list->id )] = esc_html( $list->name );
                }
            }
            $unsubscribe_dropdown_tooltip = esc_html__( "Choose your list from the dropdown or keep the default one specified in the “Settings” section", "kleverlist" );
            woocommerce_wp_select( array(
                'id'            => 'unsubscribe_product_list',
                'label'         => __( 'Choose a list', 'kleverlist' ),
                'options'       => $unsubscribe_options,
                'wrapper_class' => 'hidden',
                'desc_tip'      => true,
                'required'      => false,
                'description'   => $unsubscribe_dropdown_tooltip,
                'value'         => esc_attr( get_post_meta( get_the_ID(), '_unsubscribe_product_list', true ) ),
            ) );
            echo ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ? $this->pro_featured_icon ?? '' : '' );
            $unsubscribe_dropdown_description = esc_html__( "The customer will be unsubscribed from the selected list on “Order complete”. To ensure proper functionality of this feature, please verify that your email marketing platform does not have any global settings that unsubscribe users from all lists.", "kleverlist" );
            echo '<p class="hidden unsubscribe_product_list_field" style="margin-left:150px;"> ' . $unsubscribe_dropdown_description . '</p>';
            /******** Unsubscribe List Pro Featured when order completed ********/
            echo '</div>';
            /******** WC Order Completed ********/
            echo '</div>';
            if ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ) {
                include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
            }
        }

        public function kleverlist_wc_custom_product_save_fields( $id ) {
            // Verify the nonce
            if ( !isset( $_POST['kleverlist_sendy_product_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kleverlist_sendy_product_nonce'] ) ), 'kleverlist_sendy_product_meta' ) ) {
                return;
            }
            if ( !current_user_can( 'manage_options' ) ) {
                return;
            }
            /******** Subscribe list when the order processing ********/
            $spi_order_processing = ( isset( $_POST['spi_order_processing'] ) && 'yes' === $_POST['spi_order_processing'] ? 'yes' : 'no' );
            update_post_meta( $id, '_order_processing_special_product', sanitize_text_field( $spi_order_processing ) );
            if ( isset( $_POST['order_processing_special_product_list'] ) && !empty( $_POST['order_processing_special_product_list'] ) ) {
                $order_processing_special_product_list = sanitize_text_field( $_POST['order_processing_special_product_list'] );
                update_post_meta( $id, '_order_processing_special_product_list', $order_processing_special_product_list );
            }
            /******** Subscribe list when the order processing ********/
            /******** Subscribe list when the order completed ********/
            $spi = ( isset( $_POST['spi'] ) && 'yes' === $_POST['spi'] ? 'yes' : 'no' );
            update_post_meta( $id, '_special_product', sanitize_text_field( $spi ) );
            if ( isset( $_POST['special_product_list'] ) && !empty( $_POST['special_product_list'] ) ) {
                $special_product_list = sanitize_text_field( $_POST['special_product_list'] );
                update_post_meta( $id, '_special_product_list', $special_product_list );
            }
        }

        public function kleverlist_get_customer_details_wc_order_processing( $order_id ) {
            if ( !$order_id ) {
                return;
            }
            // Check If API key and API URL exists or not
            $kleverlist_service_settings = get_option( 'kleverlist_service_settings', '' );
            if ( !empty( $kleverlist_service_settings ) ) {
                if ( $kleverlist_service_settings['service_verified'] != KLEVERLIST_SERVICE_VERIFIED ) {
                    return;
                } else {
                    $api_url = ( isset( $kleverlist_service_settings['service_domain_name'] ) ? sanitize_text_field( $kleverlist_service_settings['service_domain_name'] ) : '' );
                    $api_key = ( isset( $kleverlist_service_settings['service_api_key'] ) ? sanitize_text_field( $kleverlist_service_settings['service_api_key'] ) : '' );
                }
            }
            // Allow code execution only once
            if ( !get_post_meta( $order_id, '_kleverlist_order_processed', true ) ) {
                // Get an instance of the WC_Order object
                $order = wc_get_order( $order_id );
                // Get an order items
                $items = $order->get_items();
                //List IDs
                $list_ids = [];
                $list_id = null;
                $unsubscribe_list_ids = [];
                $tagsToStore = [];
                $orderProcessingTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    $product_variation_id = $item->get_variation_id();
                    // Individual product list checkbox is checked and list is assigned (wither default/any other )
                    $pro_spi = get_post_meta( $product_id, '_order_processing_special_product', true );
                    $pro_spl = get_post_meta( $product_id, '_order_processing_special_product_list', true );
                    // Get Subscribe list ids
                    if ( $pro_spi === 'yes' && !empty( $pro_spl ) ) {
                        $list_id = $pro_spl;
                    } elseif ( !empty( get_option( 'kleverlist_global_sendy_list_id' ) ) ) {
                        $list_id = get_option( 'kleverlist_global_sendy_list_id' );
                    }
                    array_push( $list_ids, $list_id );
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_sendy_order_processing_tag' ) ) {
                        // Check if 'order processing' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_PROCESSING_TAG, $orderProcessingTags ) ) {
                            $orderProcessingTags[] = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                        }
                    }
                    // Get All Proceesing Tags and Stored into Option
                    update_post_meta( $order_id, '_klerverlist_sendy_all_processing_tags', $orderProcessingTags );
                }
                $unique_list_ids = array_unique( $list_ids );
                $is_fullname = null;
                // User Fullname
                if ( '1' === get_option( 'kleverlist_sendy_mapping_user_fullname' ) ) {
                    $is_fullname = get_option( 'kleverlist_sendy_mapping_user_fullname' );
                }
                // Get the Customer ID (User ID)
                $customer_id = $order->get_customer_id();
                // Or $order->get_user_id();
                // Get the WP_User Object instance
                $user = $order->get_user();
                // Get the Customer billing email
                $billing_email = sanitize_email( $order->get_billing_email() );
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
                $province = sanitize_text_field( $order->get_billing_state() );
                $postcode = sanitize_text_field( $order->get_billing_postcode() );
                $country = sanitize_text_field( $order->get_billing_country() );
                // Username
                $user = $order->get_user();
                $username = sanitize_user( $user->user_login );
                //Check fields
                if ( !empty( $billing_email ) ) {
                    //------ Customer Details Send to Sendy Subscribe Start ------//
                    $fullname = $firstname . ' ' . $lastname;
                    if ( !empty( $list_id ) ) {
                        foreach ( $unique_list_ids as $key => $listID ) {
                            // Check Subscription status
                            $subscription_status_postdata = http_build_query( array(
                                'api_key' => $api_key,
                                'email'   => $billing_email,
                                'list_id' => $listID,
                            ) );
                            $subscription_status_response = wp_remote_post( $api_url . '/api/subscribers/subscription-status.php', array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Content-Type' => 'application/x-www-form-urlencoded',
                                ),
                                'body'    => $subscription_status_postdata,
                            ) );
                            if ( is_wp_error( $subscription_status_response ) ) {
                                continue;
                            }
                            $subscription_status_result = wp_remote_retrieve_body( $subscription_status_response );
                            if ( $subscription_status_result === 'Unsubscribed' && '0' === get_option( 'kleverlist_global_resubscribe' ) ) {
                                continue;
                            }
                            $resubscribe_order_action_option = get_option( 'kleverlist_sendy_global_resubscribe_order_action_option' );
                            if ( '1' === get_option( 'kleverlist_global_resubscribe' ) && $subscription_status_result === 'Unsubscribed' && $resubscribe_order_action_option !== 'kleverlist_sendy_global_resubscribe_order_on_processing' ) {
                                continue;
                            }
                            //Subscribe
                            $postdata = array(
                                'name'    => ( !is_null( $is_fullname ) ? $fullname : '' ),
                                'email'   => $billing_email,
                                'list'    => $listID,
                                'api_key' => $api_key,
                                'boolean' => 'true',
                                'tags'    => implode( ',', $orderProcessingTags ),
                            );
                            $postdata['firstname'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_firstname' ) ? $firstname : '' );
                            $postdata['lastname'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_lastname' ) ? $lastname : '' );
                            $postdata['username'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_username' ) ? $username : '' );
                            $response = wp_remote_post( $api_url . '/subscribe', array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Content-Type' => 'application/x-www-form-urlencoded',
                                ),
                                'body'    => $postdata,
                            ) );
                        }
                    }
                }
                $order->update_meta_data( '_kleverlist_order_processed', true );
                $order->save();
                // Save
            }
        }

        public function kleverlist_get_customer_details_wc_order_completed( $order_id ) {
            if ( !$order_id ) {
                return;
            }
            // Retrieve and log the processing tags from order meta
            $all_processing_tags = get_post_meta( $order_id, '_klerverlist_sendy_all_processing_tags', true );
            $all_processing_tags = ( $all_processing_tags ? $all_processing_tags : array() );
            // Check If API key and API URL exists or not
            $kleverlist_service_settings = get_option( 'kleverlist_service_settings', '' );
            if ( !empty( $kleverlist_service_settings ) ) {
                if ( $kleverlist_service_settings['service_verified'] != KLEVERLIST_SERVICE_VERIFIED ) {
                    return;
                } else {
                    $api_url = ( isset( $kleverlist_service_settings['service_domain_name'] ) ? sanitize_text_field( $kleverlist_service_settings['service_domain_name'] ) : '' );
                    $api_key = ( isset( $kleverlist_service_settings['service_api_key'] ) ? sanitize_text_field( $kleverlist_service_settings['service_api_key'] ) : '' );
                }
            }
            // Allow code execution only once
            if ( !get_post_meta( $order_id, '_kleverlist_order_completed', true ) ) {
                // Get an instance of the WC_Order object
                $order = wc_get_order( $order_id );
                // Get an order items
                $items = $order->get_items();
                //List IDs
                $list_ids = [];
                $list_id = null;
                $unsubscribe_list_ids = [];
                $tagsToStore = [];
                $orderCompletedTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    $product_variation_id = $item->get_variation_id();
                    // Individual product list checkbox is checked and list is assigned (wither default/any other )
                    $pro_spi = get_post_meta( $product_id, '_special_product', true );
                    $pro_spl = get_post_meta( $product_id, '_special_product_list', true );
                    // Get Subscribe list ids
                    if ( $pro_spi === 'yes' && !empty( $pro_spl ) ) {
                        $list_id = $pro_spl;
                    } elseif ( !empty( get_option( 'kleverlist_global_sendy_list_id' ) ) ) {
                        $list_id = get_option( 'kleverlist_global_sendy_list_id' );
                    }
                    array_push( $list_ids, $list_id );
                    //Product Tag Start
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_sendy_order_completed_tag' ) ) {
                        // Check if 'order completed' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_COMPLETED_TAG, $orderCompletedTags ) ) {
                            $orderCompletedTags[] = KLEVERLIST_DEFAULT_COMPLETED_TAG;
                        }
                    }
                    // Merge existing tags with the orderCompletedTags
                    $orderCompletedTags = array_unique( array_merge( $all_processing_tags, $orderCompletedTags ) );
                    // Removed Order Processing Tag on Order Completed Action
                    if ( '1' === get_option( 'kleverlist_sendy_remove_order_processing_tag' ) && '1' === get_option( 'kleverlist_sendy_order_completed_tag' ) ) {
                        $tagToRemove = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                        $index = array_search( $tagToRemove, $orderCompletedTags );
                        if ( $index !== false ) {
                            unset($orderCompletedTags[$index]);
                        }
                    }
                }
                $unique_list_ids = array_unique( $list_ids );
                $is_fullname = null;
                // User Fullname
                if ( '1' === get_option( 'kleverlist_sendy_mapping_user_fullname' ) ) {
                    $is_fullname = get_option( 'kleverlist_sendy_mapping_user_fullname' );
                }
                // Get the Customer ID (User ID)
                $customer_id = $order->get_customer_id();
                // Or $order->get_user_id();
                // Get the WP_User Object instance
                $user = $order->get_user();
                // Get the Customer billing email
                $billing_email = sanitize_email( $order->get_billing_email() );
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
                $province = sanitize_text_field( $order->get_billing_state() );
                $postcode = sanitize_text_field( $order->get_billing_postcode() );
                $country = sanitize_text_field( $order->get_billing_country() );
                // Username
                $user = $order->get_user();
                $username = sanitize_user( $user->user_login );
                //Check fields
                if ( !empty( $billing_email ) ) {
                    //------ Customer Details Send to Sendy Subscribe Start ------//
                    $fullname = $firstname . ' ' . $lastname;
                    if ( !empty( $list_id ) ) {
                        foreach ( $unique_list_ids as $key => $listID ) {
                            // Check Subscription status
                            $subscription_status_postdata = http_build_query( array(
                                'api_key' => $api_key,
                                'email'   => $billing_email,
                                'list_id' => $listID,
                            ) );
                            $subscription_status_response = wp_remote_post( $api_url . '/api/subscribers/subscription-status.php', array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Content-Type' => 'application/x-www-form-urlencoded',
                                ),
                                'body'    => $subscription_status_postdata,
                            ) );
                            if ( is_wp_error( $subscription_status_response ) ) {
                                continue;
                            }
                            $subscription_status_result = wp_remote_retrieve_body( $subscription_status_response );
                            if ( $subscription_status_result === 'Unsubscribed' && '0' === get_option( 'kleverlist_global_resubscribe' ) ) {
                                continue;
                            }
                            $resubscribe_order_action_option = get_option( 'kleverlist_sendy_global_resubscribe_order_action_option' );
                            if ( '1' === get_option( 'kleverlist_global_resubscribe' ) && $subscription_status_result === 'Unsubscribed' && $resubscribe_order_action_option !== 'kleverlist_sendy_global_resubscribe_order_on_complete' ) {
                                continue;
                            }
                            //Subscribe
                            $postdata = array(
                                'name'    => ( !is_null( $is_fullname ) ? $fullname : '' ),
                                'email'   => $billing_email,
                                'list'    => $listID,
                                'api_key' => $api_key,
                                'boolean' => 'true',
                                'tags'    => implode( ',', $orderCompletedTags ),
                            );
                            $postdata['firstname'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_firstname' ) ? $firstname : '' );
                            $postdata['lastname'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_lastname' ) ? $lastname : '' );
                            $postdata['username'] = ( '1' === get_option( 'kleverlist_sendy_mapping_user_username' ) ? $username : '' );
                            $response = wp_remote_post( $api_url . '/subscribe', array(
                                'method'  => 'POST',
                                'headers' => array(
                                    'Content-Type' => 'application/x-www-form-urlencoded',
                                ),
                                'body'    => $postdata,
                            ) );
                        }
                    }
                }
                delete_post_meta( $order_id, '_klerverlist_sendy_all_processing_tags' );
                $order->update_meta_data( '_kleverlist_order_completed', true );
                $order->save();
                // Save
            }
        }

        /**
         * Get Product Tags By IDs.
         *
         */
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

        /**
         * Register the stylesheets for the wc admin area.
         *
         * @since    1.0.0
         */
        public function wc_enqueue_styles() {
            wp_enqueue_style(
                $this->plugin_name,
                KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'css/kleverlist-wc-admin.css',
                array(),
                $this->version,
                'all'
            );
        }

        /**
         * Register the JavaScript for the wc admin area.
         *
         * @since    1.0.0
         */
        public function wc_enqueue_scripts() {
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/kleverlist-wc-admin.js',
                array('jquery'),
                $this->version,
                false
            );
            // kleverlist plugin wc object
            if ( !empty( get_post_meta( get_the_ID(), '_special_product_list', true ) ) ) {
                $defualt_pro_list_order_complete = '';
            } else {
                $defualt_pro_list_order_complete = get_option( 'kleverlist_global_sendy_list_id', '' );
            }
            if ( !empty( get_post_meta( get_the_ID(), '_order_processing_special_product_list', true ) ) ) {
                $defualt_pro_list_order_processing = '';
            } else {
                $defualt_pro_list_order_processing = get_option( 'kleverlist_global_sendy_list_id', '' );
            }
            $active_all_order_processing_action = null;
            $active_all_order_complete_action = null;
            if ( empty( get_post_meta( get_the_ID(), '_order_processing_special_product', true ) ) && get_option( 'kleverlist_sendy_global_active_all_products' ) === '1' && get_option( 'kleverlist_sendy_global_active_all_order_processing_action' ) === 'yes' ) {
                $order_processing_checkbox_value = 'yes';
            }
            if ( '1' === get_option( 'kleverlist_sendy_global_active_all_products' ) && 'yes' === get_option( 'kleverlist_sendy_global_active_all_order_processing_action' ) ) {
                $active_all_order_processing_action = 'yes';
            }
            if ( '1' === get_option( 'kleverlist_sendy_global_active_all_products' ) && 'yes' === get_option( 'kleverlist_sendy_global_active_all_order_complete_action' ) ) {
                $active_all_order_complete_action = 'yes';
            }
            wp_localize_script( $this->plugin_name, 'kleverlist_wc_object', array(
                'ajax_url'                           => esc_url( admin_url( 'admin-ajax.php' ) ),
                'admin_url'                          => esc_url( admin_url() ),
                'nonce'                              => wp_create_nonce( 'kleverlist_ajax_nonce' ),
                'defualt_pro_list_order_processing'  => esc_attr( $defualt_pro_list_order_processing ),
                'defualt_pro_list_order_complete'    => esc_attr( $defualt_pro_list_order_complete ),
                'active_all_order_processing_action' => esc_attr( $active_all_order_processing_action ),
                'active_all_order_complete_action'   => esc_attr( $active_all_order_complete_action ),
                'active_all_products'                => esc_attr( get_option( 'kleverlist_sendy_global_active_all_products' ) ),
                'product_id'                         => esc_attr( get_the_ID() ),
                'special_product_type'               => esc_attr( get_post_meta( get_the_ID(), '_special_product', true ) ),
            ) );
        }

    }

}