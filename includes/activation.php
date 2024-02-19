<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function is_woocommerce_active() {
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}

// Notification for inactive WooCommerce
function woocommerce_not_activated_notice() {
    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('WooCommerce is not activated', 'duplicate-products-report') . '</p></div>';
}
