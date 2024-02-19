<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function is_woocommerce_active() {
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}

function check_required_plugins_on_activation() {
    if (!is_woocommerce_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('This plugin requires WooCommerce to be activated. Please activate WooCommerce and try again.', 'duplicate-products-report'));
    }
}
register_activation_hook(__FILE__, 'check_required_plugins_on_activation');
