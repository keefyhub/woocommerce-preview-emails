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
 * Plugin Name:       Woocommerce Email Preview
 * Description:       Allows previews for Woocommerce email templates.
 * Version:           1.0.6
 * Author:            Keith Light | Strawberrysoup
 * Author URI:        https://www.strawberrysoup.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-email-preview
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

if (!is_woocommerce_active()) {
    add_action('admin_notices', function () {
        $plugin_data = get_plugin_data(__FILE__);
        $plugin_name = $plugin_data['Name'];
        echo '<div class="error"><p>';
        esc_html_e(sprintf('%s is inactive because WooCommerce is not installed.', $plugin_name));
        echo '</p></div>';
    });

    return;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define('WOOCOMMERCE_EMAIL_PREVIEW_VERSION', '1.0.6');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-email-preview-activator.php
 */
function activate_woocommerce_email_preview()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-email-preview-activator.php';
    Woocommerce_Email_Preview_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-email-preview-deactivator.php
 */
function deactivate_woocommerce_email_preview()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-email-preview-deactivator.php';
    Woocommerce_Email_Preview_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_email_preview');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_email_preview');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woocommerce-email-preview.php';

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