<?php
function mb_product_filter_shortcode()
{
    ob_start();

    // Check if it's a 'mb-category' category page
    if (is_tax('mb-category')) {

        $userLocationMetaKey = isset($_COOKIE['mb_shipping_store']) ? $_COOKIE['mb_shipping_store'] : "T2G 4C2";

        $post_id = get_posts(array(
            'numberposts' => 1,
            'meta_key' => 'zip_code',
            'meta_value' => $userLocationMetaKey,
            'meta_compare' => '=',
            'post_type' => 'location',
            'fields' => 'ids'
        ))[0];

        $locationId = get_post_meta($post_id, "location_id", true);
        $userSelectedStoreId = "store_" . $locationId;

        // Get all product categories assigned to products in the current 'mb-category'
        $category = get_queried_object();
        $category_id = $category->term_id;
        $args = array(
            'posts_per_page' => -1,  // Set to -1 to retrieve all posts
            'post_type'      => 'product',
            'meta_query'     => array(
                'relation' => 'AND', // Both conditions should be true
                array(
                    'key'     => $userSelectedStoreId,
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'NUMERIC',
                ),
                array(
                    'key'     => 'status',
                    'value'   => 1,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'mb-category',
                    'field'    => 'id',
                    'terms'    => $category_id,
                    'operator' => 'IN',
                ),
            ),
        );

        $products = get_posts($args);

        $product_ids = wp_list_pluck($products, 'ID');
		
		$terms = wp_get_object_terms($product_ids,'filter');

		$filter_names = wp_list_pluck($terms, 'name');


        // If there are matching 'filter' categories, display the filter form
        if (!empty($filter_names)) {
?>
            <form id="product-filter-form" action="" method="GET">
                <div class="mb-filter-wrap">
                    <ul class="product-categories">
                        <?php
                        foreach ($filter_names as $category) {
                        ?>
                            <li class="mb-parrent-cat" id="mb-parrent-cat-<?php echo esc_html($category->slug); ?>">
                                <?php echo esc_html($category->name); ?>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>

                    <p>
                        <input type="submit" value="Filter Products">
                        <input type="reset" value="Reset Filter">
                    </p>
                </div>
            </form>
<?php
        }
    }

    // Get the output buffer contents and clean the buffer
    $output = ob_get_clean();
    return $output;
}

// Register the shortcode
add_shortcode('mb_product_filter', 'mb_product_filter_shortcode');

// Filter products based on selected categories
function product_filter_function($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (is_tax('mb-category') && isset($_GET['product_category'])) {
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'filter',
                'field'    => 'slug',
                'terms'    => $_GET['product_category'],
                'operator' => 'IN',
            ),
        ));
    }
}
add_action('pre_get_posts', 'product_filter_function');
?>