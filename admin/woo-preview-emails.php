<?php
function ss_preview_woo_emails()
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
            'plain'
        ];

        $list = array_diff($files, $exclude);
        $woocommerce_orders = new WP_Query([
            'post_type' => 'shop_order',
            'posts_per_page' => 10,
            'order' => 'ASC',
            'post_status' => ['wc-completed']
        ]);

        $order_drop_down_array = [];

        if ($woocommerce_orders->have_posts()) {
            while ($woocommerce_orders->have_posts()) {
                $woocommerce_orders->the_post();
                $order_drop_down_array[get_the_ID()] = get_the_title();
            }
        }
        ?>
        <form class="template-selector" method="get" action="<?php echo site_url(); ?>/wp-admin/admin-ajax.php">
            <div class="template-row">
                <input id="setorder" type="hidden" name="order" value="">
                <input type="hidden" name="action" value="previewemail">
                <span class="choose-email">Choose your email template: </span>
                <select name="file" id="email-select">
                    <?php
                    foreach ($list as $item): ?>
                        <option value="<?php echo $item; ?>"><?php echo str_replace('.php', '', $item); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="order-row">
                <span class="choose-order">Choose an order number: </span>
                <select id="order" name="order">
                    <?php foreach ($order_drop_down_array as $order_id => $order_name): ?>
                        <option value="<?php echo $order_id; ?>" <?php selected(((isset($_GET['order'])) ? $_GET['order'] : key($order_drop_down_array)), $order_id); ?>><?php echo $order_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" value="Go">
        </form>
        <?php
        global $order, $billing_email;
        reset($order_drop_down_array);
        $order_number = isset($_GET['order']) ? $_GET['order'] : key($order_drop_down_array);
        $order = new WC_Order($order_number);
        $emails = new WC_Emails();
        $email_heading = get_woocommerce_email_heading($emails->emails, $_GET['file'], $order_number);
        $user_id = !empty($order->get_customer_id()) ? $order->get_customer_id() : $order->post->post_author;
        $user_details = get_user_by('id', $user_id);

        if (in_array($_GET['file'], array('email-customer-details.php', 'email-order-details.php'))) {
            wc_get_template('emails/email-header.php', array(
                'order' => $order,
                'email_heading' => $email_heading
            ));
        }

        do_action('woocommerce_email_before_order_table', $order, false, false);

        wc_get_template('emails/' . $_GET['file'], [
            'order' => $order,
            'email_heading' => $email_heading,
            'sent_to_admin' => false,
            'plain_text' => false,
            'email' => $user_details->user_email,
            'user_login' => $user_details->user_login,
            'blogname' => get_bloginfo('name'),
            'customer_note' => $order->customer_note,
            'partial_refund' => ''
        ]);
    }
    wp_die();
}

add_filter('woocommerce_email_settings', 'add_preview_email_links');

function add_preview_email_links($settings)
{
    $updated_settings = [];
    foreach ($settings as $section) {
        if (isset($section['id']) && 'email_recipient_options' == $section['id'] && isset($section['type']) && 'sectionend' == $section['type']) {
            $updated_settings[] = [
                'title' => __('Preview Email Templates', 'previewemail'),
                'type' => 'title',
                'desc' => __('<a href="' . site_url() . '/wp-admin/admin-ajax.php?action=previewemail&file=customer-new-account.php" target="_blank">Preview email templates</a>.', 'previewemail'),
                'id' => 'email_preview_links'
            ];
        }
        $updated_settings[] = $section;
    }
    return $updated_settings;
}

add_action('wp_ajax_previewemail', 'ss_preview_woo_emails');

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
            $message = str_replace('{order_number}', '#' . $order_number, $email_class->get_default_heading());
            $message = str_replace('{site_title}', get_bloginfo('name'), $message);

            return $message;
        }
    }

    return false;
}