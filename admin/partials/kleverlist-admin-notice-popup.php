<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
    
if (KLEVERLIST_PLUGIN_PLAN === 'kleverlist-free') :
    ?>
    <div class="kleverlist-premium-popup-wrapper overlay-kleverlist" id="kleverlist-notice-popup" style="display:none;">
        <div class="kleverlist-premium-popup-inner-wrapper">
            <div class="kleverlist-premium-popup-image-popup">
                <img src="<?php echo esc_url(KLEVERLIST_PLUGIN_ADMIN_DIR_URL . 'images/warning-sign.png'); ?>" alt="warning-sign">
            </div>
            <div class="kleverlist-premium-popup-content">
                <h2><?php esc_html_e('Unlock the Power of KleverList Pro!', 'kleverlist');?></h2>
                <p>
                <?php
                    printf(
                        __('Ready to supercharge your email marketing strategy? Upgrade to %s today, available for download on our website and gain access to a world of advanced features designed to take your WooCommerce store to new heights. Boost customer engagement, drive higher conversions, and say goodbye to limitations with the full potential of KleverList Pro at your fingertips.'),
                        '<a href="https://kleverlist.com/pricing/" target="_blank" rel="noreferrer ugc nofollow">KleverList Pro</a>'
                    );
                ?>     
                </p>
                <div class="kleverlist-premium-btn">
                    <a href="javascript:void(0)"><?php esc_html_e('Close', 'kleverlist');?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
