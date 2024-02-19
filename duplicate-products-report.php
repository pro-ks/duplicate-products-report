<?php
/*
Plugin Name: Duplicate Products Report
Description: Generates a report on duplicate products by name and article.
Version: 1.0
Author: KoSteam
Author URI: https://t.me/koSteams
Plugin URI: https://github.com/pro-ks/duplicate-products-report
*/

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


function load_duplicate_products_textdomain() {
    load_plugin_textdomain( 'duplicate-products-report', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_duplicate_products_textdomain' );


