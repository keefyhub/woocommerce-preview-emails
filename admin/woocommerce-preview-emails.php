<?php
function woocommerce_preview_emails()
{
    if (is_admin()) {
        $default_path = WC()->plugin_path() . '/templates/';
        $files = scandir($default_path . 'emails');
        $exclude = [
            '.',
            '..',
            'email-header.php',
            'email-footer.php',
            'email-styles.php',
            'email-order-items.php',
            'email-addresses.php',
            'email-customer-details.php',
            'email-downloads.php',
            'email-order-details.php',
            'email-mobile-messaging.php',
            'plain'
        ];

        $list = array_diff($files, $exclude);
        $order_drop_down_array = [];
        $orders = wc_get_orders(array('numberposts' => -1));

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $order_drop_down_array[$order->get_id()] = $order->get_id();
            }
        }
?>
        <form class="template-selector" method="get" action="<?= site_url(); ?>/wp-admin/admin-ajax.php">
            <div class="template-row">
                <input type="hidden" name="action" value="woocommerce_preview_emails">
                <span class="choose-email">Choose email template: </span>
                <select name="file" id="email-select">
                    <?php
                    foreach ($list as $item) : ?>
                        <option value="<?= $item; ?>">
                            <?= str_replace('.php', '', $item); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="order-row">
                <span class="choose-order">Choose order number: </span>
                <select id="order" name="order">
                    <?php foreach ($order_drop_down_array as $order_id => $order_name) : ?>
                        <option value="<?= $order_id; ?>" <?php selected(((isset($_GET['order'])) ? $_GET['order'] : key($order_drop_down_array)), $order_id); ?>>
                            <?= $order_name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" value="View">
        </form>
<?php
        global $order, $billing_email, $additional_content;
        reset($order_drop_down_array);
        $order_number = isset($_GET['order']) ? $_GET['order'] : key($order_drop_down_array);
        $order = new WC_Order($order_number);
        $emails = new WC_Emails();
        $email_heading = get_woocommerce_email_heading($emails->emails, $_GET['file'], $order_number);
        $user_id = !empty($order->get_customer_id()) ? (int)$order->get_customer_id() : (int)wp_get_current_user()->ID;
        $user_details = get_user_by('id', $user_id);

        // do_action('woocommerce_email_before_order_table', $order, false, false);

        wc_get_template('emails/' . $_GET['file'], [
            'order' => $order,
            'email_heading' => $email_heading,
            'sent_to_admin' => false,
            'plain_text' => false,
            'email' => $user_details->user_email,
            'user_login' => $user_details->user_login,
            'blogname' => get_bloginfo('name'),
            'customer_note' => $order->get_customer_note(),
            'partial_refund' => '',
            'additional_content' => $additional_content,
            'password_generated' => false,
            'reset_key' => get_password_reset_key($user_details),
            'user_id' => $user_id
        ]);
    }
    wp_die();
}

add_action('wp_ajax_woocommerce_preview_emails', 'woocommerce_preview_emails');

function add_preview_email_links($settings)
{
    $updated_settings = [];
    foreach ($settings as $section) {
        if (isset($section['id']) && 'email_recipient_options' == $section['id'] && isset($section['type']) && 'sectionend' == $section['type']) {
            $updated_settings[] = [
                'title' => __('Preview Email Templates', 'woocommerce_preview_emails'),
                'type' => 'title',
                'desc' => __('<a href="' . site_url() . '/wp-admin/admin-ajax.php?action=woocommerce_preview_emails&file=customer-new-account.php" target="_blank">Preview email templates</a>.', 'woocommerce_preview_emails'),
                'id' => 'email_preview_links'
            ];
        }
        $updated_settings[] = $section;
    }
    return $updated_settings;
}

add_filter('woocommerce_email_settings', 'add_preview_email_links');

function get_woocommerce_email_heading($emails_array, $template_name, $order_number)
{
    if (!$emails_array || !$template_name) {
        return false;
    }
    $template_name = str_replace('.php', '', str_replace('-', '_', $template_name));
    $template_name = str_replace('admin_', '', $template_name);

    foreach ($emails_array as $email_class => $email) {
        if ($email->id == $template_name) {
            $email_class = new $email_class();

            // `{order_number}` was replaced by `#{order_number}` in version 3.5 so no need to add the `#`
            if (wcpe_woocommerce_version_check()) {
                $heading = $email_class->get_option('heading', $email_class->get_default_heading());
                $message = str_replace('{order_number}', $order_number, $heading);
            } else {
                $heading = $email_class->get_option('heading', $email_class->get_default_heading());
                $message = str_replace('{order_number}', '#' . $order_number, $heading);
            }

            $message = str_replace('{site_title}', get_bloginfo('name'), $message);

            return $message;
        }
    }

    return false;
}
