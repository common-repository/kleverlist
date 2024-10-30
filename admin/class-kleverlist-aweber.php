<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
const AWEBER_BASE_URL = 'https://api.aweber.com/1.0/';
class Kleverlist_AWeber {
    private $plugin_name;

    private $version;

    private $screen_ids;

    public static $aweberClientId = 'JU5i6Kny0d3fuHclYDmmVkUOFFcOGATy';

    public static $codeChallengeMethod = 'S256';

    private $pro_featured_icon;

    private $privacy_consent = [];

    private $privacy_consent_toggle = null;

    private $privacy_checkbox = false;

    private $privacy_consent_input_text = null;

    private $extra_tablenav_added = false;

    public static $aweberScopes = array(
        'account.read',
        'list.read',
        'list.write',
        'subscriber.read',
        'subscriber.write',
        'email.read',
        'email.write',
        'subscriber.read-extended',
        'landing-page.read'
    );

    public static $aweberResponseType = 'code';

    public static $aweberAuthorizeBaseURL = 'https://auth.aweber.com/oauth2/authorize';

    public static $tokenBaseURL = 'https://auth.aweber.com/oauth2/token';

    // protected $tokenBaseURL = 'https://auth.aweber.com/oauth2/token';
    protected $revokeTokenURL = 'https://auth.aweber.com/oauth2/revoke';

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->pro_featured_icon = '<div class="wc-pro-featured-icon"><img src="' . esc_url( KLEVERLIST_PLUGIN_ADMIN_DIR_URL ) . '/images/pro_featured.png"></div>';
        /** AWeber Settings Call **/
        add_action( 'wp_ajax_kleverlist_aweber_settings', array($this, 'kleverlist_aweber_settings_handle') );
        if ( get_option( 'kleverlist_service_type' ) === KLEVERLIST_SERVICE_AWEBER && !empty( get_option( 'kleverlist_aweber_user_selected_account_id' ) ) ) {
            add_action( 'woocommerce_order_status_processing', array($this, 'kleverlist_send_order_data_to_aweber_on_wc_order_processing') );
            add_action(
                'woocommerce_order_status_completed',
                array($this, 'kleverlist_send_order_data_to_aweber_on_wc_order_completed'),
                10,
                1
            );
            add_filter(
                'woocommerce_product_data_tabs',
                array($this, 'kleverlist_aweber_custom_product_tab'),
                10,
                1
            );
            add_action( 'woocommerce_product_data_panels', array($this, 'kleverlist_aweber_wc_custom_product_panels') );
            add_action( 'woocommerce_process_product_meta', array($this, 'kleverlist_aweber_wc_custom_product_save_fields') );
            add_action( 'woocommerce_product_options_general_product_data', array($this, 'kleverlist_aweber_add_product_nonce_field') );
            add_action(
                "manage_posts_extra_tablenav",
                array($this, "kleverlist_aweber_execute_extra_tablenav"),
                10,
                1
            );
        }
    }

    public static function getState() {
        // State token, a uuid is fine here
        return uniqid();
    }

    public static function generateCodeVerifier() {
        // Generate a random code verifier
        return rtrim( strtr( base64_encode( random_bytes( 32 ) ), '+/', '-_' ), '=' );
    }

    public static function generateCodeChallenge( $codeVerifier ) {
        // Hash the code verifier using SHA-256
        $challengeBytes = hash( 'sha256', $codeVerifier, true );
        // Encode the hashed code verifier using URL safe Base64 encoding
        return rtrim( strtr( base64_encode( $challengeBytes ), "+/", "-_" ), "=" );
    }

    public static function GenerateAWeberAuthorizationUrl() {
        $aweberAuthUrl = self::$aweberAuthorizeBaseURL;
        // Generate a random code verifier
        $codeVerifier = self::generateCodeVerifier();
        $current_user_id = get_current_user_id();
        update_user_meta( $current_user_id, 'aweber_code_verifier', $codeVerifier );
        // Generate the code challenge
        $codeChallenge = self::generateCodeChallenge( $codeVerifier );
        $authQueryParams = array(
            'response_type'         => self::$aweberResponseType,
            'client_id'             => self::$aweberClientId,
            'state'                 => self::getState(),
            'redirect_uri'          => 'urn:ietf:wg:oauth:2.0:oob',
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => self::$codeChallengeMethod,
            'scope'                 => implode( ' ', self::$aweberScopes ),
        );
        return $aweberAuthUrl . '?' . http_build_query( $authQueryParams );
    }

    public function kleverlist_aweber_settings_handle() {
        $response_arr = array();
        $valid_request = isset( $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'kleverlist_aweber_nonce' ) && isset( $_REQUEST['service_name'] ) && !empty( $_REQUEST['service_name'] ) && isset( $_POST['auth_code'] ) && !empty( $_POST['auth_code'] );
        if ( $valid_request ) {
            $authorization_code = sanitize_text_field( $_POST['auth_code'] );
            $service_name = sanitize_text_field( $_REQUEST['service_name'] );
            $client_id = self::$aweberClientId;
            $client_secret = '';
            // get same code verifier from user meta
            $current_user_id = get_current_user_id();
            $code_verifier = get_user_meta( $current_user_id, 'aweber_code_verifier', true );
            // For Proof Key for Code Exchange (PKCE)
            $body = array(
                'client_id'     => $client_id,
                'code_verifier' => $code_verifier,
                'grant_type'    => 'authorization_code',
                'code'          => $authorization_code,
            );
            // Encode the request body
            $body_encoded = http_build_query( $body );
            // Construct the request headers
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
            // Make the POST request
            $response = wp_remote_post( self::$tokenBaseURL, array(
                'method'  => 'POST',
                'headers' => $headers,
                'body'    => $body_encoded,
            ) );
            // Check for errors
            if ( !is_wp_error( $response ) ) {
                // Get the response status code
                $response_code = wp_remote_retrieve_response_code( $response );
                // Get the response body
                $response_body = wp_remote_retrieve_body( $response );
                // Parse the JSON response
                $token_data = json_decode( $response_body, true );
                // Check if access token exists in response and status code is 200
                if ( $response_code === 200 && isset( $token_data['access_token'] ) ) {
                    // Store token data into option array
                    $expiration_time = time() + $token_data['expires_in'];
                    // Calculate expiration time
                    $token_data['expiration_time'] = $expiration_time;
                    update_option( 'kleverlist_aweber_tokenData', $token_data );
                    update_option( 'kleverlist_service_type', $service_name );
                    update_option( 'kleverlist_aweber_auth_code', $authorization_code );
                    self::kleverlist_aweber_get_accounts_details();
                    $page_url = admin_url( 'admin.php?page=kleverlist-integrations' );
                    $response_arr = array(
                        'status'   => 1,
                        'page_url' => $page_url,
                        'message'  => __( 'Verified Successfully', 'kleverlist' ),
                    );
                } else {
                    $response_arr = array(
                        'status'  => 0,
                        'message' => __( 'Invalid Request', 'kleverlist' ),
                    );
                }
            } else {
                $response_arr = array(
                    'status'  => 0,
                    'message' => __( 'Something Wrong, Try again', 'kleverlist' ),
                );
            }
        } else {
            $response_arr = array(
                'status'  => 0,
                'message' => __( 'Something went wrong', 'kleverlist' ),
            );
        }
        wp_send_json( $response_arr );
        wp_die();
    }

    public static function kleverlist_aweber_get_accounts_details() {
        $accessToken = '';
        $awebertokenData = get_option( 'kleverlist_aweber_tokenData' );
        $accounts_id = get_option( 'kleverlist_aweber_accounts_id' );
        if ( empty( $awebertokenData ) || !isset( $awebertokenData['access_token'] ) || empty( $awebertokenData['access_token'] ) ) {
            return;
        }
        if ( !empty( $accounts_id ) ) {
            return;
        }
        /*---Get Aweber Token --*/
        $accessToken = self::Kleverlist_Get_AWeberToken();
        // Check if AWeber access token data is empty
        if ( empty( $accessToken ) ) {
            return;
        }
        $response = wp_remote_get( 'https://api.aweber.com/1.0/accounts', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'User-Agent'    => 'AWeber-PHP-code-sample/1.0',
            ),
        ) );
        if ( !is_wp_error( $response ) ) {
            $response_code = wp_remote_retrieve_response_code( $response );
            if ( 200 === $response_code ) {
                $body = wp_remote_retrieve_body( $response );
                // Assuming $body contains JSON data with 'id' entries
                $body_json = json_decode( $body, true );
                if ( is_array( $body_json ) && !empty( $body_json['entries'][0]['id'] ) ) {
                    $account_id = $body_json['entries'][0]['id'];
                    self::kleverlist_aweber_get_lists( $account_id, $accessToken );
                    update_option( 'kleverlist_aweber_accounts_id', $account_id );
                }
            }
        }
    }

    public static function kleverlist_aweber_get_lists( $account_id, $accessToken ) {
        $lists_data = array();
        if ( empty( $account_id ) || empty( $accessToken ) ) {
            return;
        }
        // Check if the option exists and has data
        $lists_data = get_option( 'kleverlist_aweber_account_lists_data' );
        if ( !empty( $lists_data ) ) {
            return;
        }
        $url = "https://api.aweber.com/1.0/accounts/{$account_id}/lists/";
        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept'        => 'application/json',
                'User-Agent'    => 'AWeber-PHP-code-sample/1.0',
            ),
        ) );
        if ( !is_wp_error( $response ) ) {
            $body = wp_remote_retrieve_body( $response );
            // Assuming $body contains JSON data with 'id' entries
            $body_json = json_decode( $body, true );
            if ( $body_json !== null ) {
                // Check if decoding was successful
                // Check if 'entries' key exists in the response and it's an array
                if ( isset( $body_json['entries'] ) && is_array( $body_json['entries'] ) ) {
                    foreach ( $body_json['entries'] as $entry ) {
                        // Store id and name into the array as key-value pairs
                        if ( is_array( $entry ) && isset( $entry['id'] ) && isset( $entry['name'] ) ) {
                            $lists_data[$entry['id']] = $entry['name'];
                        }
                    }
                }
                // Save the lists data into an option
                update_option( 'kleverlist_aweber_account_lists_data', $lists_data );
            }
        }
    }

    public function aweber_enqueue_scripts() {
        wp_enqueue_script(
            'kleverlist-aweber',
            plugin_dir_url( __FILE__ ) . 'js/kleverlist-aweber-admin.js',
            array('jquery'),
            $this->version,
            false
        );
        // kleverlist plugin object
        $is_kleverlist_premium_type = null;
        $totalCustomers = 0;
        wp_localize_script( 'kleverlist-aweber', 'kleverlist_aweber_object', array(
            'ajax_url'              => admin_url( 'admin-ajax.php' ),
            'nonce'                 => wp_create_nonce( 'kleverlist_aweber_nonce' ),
            'totalCustomers'        => $totalCustomers,
            'is_kleverlist_premium' => $is_kleverlist_premium_type,
        ) );
    }

    /*------- Add and update order data to  subscriber in aweber -------*/
    public function kleverlist_send_order_data_to_aweber_on_wc_order_processing( $order_id ) {
        // Input validation
        if ( !$order_id && get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_AWEBER && empty( get_option( 'kleverlist_aweber_tokenData' ) ) ) {
            return;
        }
        // Check if order processing has already been done
        if ( get_post_meta( $order_id, '_kleverlist_aweber_order_processing', true ) ) {
            return;
        }
        $aweber_accounts_id = get_option( 'kleverlist_aweber_accounts_id' );
        if ( empty( $aweber_accounts_id ) ) {
            return;
        }
        $aweber_list_data = get_option( 'kleverlist_aweber_user_selected_account_id' );
        if ( empty( $aweber_list_data ) ) {
            return;
        }
        // Allow code execution only once
        if ( !get_post_meta( $order_id, '_kleverlist_aweber_order_processing', true ) ) {
            /**------crate custom field in aweber ----- */
            $this->kleverlist_create_aweber_custom_field();
            // Get an instance of the WC_Order object
            $order = wc_get_order( $order_id );
            // Get customer information
            $customer_id = $order->get_customer_id();
            $user = $order->get_user();
            $billing_email = sanitize_email( $order->get_billing_email() );
            $order_date = wc_format_datetime( $order->get_date_created() );
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
            $username = sanitize_user( $user->user_login );
            $firstname = ( '1' === get_option( 'kleverlist_aweber_firstname' ) ? $firstname : '' );
            $lastname = ( '1' === get_option( 'kleverlist_aweber_lastname' ) ? $lastname : '' );
            $username = ( '1' === get_option( 'kleverlist_aweber_username' ) ? $username : '' );
            $aweber_list_id = null;
            if ( !empty( $billing_email ) ) {
                $fullname = $firstname . ' ' . $lastname;
                $order_data = [
                    'email' => $billing_email,
                    'name'  => $fullname,
                ];
                $order_data['custom_fields']['first_name'] = $firstname;
                $order_data['custom_fields']['last_name'] = $lastname;
                $order_data['custom_fields']['username'] = $username;
                // Instantiate Guzzle client with SSL verification disabled
                $client = new GuzzleHttp\Client([
                    'verify' => false,
                ]);
                // Get an order items
                $items = $order->get_items();
                $orderProcessingTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    $pro_spi = get_post_meta( $product_id, '_order_processing_aweber_special_product', true );
                    if ( $pro_spi !== 'yes' ) {
                        continue;
                    } else {
                        $aweber_list_id = $aweber_list_data;
                    }
                    /**--------Add tag-------- */
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_aweber_order_processing_tag' ) ) {
                        // Check if 'order processing' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_PROCESSING_TAG, $orderProcessingTags ) ) {
                            $orderProcessingTags[] = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                        }
                        // $remove_complete_tag[] = KLEVERLIST_DEFAULT_COMPLETED_TAG;
                    }
                }
                if ( !empty( $aweber_accounts_id ) && !empty( $aweber_list_id ) && !is_null( $aweber_list_id ) ) {
                    /*---Get Aweber Token --*/
                    $access_token = self::Kleverlist_Get_AWeberToken();
                    // Check if AWeber access token  is empty
                    if ( empty( $access_token ) ) {
                        return;
                    }
                    // Get AWeber accounts and lists
                    $accounts = $this->aweber_getCollection( $client, $access_token, AWEBER_BASE_URL . 'accounts' );
                    if ( !empty( $accounts ) ) {
                        foreach ( $accounts as $account_data ) {
                            if ( $aweber_accounts_id == $account_data['id'] ) {
                                $listsUrl = $account_data['lists_collection_link'];
                                $lists = $this->aweber_getCollection( $client, $access_token, $listsUrl );
                                if ( !empty( $lists ) ) {
                                    foreach ( $lists as $list_value ) {
                                        if ( $aweber_list_id == $list_value['id'] ) {
                                            // Find out if a subscriber exists on the first list
                                            $params = [
                                                'ws.op' => 'find',
                                                'email' => $billing_email,
                                            ];
                                            $subsUrl = $list_value['subscribers_collection_link'];
                                            $findUrl = $subsUrl . '?' . http_build_query( $params );
                                            $foundSubscribers = $this->aweber_getCollection( $client, $access_token, $findUrl );
                                            $resubscribe_order_action_option = get_option( 'kleverlist_aweber_global_resubscribe_order_action_option' );
                                            if ( isset( $foundSubscribers[0]['self_link'] ) ) {
                                                $existing_subscriber_status = $foundSubscribers[0]['status'];
                                                if ( $existing_subscriber_status === 'subscribed' || $existing_subscriber_status === 'unsubscribed' && get_option( 'kleverlist_global_resubscribe' ) === '1' && $resubscribe_order_action_option == 'kleverlist_aweber_global_resubscribe_order_on_processing' ) {
                                                    // Update Subscriber
                                                    try {
                                                        $order_data['status'] = 'subscribed';
                                                        $order_data['tags'] = array(
                                                            'add' => $orderProcessingTags,
                                                        );
                                                        $subscriberUrl = $foundSubscribers[0]['self_link'];
                                                        $subscriberResponse = $client->patch( $subscriberUrl, [
                                                            'json'    => $order_data,
                                                            'headers' => [
                                                                'Authorization' => 'Bearer ' . $access_token,
                                                            ],
                                                        ] )->getBody();
                                                        if ( $existing_subscriber_status === 'subscribed' ) {
                                                            $message = 'Order Proccessing Subscriber updated successfully.';
                                                        } else {
                                                            $message = 'Order Proccessing Subscriber resubscribed successfully.';
                                                        }
                                                        wc_get_logger()->debug( $message, array(
                                                            'source' => 'kleverlist',
                                                        ) );
                                                    } catch ( \GuzzleHttp\Exception\ClientException $e ) {
                                                        $responseBody = $e->getResponse()->getBody()->getContents();
                                                        $errorMessage = json_decode( $responseBody, true )['error']['message'];
                                                        if ( $existing_subscriber_status === 'subscribed' ) {
                                                            $error = "Order Proccessing Error updating subscriber: " . $errorMessage;
                                                        } else {
                                                            $error = "Order Proccessing Error resubscribing subscriber: " . $errorMessage;
                                                        }
                                                        wc_get_logger()->error( $error, array(
                                                            'source' => 'kleverlist',
                                                        ) );
                                                    }
                                                }
                                            } else {
                                                // Add Subscriber
                                                try {
                                                    $order_data['tags'] = $orderProcessingTags;
                                                    $body = $client->post( $subsUrl, [
                                                        'json'    => $order_data,
                                                        'headers' => [
                                                            'Authorization' => 'Bearer ' . $access_token,
                                                        ],
                                                    ] );
                                                    wc_get_logger()->debug( 'Order Proccessing Subscriber Added successfully.', array(
                                                        'source' => 'kleverlist',
                                                    ) );
                                                } catch ( \GuzzleHttp\Exception\ClientException $e ) {
                                                    $responseBody = $e->getResponse()->getBody()->getContents();
                                                    $errorMessage = json_decode( $responseBody, true )['error']['message'];
                                                    wc_get_logger()->error( $errorMessage, array(
                                                        'source' => 'kleverlist',
                                                    ) );
                                                }
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }
            $order->update_meta_data( '_kleverlist_aweber_order_processing', true );
            $order->save();
            // Save
        }
    }

    /**
     * Retrieve AWeber access token.         
     */
    public static function Kleverlist_Get_AWeberToken() {
        // Get AWeber access token data
        $awebertokenData = get_option( 'kleverlist_aweber_tokenData' );
        // Check if token data exists
        if ( !empty( $awebertokenData ) ) {
            $current_time = time();
            // Current UNIX timestamp
            // Check if token is expired
            if ( $current_time >= $awebertokenData['expiration_time'] ) {
                $refreshToken = $awebertokenData['refresh_token'];
                // Check if refresh token is available
                if ( !empty( $refreshToken ) ) {
                    // Prepare request body
                    $body = array(
                        'grant_type'    => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    );
                    // Encode request body
                    $body_encoded = http_build_query( $body );
                    // Prepare headers
                    $client_id = self::$aweberClientId;
                    $authentication = base64_encode( $client_id . ':' );
                    $headers = array(
                        'Content-Type'  => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Basic ' . $authentication,
                    );
                    // Make POST request to refresh the token
                    $response = wp_remote_post( self::$tokenBaseURL, array(
                        'method'  => 'POST',
                        'headers' => $headers,
                        'body'    => $body_encoded,
                    ) );
                    // Check for errors
                    if ( !is_wp_error( $response ) ) {
                        // Get response body
                        $response_body = wp_remote_retrieve_body( $response );
                        // Parse JSON response
                        $token_data = json_decode( $response_body, true );
                        // Check if access token exists in response
                        if ( isset( $token_data['access_token'] ) ) {
                            // Update token expiration time
                            $expiration_time = $current_time + $token_data['expires_in'];
                            $token_data['expiration_time'] = $expiration_time;
                            // Update token data in options
                            update_option( 'kleverlist_aweber_tokenData', $token_data );
                            // Return access token
                            return $token_data['access_token'];
                        }
                    }
                    // Return  if access token retrieval fails
                    return '';
                }
            } else {
                // Return access token if not expired
                return $awebertokenData['access_token'];
            }
        }
        // Return if token data is empty
        return '';
    }

    // Function to create custom Aweber audience fields
    public function kleverlist_create_aweber_custom_field() {
        // Check if AWeber is the selected service
        if ( get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_AWEBER ) {
            return;
        }
        $aweber_accounts_id = get_option( 'kleverlist_aweber_accounts_id' );
        $aweber_list_id = get_option( 'kleverlist_aweber_user_selected_account_id' );
        // Create a new HTTP client instance with SSL verification disabled
        $client = new GuzzleHttp\Client([
            'verify' => false,
        ]);
        /*---Get Aweber Token --*/
        $accessToken = self::Kleverlist_Get_AWeberToken();
        // Check if AWeber access token is empty
        if ( empty( $accessToken ) ) {
            return;
        }
        // Define custom field data
        $customFieldsData = array(
            'first_name' => 'first name',
            'last_name'  => 'last Name',
            'username'   => strtoupper( 'Username' ),
        );
        try {
            // Get account information
            $accounts = $this->aweber_getCollection( $client, $accessToken, AWEBER_BASE_URL . 'accounts' );
            if ( !empty( $aweber_accounts_id ) && !empty( $aweber_list_id ) ) {
                if ( !empty( $accounts ) ) {
                    foreach ( $accounts as $account_data ) {
                        if ( $aweber_accounts_id == $account_data['id'] ) {
                            // Get list information
                            $listsUrl = $account_data['lists_collection_link'];
                            $lists = $this->aweber_getCollection( $client, $accessToken, $listsUrl );
                            if ( !empty( $lists ) ) {
                                foreach ( $lists as $list_value ) {
                                    if ( $aweber_list_id == $list_value['id'] ) {
                                        // Get custom fields collection URL
                                        $customFieldsUrl = $list_value['custom_fields_collection_link'];
                                        $customFields = $this->aweber_getCollection( $client, $accessToken, $customFieldsUrl );
                                        // Determine custom fields to create
                                        $fieldsToCreate = [];
                                        foreach ( $customFieldsData as $fieldName => $fieldValue ) {
                                            $exists = false;
                                            foreach ( $customFields as $entry ) {
                                                if ( $entry['name'] === $fieldName ) {
                                                    $exists = true;
                                                    break;
                                                }
                                            }
                                            if ( !$exists ) {
                                                $fieldsToCreate[$fieldName] = $fieldValue;
                                            }
                                        }
                                        // Create custom fields
                                        foreach ( $fieldsToCreate as $fieldName => $fieldValue ) {
                                            $createResponse = $client->post( $customFieldsUrl, [
                                                'form_params' => [
                                                    'ws.op' => 'create',
                                                    'name'  => $fieldName,
                                                ],
                                                'headers'     => [
                                                    'Authorization' => 'Bearer ' . $accessToken,
                                                ],
                                            ] );
                                            // Check if the request was successful
                                            if ( $createResponse->getStatusCode() === 201 ) {
                                                $fieldUrl = $createResponse->getHeader( 'Location' )[0];
                                                $message = "Created new custom field '{$fieldName}' at {$fieldUrl}\n";
                                                wc_get_logger()->debug( $message, array(
                                                    'source' => 'kleverlist',
                                                ) );
                                            } else {
                                                // Handle unsuccessful response
                                                $responseBody = $createResponse->getBody()->getContents();
                                                $message = "Error creating custom field '{$fieldName}': {$responseBody}\n";
                                                wc_get_logger()->debug( $message, array(
                                                    'source' => 'kleverlist',
                                                ) );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } catch ( Exception $e ) {
            // Print any exception messages for debugging
            // var_dump($e->getMessage());
        }
    }

    // Get kleverlist Product Tags By IDs
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

    /*------- call aweber api  -------*/
    public function aweber_getCollection( $client, $accessToken, $url ) {
        try {
            $collection = array();
            while ( isset( $url ) ) {
                $request = $client->get( $url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                ] );
                $body = $request->getBody();
                $page = json_decode( $body, true );
                $collection = array_merge( $page['entries'], $collection );
                $url = ( isset( $page['next_collection_link'] ) ? $page['next_collection_link'] : null );
            }
            return $collection;
        } catch ( \GuzzleHttp\Exception\ClientException $e ) {
            // Log the error
            error_log( 'AWeber API Error: ' . $e->getMessage() );
            // Retrieve and display the error message
            $responseBody = $e->getResponse()->getBody()->getContents();
            $errorMessage = json_decode( $responseBody, true )['error']['message'];
            //echo "Error :" . $errorMessage;
            // Return an empty array or handle the error as needed
            return array();
        }
    }

    public function kleverlist_send_order_data_to_aweber_on_wc_order_completed( $order_id ) {
        if ( !$order_id && get_option( 'kleverlist_service_type' ) !== KLEVERLIST_SERVICE_AWEBER && empty( get_option( 'kleverlist_aweber_tokenData' ) ) ) {
            return;
        }
        if ( get_post_meta( $order_id, '_kleverlist_aweber_order_completed', true ) ) {
            return;
        }
        $aweber_accounts_id = get_option( 'kleverlist_aweber_accounts_id' );
        if ( empty( $aweber_accounts_id ) ) {
            return;
        }
        $aweber_list_data = get_option( 'kleverlist_aweber_user_selected_account_id' );
        if ( empty( $aweber_list_data ) ) {
            return;
        }
        if ( !get_post_meta( $order_id, '_kleverlist_aweber_order_completed', true ) ) {
            /**------crate custom field in aweber ----- */
            $this->kleverlist_create_aweber_custom_field();
            // Get an instance of the WC_Order object
            $order = wc_get_order( $order_id );
            // Get customer information
            $aweber_list_id = '';
            $customer_id = $order->get_customer_id();
            $user = $order->get_user();
            $billing_email = sanitize_email( $order->get_billing_email() );
            $order_date = wc_format_datetime( $order->get_date_created() );
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
            $username = sanitize_user( $user->user_login );
            $firstname = ( '1' === get_option( 'kleverlist_aweber_firstname' ) ? $firstname : '' );
            $lastname = ( '1' === get_option( 'kleverlist_aweber_lastname' ) ? $lastname : '' );
            $username = ( '1' === get_option( 'kleverlist_aweber_username' ) ? $username : '' );
            // Check if billing email is not empty
            if ( !empty( $billing_email ) ) {
                $fullname = $firstname . ' ' . $lastname;
                $order_data = [
                    'email' => $billing_email,
                    'name'  => $fullname,
                ];
                $order_data['custom_fields']['first_name'] = $firstname;
                $order_data['custom_fields']['last_name'] = $lastname;
                $order_data['custom_fields']['username'] = $username;
                // Instantiate Guzzle client with SSL verification disabled
                $client = new GuzzleHttp\Client([
                    'verify' => false,
                ]);
                // Get an order items
                $items = $order->get_items();
                $remove_processing_tag = [];
                $ordercompleteTags = [];
                foreach ( $items as $item ) {
                    $product_name = $item->get_name();
                    $product_id = $item->get_product_id();
                    // Individual product list checkbox is checked and list is assigned (wither default/any other )
                    $pro_spi = get_post_meta( $product_id, '_order_completed_aweber_special_product', true );
                    if ( $pro_spi !== 'yes' ) {
                        continue;
                    } else {
                        $aweber_list_id = $aweber_list_data;
                    }
                    /**--------Add tag-------- */
                    if ( $pro_spi === 'yes' && '1' === get_option( 'kleverlist_aweber_order_completed_tag' ) ) {
                        // Check if 'order processing' tag is already in the tag array
                        if ( !in_array( KLEVERLIST_DEFAULT_COMPLETED_TAG, $ordercompleteTags ) ) {
                            $ordercompleteTags[] = KLEVERLIST_DEFAULT_COMPLETED_TAG;
                        }
                        if ( '1' === get_option( 'kleverlist_aweber_remove_order_processing_tag' ) ) {
                            $remove_processing_tag[] = KLEVERLIST_DEFAULT_PROCESSING_TAG;
                        }
                    }
                }
                if ( !empty( $aweber_accounts_id ) && !empty( $aweber_list_id ) && !is_null( $aweber_list_id ) ) {
                    /*---Get Aweber Token --*/
                    $access_token = self::Kleverlist_Get_AWeberToken();
                    // Check if AWeber access token  is empty
                    if ( empty( $access_token ) ) {
                        return;
                    }
                    // Get AWeber accounts and lists
                    $accounts = $this->aweber_getCollection( $client, $access_token, AWEBER_BASE_URL . 'accounts' );
                    if ( !empty( $accounts ) ) {
                        foreach ( $accounts as $account_data ) {
                            if ( $aweber_accounts_id == $account_data['id'] ) {
                                $listsUrl = $account_data['lists_collection_link'];
                                $lists = $this->aweber_getCollection( $client, $access_token, $listsUrl );
                                if ( !empty( $lists ) ) {
                                    foreach ( $lists as $list_value ) {
                                        if ( $aweber_list_id == $list_value['id'] ) {
                                            // Find out if a subscriber exists on the first list
                                            $params = [
                                                'ws.op' => 'find',
                                                'email' => $billing_email,
                                            ];
                                            $subsUrl = $list_value['subscribers_collection_link'];
                                            $findUrl = $subsUrl . '?' . http_build_query( $params );
                                            $foundSubscribers = $this->aweber_getCollection( $client, $access_token, $findUrl );
                                            if ( isset( $foundSubscribers[0]['self_link'] ) ) {
                                                $existing_subscriber_status = $foundSubscribers[0]['status'];
                                                $resubscribe_order_action_option = get_option( 'kleverlist_aweber_global_resubscribe_order_action_option' );
                                                if ( $existing_subscriber_status === 'subscribed' || $existing_subscriber_status === 'unsubscribed' && get_option( 'kleverlist_global_resubscribe' ) === '1' && $resubscribe_order_action_option == 'kleverlist_aweber_global_resubscribe_order_on_complete' ) {
                                                    // Update Subscriber
                                                    try {
                                                        $order_data['status'] = 'subscribed';
                                                        $order_data['tags'] = array(
                                                            'add'    => $ordercompleteTags,
                                                            'remove' => $remove_processing_tag,
                                                        );
                                                        $subscriberUrl = $foundSubscribers[0]['self_link'];
                                                        $subscriberResponse = $client->patch( $subscriberUrl, [
                                                            'json'    => $order_data,
                                                            'headers' => [
                                                                'Authorization' => 'Bearer ' . $access_token,
                                                            ],
                                                        ] )->getBody();
                                                        if ( $existing_subscriber_status === 'subscribed' ) {
                                                            $message = 'Order Complete Subscriber updated successfully.';
                                                        } else {
                                                            $message = 'Order Complete Subscriber resubscribed successfully.';
                                                        }
                                                        wc_get_logger()->debug( $message, array(
                                                            'source' => 'kleverlist',
                                                        ) );
                                                    } catch ( \GuzzleHttp\Exception\ClientException $e ) {
                                                        $responseBody = $e->getResponse()->getBody()->getContents();
                                                        $errorMessage = json_decode( $responseBody, true )['error']['message'];
                                                        if ( $existing_subscriber_status === 'subscribed' ) {
                                                            $error = "Order Complete Error updating subscriber: " . $errorMessage;
                                                        } else {
                                                            $error = "Order Complete Error resubscribing subscriber: " . $errorMessage;
                                                        }
                                                        wc_get_logger()->error( $error, array(
                                                            'source' => 'kleverlist',
                                                        ) );
                                                    }
                                                }
                                            } else {
                                                // Add Subscriber
                                                try {
                                                    $order_data['tags'] = $ordercompleteTags;
                                                    $body = $client->post( $subsUrl, [
                                                        'json'    => $order_data,
                                                        'headers' => [
                                                            'Authorization' => 'Bearer ' . $access_token,
                                                        ],
                                                    ] );
                                                    wc_get_logger()->debug( 'Order Complete Subscriber Added successfully.', array(
                                                        'source' => 'kleverlist',
                                                    ) );
                                                } catch ( \GuzzleHttp\Exception\ClientException $e ) {
                                                    $responseBody = $e->getResponse()->getBody()->getContents();
                                                    $errorMessage = json_decode( $responseBody, true )['error']['message'];
                                                    wc_get_logger()->error( $errorMessage, array(
                                                        'source' => 'kleverlist',
                                                    ) );
                                                }
                                            }
                                        } else {
                                            continue;
                                        }
                                    }
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }
            $order->update_meta_data( '_kleverlist_aweber_order_completed', true );
            $order->save();
            // Save
        }
    }

    public function kleverlist_aweber_custom_product_tab( $tabs ) {
        $tabs['kleverlist_wc_custom_tab'] = array(
            'label'    => __( 'KleverList', 'kleverlist' ),
            'target'   => 'kleverlist_aweber_wc_custom_product_panels',
            'priority' => 10,
            'class'    => array('show_if_kleverlist_aweber'),
        );
        return $tabs;
    }

    public function kleverlist_aweber_wc_custom_product_panels() {
        echo '<div id="kleverlist_aweber_wc_custom_product_panels" class="panel woocommerce_options_panel hidden">';
        /******** WC Order Processing ********/
        echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Processing', 'kleverlist' ) . '</h2>';
        $audience_arr = get_option( 'kleverlist_aweber_audience_lists' );
        /******** Subscribe list when order processing ********/
        $order_processing_description = esc_html__( "If enabled, you can subscribe the customer to a  List on “order processing”", "kleverlist" );
        $processing_aweber_special_product = get_post_meta( get_the_ID(), '_order_processing_aweber_special_product', true );
        $is_active_all_products = get_option( 'kleverlist_aweber_global_active_all_products' );
        $is_order_processing_action = get_option( 'kleverlist_aweber_global_active_all_order_action' );
        /******** WC Order Processing ********/
        $order_processing_checkbox_value = ( empty( $processing_aweber_special_product ) && $is_active_all_products === '1' && $is_order_processing_action === 'order_processing' ? 'yes' : $processing_aweber_special_product );
        woocommerce_wp_checkbox( array(
            'id'            => 'aweber_spi_order_processing',
            'value'         => esc_attr( $order_processing_checkbox_value ),
            'label'         => __( 'Subscribe to a List', 'kleverlist' ),
            'desc_tip'      => true,
            'wrapper_class' => "kleverlist_special_product",
            'description'   => $order_processing_description,
        ) );
        $order_processing_aweber_lists = get_option( 'kleverlist_aweber_account_lists_data' );
        if ( !empty( $order_processing_aweber_lists ) ) {
            foreach ( $order_processing_aweber_lists as $key => $process_list ) {
                $order_processing_subscribe_options[$key] = $process_list;
            }
        }
        woocommerce_wp_text_input( array(
            'id'                => 'order_processing_aweber_special_product_list',
            'label'             => __( 'List:', 'kleverlist' ),
            'wrapper_class'     => 'hidden',
            'required'          => true,
            'value'             => ( !empty( $order_processing_subscribe_options ) ? reset( $order_processing_subscribe_options ) : '' ),
            'custom_attributes' => array(
                'disabled' => 'disabled',
            ),
        ) );
        $order_processing_dropdown_description = esc_html__( "The customer will be added to the selected List on “Order processing”", "kleverlist" );
        echo '<p class="hidden order_processing_aweber_special_product_list_field" style="margin-left:150px;"> ' . $order_processing_dropdown_description . '</p>';
        /******** Subscribe list when order processing ********/
        /******** WC Order Completed ********/
        echo '<h2 class="kleverlist_wc_tab_title">' . esc_html__( 'Actions on Order Complete', 'kleverlist' ) . '</h2>';
        /******** Subscribe list when order completed ********/
        $order_completed_description = esc_html__( "If enabled, you can subscribe the customer to a  List on “order completed” ", "kleverlist" );
        $completed_aweber_special_product = get_post_meta( get_the_ID(), '_order_completed_aweber_special_product', true );
        $is_active_all_products = get_option( 'kleverlist_aweber_global_active_all_products' );
        $is_order_complete_action = get_option( 'kleverlist_aweber_global_active_all_order_action' );
        $order_completed_checkbox_value = ( empty( $completed_aweber_special_product ) && $is_active_all_products === '1' && $is_order_complete_action === 'order_completed' ? 'yes' : $completed_aweber_special_product );
        woocommerce_wp_checkbox( array(
            'id'            => 'aweber_spi_order_completed',
            'value'         => esc_attr( $order_completed_checkbox_value ),
            'label'         => __( 'Subscribe to a List', 'kleverlist' ),
            'desc_tip'      => true,
            'wrapper_class' => "kleverlist_special_product",
            'description'   => $order_completed_description,
        ) );
        woocommerce_wp_text_input( array(
            'id'                => 'order_completed_aweber_special_product_list',
            'label'             => __( 'List:', 'kleverlist' ),
            'wrapper_class'     => 'hidden',
            'required'          => true,
            'value'             => ( !empty( $order_processing_subscribe_options ) ? reset( $order_processing_subscribe_options ) : '' ),
            'custom_attributes' => array(
                'disabled' => 'disabled',
            ),
        ) );
        $dropdown_description = esc_html__( "The customer will be added to the selected List on “Order complete”", "kleverlist" );
        echo '<p class="hidden order_completed_aweber_special_product_list_field" style="margin-left:150px;"> ' . $dropdown_description . '</p>';
        /******** Subscribe list when order completed ********/
        echo '</div>';
        if ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
        }
    }

    public function kleverlist_aweber_add_product_nonce_field() {
        wp_nonce_field( 'kleverlist_aweber_product_meta', 'kleverlist_aweber_product_nonce' );
    }

    public function kleverlist_aweber_wc_custom_product_save_fields( $id ) {
        // Verify the nonce
        if ( !isset( $_POST['kleverlist_aweber_product_nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kleverlist_aweber_product_nonce'] ) ), 'kleverlist_aweber_product_meta' ) ) {
            return;
        }
        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        /******** Subscribe list when the order processing ********/
        $aweber_spi_order_processing = ( isset( $_POST['aweber_spi_order_processing'] ) && 'yes' === $_POST['aweber_spi_order_processing'] ? 'yes' : 'no' );
        update_post_meta( $id, '_order_processing_aweber_special_product', sanitize_text_field( $aweber_spi_order_processing ) );
        if ( isset( $_POST['order_processing_aweber_special_product_list'] ) && !empty( $_POST['order_processing_aweber_special_product_list'] ) ) {
            $order_processing_aweber_special_product_list = sanitize_text_field( $_POST['order_processing_aweber_special_product_list'] );
            update_post_meta( $id, '_order_processing_aweber_special_product_list', $order_processing_aweber_special_product_list );
        }
        /******** Subscribe list when the order processing ********/
        /******** Subscribe list when the order completed ********/
        $aweber_spi_order_completed = ( isset( $_POST['aweber_spi_order_completed'] ) && 'yes' === $_POST['aweber_spi_order_completed'] ? 'yes' : 'no' );
        update_post_meta( $id, '_order_completed_aweber_special_product', sanitize_text_field( $aweber_spi_order_completed ) );
        if ( isset( $_POST['order_completed_aweber_special_product_list'] ) && !empty( $_POST['order_completed_aweber_special_product_list'] ) ) {
            $order_completed_aweber_special_product_list = sanitize_text_field( $_POST['order_completed_aweber_special_product_list'] );
            update_post_meta( $id, '_order_completed_aweber_special_product_list', $order_completed_aweber_special_product_list );
        }
        /******** Subscribe list when the order completed ********/
    }

    public function kleverlist_aweber_execute_extra_tablenav( $which ) {
        if ( !$this->extra_tablenav_added ) {
            if ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
                include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-aweber-bulk-products-settings.php';
            }
            // Set flag to true after adding extra tablenav
            $this->extra_tablenav_added = true;
        }
    }

}
