<?php
/**
 * Only for developer
 * @author Fazle Bari
 */
if( ! function_exists('dd') ){
    function dd( ...$vals){
        if( ! empty($vals) && is_array($vals) ){
            ob_start(); // Start output buffering
            foreach($vals as $val ){
                echo "<pre>";
                    var_dump($val);
                echo "</pre>";
            }
            $output = ob_get_clean(); // Get the buffered output and clear the buffer
            echo $output; // Output the buffered content
        }
    }
}

// Write all your custom codes here if you don't want to use OOP 

// Define the shortcode for the filter
function mb_product_filter_shortcode() {
    ob_start(); ?>

    <form id="product-filter-form" action="" method="GET">
        <ul class="product-categories">
            <?php
            // Get all product categories
            $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            ));

            foreach ($categories as $category) {
                ?>
                <li>
                    <input type="checkbox" name="product_category[]" 
                    value="<?php echo esc_attr($category->slug); ?>" 
                    <?php echo (isset($_GET['product_category']) && in_array($category->slug, $_GET['product_category'])) ? 'checked' : ''; ?>>
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
    </form>
    <?php
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
