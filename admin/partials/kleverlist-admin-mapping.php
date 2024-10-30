<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
    
$title = null;
$description = null;
$doc_link = null;
$settings = get_option('kleverlist_service_settings');

if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_MAILCHIMP) {
    $title = __('Global Settings - Mapping for Mailchimp', 'kleverlist');
    $description = __('On this page, you can choose which fields will be synchronized between WooCommerce and Mailchimp. Mapping Fields are specific pieces of information related to your contacts. They are used to store demographic information or other static details about your contacts. Choose which Mapping Fields you want to synchronize from WooCommerce to your Mailchimp Audience.', 'kleverlist');
    $doc_link = 'https://kleverlist.com/docs/config/mapping-mailchimp/';
} elseif (!empty($settings)) {
    if ($settings['service_type'] === KLEVERLIST_SERVICE_SENDY) {
        $title = __('Global Settings - Mapping for Sendy Integration', 'kleverlist');
        $description = __('On this page, you can choose which fields will be synchronized between WooCommerce and Sendy. Mapping Fields are specific pieces of information related to your contacts. They are used to store demographic information or other static details about your contacts. Choose which Mapping Fields you want to synchronize from WooCommerce to your Sendy list(s).', 'kleverlist');
        $doc_link = 'https://kleverlist.com/docs/config/mapping-sendy/';
    }
} elseif (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER) {
    $title = __('Global Settings - Mapping for AWeber', 'kleverlist');
    $description = __('On this page, you can choose which fields will be synchronized between WooCommerce and AWeber. Mapping Fields are specific pieces of information related to your contacts. They are used to store demographic information or other static details about your contacts. Choose which Mapping Fields you want to synchronize from WooCommerce to your AWeber List.', 'kleverlist');
    $doc_link = 'https://kleverlist.com/docs/config/mapping-aweber/';
} else {
    $title = __('Global Settings - Mapping', 'kleverlist');
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
                    ), admin_url('admin.php')));?>"><?php esc_html_e('Global Settings', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-mapping',
                    ), admin_url('admin.php')));?>" class="active"><?php esc_html_e('Mapping', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-tags',
                    ), admin_url('admin.php')));?>"><?php esc_html_e('Tags', 'kleverlist');?></a>
                    </li>
                </ul>
                <div class="kleverlist-settings-top-description">
                    <p class="kleverlist-page-main-description"><?php echo esc_html($description);?>
                        <?php if (!empty($doc_link)) :?>
                            <a target="_blank" href="<?php echo esc_url($doc_link);?>"><?php esc_html_e('Link to the Documentation', 'kleverlist');?></a>
                        <?php endif;?>
                    </p>
                </div>
            </div>
        </div>
        <?php
        $user_audience = get_option('kleverlist_mailchimp_user_audience');
        $sendy_lists = get_option('kleverlist_sendy_lists');
        $account_id = get_option('kleverlist_aweber_user_selected_account_id');
        if (empty($user_audience) && empty($sendy_lists) && empty($account_id)) :
            ?>
            <div class="postbox kleverlist-postbox">
                <span>
                <?php
                    $admin_url = add_query_arg(array(
                        'page' => 'kleverlist-integrations',
                    ), admin_url('admin.php'));

                    printf(
                        esc_html__('%1$s %2$s', 'kleverlist'),
                        esc_html__('Please Configure API and then generate list from Integrations tab.', 'kleverlist'),
                        sprintf(
                            '<a href="%s">%s</a>',
                            esc_url($admin_url),
                            esc_html__('Go to Integrations', 'kleverlist')
                        )
                    );
                ?>     
                </span>
            </div>
        <?php endif;?>
        <?php
        if (!empty($settings)) {
            if ($settings['service_type'] === KLEVERLIST_SERVICE_SENDY) {
                include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-sendy-mapping.php';
            }
        }
                            
        if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_MAILCHIMP) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-mailchimp-mapping.php';
        }

        if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-aweber-mapping.php';
        }
        ?>
    </div>
    <!--New Code-->
</div>
<?php
if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') {
    include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
}
?>
