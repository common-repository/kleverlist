<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
    
$service_type = '';
$service_api_key = '';
$service_domain_name = '';
$service_verified = '';
$integrations_message = '';
$authorizationCode = '';
    
$kleverlist_service_settings = get_option('kleverlist_service_settings', '');
if (!empty($kleverlist_service_settings)) {
    $service_verified = $kleverlist_service_settings['service_verified'];
    $service_type = $kleverlist_service_settings['service_type'];
    $service_api_key = $kleverlist_service_settings['service_api_key'];
    $service_domain_name = $kleverlist_service_settings['service_domain_name'];
} elseif (get_option('kleverlist_service_type', '')) {
    $service_type = get_option('kleverlist_service_type', '');
}

$sendy_lists = get_option('kleverlist_sendy_lists', '');
$brands = get_option('kleverlist_sendy_brands', '');
    
if (!empty($service_api_key) && get_option('kleverlist_service_type') === 'sendy') {
    $service_api_key = Kleverlist_Admin::hide_input_character($service_api_key, 5);
}

if (!empty(get_option('kleverlist_mailchimp_apikey')) && get_option('kleverlist_service_type') === 'mailchimp') {
    $service_api_key = Kleverlist_Admin::hide_input_character(get_option('kleverlist_mailchimp_apikey'), 5);
}

if ($service_verified === KLEVERLIST_SERVICE_VERIFIED && empty($sendy_lists)) {
    $integrations_message = __('Almost Done! Now Choose a Brand and Load the Lists', 'kleverlist');
} elseif (!empty($sendy_lists)) {
    $integrations_message = __('Integration Successful', 'kleverlist');
}

if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_MAILCHIMP &&
        empty(get_option('kleverlist_mailchimp_user_audience'))
    ) {
    $integrations_message = __('Almost Done! Now Choose a Mailchimp Audience', 'kleverlist');
} elseif (!empty(get_option('kleverlist_mailchimp_user_audience'))) {
    $integrations_message = __('Integration Successful', 'kleverlist');
}

if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER &&
    empty(get_option('kleverlist_aweber_user_selected_account_id'))
) {
    $integrations_message = __('Almost Done! Now Choose a list', 'kleverlist');
} elseif (!empty(get_option('kleverlist_aweber_user_selected_account_id'))) {
    $integrations_message = __('Integration Successful', 'kleverlist');
}

$authorizationUrl = Kleverlist_AWeber::GenerateAWeberAuthorizationUrl();
if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER &&
    !empty(get_option('kleverlist_aweber_auth_code'))
) {
    $authorizationCode = Kleverlist_Admin::hide_input_character(get_option('kleverlist_aweber_auth_code'), 5);
}
?>
<div class="wrap kleverlist-settings-page">
    <div class="kleverlist-main-div-integrate-icon">
        <div class="kleverlist-icon-list">
            <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
        </div>
        <h1><?php esc_html_e('Integrations', 'kleverlist');?></h1>
    </div>
    <p class="kleverlist-page-main-description">
        <?php esc_html_e('Please choose your integration and configure it by entering the appropriate information. Once this initial step is done, you can proceed by configuring the Settings.', 'kleverlist');?>
        <a href="https://kleverlist.com/docs/config/introduction-to-kleverlist/" target="_blank">
            <?php esc_html_e('Link to the Documentation', 'kleverlist'); ?>
        </a>
    </p>
    <!--- Dashboard Settings Form Start -->  
    <table class="form-table width-900 klever-list-data-outer-div kleverlist-services">
        <tbody>
            <th class="klever-list-data-heading">
                <em><?php esc_html_e('Choose your integration', 'kleverlist');?></em>
            </th>
            <!-- Sendy Integration -->
            <td class="klever-list-btn-padd <?php echo ( $service_type === 'sendy' ) ? 'active' : ''; ?>">
                <div class="kleverlist-integrations integrations-block klever-width-btn">
                    <input
                        id="sendy"
                        class="kleverlist-checkbox"
                        name="kleverlist_service[]"
                        type="checkbox"
                        value="sendy"
                        <?php echo ( !empty($service_type) || $service_type === 'sendy') ? 'disabled' : ''; ?>
                        <?php checked($service_type, 'sendy'); ?> />
                    <label for="sendy" role="checkbox" class="kle-integration-label">
                        <span
                            class="labelauty-checked-image"
                            style="background-image:url('<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/sendy-logo.png'); ?>')"></span>
                        <span class="labelauty-checked" >
                            <?php esc_html_e('Sendy.Co', 'kleverlist');?>
                        </span>
                    </label>
                </div>
            </td>
            <!-- Sendy Integration -->

            <!-- Mailchimp Integration -->
            <td class="klever-list-btn-padd <?php echo ( $service_type === KLEVERLIST_SERVICE_MAILCHIMP ) ? 'active' : ''; ?>">
                <div class="kleverlist-integrations integrations-block klever-width-btn">
                    <input
                        id="mailchimp"
                        class="kleverlist-checkbox"
                        name="kleverlist_service[]"
                        type="checkbox"
                        value="mailchimp"
                        <?php echo ( !empty($service_type) || $service_type === 'mailchimp') ? 'disabled' : ''; ?>
                        <?php checked($service_type, 'mailchimp'); ?> />
                    <label for="mailchimp" role="checkbox" class="kle-integration-label">
                        <span
                            class="labelauty-checked-image"
                            style="background-image:url('<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/mailchimp-logo.png'); ?>')"></span>
                        <span class="labelauty-checked" ><?php esc_html_e('Mailchimp', 'kleverlist');?></span>
                    </label>
                </div>
            </td>
            <!-- Mailchimp Integration -->

            <!-- AWeber Integration -->
            <td class="klever-list-btn-padd <?php echo (($service_type === KLEVERLIST_SERVICE_AWEBER) || (isset($_GET['page']) && isset($_GET['code']) && !empty($_GET['code']) && $_GET['page'] === 'kleverlist-integrations')) ? 'active' : ''; ?>">
                <div class="kleverlist-integrations integrations-block klever-width-btn">
                    <input
                        id="aweber"
                        class="kleverlist-checkbox"
                        name="kleverlist_service[]"
                        type="checkbox"
                        value="aweber"
                        <?php echo ( !empty($service_type) || $service_type === 'aweber') ? 'disabled' : ''; ?>
                        <?php checked($service_type === 'aweber' || (isset($_GET['code']) && !empty($_GET['code']))); ?> />

                    <label for="aweber" role="checkbox" class="kle-integration-label">
                        <span
                            class="labelauty-checked-image"
                            style="background-image:url('<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/aweber-logo.png'); ?>')"></span>
                        <span class="labelauty-checked" ><?php esc_html_e('AWeber', 'kleverlist');?></span>
                    </label>
                </div>
            </td>
            <!-- AWeber Integration -->
        </tbody>  
    </table>

    <!-- Sendy Integration -->    
    <form method="POST" class="kleverlist-integration-forms" id="kleverlist_sendy_settings" style="display: <?php echo ( empty($service_type) || $service_type !== 'sendy' ) ? 'none' : 'block'; ?>;">
        <table class="form-table width-900 klever-list-data-outer-div kleverlist-service-table">
            <!-- Sendy Integration Inputs -->
            <tbody id="kleverlist_sendy_integration" class="settings-input-section sendy-integration-inputs" >
                <tr>
                    <td class="klever-list-data-td">
                        <label for="service_api_key">
                            <?php esc_html_e('API Key', 'kleverlist');?>
                        </label><br>
                        <input
                            type="text"
                            name="service_api_key"
                            id="service_api_key"
                            placeholder="<?php esc_attr_e('Please enter api key', 'kleverlist');?>"
                            <?php disabled($service_verified, KLEVERLIST_SERVICE_VERIFIED); ?>
                            value="<?php echo esc_attr($service_api_key);?>" required/>
                        <p></p>
                    </td>
                    <td>
                        <label for="domain_name">
                            <?php esc_html_e('Sendy Website', 'kleverlist');?>
                        </label><br/>
                        <input
                            id="domain_name"
                            class="kleverlist-input"
                            name="domain_name"
                            type="text"
                            placeholder="<?php esc_attr_e('Your domain: ie. example.com', 'kleverlist');?>"
                            <?php disabled($service_verified, KLEVERLIST_SERVICE_VERIFIED); ?>
                            value="<?php echo esc_attr($service_domain_name); ?>" required>
                        <p class="klever-list-data-paragraph">
                            <?php esc_html_e('Please make sure that your website is using HTTPS. If not, the integration will not work.', 'kleverlist');?>
                        </p>       
                    </td>             
                </tr>
                <?php if ($service_verified != KLEVERLIST_SERVICE_VERIFIED) :?>
                <tr>
                    <td class="kleverlist-position klever-list-data-mainchanges klever-list-data-one">
                        <?php
                            $submit_btn_attributes = array( 'id' => 'settings_submit_button' );
                            submit_button(__('Save Changes', 'kleverlist'), 'button button-primary', '', true, $submit_btn_attributes);
                        ?>
                        <div id="loader" class="kleverlist-loader-outer-div hidden"></div>
                    </td>
                </tr>
                <?php endif;?>
            </tbody>
            <!-- Sendy Integration Inputs -->
        </table>
        <p class="kleverlist-response verfied-klever-center"></p>
    </form>
    <!-- Sendy Integration -->

    <!-- Mailchimp Integration -->    
    <form method="POST" class="kleverlist-integration-forms" id="kleverlist_mailchimp_settings" style="display: <?php echo ( get_option('kleverlist_service_type') !== 'mailchimp' ) ? 'none' : 'block'; ?>;">
        <table class="form-table width-900 klever-list-data-outer-div kleverlist-service-table">
            <!-- Mailchimp Integration Inputs -->
            <tbody id="kleverlist_mailchimp_integration" class="settings-input-section mailchimp-integration-inputs"> 
                <tr>
                    <td class="klever-list-data-td">
                        <label for="mailchimp_apikey">
                            <?php esc_html_e('API Key', 'kleverlist');?>
                        </label><br>
                        <input
                            type="text"
                            name="mailchimp_apikey"
                            id="mailchimp_apikey"
                            <?php echo ( !empty($service_type) || $service_type === KLEVERLIST_SERVICE_MAILCHIMP ) ? 'disabled' : ''; ?>
                            placeholder="<?php esc_attr_e('Please enter api key', 'kleverlist');?>"                            
                            value="<?php echo esc_attr($service_api_key);?>" required/>                            
                    </td> 
                    <td>
                        <label for="mailchimp_api_url">
                            <?php esc_html_e('API URL', 'kleverlist');?>
                        </label><br/>
                        <input
                            id="mailchimp_api_url"
                            class="kleverlist-input"
                            name="mailchimp_api_url"
                            type="text"
                            placeholder="<?php esc_attr_e('Please enter api url', 'kleverlist');?>"
                            <?php disabled(get_option('kleverlist_service_type'), KLEVERLIST_SERVICE_MAILCHIMP); ?>
                            value="<?php echo get_option('kleverlist_mailchimp_apiurl'); ?>" required>
                        <p class="klever-list-data-paragraph">
                            <?php esc_html_e('Please make sure that your website is using HTTPS. If not, the integration will not work.', 'kleverlist');?>
                        </p>       
                    </td>                           
                </tr>
                <?php if ($service_type != KLEVERLIST_SERVICE_MAILCHIMP) :?>
                <tr>
                    <td class="kleverlist-position klever-list-data-mainchanges klever-list-data-one">
                        <?php
                            $submit_btn_attributes = array( 'id' => 'mailchimp_submit_button' );
                            submit_button(__('Save Changes', 'kleverlist'), 'button button-primary', '', true, $submit_btn_attributes);
                        ?>
                        <div id="mailchimp_loader" class="kleverlist-loader-outer-div hidden"></div>
                    </td>
                </tr>
                <?php endif;?>
            </tbody>
            <!-- Mailchimp Integration Inputs -->
        </table>
        <p class="kleverlist-response verfied-klever-center"></p>
    </form>
    <!-- Mailchimp Integration -->

    <!-- AWeber Integration -->    
    <form method="POST" class="kleverlist-integration-forms" id="kleverlist_aweber_settings" style="display: <?php echo ((get_option('kleverlist_service_type') === 'aweber') || (isset($_GET['page']) && isset($_GET['code']) && !empty($_GET['code']) && $_GET['page'] === 'kleverlist-integrations')) ? 'block' : 'none'; ?>;">
        <table class="form-table width-900 klever-list-data-outer-div kleverlist-service-table">
            <!-- AWeber Integration Inputs -->
            <tbody id="kleverlist_aweber_integration" class="settings-input-section aweber-integration-inputs"> 
                <tr>
                    <td class="klever-list-data-td">                        
                        <em> <a tabindex="-1" href="<?php echo esc_url($authorizationUrl) ?>" target="_blank"><?php esc_html_e('Get my AWeber App Authorization Code', 'kleverlist');?></a>.</em>
                    </td> 
                    <td>
                        <label for="kleverlist_aweber_auth_code">
                            <?php esc_html_e('Authorization Code', 'kleverlist');?>
                        </label><br/>
                        <textarea class="kleverlist-input" name="kleverlist_aweber_auth_code" id="kleverlist_aweber_auth_code"<?php disabled(get_option('kleverlist_service_type'), KLEVERLIST_SERVICE_AWEBER); ?>  rows="5"><?php echo esc_attr($authorizationCode);?></textarea>
                        <p class="klever-list-data-paragraph">
                            <?php esc_html_e('Please make sure that your website is using HTTPS. If not, the integration will not work.', 'kleverlist');?>
                        </p>       
                    </td>                           
                </tr>
                <?php if ($service_type != KLEVERLIST_SERVICE_AWEBER) :?>
                <tr>
                    <td class="kleverlist-position klever-list-data-mainchanges klever-list-data-one">
                        <?php
                            $submit_btn_attributes = array( 'id' => 'aweber_submit_button' );
                            submit_button(__('Save Changes', 'kleverlist'), 'button button-primary', '', true, $submit_btn_attributes);
                        ?>
                        <div id="aweber_loader" class="kleverlist-loader-outer-div hidden"></div>
                    </td>
                </tr>
                <?php endif;?>
            </tbody>
            <!-- AWeber Integration Inputs -->
        </table>
        <p class="kleverlist-response verfied-klever-center"></p>
    </form>
    <!-- AWeber Integration -->

    <!--- Dashboard Settings Form End -->
    <?php if (!empty($integrations_message)) :?>
    <div class="klever-list-data-heading-load-list">
        <h1><?php echo esc_html($integrations_message); ?></h1>
    </div>
    <?php endif;?>

    <!-- Remove Button Code Start -->
    <div class="klever-list-data-brandselect-main">
        <form method="POST" id="kleverlist_settings">
            <table class="form-table width-900 klever-list-data-removebtn">
                <tbody>
                    <?php if ($service_verified === KLEVERLIST_SERVICE_VERIFIED || get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_MAILCHIMP || get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER) :?>
                        <tr>
                            <td class="kleverlist-position">                
                                <?php
                                    $remove_btn_attributes = array( 'id' => 'kleverlist_remove_settings' );
                                    submit_button(__('Remove Integration', 'kleverlist'), 'delete', '', true, $remove_btn_attributes);
                                ?>
                                <div id="loader" class="kleverlist-loader-outer-div hidden"></div>
                            </td>
                        </tr>
                    <?php endif;?>
                </tbody>
            </table>
        </form>
        <!-- Remove Button Code End -->

        <?php
        if (!empty($brands)) :
            $brands_array = json_decode(json_encode($brands), true);
            ?>
            <!--- Brand Select Form Start -->
            <form method="POST" id="kleverlist_brands_settings">
                <table>
                    <tbody>                
                        <tr>                    
                            <td class="klever-list-data-dropdown">
                                <label for="sendy_brands"><?php esc_html_e('Choose a brand', 'kleverlist');?>:</label>
                                <select name="sendy_brands" class="kleverlist-load-integration-dropdown" id="sendy_brands" required>
                                    <option value=""><?php esc_html_e('Choose a brand', 'kleverlist');?></option>
                                <?php
                                foreach ($brands_array as $key => $brand) {
                                    $brand_id = sanitize_text_field($brand['id']);
                                    $brand_name = sanitize_text_field($brand['name']);
                                    $sendy_lists_array = is_array($sendy_lists) ? $sendy_lists : array();
                                    $selected = in_array($brand['id'], $sendy_lists_array, true) ? ' selected="selected" ' : '';
                                    echo '<option value="' . esc_attr($brand_id) . '" ' . $selected . '>' . esc_html($brand_name) . '</option>';
                                }
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="kleverlist-position klever-list-data-generate">
                                <?php
                                $other_attributes = array( 'id' => 'generate_lists' );
                                submit_button(__('Load Lists from Brand', 'kleverlist'), 'secondary', '', true, $other_attributes);
                                ?>
                                <p class="klever-list-data-paragraph"><?php esc_html_e('Do not forget to click this button every time you create or modify a new list.', 'kleverlist');?></p>  
                                <div id="brand_loader" class="kleverlist-loader-outer-div hidden"></div>
                            </td>
                        </tr>
                    <tbody>                    
                </table>            
            </form>  
            <p class="kleverlist-response-brands klever-list-data-generate-text"></p>
            <!--- Brand Select Form End -->
        <?php endif;?>

        <?php if (!empty(get_option('kleverlist_mailchimp_audience_lists'))) : ?>
            <!--- Mailchimp Audience Select Form Start -->
            <form method="POST" id="kleverlist_mailchimp_audience_settings">
                <table>
                    <tbody>                
                        <tr>                    
                            <td class="klever-list-data-dropdown">
                                <label for="mailchimp_audience"><?php esc_html_e('Choose an Audience', 'kleverlist');?>:</label>
                                <select name="mailchimp_audience" class="kleverlist-load-integration-dropdown" id="mailchimp_audience" required>
                                    <option value=""><?php esc_html_e('Choose an Audience', 'kleverlist');?></option>
                                    <?php
                                    $mailchimp_audience = get_option('kleverlist_mailchimp_audience_lists');
                                    $user_audience = get_option('kleverlist_mailchimp_user_audience');
                                    $selected = '';
                                    
                                    foreach ($mailchimp_audience as $key => $audience) {
                                        $audience_key = sanitize_text_field($key);
                                        $audience_name = sanitize_text_field($audience);
                                        $is_selected = ($key === $user_audience) ? true : false;
                                        echo '<option value="' . esc_attr($audience_key) . '" ' . selected($is_selected, true, false) . '>' . esc_html($audience_name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="kleverlist-position klever-list-data-generate">
                                <?php
                                    $other_attributes = array( 'id' => 'audience_lists' );
                                    submit_button(__('Load the Audience', 'kleverlist'), 'secondary', '', true, $other_attributes);
                                ?>                                           
                                <div id="audience_loader" class="kleverlist-loader-outer-div hidden"></div>
                            </td>
                        </tr>
                    </tbody>                    
                </table>            
            </form>  
            <p class="kleverlist-response-mailchimp-audience klever-list-data-generate-text"></p>
            <!--- Mailchimp Audience Select Form End -->
        <?php endif;?>

        <?php if (!empty(get_option('kleverlist_aweber_account_lists_data'))) : ?>
            <!--- AWeber Account Lists Select Form Start -->
            <form method="POST" id="kleverlist_aweber_account_list_settings">
                <table>
                    <tbody>                
                        <tr>                    
                            <td class="klever-list-data-dropdown">
                                <label for="aweber_account_list">
                                    <?php esc_html_e('Choose a List', 'kleverlist');?>:
                                </label>
                                <select name="aweber_account_list" class="kleverlist-load-integration-dropdown" id="aweber_account_list" required>
                                    <option value=""><?php esc_html_e('Choose a List', 'kleverlist');?></option>
                                    <?php
                                        $aweber_account_lists = get_option('kleverlist_aweber_account_lists_data');
                                        $user_account_id = get_option('kleverlist_aweber_user_selected_account_id', '');
                                        $selected = '';
                                    foreach ($aweber_account_lists as $id => $name) {
                                        $account_id = sanitize_text_field($id);
                                        $account_name = sanitize_text_field($name);
                                        $is_selected = ($account_id === $user_account_id) ? true : false;
                                        echo '<option value="' . esc_attr($account_id) . '" ' . selected($is_selected, true, false) . '>' . esc_html($account_name) . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="kleverlist-position klever-list-data-generate">
                                <?php
                                    $other_attributes = array( 'id' => 'aweber_account_btn' );
                                    submit_button(__('Load the Lists', 'kleverlist'), 'secondary', '', true, $other_attributes);
                                ?>                                           
                                <div id="aweber_list_loader" class="kleverlist-loader-outer-div hidden"></div>
                            </td>
                        </tr>
                    </tbody>                    
                </table>            
            </form>  
            <p class="kleverlist-response-aweber-list kleverlist-data-generate-text"></p>
            <!--- AWeber Account Lists Select Form End -->
        <?php endif;?>        
    </div>
</div>
