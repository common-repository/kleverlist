<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://kleverlist.com/
 * @since             1.0.0
 * @package           Kleverlist
 *
 * @wordpress-plugin
 * Plugin Name:       KleverList
 * Plugin URI:        https://kleverlist.com/
 * Description:       A powerful and user-friendly WordPress plugin to integrate your WooCommerce store with Sendy, AWeber or Mailchimp, and unlock the true potential of customer segmentation.
 * Version:           2.4.2
 * Author:            KleverPlugins
 * Author URI:        https://kleverplugins.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kleverlist
 * Domain Path:       /languages
  *
  */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (!function_exists('kleverlist_activate')) {

    include dirname(__FILE__) . '/kleverlist-load.php';

}