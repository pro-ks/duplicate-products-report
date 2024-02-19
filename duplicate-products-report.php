<?php
/**
 * Plugin Name: Duplicate Products Report
 * Description: Generates a report on duplicate products by name and article.
 * Version: 1.0.1
 * Author: KoSteam
 * Author URI: https://t.me/koSteams
 * Plugin URI: https://github.com/pro-ks/duplicate-products-report
 * 
 * Requires at least: 5.4
 * Requires PHP: 8.1
 * 
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
**/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Проверка активации WooCommerce при активации плагина
require_once plugin_dir_path(__FILE__) . 'includes/activation.php';

// Добавление элемента меню
require_once plugin_dir_path(__FILE__) . 'includes/menu.php';

// Функция обработки страницы отчета о дубликатах продуктов
require_once plugin_dir_path(__FILE__) . 'includes/report-page.php';


// Добавление ссылки на страницу настроек плагина на страницу настроек плагинов
function add_plugin_settings_link($links) {
    $settings_link = '<a href="admin.php?page=duplicate-products">'.__('Settings','duplicate-products-report').'</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'add_plugin_settings_link');

//подключаем перевод
function load_duplicate_products_textdomain() {
    load_plugin_textdomain( 'duplicate-products-report', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_duplicate_products_textdomain' );

//Проверка на наличие woocommerce
function check_required_plugins_on_activation() {
    if (!is_woocommerce_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(esc_html__('This plugin requires WooCommerce to be activated. Please activate WooCommerce and try again.', 'duplicate-products-report'));
    }
}
register_activation_hook(__FILE__, 'check_required_plugins_on_activation');