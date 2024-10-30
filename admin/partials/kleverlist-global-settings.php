<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="kleverlist-global-settings-page kleverlist-setting-page kleverlist-setting-top-area">
    <!--New Code-->
    <div id="kleverlist_global_settings_content" class="kleverlist-global-settings-content">
        <div class="kleverlist-main-div-integrate-icon">
            <div class="kleverlist-settings-top-section">
                <div class="kleverlist-settings-logo-section">
                    <div class="kleverlist-icon-list">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
                    </div>
                    <h1 class="kleverlist_mapping_heading"><?php esc_html_e('Global Settings', 'kleverlist');?></h1>  
                </div>            
                <ul class="kleverlist-admin-menu-tabs">
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-global-settings',
                    ), admin_url('admin.php')));?>" class="active"><?php esc_html_e('Global Settings', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-mapping',
                    ), admin_url('admin.php')));?>"><?php esc_html_e('Mapping', 'kleverlist');?></a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg(array(
                        'page' => 'kleverlist-tags',
                    ), admin_url('admin.php')));?>"><?php esc_html_e('Tags', 'kleverlist');?></a>
                    </li>
                </ul>
                <div class="kleverlist-settings-top-description">
                    <p class="kleverlist-page-main-description"><?php esc_html_e('On this page you can apply some global conditions and rules that will be applied for the integration.', 'kleverlist');?>
                        <a href="https://kleverlist.com/docs/config/settings/" target="_blank">
                            <?php esc_html_e('Link to the Documentation', 'kleverlist'); ?>
                        </a>     
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
        $settings = get_option('kleverlist_service_settings');
        if (!empty($settings)) {
            if ($settings['service_type'] === KLEVERLIST_SERVICE_SENDY) {
                include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-sendy-global-settings.php';
            }
        }

        if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_MAILCHIMP) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-mailchimp-global-settings.php';
        }

        if (get_option('kleverlist_service_type') === KLEVERLIST_SERVICE_AWEBER) {
            include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-aweber-global-settings.php';
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
