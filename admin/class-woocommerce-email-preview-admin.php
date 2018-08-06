<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.strawberrysoup.co.uk
 * @since      1.0.0
 *
 * @package    Woocommerce_Email_Preview
 * @subpackage Woocommerce_Email_Preview/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Email_Preview
 * @subpackage Woocommerce_Email_Preview/admin
 * @author     Keith Light <keith.light@strawberrysoup.co.uk>
 */
class Woocommerce_Email_Preview_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('init', [$this , 'ss_woocommerce_load_email_preview']);
        add_action('woocommerce_email_header', [$this, 'ss_woocommerce_email_header'], 10, 2);
        add_action('woocommerce_email_before_order_table', [$this, 'ss_woocommerce_email_header'], 10, 2);
        add_action('woocommerce_email_footer', [$this, 'ss_woocommerce_email_footer'], 10, 2);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Email_Preview_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Email_Preview_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-email-preview-admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Email_Preview_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Email_Preview_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-email-preview-admin.js', ['jquery'], $this->version, false);
    }


    public function ss_woocommerce_load_email_preview()
    {
        $preview = plugin_dir_path(__FILE__)  . 'woo-preview-emails.php';
        
        if (file_exists($preview)) {
            require $preview;
        }

        return false;
    }

    public function ss_woocommerce_email_header()
    {
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (strpos($url, 'admin-ajax.php') !== false):
            ?>
            <style>
                <?php wc_get_template( 'emails/email-styles.php'); ?>
                .template-selector {
                    background: #333;
                    color: white;
                    text-align: center;
                    padding: 2rem 1rem;
                    font-weight: 400;
                    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
                    margin: 0;
                }

                .template-selector .template-row,
                .template-selector .order-row {
                    display: block;
                    margin: .75rem 0;
                }

                .template-selector span {
                    display: inline-block;
                    margin: 0 1rem;
                }

                .template-selector select,
                .template-selector input {
                    background: #e3e3e3;
                    font-family: 'Lato', sans-serif;
                    color: #333;
                    padding: 0.5rem 1rem;
                    border: 0px;
                }

                @media screen and (min-width: 1100px) {
                    .template-selector .template-row,
                    .template-selector .order-row {
                        display: inline-block;
                    }
                }
            </style>
        <?php endif;
    }

    public function ss_woocommerce_email_footer()
    {
        $url = "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        if (strpos($url, 'admin-ajax.php') !== false):
            ?>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
            <script language="javascript">
                function getUrlVars() {
                    var vars = [], hash;
                    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
                    for (var i = 0; i < hashes.length; i++) {
                        hash = hashes[i].split('=');
                        vars.push(hash[0]);
                        vars[hash[0]] = hash[1];
                    }
                    return vars;
                }

                var order = getUrlVars()["order"];
                var file = getUrlVars()["file"];
                jQuery('form input#order').val(decodeURI(order));
                jQuery('select#email-select').val(decodeURI(file));
            </script>
        <?php endif;
    }
}
