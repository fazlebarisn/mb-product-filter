<?php
// Shortcode to display the filter form
function mb_product_filter_shortcode()
{
    ob_start();

    // Check if it's a 'mb-category' category page
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
    // $userSelectedStoreId = "store_" . $locationId;

    $category = get_queried_object();
    $category_id = absint($category->term_id);

    if (is_shop()) {
        // Code for the shop page
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'store_10',
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
        );
    } else {
        // Code for other pages
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => 'store_10',
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
    }

    $products = get_posts($args);

    // Check if the query was successful before proceeding
    if (!empty($products)) {
        $terms = wp_get_object_terms(wp_list_pluck($products, 'ID'), 'filter');

        // If there are matching 'filter' categories, display the filter form
?>
        <form id="mb-product-filter-form" action="" method="GET">
            <div class="mb-filter-wrap">
                <h2 class="mb-filter-title">Filter</h2>
                <div class="mb-filter-body">
                    <ul class="product-categories">
                        <?php
                        foreach ($terms as $category) {
                            $children = get_term_children($category->term_id, 'filter');

                            // Display main category only if it has children
                            if (!empty($children)) {
                        ?>
                                <li class="mb-parrent-cat" id="mb-parrent-cat-<?php echo esc_html($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                    <div class="toggle-container">
                                        <span class="toggle-arrow">▼</span>
                                    </div>
                                </li>
                                <div class="mb-child-category-<?php echo esc_html($category->slug); ?>">
                                    <div class="mb-list-group-item">
                                        <ul class="product-subcategories">
                                            <?php
                                            foreach ($children as $child_id) {
                                                $child = get_term($child_id, 'filter');
                                            ?>
                                                <li>
                                                    <input type="checkbox" name="mb_filter[]" value="<?php echo esc_attr($child->slug); ?>" <?php echo (isset($_GET['mb_filter']) && in_array($child->slug, $_GET['mb_filter'])) ? 'checked' : ''; ?>>
                                                    <?php echo esc_html($child->name); ?>
                                                </li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <p>
                    <input type="submit" class="mb-filter-submit" value="Filter Products">
                    <!-- <input type="reset" value="Reset Filter"> -->
                </p>
            </div>
        </form>
<?php
    }

    // Get the output buffer contents and clean the buffer
    $output = ob_get_clean();
    return $output;
}

// Register the shortcode
add_shortcode('mb_product_filter', 'mb_product_filter_shortcode');


function mb_product_filter_handler()
{
    // Retrieve the form data
    parse_str($_GET['formData'], $form_data);

    // Define default args
    $args = array(
        'posts_per_page' => 12,
        'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        'post_type'      => 'product',
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => 'store_10',
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
                'terms'    => $form_data['mb_category_id'],
                'operator' => 'IN',
            ),
        ),
    );

    // Merge the default args with the form data
    $args = array_merge($args, $form_data);

    // Query posts
    $products = new WP_Query($args);

    // Your loop to display posts goes here
    if ($products->have_posts()) {

        do_action('woocommerce_before_shop_loop');

        woocommerce_product_loop_start();

        if (wc_get_loop_prop('total')) {

            while ($products->have_posts()) {
                $products->the_post();
                do_action('woocommerce_shop_loop');
                wc_get_template_part('content', 'product');
            }
        }

        woocommerce_product_loop_end();
        wp_reset_postdata();

        /**
         * Hook: woocommerce_after_shop_loop.
         *
         * @hooked woocommerce_pagination - 10
         */
        do_action('woocommerce_after_shop_loop');
        // Pagination
        if ($products->max_num_pages > 1) {
            $current_page = max(1, get_query_var('paged'));

            echo '<div class="mb-pagination">';
            echo paginate_links(array(
                'total'      => $products->max_num_pages,
                'current'    => $current_page,
                'prev_text'  => '&laquo;',
                'next_text'  => '&raquo;',
            ));
            echo '</div>';
        }
    } else {
        do_action('woocommerce_no_products_found');
    }

    // Reset post data
    wp_reset_postdata();

    // Send a response (you can customize this based on your needs)
    wp_send_json_success('Form submitted successfully!');
}
add_action('wp_ajax_mb_product_filter_handler', 'mb_product_filter_handler');
add_action('wp_ajax_nopriv_mb_product_filter_handler', 'mb_product_filter_handler'); // If you want to handle non-logged-in users
