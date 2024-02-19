<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Функция обработки страницы отчета о дубликатах продуктов
function process_duplicate_products() {
	// include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

    class Duplicate_Products_Table extends WP_List_Table {
        private $tab;

        function __construct($tab){
            $this->tab = $tab;
            parent::__construct( array(
                'singular'  => 'product',
                'plural'    => 'products',
                'ajax'      => false
            ));
        }

        function column_default( $item, $column_name ){
            switch( $column_name ) { 
                case 'post_title':
                case 'sku':
                case 'edit':
                    return $item[ $column_name ];
                default:
                    return print_r( $item, true ); 
            }
        }

        function get_columns(){
            $columns = array(
                'post_title'    => __('Product Name','duplicate-products-report'),
                'sku'           => __('SKU','duplicate-products-report'),
                'edit'          => __('Edit','duplicate-products-report')
            );
            return $columns;
        }

        function prepare_items() {
            global $wpdb;
            $query = '';
            if ($this->tab == 'title') {
                $query = "
                    SELECT post.ID, post.post_title, postmeta.meta_value AS sku
                    FROM {$wpdb->posts} AS post
                    JOIN {$wpdb->postmeta} AS postmeta ON post.ID = postmeta.post_id
                    WHERE post.post_type = 'product' 
                    AND postmeta.meta_key = '_sku' 
                    AND post.post_status = 'publish'
                    AND post.ID IN (
                        SELECT p1.ID
                        FROM {$wpdb->posts} AS p1
                        JOIN (
                            SELECT post_title
                            FROM {$wpdb->posts}
                            WHERE post_type = 'product'
                            AND post_title IS NOT NULL
                            AND post_status = 'publish'
                            GROUP BY post_title
                            HAVING COUNT(*) > 1
                        ) AS p2 ON p1.post_title = p2.post_title
                    )
                    ORDER BY post.post_title ASC
                ";
            } elseif ($this->tab == 'sku') {
                $query = "
                    SELECT post.ID, post.post_title, postmeta.meta_value AS sku
                    FROM {$wpdb->posts} AS post
                    JOIN {$wpdb->postmeta} AS postmeta ON post.ID = postmeta.post_id
                    WHERE post.post_type = 'product' 
                    AND postmeta.meta_key = '_sku' 
                    AND post.post_status = 'publish'
                    GROUP BY postmeta.meta_value
                    HAVING COUNT(*) > 1
                ";
            }

            $duplicate_products = $wpdb->get_results($query, ARRAY_A);
            $data = array();
            $count = 1;
            foreach ($duplicate_products as $product) {
                $product_id = $product['ID'];
                $edit_link = get_edit_post_link($product_id);
                $data[] = array(
                    'post_title'    => '<a href="' . get_permalink($product_id) . '" target="_blank">' . $product['post_title'] . '</a>',
                    'sku'           => $product['sku'],
                    'edit'          => '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-edit"></span> '.__('Edit Product','duplicate-products-report').'</a>'
                );
                $count++;
            }
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $data;
        }
    }

    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'title'; ?>

    <div class="wrap">
        <h1 class="wp-heading-inline"><?php _e('Duplicate Products Report','duplicate-products-report'); ?></h1>
        <div class="nav-tab-wrapper">
            <a class="nav-tab<?php echo ($active_tab === 'title' || !isset($active_tab)) ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url(menu_page_url('duplicate-products', false)); ?>">
                <?php _e('Duplicates by Name','duplicate-products-report'); ?>

            </a>
            <a class="nav-tab<?php echo ($active_tab === 'sku') ? ' nav-tab-active' : ''; ?>" href="<?php echo esc_url(menu_page_url('duplicate-products', false)); ?>&tab=sku">
                <?php _e('Duplicates by SKU','duplicate-products-report'); ?>
            </a>
        </div>

        <?php 

        $duplicate_products_table = new Duplicate_Products_Table($active_tab);
        $duplicate_products_table->prepare_items();
        ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                            <?php $duplicate_products_table->display(); ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>
<?php } ?>