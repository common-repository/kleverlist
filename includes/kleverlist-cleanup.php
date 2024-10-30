<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// If uninstall not called from WordPress, then exit.
function kleverlist_cleanup()
{
    global $wpdb, $wp_version;
    // Delete options.
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'kleverlist\_%' OR option_name LIKE 'mapping_user\_%';");

    // Delete postmeta.
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_special\_%' OR meta_key LIKE '_unsubscribe\_%' OR meta_key LIKE '_order\_%' OR meta_key LIKE '_kleverlist\_%';");
}
add_action('fs_uninstall_cleanup', 'kleverlist_cleanup');
