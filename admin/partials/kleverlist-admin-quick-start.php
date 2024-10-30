<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
    
$links = [
    'integration' => add_query_arg(array( 'page' => 'kleverlist-integrations' ), admin_url('admin.php')),
    'mapping' => add_query_arg(array( 'page' => 'kleverlist-mapping' ), admin_url('admin.php')),
    'gobal-settings' => add_query_arg(array( 'page' => 'kleverlist-global-settings' ), admin_url('admin.php')),
    'wc-products' => add_query_arg(array( 'post_type' => 'product' ), admin_url('edit.php')),
    'kleverlist-docs' => 'https://kleverlist.com/docs/',
];
?>
<div class="wrap kleverlist-quick-start-page kleverlist-setting-page">
    <div class="kleverlist-quick-start-content">
        <div class="kleverlist-main-div-integrate-icon">
            <div class="kleverlist-icon-list">
                <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/integration-icon.png'); ?>" alt="integration">
            </div>
            <h1 class="kleverlist_quick_start_heading">
                <?php esc_html_e('Quick Start', 'kleverlist');?>
            </h1>
            <p class="kleverlist-page-main-description">
                <?php
                    esc_html_e("Welcome to KleverList, the powerful plugin designed to streamline your email marketing efforts and enhance customer segmentation. With KleverList, you can seamlessly integrate your WooCommerce store with your preferred email marketing platform, allowing you to effectively manage and target your customers. This Quick Start page will walk you through the essential steps to get up and running with KleverList in no time.", "kleverlist");
                ?>
                <strong><?php esc_html_e("Let's dive in!", "kleverlist");?></strong>
            </p>
        </div>
    </div>

    <div id="kleverlist-quick-start" class="wrap kleverlist-qs-admin-wrap">  
        <!--Video-->
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed kleverlist-quick-start-video-section">
            <div class="kleverlist-qs-admin-column-80">
                <div class="kleverlist-quick-guide-video">
                    <iframe src="https://www.youtube.com/embed/17oWynOhCao?si=9owC5ke4V3QnOHZw" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                </div>
            </div>
        </div>  
        <!--Video-->

        <!--Integrations-->
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed">
            <div class="kleverlist-qs-admin-column-80">                
                <h2>
                    <span class="kleverlist-qs-emoji qs-emoji-integration">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/step1.png'); ?>" alt="step1">
                    </span>                    
                    <?php esc_html_e('Step 1: Integration', 'kleverlist');?>
                </h2>
                <ol>
                    <li><?php esc_html_e('Go to the KleverList plugin "Integrations" tab in your WordPress admin panel.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Choose your desired email marketing platform and fill the required information to successfully complete the integration.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Save the settings to connect KleverList with your email marketing platform.', 'kleverlist');?></li>
                </ol>
                <a href="<?php echo esc_url($links['integration']);?>" class="kleverlist-qs-admin-section-post-link">
                    <?php esc_html_e('Go to Integrations', 'kleverlist'); ?>
                    <span class="dashicons dashicons-admin-links"></span>
                </a>
            </div>
        </div>
        <!--Integrations-->

        <!--Global Settings-->        
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed">
            <div class="kleverlist-qs-admin-column-80">
                <h2>
                    <span class="kleverlist-qs-emoji qs-emoji-global-setting">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/step3.png'); ?>" alt="step3">
                    </span>
                    <?php esc_html_e('Step 2: Global Settings', 'kleverlist');?>
                </h2>
                <ol>
                    <li><?php esc_html_e('Access the "Settings" tab in the KleverList plugin settings.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Depending on your integration, select the default list / audience for new subscribers.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Enable or Keep disabled the resubscribe option to allow previously unsubscribed users to be resubscribed.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Enable the plugin to sync all products to the email marketing provider, with 1-Click Activation for seamless integration.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Customize the privacy consent settings to ensure compliance with GDPR and other privacy regulations (Pro Version).', 'kleverlist');?></li>
                    <li><?php esc_html_e('Enable the Migration feature if you need to migrate your existing WooCommerce customer base in your email marketing platform (Pro Version).', 'kleverlist');?></li>
                    <li><?php esc_html_e('Save the settings to apply the global configurations.', 'kleverlist');?></li>
                </ol>
                <a href="<?php echo esc_url($links['gobal-settings']);?>" class="kleverlist-qs-admin-section-post-link">
                    <?php esc_html_e('Go to Settings', 'kleverlist'); ?>
                    <span class="dashicons dashicons-admin-links"></span>
                </a>
            </div>
        </div>
        <!--Global Settings-->

        <!--Mapping-->        
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed">
            <div class="kleverlist-qs-admin-column-80">
                <h2>
                    <span class="kleverlist-qs-emoji qs-emoji-mapping">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/step2.png'); ?>" alt="step2">
                    </span>
                    <?php esc_html_e('Step 3: Mapping and Tagging', 'kleverlist');?>
                </h2>
                <ol>
                    <li><?php esc_html_e('Navigate to the "Mapping" tab in the KleverList plugin settings.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Configure demographics mapping to synchronize essential fields from your customer to your email marketing platform.', 'kleverlist');?></li>
                    <li><?php esc_html_e('For the other mapping fields (Pro Version) , customize the mapping of additional WooCommerce billing and user fields to achieve optimal segmentation.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Navigate to the "Tags" tab in the KleverList plugin settings and follow the instructions to configure the behavioral tagging options.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Save the settings to apply mapping and tagging configurations.', 'kleverlist');?></li>
                </ol>
                <a href="<?php echo esc_url($links['mapping']);?>" class="kleverlist-qs-admin-section-post-link">
                    <?php esc_html_e('Go to Mapping', 'kleverlist'); ?>
                    <span class="dashicons dashicons-admin-links"></span>
                </a>
            </div>
        </div>
        <!--Mapping-->

        <!--WC Product Assign-->        
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed">
            <div class="kleverlist-qs-admin-column-80">
                <h2>                    
                    <span class="kleverlist-qs-emoji qs-emoji-list-assign">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/step4.png'); ?>" alt="step4">
                    </span>
                    <?php esc_html_e('Step 4: List Assignation in WooCommerce Product Detail', 'kleverlist');?>                    
                </h2>
                <ol>
                    <li><?php esc_html_e('Open a product in your WooCommerce store for editing.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Navigate to the "KleverList" tab in the product detail section.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Depending on your integration, assign the product to a specific list / audience / tags, or unsubscribe from a list (Pro Version) based on different order status.', 'kleverlist');?></li>
                    <li><?php esc_html_e('Save the changes to apply the list assignation for the product.', 'kleverlist');?></li>                  
                    <li><?php esc_html_e('Alternatively, you can navigate to “Products → All Products” tab on WooCommerce, and bulk add products to specific lists / audience, based on different order status.', 'kleverlist');?></li>                  
                </ol>
                <a href="<?php echo esc_url($links['wc-products']);?>" class="kleverlist-qs-admin-section-post-link">
                    <?php esc_html_e('Go to WooCommerce Products', 'kleverlist'); ?>
                    <span class="dashicons dashicons-admin-links"></span>
                </a>
            </div>
        </div>
        <!--WC Product Assign-->

        <!--Final Step-->
        <div class="kleverlist-admin-qs-section kleverlist-admin-qs-section-squashed">
            <div class="kleverlist-qs-admin-column-80">
                <h2>
                    <span class="kleverlist-qs-emoji qs-emoji-congrats">
                        <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/congrats.png'); ?>" alt="congrats">
                    </span>
                    <?php esc_html_e('Congratulations!', 'kleverlist');?>
                </h2>
                <p><?php esc_html_e('You have completed the quick start process for KleverList. Your WooCommerce store is now synchronized with your email marketing platform, allowing for targeted communication and improved customer segmentation. To read more in-depth instructions on how to integrate and configure KleverList, have a look at our documentation.', 'kleverlist');?></p>
                <a href="<?php echo esc_url($links['kleverlist-docs']);?>" target="_blank" rel="noopener noreferrer" class="kleverlist-qs-admin-section-post-link">
                    <?php esc_html_e('Documentation', 'kleverlist'); ?>                    
                    <span class="dashicons dashicons-external"></span>
                </a>
            </div>
        </div>
        <!--Final Step-->
    </div>
</div>
