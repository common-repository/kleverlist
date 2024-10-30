<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (get_option('kleverlist_service_type') !== KLEVERLIST_SERVICE_SENDY) {
    return;
}
    
$title = null;
$description = null;
$settings = get_option('kleverlist_service_settings');
if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_SENDY) {
    $title = esc_html__('Global Settings - Tags Management for Sendy', 'kleverlist');
    $description = esc_html__('On this page, you can choose which tags will be synchronized between WooCommerce and Sendy. Tags are labels you assign to your contacts to categorize them based on certain characteristics or interactions. Unlike fields, tags are more flexible and can be added or removed as needed. Tags are used to track and segment contacts based on their behavior, interests, or engagement with your brand.', 'kleverlist');
}
?>
<div class="kleverlist-mapping-page kleverlist-setting-page kleverlist-setting-top-area">
    <!--New Code-->
    <div id="kleverlist_mapping_settings_content" class="kleverlist-mapping-content">
        <div class="kleverlist-main-div-integrate-icon">
            <div class="kleverlist-settings-top-section">
                <div class="kleverlist-settings-logo-section">
                    <div class="kleverlist-icon-list">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
                    </div>
                    <h1 class="kleverlist_mapping_heading"><?php echo esc_html($title);?></h1>  
                </div>            
                <ul class="kleverlist-admin-menu-tabs">
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-global-settings',
                    ), admin_url('admin.php')));?>" ><?php esc_html_e('Global Settings', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-mapping',
                    ), admin_url('admin.php')));?>"><?php esc_html_e('Mapping', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-tags',
                    ), admin_url('admin.php')));?>" class="active"><?php esc_html_e('Tags', 'kleverlist');?></a>
                    </li>
                </ul>
                <div class="kleverlist-settings-top-description">
                    <p class="kleverlist-page-main-description"><?php echo esc_html($description);?>
                        <a href="https://kleverlist.com/docs/config/tags-sendy/" target="_blank">
                            <?php esc_html_e('Link to the Documentation', 'kleverlist'); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <div class="klever-list-settings-main">
            <form method="post" id="kleverlist_sendy_tags_settings">  
                <div class="kleverlist-tag-section">   
                    <table class="form-table width-900 ">
                        <div class="kleverlist-mapping-page-heading">
                            <h2><?php esc_html_e('Behavioral Tags', 'kleverlist');?></h2>
                            <p><?php esc_html_e('This segment is based on customer behavior and interaction with your brand.', 'kleverlist');?></p>
                        </div>                      
                    </table> 
                    <div class="klever-list-data-settings-field-page">    
                        <table class="form-table width-900">                       
                            <tbody class="kleverlist-free-option">
                                <tr>
                                    <th></th>
                                    <td class="klever-list-data-mappinng-page-heading">
                                        <div>                                    
                                            <h4><?php esc_html_e('Choose whether to enable / disable the default tags. Read the ? icon for further instructions.', 'kleverlist');?></h4>
                                        </div>
                                    </td>
                                </tr>  
                            </tbody>
                            <tbody class="kleverlist-free-option">                     
                                <tr>
                                    <th><?php esc_html_e('Order Processing', 'kleverlist');?></th>
                                    <td>          
                                        <div class="kleverlist-container">
                                            <label class="kleverlist-switch" for="kleverlist_sendy_order_processing_tag">
                                                <input type="checkbox" name="kleverlist_sendy_order_processing_tag" class="kleverlist-mapping-checkbox" id="kleverlist_sendy_order_processing_tag" <?php checked('1' === get_option('kleverlist_sendy_order_processing_tag'));?> value="1" />
                                                <div class="kleverlist-slider kleverlist-round"></div>
                                            </label>
                                        </div>
                                        <div class="kleverlist-tooltip kleverlist-tooltip-box">
                                            <span class="dashicons dashicons-editor-help"></span>
                                            <span class="kleverlist-tooltiptext">
                                                <?php esc_html_e('“tags” custom field must be manually created in Sendy in advance before to activate the toggle.', 'kleverlist');?>
                                            </span>
                                        </div>        
                                        <p class="kleverlist-maapping-subheading">
                                        <?php
                                            printf(
                                                esc_html__('If %1$senabled%2$s, a tag named “order processing” will be added to the customer contact on order processing status, only for the product(s) in which the integration is active.', 'kleverlist'),
                                                '<strong>',
                                                '</strong>'
                                            );
                                            ?>
                                        </p>       
                                    </td>
                                </tr>
                            </tbody>
                            <tbody class="kleverlist-free-option">
                                <tr>
                                    <th><?php esc_html_e('Order Completed', 'kleverlist');?></th>
                                    <td>          
                                        <div class="kleverlist-container">
                                            <label class="kleverlist-switch" for="kleverlist_sendy_order_completed_tag">
                                                <input type="checkbox" name="kleverlist_sendy_order_completed_tag" class="kleverlist-mapping-checkbox" id="kleverlist_sendy_order_completed_tag" <?php checked('1' === get_option('kleverlist_sendy_order_completed_tag'));?> value="1" />
                                                <div class="kleverlist-slider kleverlist-round"></div>
                                            </label>
                                        </div>
                                        <div class="kleverlist-tooltip kleverlist-tooltip-box">
                                            <span class="dashicons dashicons-editor-help"></span>
                                            <span class="kleverlist-tooltiptext">
                                                <?php esc_html_e('“tags” custom field must be manually created in Sendy in advance before to activate the toggle.', 'kleverlist');?>
                                            </span>
                                        </div>        
                                        <p class="kleverlist-maapping-subheading">
                                            <?php
                                            printf(
                                                esc_html__('If %1$senabled%2$s, a tag named “order complete” will be added to the customer contact on order complete status, only for the product(s) in which the integration is active.', 'kleverlist'),
                                                '<strong>',
                                                '</strong>'
                                            );
                                            ?>      
                                        </p> 

                                        <div class="kleverlist-sendy-remove-order-processing-tag-section <?php echo ( esc_attr(get_option('kleverlist_sendy_order_completed_tag')) === '1') ? 'show-input': 'hide-input'?>">
                                            <div class="kleverlist-free-option">
                                                <div class="kleverlist-toggle-options kleverlist-sendy-remove-order-processing-tag-input">
                                                    <div class="kleverlist-sendy-pro-input-block">
                                                        <label for="kleverlist_sendy_remove_order_processing_tag">
                                                            <input type="checkbox" id="kleverlist_sendy_remove_order_processing_tag" name="kleverlist_sendy_remove_order_processing_tag" <?php checked('1' === get_option('kleverlist_sendy_remove_order_processing_tag', ''));?> value="yes">
                                                        <span><strong><?php esc_html_e('Remove Order Processing Tag', 'kleverlist');?></strong></span>
                                                        </label>
                                                    </div>
                                                </div>      
                                                <p class="kleverlist-data kleverlist-sendy-toggle-text">
                                                <?php esc_html_e('If selected, Tag created on Order Processing will be deleted, if present, when order status changes to “Order Complete”.', 'kleverlist');?>
                                                </p>  
                                            </div>
                                        </div>
                                    </td>
                                </tr>                        
                            </tbody>  
                            <tbody class="kleverlist-premium-option <?php echo esc_attr(KLEVERLIST_PLUGIN_CLASS)?>">
                                <tr>
                                    <th></th>
                                    <td class="klever-list-data-mappinng-page-heading">
                                        <div>                                    
                                            <h4><?php esc_html_e('Choose whether to enable / disable product tags synchronization. Read the ? icon for further instructions.', 'kleverlist');?></h4>
                                        </div>
                                    </td>
                                </tr> 
                                <tr>
                                    <th><?php esc_html_e('Product Tags', 'kleverlist');?>
                                        <?php if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') : ?>
                                            <div class="pro-featured-icon"> 
                                                <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/pro_featured.png'); ?>" alt="pro_featured">
                                            </div>   
                                        <?php endif; ?> 
                                    </th>
                                    <td class="kleverlist-global-help-info">   
                                        <div class="kleverlist-container">
                                            <label class="kleverlist-switch" for="klerverlist_sendy_product_tag_allow">
                                                <input type="checkbox" name="klerverlist_sendy_product_tag_allow" id="klerverlist_sendy_product_tag_allow" <?php checked('1' === get_option('klerverlist_sendy_product_tag_allow') && KLEVERLIST_PLUGIN_PLAN === 'kleverlist-primium');?> class="kleverlist-global-checkbox" value="1" />
                                                <div class="kleverlist-slider kleverlist-round"></div>
                                            </label>
                                        </div>                          
                                         
                                        <div class="kleverlist-tooltip kleverlist-tooltip-box">
                                            <span class="dashicons dashicons-editor-help"></span>
                                            <span class="kleverlist-tooltiptext">
                                                <?php esc_html_e('“tags” custom field must be manually created in Sendy in advance before to activate the toggle.', 'kleverlist');?>
                                            </span>
                                        </div>        
                                        <p class="kleverlist-maapping-subheading">
                                            <?php
                                            printf(
                                                esc_html__('If %1$senabled%2$s, tags associated to a product will be added to the customer contact, only for the product(s) in which the integration is active. Product Tags can be created on “Products → Tags” page.', 'kleverlist'),
                                                '<strong>',
                                                '</strong>'
                                            );
                                            ?>      
                                        </p>            
                                    </td>
                                </tr> 
                            </tbody>                          
                        </table>  
                    
                        <table class="form-table width-900">
                            <tbody class="kleverlist-data-mapping-bg kleverlist-margin">
                                <tr>
                                    <th></th>
                                    <td class="kleverlist-position button-mapping">
                                        <?php
                                            $button_attributes = array( 'id' => 'kleverlist_sendy_tag_settings_save' );
                                            submit_button(__('Save Changes', 'kleverlist'), 'primary', '', true, $button_attributes);
                                        ?>
                                        <div class="kleverlist-loader-outer-div">
                                            <div id="loader" class="hidden"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
            <p class="kleverlist-response"></p>
        </div>
    </div>
</div>
<?php
if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') {
    include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
}
?>

