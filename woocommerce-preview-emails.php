<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.strawberrysoup.co.uk
 * @since             1.0.0
 * @package           Woocommerce_Email_Preview
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Preview Emails
 * Description:       Allows previews for Woocommerce email templates.
 * Version:           1.0.11
 * Author:            Keith Light | Strawberrysoup
 * Author URI:        https://www.strawberrysoup.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-preview-emails
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 3.4.5
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Required functions
if (!function_exists('woothemes_queue_update')) {
    require_once(dirname(__FILE__) . '/woo-includes/woo-functions.php');
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('WOOCOMMERCE_EMAIL_PREVIEW_VERSION', '1.0.11');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-preview-emails-activator.php
 */
function activate_woocommerce_email_preview()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-preview-emails-activator.php';
    Woocommerce_Email_Preview_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-preview-emails-deactivator.php
 */
function deactivate_woocommerce_email_preview()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-preview-emails-deactivator.php';
    Woocommerce_Email_Preview_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_email_preview');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_email_preview');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woocommerce-preview-emails.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_email_preview()
{
    $plugin = new Woocommerce_Email_Preview();
    $plugin->run();
}

run_woocommerce_email_preview();

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/keefyhub/woocommerce-preview-emails/',
    __FILE__,
    'woocommerce-preview-emails'
);

$myUpdateChecker->setBranch('master');