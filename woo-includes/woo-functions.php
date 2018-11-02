<?php
/**
 * WC Detection
 */
function wcpe_is_woocommerce_active()
{
    $active_plugins = (array)get_option('active_plugins', []);
    return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

/**
 * WC version check
 */
function wcpe_woocommerce_version_check($version = '3.5')
{
    if (wcpe_is_woocommerce_active()) {
        $installed_version = get_option('woocommerce_version');
        if (version_compare($installed_version, $version, ">=")) {
            return true;
        }
    }
    return false;
}
