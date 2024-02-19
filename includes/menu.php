<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function add_custom_menu_item_to_woocommerce() {
    if (!is_woocommerce_active()) {
        add_action('admin_notices', 'woocommerce_not_activated_notice');
        return;
    }

    add_submenu_page(
        'edit.php?post_type=product',
        __('Duplicate Products Report', 'duplicate-products-report'),
        __('Duplicate Products', 'duplicate-products-report'),
        'edit_shop_orders',
        'duplicate-products',
        'process_duplicate_products'
    );
}
add_action('admin_menu', 'add_custom_menu_item_to_woocommerce');
