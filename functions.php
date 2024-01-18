<?php
// Shortcode to display the filter form
function mb_product_filter_shortcode()
{
    ob_start();

    // Check if it's a 'mb-category' category page
    // $userLocationMetaKey = isset($_COOKIE['mb_shipping_store']) ? $_COOKIE['mb_shipping_store'] : "T2G 4C2";

    // $post_id = get_posts(array(
    //     'numberposts' => 1,
    //     'meta_key' => 'zip_code',
    //     'meta_value' => $userLocationMetaKey,
    //     'meta_compare' => '=',
    //     'post_type' => 'location',
    //     'fields' => 'ids'
    // ))[0];

    // $locationId = get_post_meta($post_id, "location_id", true);
    // $userSelectedStoreId = "store_" . $locationId;

    $category = get_queried_object();
    $category_id = absint($category->term_id);
    
    if (is_shop()) {
        // Code for the shop page
        $args = array(
            'posts_per_page' => 1000,
            'post_type'      => 'product',
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 1,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'filter',
                    'operator' => 'EXISTS',
                ),
            ),
        );
    } else {
        // Code for other pages
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'meta_query'     => array(
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
    if ( !empty( $products ) ) {
        $terms = wp_get_object_terms(wp_list_pluck($products, 'ID'), 'filter');
    
        // If there are matching 'filter' categories, display the filter form
        ?>
        <form id="mb-product-filter-form" action="" method="GET">
            <div class="mb-filter-wrap">
                <h2 class="mb-filter-title">Filter</h2>
                <div class="mb-filter-body">
                    <ul class="product-categories">
                        <?php
                        foreach ( $terms as $category_term ) {
                            $children = get_term_children($category_term->term_id, 'filter');
    
                            // Display main category only if it has children
                            if ( !empty($children ) ) {
                                ?>
                                <li class="mb-parrent-cat" id="mb-parrent-cat-<?php echo esc_html($category_term->slug); ?>">
                                    <?php echo esc_html($category_term->name); ?>
                                    <div class="toggle-container">
                                        <span class="toggle-arrow">▼</span>
                                    </div>
                                </li>
                                <div class="mb-child-category-<?php echo esc_html($category_term->slug); ?>">
                                    <div class="mb-list-group-item">
                                        <ul class="product-subcategories">
                                            <?php
                                            $child_terms = get_terms('filter', array('parent' => $category_term->term_id));
                                            
                                            foreach ($child_terms as $child_term) {
                                                $child_name = $child_term->name;
                                                $child_slug = sanitize_title($child_name);
                                                // Check if there are products associated with the current child category

                                                if( is_shop() ){
                                                    $child_args = array(
                                                        'posts_per_page' => 1,
                                                        'post_type'      => 'product',
                                                        'tax_query'      => array(
                                                            array(
                                                                'taxonomy' => 'filter',
                                                                'field'    => 'id',
                                                                'terms'    => $child_term->term_id,
                                                                'operator' => 'IN',
                                                            ),
                                                        ),
                                                    );
                                                }else{
                                                    $child_args = array(
                                                        'posts_per_page' => 1,
                                                        'post_type'      => 'product',
                                                        'tax_query'      => array(
                                                            array(
                                                                'taxonomy' => 'filter',
                                                                'field'    => 'id',
                                                                'terms'    => $child_term->term_id,
                                                                'operator' => 'IN',
                                                            ),
                                                            array(
                                                                'taxonomy' => 'mb-category',
                                                                'field'    => 'id',
                                                                'terms'    => $category->term_id,
                                                                'operator' => 'IN',
                                                            ),
                                                        ),
                                                    );
                                                }
                                                
                                                $child_products = get_posts($child_args);
                                                
                                                if (!empty($child_products)) {
                                                    ?>
                                                    <li>
                                                        <input type="checkbox" name="mb_filter[]" value="<?php echo esc_attr($child_slug); ?>" <?php echo (isset($_GET['mb_filter']) && in_array($child_slug, $_GET['mb_filter'])) ? 'checked' : ''; ?>>
                                                        <?php echo esc_html($child_name); ?>
                                                    </li>
                                                    <?php
                                                }
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


// function mb_product_filter_handler() {
//     // Retrieve the form data
//     if( isset( $_GET['formData'] ) ){
//         parse_str($_GET['formData'], $form_data_array);
//         $category_id = $form_data_array['mb_category_id'];
//         $is_shop = $form_data_array['is_shop'];
//         $checkbox_values = isset($form_data_array['mb_filter']) ? $form_data_array['mb_filter'] : array();

//         // Define default args
//         if( $is_shop ){
//             $args = array(
//                 'posts_per_page' => 12,
//                 'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
//                 'post_type'      => 'product',
//                 'meta_query'     => array(
//                     array(
//                         'key'     => 'status',
//                         'value'   => 1,
//                         'compare' => '=',
//                         'type'    => 'NUMERIC',
//                     ),
//                 ),
//                 'tax_query'      => array(
//                     array(
//                         'taxonomy' => 'filter',
//                         'field'    => 'slug',
//                         'terms'    => $checkbox_values,
//                         'operator' => 'IN',
//                     ),
//                 ),
//             );
//         }else{
//             $args = array(
//                 'posts_per_page' => 12,
//                 'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
//                 'post_type'      => 'product',
//                 'meta_query'     => array(
//                     array(
//                         'key'     => 'status',
//                         'value'   => 1,
//                         'compare' => '=',
//                         'type'    => 'NUMERIC',
//                     ),
//                 ),
//                 'tax_query'      => array(
//                     array(
//                         'taxonomy' => 'mb-category',
//                         'field'    => 'id',
//                         'terms'    => $category_id,
//                         'operator' => 'IN',
//                     ),
//                     array(
//                         'taxonomy' => 'filter',
//                         'field'    => 'slug',
//                         'terms'    => $checkbox_values,
//                         'operator' => 'IN',
//                     ),
//                 ),
//             );
//         }
        

//         $products = new WP_Query($args);
//     }

//     // Your loop to display posts goes here
//     if ($products->have_posts()) {
//         do_action('woocommerce_before_shop_loop');

//         woocommerce_product_loop_start();

//         // if (wc_get_loop_prop('total')) {
//             // dd($products);
//             while ($products->have_posts()) {
//                 $products->the_post();

//                 // $stock = get_post_meta($product->get_id(), "store_10", true);
//                 // var_dump($stock);

//                 /**
//                  * Hook: woocommerce_shop_loop.
//                  */
//                 do_action('woocommerce_shop_loop');

//                 wc_get_template_part('content', 'product');
//             }
//         // }

//         woocommerce_product_loop_end();
//         wp_reset_postdata();

//         /**
//          * Hook: woocommerce_after_shop_loop.
//          *
//          * @hooked woocommerce_pagination - 10
//          */
//         do_action('woocommerce_after_shop_loop');
        
//     } else {
//         do_action('woocommerce_no_products_found');
//     }
    
//     // Reset post data
//     wp_reset_postdata();

//     wp_die();
// }
// add_action('wp_ajax_mb_product_filter_handler', 'mb_product_filter_handler');
// add_action('wp_ajax_nopriv_mb_product_filter_handler', 'mb_product_filter_handler'); // If you want to handle non-logged-in users
