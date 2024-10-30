<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (get_option('kleverlist_service_type') !== KLEVERLIST_SERVICE_AWEBER &&
        get_option('kleverlist_aweber_tokenData')
    ) {
    return;
}
?>
<div class="kleverlist-bulk-list-product-filters">
    <div class="kleverlist-aweber-bulk-response"></div>
    <div class="kleverlist-aweber-bulk-lists-input">
        <div class="kleverlist-option-bulk-log-section">
            <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
        </div>
        <select id="kleverlist_aweber_bulk_choosen_list" name="kleverlist_aweber_bulk_choosen_list">
            <option value=""><?php esc_html_e('Choose a list', 'kleverlist');?></option>
            <?php
                $aweber_lists = get_option('kleverlist_aweber_account_lists_data');
                if (!empty($aweber_lists)) {
                    foreach ($aweber_lists as $key => $option_list) {                   
                        echo '<option value="' . esc_attr($key) . '">' . esc_html($option_list) . '</option>';
                    }
                }
            ?>
        </select>
        
        <div class="kleverlist-aweber-bulk-list-checkbox-selection">
            <label for="kleverlist_aweber_bulk_list_order_processing_checkbox">
                <strong><?php esc_html_e('Order Processing', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_aweber_bulk_list_order_processing_checkbox" name="kleverlist_aweber_bulk_list_order_processing_checkbox"> 

            <label for="kleverlist_aweber_bulk_list_order_completed_checkbox">
                <strong><?php esc_html_e('Order Completed', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_aweber_bulk_list_order_completed_checkbox" name="kleverlist_aweber_bulk_list_order_completed_checkbox"> 
        </div>

        <input type="button" name="kleverlist_aweber_bulk_list_apply_filters" class="button kleverlist-aweber-bulk-list-apply" value="<?php esc_html_e('Apply', 'kleverlist'); ?>">    
    </div>
</div>
