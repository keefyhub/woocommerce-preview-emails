<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.strawberrysoup.co.uk
 * @since      1.0.3
 *
 * @package    Woocommerce_Email_Preview
 * @subpackage Woocommerce_Email_Preview/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.3
 * @package    Woocommerce_Email_Preview
 * @subpackage Woocommerce_Email_Preview/includes
 * @author     Keith Light <keith.light@strawberrysoup.co.uk>
 */
class Woocommerce_Email_Preview_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.3
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'woocommerce-email-preview',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );

    }
}
