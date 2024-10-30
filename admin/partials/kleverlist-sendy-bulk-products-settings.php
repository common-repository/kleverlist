<?php
if (! defined('ABSPATH')) {
    exit;
}

$sendy_lists = get_option('kleverlist_sendy_lists', '');
if (empty($sendy_lists)) {
    return;
}

?>
<div class="kleverlist-bulk-list-product-filters">
    <div class="kleverlist-sendy-bulk-response"></div>
    <div class="kleverlist-sendy-bulk-lists-input">
        <div class="kleverlist-option-bulk-log-section">
            <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
        </div>
        <select id="kleverlist_sendy_bulk_choosen_list" name="kleverlist_sendy_bulk_choosen_list">
            <option value=""><?php esc_html_e('Choose The List', 'kleverlist');?></option>
            <?php
            if (!empty($sendy_lists) && count((array) $sendy_lists['sendy_api_lists']) > 0) {
                foreach ($sendy_lists['sendy_api_lists'] as $key => $list) {
                    echo '<option value="' . esc_attr($list->id) . '">' . esc_html($list->name) . '</option>';
                }
            }
            ?>
        </select>
        
        <div class="kleverlist-sendy-bulk-list-checkbox-selection">
            <label for="kleverlist_sendy_bulk_list_order_processing_checkbox">
                <strong><?php esc_html_e('Order Processing', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_sendy_bulk_list_order_processing_checkbox" name="kleverlist_sendy_bulk_list_order_processing_checkbox"> 

            <label for="kleverlist_sendy_bulk_list_order_completed_checkbox">
                <strong><?php esc_html_e('Order Completed', 'kleverlist');?>:</strong>
            </label>
            <input type="checkbox" class="kleverlist-bulk-list-checkbox" id="kleverlist_sendy_bulk_list_order_completed_checkbox" name="kleverlist_sendy_bulk_list_order_completed_checkbox"> 
        </div>

        <div class="kleverlist-sendy-bulk-list-radio-selection">
            <label for="kleverlist_sendy_bulk_list_subscribe_radio">
                <strong><?php esc_html_e('Subscribe', 'kleverlist');?>:</strong>
                <input type="radio" class="kleverlist-bulk-list-radio" id="kleverlist_sendy_bulk_list_subscribe_radio" name="kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio" value="subscribe" >
            </label>

            <label for="kleverlist_sendy_bulk_list_unsubscribe_radio">
                <strong><?php esc_html_e('Unsubscribe', 'kleverlist');?>:</strong>
                <input type="radio" class="kleverlist-bulk-list-radio" id="kleverlist_sendy_bulk_list_unsubscribe_radio" name="kleverlist_sendy_bulk_list_subscribe_unsubscribe_radio" value="unsubscribe"> 
            </label>
        </div>

        <input type="button" name="kleverlist_sendy_bulk_list_apply_filters" class="button kleverlist-sendy-bulk-list-apply" value="<?php esc_html_e('Apply', 'kleverlist');?>">
    </div>
</div>
