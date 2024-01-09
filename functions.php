<?php
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
    $userSelectedStoreId = "store_" . $locationId;

    if (is_shop()) {
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'meta_query'     => array(
                'relation' => 'AND',
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
        );
    } else {
        $category = get_queried_object();
        $category_id = absint($category->term_id);
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => 'product',
            'meta_query'     => array(
                'relation' => 'AND',
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
    }

    $products = get_posts($args);

    // Check if the query was successful before proceeding
    if (!empty($products)) {
        $terms = wp_get_object_terms(wp_list_pluck($products, 'ID'), 'filter');

        // If there are matching 'filter' categories, display the filter form
        if (!empty($terms)) {
?>
            <form id="mb-product-filter-form" action="<?php echo esc_url(home_url($_SERVER['REQUEST_URI'])); ?>" method="GET">
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
                                    </li>
                                    <div class="mb-child-category-<?php echo esc_html($category->slug); ?>">
                                        <div class="mb-list-group-item">
                                            <ul class="product-subcategories">
                                                <?php
                                                foreach ($children as $child_id) {
                                                    $child = get_term($child_id, 'filter');
                                                ?>
                                                    <li>
                                                        <input type="checkbox" name="filter[]" value="<?php echo esc_attr($child->slug); ?>" <?php echo (isset($_GET['filter']) && in_array($child->slug, $_GET['filter'])) ? 'checked' : ''; ?>>
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
function product_filter_function($query) {
	
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (isset($_GET['filter'])) {

//           $query->set('tax_query', array(
//             'relation' => 'AND',
//             array(
//                 'taxonomy' => 'filter',
//                 'field'    => 'slug',
//                 'terms'    => $_GET['filter'],
//                 'operator' => 'IN',
//             ),
//         ));

		 $taxonomy_query = array(
            array(
                'taxonomy' => 'filter',
                'field'    => 'slug',
                'terms'    => $_GET['filter'],
                'operator' => 'IN',
            ),
        );
		
		  
        if (is_product_category()) {
            
            $current_category = get_queried_object();

            $taxonomy_query[] = array(
                'taxonomy' => 'mb-category',
                'field'    => 'id',
                'terms'    => $current_category->term_id,
            );
        }

        // Set the tax_query
        $query->set('tax_query', $taxonomy_query);
		
        
         //$query->set('post_type', 'product');
 		//dd($query);
    }
}

add_action('pre_get_posts', 'product_filter_function');

