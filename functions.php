<?php
// Shortcode to display the filter form
function mb_product_filter_shortcode()
{
    ob_start();

    $category = get_queried_object();
    $category_id = absint($category->term_id);
    // var_dump( is_search() );
    if (is_search()) {
        // Code for the search page
        $args = array(
            'posts_per_page' => 100,
            'post_type'      => 'product',
            'meta_query'     => array(
                array(
                    'key'     => 'status',
                    'value'   => 1,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
            // Add additional search-related conditions if needed
            's' => get_search_query(), // Include the search query in the args
        );
    } elseif (function_exists('is_shop') && is_shop()) {
        // Code for the shop page (WooCommerce)
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
                                        <span class="toggle-arrow"><i class="fa fa-angle-up"></i></span>
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

                                                if (is_search()) {
                                                    // Code for the search page
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
                                                        's' => get_search_query(),
                                                    );
                                                } elseif (function_exists('is_shop') && is_shop()) {
                                                    // Code for the shop page (WooCommerce)
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
                                                } else {
                                                    // Code for other pages
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
                                                    <input type="checkbox" name="mb_filter[]" value="<?php echo esc_attr($child_slug); ?>" data-name="<?php echo esc_attr($child_name); ?>" <?php echo (isset($_GET['mb_filter']) && in_array($child_slug, $_GET['mb_filter'])) ? 'checked' : ''; ?>>
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
                <div class="mb-filter-values">
                </div>
                <p class="pt-2 d-flex justify-content-between align-items-center">
                    <input type="submit" class="mb-filter-submit" value="Filter Products">
                    <input type="button" class="mb-reset-button" value="RESET">
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
