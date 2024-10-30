<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (get_option('kleverlist_service_type') !== KLEVERLIST_SERVICE_MAILCHIMP &&
        get_option('kleverlist_mailchimp_user_audience')
    ) {
    return;
}
?>
<div class="kleverlist-bulk-list-product-filters">
    <div class="kleverlist-mailchimp-bulk-response"></div>
    <div class="kleverlist-mailchimp-bulk-lists-input">
        <div class="kleverlist-option-bulk-log-section">
            <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
        </div>
        <select id="kleverlist_mailchimp_bulk_choosen_audience" name="kleverlist_mailchimp_bulk_choosen_audience">
            <option value=""><?php esc_html_e('Choose an Audience', 'kleverlist');?></option>
            <?php
            $mailchimp_audience = get_option('kleverlist_mailchimp_audience_lists');
            if (is_array($mailchimp_audience) && !empty($mailchimp_audience)) {
                foreach ($mailchimp_audience as $key => $audience) {
                    echo '<option value="' . esc_attr($key) . '">' . esc_html($audience) . '</option>';
                }
            }
            ?>
        </select>
        
        <div class="kleverlist-mailchimp-bulk-list-checkbox-selection">
            <label for="kleverlist_mailchimp_bulk_list_order_processing_checkbox">
                <strong><?php esc_html_e('Order Processing', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_mailchimp_bulk_list_order_processing_checkbox" name="kleverlist_mailchimp_bulk_list_order_processing_checkbox"> 

            <label for="kleverlist_mailchimp_bulk_list_order_completed_checkbox">
                <strong><?php esc_html_e('Order Completed', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_mailchimp_bulk_list_order_completed_checkbox" name="kleverlist_mailchimp_bulk_list_order_completed_checkbox"> 
        </div>

        <input type="button" name="kleverlist_mailchimp_bulk_list_apply_filters" class="button kleverlist-mailchimp-bulk-list-apply" value="<?php esc_html_e('Apply', 'kleverlist'); ?>">    
    </div>
</div>
