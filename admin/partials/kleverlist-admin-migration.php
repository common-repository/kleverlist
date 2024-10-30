<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}
if ( !defined( 'KLEVERLIST_PLUGIN_DIR' ) ) {
    die;
}
?>
<div class="wrap kleverlist-migration-settings-page kleverlist-setting-page">
    <div id="kleverlist_migration_settings_content" class="kleverlist-migration-settings-content">
        <div class="kleverlist-main-div-integrate-icon">
            <div class="kleverlist-icon-list">
                <img src="<?php 
echo esc_url( KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png' );
?>" alt="integration">
            </div>
            <h1 class="kleverlist_mapping_heading">
                <?php 
esc_html_e( 'Migrate your WooCommerce customers', 'kleverlist' );
?>
            </h1>  

            <?php 
if ( get_option( 'klerverlist_sendy_migration_allow' ) === '1' ) {
    ?>
                <p class="kleverlist-page-main-description">
                    <?php 
    esc_html_e( "On this page you can effortlessly export your pre-existing WooCommerce customers that were present before the plugin installation, and reimport them inside your selected Sendy list. Our intuitive syncing feature ensures that customer billing fields are seamlessly matched and transferred based on your preferences set in the 'Mapping' section of the plugin. Therefore, make sure to configure that section first. Please note, this feature is intended for one-time use only, for example to import the existing customer list into a new Sendy instance.", "kleverlist" );
    ?>
                </p>
            <?php 
}
?>

            <?php 
if ( get_option( 'klerverlist_mailchimp_migration_allow' ) === '1' ) {
    ?>
                <p class="kleverlist-page-main-description"><?php 
    esc_html_e( "On this page you can effortlessly export your pre-existing WooCommerce customers that were present before the plugin installation, and reimport them inside your Mailchimp audience. Our intuitive syncing feature ensures that customer billing fields are seamlessly matched and transferred based on your preferences set in the 'Mapping' section of the plugin. Therefore, make sure to configure that section first. Please note, this feature is intended for one-time use only, for example to import the existing customer list into a new Mailchimp audience.", "kleverlist" );
    ?></p>
            <?php 
}
?>
        </div>
        
        <?php 
?>

        <?php 
?>

        <?php 
?>
    </div>
</div>

<?php 
if ( KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free' ) {
    include KLEVERLIST_ROOT_DIR_ADMIN . '/partials/kleverlist-admin-notice-popup.php';
}