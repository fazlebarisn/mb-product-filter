<?php
function mb_product_filter_shortcode()
{
    ob_start(); ?>

    <form id="product-filter-form" action="" method="GET">
        <div class="mb-filter-wrap">
            <ul class="product-categories">
                <?php
                // Get all product categories
                $categories = get_terms(array(
                    'taxonomy'   => 'filter',
                    'hide_empty' => true,
                ));

                foreach ($categories as $category) {
                    // Display main category
                ?>
                    <li>
                        <input type="checkbox" name="product_category[]" value="<?php echo esc_attr($category->slug); ?>" <?php echo (isset($_GET['product_category']) && in_array($category->slug, $_GET['product_category'])) ? 'checked' : ''; ?>>
                        <?php echo esc_html($category->name); ?>
                    </li>

                    <?php
                    // Get children of the main category
                    $children = get_term_children($category->term_id, 'filter');

                    if (!empty($children)) {
                        echo '<ul class="product-subcategories">';
                        foreach ($children as $child_id) {
                            $child = get_term($child_id, 'filter');
                    ?>
                            <li style="margin-left: 20px;">
                                <input type="checkbox" name="product_category[]" value="<?php echo esc_attr($child->slug); ?>" <?php echo (isset($_GET['product_category']) && in_array($child->slug, $_GET['product_category'])) ? 'checked' : ''; ?>>
                                <?php echo esc_html($child->name); ?>
                            </li>
                <?php
                        }
                        echo '</ul>';
                    }
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

    if (isset($_GET['product_category'])) {
        $query->set('tax_query', array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $_GET['product_category'],
                'operator' => 'IN',
            ),
        ));
    }
}
add_action('pre_get_posts', 'product_filter_function');
