<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Retrieve audience lists
$audience = null;
$audience_ids = get_option('kleverlist_mailchimp_audience_lists');
if (!empty($audience_ids)) {
    $audience = reset($audience_ids);
}

if (empty(get_option('kleverlist_mailchimp_user_audience'))) {
    return;
}
       
// Retrieve privacy consent settings
$privacy_consent_toggle = null;
$privacy_consent_input_text = null;
$privacy_consent = get_option('kleverlist_mailchimp_global_checkout_privacy_consent', '');
   
if (!empty($privacy_consent)) {
    $privacy_consent_toggle = $privacy_consent['kleverlist_mailchimp_global_checkout_privacy_toggle'];
    $privacy_consent_input_text = $privacy_consent['kleverlist_mailchimp_global_checkout_privacy_input_text'];
}

// Default privacy consent text
$privacy_consent_text = !is_null($privacy_consent_input_text)
    ? sanitize_text_field($privacy_consent_input_text)
    : __('I consent to have my email address collected for marketing purposes', 'kleverlist');
?>
<div class="klever-list-settings-main">
    <form method="post" id="kleverlist_mailchimp_global_settings">        
        <div class="kleverlist-mailchimp-integration-section">
            <table class="form-table kleverlist-choose-lists">
                <tbody class="klever-list-data-settings-page">
                    <tr>
                        <th class="klever-list-data-heading-mapping"><?php esc_html_e('Audience', 'kleverlist');?>:</th>
                        <td>
                            <div class="kleverlist-mailchimp-global-audience-input">
                                <input type="text" name="kleverlist_mailchimp_global_audience" class="kleverlist_mailchimp_global_audience" id="kleverlist_mailchimp_global_audience" disabled value="<?php echo esc_attr($audience);?>">
                            </div>                              
                        </td>
                    </tr>

                    <tr>
                        <th></th>
                        <td>
                            <div>
                                <p><?php esc_html_e('This is the Audience you have selected when setting up the plugin. You can choose for a different Audience in “Integrations”.', 'kleverlist');?></p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="klever-list-data-settings-field-page">
            <table class="form-table width-900">
                <tbody class="kleverlist-free-option"> 
                    <tr>
                        <th><?php esc_html_e('Resubscribe', 'kleverlist');?></th>
                        <td>          
                            <div class="kleverlist-container">
                                <label class="kleverlist-switch" for="kleverlist_mailchimp_user_resubscribe">
                                    <input type="checkbox" name="kleverlist_mailchimp_user_resubscribe" class="kleverlist-global-checkbox kleverlist-resubscribe-toggle" data-target-input-class=".kleverlist-mailchimp-global-resubscribe-input" id="kleverlist_mailchimp_user_resubscribe" <?php checked('1' === get_option('kleverlist_mailchimp_global_resubscribe'));?> value="1" />
                                    <div class="kleverlist-slider kleverlist-round"></div>
                                </label>
                            </div>                          
                                                        
                            <p class="kleverlist-data">
                                <?php
                                printf(
                                    esc_html__('If %1$senabled%2$s, resubscribe a previously unsubscribed user in the audience.', 'kleverlist'),
                                    '<strong>',
                                    '</strong>'
                                );
                                ?>
                            </p>   

                            <div class="kleverlist-radio-options kleverlist-mailchimp-global-resubscribe-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_resubscribe') ) ? 'show-input': 'hide-input'?>">
                                <label for="kleverlist_mailchimp_global_resubscribe_order_action1">
                                    <input type="radio" id="kleverlist_mailchimp_global_resubscribe_order_action1" name="kleverlist_mailchimp_global_resubscribe_order_action" value="kleverlist_mailchimp_global_resubscribe_order_on_processing" <?php checked('kleverlist_mailchimp_global_resubscribe_order_on_processing' === get_option('kleverlist_mailchimp_global_resubscribe_order_action_option', ''));?> >
                                    <?php esc_html_e('Order Processing', 'kleverlist');?>
                                </label>

                                <label for="kleverlist_mailchimp_global_resubscribe_order_action2">
                                    <input type="radio" id="kleverlist_mailchimp_global_resubscribe_order_action2" name="kleverlist_mailchimp_global_resubscribe_order_action" value="kleverlist_mailchimp_global_resubscribe_order_on_complete" <?php checked('kleverlist_mailchimp_global_resubscribe_order_on_processing' !== get_option('kleverlist_mailchimp_global_resubscribe_order_action_option', ''));?> >
                                    <?php esc_html_e('Order Complete', 'kleverlist');?>
                                </label>
                            </div>  

                            <p class="kleverlist-data kleverlist-mailchimp-global-resubscribe-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_resubscribe') ) ? 'show-input': 'hide-input'?> ">
                                <?php esc_html_e('Select the order status for your contact to be added back to your list.', 'kleverlist');?>
                            </p>                           
                        </td>
                    </tr>    
                    
                    <tr>
                        <th><?php esc_html_e('1-Click Activation', 'kleverlist');?></th>
                        <td>          
                            <div class="kleverlist-container">
                                <label class="kleverlist-switch" for="klerverlist_mailchimp_active_all_products">
                                    <input type="checkbox" name="klerverlist_mailchimp_active_all_products" id="klerverlist_mailchimp_active_all_products" <?php checked('1' === get_option('kleverlist_mailchimp_global_active_all_products'))?> class="kleverlist-global-checkbox kleverlist-active-all-toggle" data-target-input-class=".kleverlist-global-active-all-input" value="1" />
                                    <div class="kleverlist-slider kleverlist-round"></div>
                                </label>
                            </div>  
                            <p class="kleverlist-data">                                
                                <?php
                                printf(
                                    esc_html__('If %1$senabled%2$s the integration will be active on all products by default, with the Default List associated. If %1$sdisabled%2$s, each product must be manually assigned to a specific list.', 'kleverlist'),
                                    '<strong>',
                                    '</strong>'
                                );
                                ?>
                            </p> 
                            <div class="kleverlist-radio-options kleverlist-global-active-all-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_active_all_products')) ? 'show-input': 'hide-input'?>">
                                <label for="kleverlist_mailchimp_global_active_all_order_processing_action">
                                    <input type="radio" id="kleverlist_mailchimp_global_active_all_order_processing_action" name="kleverlist_mailchimp_global_active_all_order_action" <?php checked(empty(get_option('kleverlist_mailchimp_global_active_all_order_action')) || 'order_processing' === get_option('kleverlist_mailchimp_global_active_all_order_action'));?> value="order_processing">
                                    <?php esc_html_e('Order Processing', 'kleverlist');?>
                                </label>

                                <label for="kleverlist_mailchimp_global_active_all_order_complete_action">
                                    <input type="radio" id="kleverlist_mailchimp_global_active_all_order_complete_action" name="kleverlist_mailchimp_global_active_all_order_action" <?php checked('order_completed' === get_option('kleverlist_mailchimp_global_active_all_order_action'));?> value="order_completed">
                                    <?php esc_html_e('Order Complete', 'kleverlist');?>
                                </label>
                            </div>   
                            <p class="kleverlist-data kleverlist-global-active-all-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_active_all_products')) ? 'show-input': 'hide-input'?>">
                                <?php esc_html_e('Select the order status for which this setting will be applied.', 'kleverlist');?>
                            </p> 
                        </td>
                    </tr>  
                </tbody>

                <!---New Featured-->
                <?php /*?>
                <tbody class="kleverlist-free-option">
                    <tr>
                        <th><?php esc_html_e('Activity & Insights', 'kleverlist');?></th>
                        <td>
                            <div class="kleverlist-container">
                                <label class="kleverlist-switch" for="kleverlist_mailchimp_global_activity_insights">
                                    <input type="checkbox" name="kleverlist_mailchimp_global_activity_insights" class="kleverlist-global-checkbox kleverlist-activity-insights-toggle" data-target-input-class=".kleverlist-mailchimp-global-activity-insights-input" id="kleverlist_mailchimp_global_activity_insights" <?php checked('1' === get_option('kleverlist_mailchimp_global_activity_insights'));?> value="1" />
                                    <div class="kleverlist-slider kleverlist-round"></div>
                                </label>
                            </div>

                            <p class="kleverlist-data">
                                <?php
                                printf(
                                    esc_html__('If %1$senabled%2$s,  KleverList will send order history and revenue in the audience.', 'kleverlist'),
                                    '<strong>',
                                    '</strong>'
                                );
                                ?>
                            </p>

                            <div class="kleverlist-radio-options kleverlist-mailchimp-global-activity-insights-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_activity_insights') ) ? 'show-input': 'hide-input'?>">
                                <label for="kleverlist_mailchimp_global_activity_insights_order_process_action">
                                    <input type="radio" id="kleverlist_mailchimp_global_activity_insights_order_process_action" name="kleverlist_mailchimp_global_activity_insights_order_action" value="kleverlist_mailchimp_global_activity_insights_order_on_processing" <?php checked('kleverlist_mailchimp_global_activity_insights_order_on_processing' === get_option('kleverlist_mailchimp_global_activity_insights_order_action_option', ''));?> >
                                    <?php esc_html_e('Order Processing', 'kleverlist');?>
                                </label>

                                <label for="kleverlist_mailchimp_global_activity_insights_order_complete_action">
                                    <input type="radio" id="kleverlist_mailchimp_global_activity_insights_order_complete_action" name="kleverlist_mailchimp_global_activity_insights_order_action" value="kleverlist_mailchimp_global_activity_insights_order_on_complete" <?php checked('kleverlist_mailchimp_global_activity_insights_order_on_processing' !== get_option('kleverlist_mailchimp_global_activity_insights_order_action_option', ''));?> >
                                    <?php esc_html_e('Order Complete', 'kleverlist');?>
                                </label>
                            </div>

                            <p class="kleverlist-data kleverlist-mailchimp-global-activity-insights-input <?php echo ( '1' === get_option('kleverlist_mailchimp_global_resubscribe') ) ? 'show-input': 'hide-input'?> ">
                                <?php esc_html_e('Select the order status in which the information will be sent.', 'kleverlist');?>
                            </p>
                        </td>
                    </tr>
                </tbody>
                <?php */?>
                <!---New Featured-->
                
                <tbody class="kleverlist-premium-option <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                    <tr>
                        <th><?php esc_html_e('Privacy Consent', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="">
                                </div>   
                            <?php endif; ?> 
                        </th>
                        <td class="kleverlist-global-help-info">   
                            <div class="kleverlist-container">
                                <label class="kleverlist-switch" for="klerverlist_mailchimp_privacy_consent">
                                    <input type="checkbox" name="klerverlist_mailchimp_privacy_consent" id="klerverlist_mailchimp_privacy_consent" <?php checked('1' === $privacy_consent_toggle && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> class="kleverlist-global-checkbox" value="1" />
                                    <div class="kleverlist-slider kleverlist-round"></div>
                                </label>
                                <div class="kleverlist-mailchimp-global-privacy-input <?php echo ( esc_attr($privacy_consent_toggle) === '1' && !is_null($privacy_consent_input_text) && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium' ) ? 'show-input': 'hide-input'?>">
                                    <input type="text" name="kleverlist_mailchimp_global_privacy_input" id="kleverlist_mailchimp_global_privacy_input" value="<?php echo esc_attr($privacy_consent_text);?>">
                                </div>
                            </div>                          
                             
                            <p class="kleverlist-data">
                            <?php
                                printf(
                                    esc_html__('If %1$senabled%2$s, the privacy consent will be activated. Read the ? icon for further instructions.', 'kleverlist'),
                                    '<strong>',
                                    '</strong>'
                                );
                                ?>
                            </p>  
                                                  
                            <div class="kleverlist-radio-options kleverlist-mailchimp-global-privacy-input <?php echo ( esc_attr($privacy_consent_toggle) === '1' && !is_null($privacy_consent_input_text) && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium' ) ? 'show-input': 'hide-input'?>">
                                <label for="kleverlist_mailchimp_global_option1">
                                    <input type="radio" id="kleverlist_mailchimp_global_option1" name="kleverlist_mailchimp_global_privacy_radio" <?php checked('yes' === get_option('kleverlist_mailchimp_global_privacy_radio_option', ''));?> value="yes">
                                    <?php esc_html_e('Checked by default', 'kleverlist');?>
                                </label>

                                <label for="kleverlist_mailchimp_global_option2">
                                    <input type="radio" id="kleverlist_mailchimp_global_option2" name="kleverlist_mailchimp_global_privacy_radio" value="no" <?php checked('yes' !== get_option('kleverlist_mailchimp_global_privacy_radio_option', ''));?> >
                                    <?php esc_html_e('Unchecked by default', 'kleverlist');?>
                                </label>
                            </div>
                            
                            <p class="kleverlist-data kleverlist-mailchimp-global-privacy-input <?php echo ( esc_attr($privacy_consent_toggle) === '1' && !is_null($privacy_consent_input_text) && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium' ) ? 'show-input': 'hide-input'?>">
                                <?php esc_html_e('Customize the appearance of the opt-in checkbox for privacy consent on the checkout page.', 'kleverlist');?>
                            </p>  

                            <div class="kleverlist-tooltip kleverlist-tooltip-box">
                                <span class="dashicons dashicons-editor-help"></span>
                                <span class="kleverlist-tooltiptext">
                                <?php
                                    printf(
                                        esc_html__('If %1$senabled%2$s a privacy consent checkbox will be shown in the WooCommerce checkout page. Users will be added into the lists only upon explicit consent.', 'kleverlist'),
                                        '<strong>',
                                        '</strong>'
                                    );
                                    ?>
                                </span>
                            </div>            
                        </td>
                    </tr>                                       
                    
                    <tr>
                        <th><?php esc_html_e('Migration', 'kleverlist');?>
                            <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                <div class="pro-featured-icon"> 
                                    <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                </div>   
                            <?php endif; ?> 
                        </th>
                        <td class="kleverlist-global-help-info">   
                            <div class="kleverlist-container">
                                <label class="kleverlist-switch" for="klerverlist_mailchimp_migration_allow">
                                    <input type="checkbox" name="klerverlist_mailchimp_migration_allow" id="klerverlist_mailchimp_migration_allow" <?php checked('1' === get_option('klerverlist_mailchimp_migration_allow') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> class="kleverlist-global-checkbox" value="1" />
                                    <div class="kleverlist-slider kleverlist-round"></div>
                                </label>
                            </div>                          
                             
                            <p class="kleverlist-data">
                                <?php esc_html_e('Looking to migrate your existing WooCommerce customers into your Mailchimp audience? Our Migration feature makes it effortless. Please note, this feature is intended for one-time use only, for example to import your existing customer base into a new Mailchimp audience.', 'kleverlist');?>
                            </p>  
                            
                            <div class="kleverlist-tooltip kleverlist-tooltip-box">
                                <span class="dashicons dashicons-editor-help"></span>
                                <span class="kleverlist-tooltiptext">
                                    <?php
                                    printf(
                                        esc_html__('If %1$senabled%2$s the Migration feature will be activated and a link to the page will appear in the left menu bar.', 'kleverlist'),
                                        '<strong>',
                                        '</strong>'
                                    );
                                    ?>
                                </span>
                            </div>            
                        </td>
                    </tr> 
                </tbody>
            </table>

            <table class="form-table width-900">
                <tbody>
                    <tr>
                        <th></th>
                        <td class="kleverlist-position">
                            <?php
                                $button_attributes = array( 'id' => 'kleverlist_global_mailchimp_button' );
                                submit_button(__('Save Changes', 'kleverlist'), 'primary', '', true, $button_attributes);
                            ?>
                            <div id="global_loader" class="kleverlist-loader-outer-div hidden"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
    <p class="kleverlist-gloabal-response"></p>
</div>  
