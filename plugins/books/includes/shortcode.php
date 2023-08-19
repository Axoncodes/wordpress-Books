<?php
require_once plugin_dir_path(__FILE__) . '../shortcode-ui/index.php';
require_once plugin_dir_path(__FILE__) . '../shortcode-ui/template-functions.php';

add_shortcode('book_display', 'book_display_shortcode');
function book_display_shortcode($atts) {
    global $books_shortcode_instance;

    // Parse shortcode attributes
    $atts = shortcode_atts(array('categories' => null,), $atts);
    $req_categories = $atts['categories'];

    // Use the counter as the instance identifier
    $unique_instance_identifier = 'books-instance-' . $books_shortcode_instance;
    $unique_instance_identifier = esc_attr($unique_instance_identifier);

    // Increment the counter for the next instance
    $books_shortcode_instance++;

    // Display the Filterbar
    $filterbar = books_filterbar(get_terms(array(
        'taxonomy' => 'book-category',
        'hide_empty' => false,
    )), $req_categories);

    return "<div class='books-shortcode-container'>
        $filterbar
        <div class='books-container' data-instance='$unique_instance_identifier'></div>
    </div>";
}

function books_shortcode_body($req_categories, $unique_instance_identifier, $booked=0) {
    ob_start();

    // Use wp_nonce_url to generate a nonce URL for security
    $ajax_url = wp_nonce_url(admin_url("admin-ajax.php?action=load_books&instance=$unique_instance_identifier&category=$req_categories"), 'load_books_nonce');

    // Validate and sanitize post type and taxonomy names
    $post_type = 'books';
    $taxonomy = 'book-category';

    // Ensure valid post type and taxonomy names
    if (!post_type_exists($post_type) || !taxonomy_exists($taxonomy)) {
        return 'Invalid post type or taxonomy.';
    }

    // Query arguments for books
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => -1,
        'paged' => $booked,
        'tax_query' => array(),
    );

    if (!is_null($req_categories)) {
        // Sanitize and validate categories attribute
        $category_slugs = array_map('sanitize_text_field', explode(',', $req_categories));
        $category_slugs = array_map('trim', $category_slugs);
        
        // Add category filter if provided
        $args['tax_query'][] = array(
            'taxonomy' => $taxonomy,
            'field' => 'slug',
            'terms' => $category_slugs,
        );
    }

    $books_query = new WP_Query($args);

    echo "<section style='
        position: relative;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        align-content: center;
        row-gap: 10px;
        column-gap: 10px;
    '>";
    if ($books_query->have_posts()) {
        // Books List
        while ($books_query->have_posts()) {
            $books_query->the_post();

            // Get and sanitize title
            $title = get_the_title();
            $sanitized_title = sanitize_text_field($title);

            // Get and sanitize thumbnail URL
            $thumbnail_url = get_the_post_thumbnail_url();
            $sanitized_thumbnail_url = esc_url($thumbnail_url);

            // Get and sanitize category
            $categories = get_the_terms(get_the_ID(), 'book-category');
            $category = !empty($categories) ? esc_html($categories[0]->name) : '';
            $category_link = esc_url(get_term_link($categories[0]));

            // Get and sanitize publish year or update year
            $publish_year = get_post_meta(get_the_ID(), 'publication_year', true);

            // Get and sanitize excerpt
            $excerpt = get_the_excerpt();
            $sanitized_excerpt = esc_html($excerpt);

            // Get and sanitize author name
            $author_name = get_the_author_meta('display_name');
            $sanitized_author_name = esc_html($author_name);
            $author_link = esc_url(get_author_posts_url(get_the_author_meta('ID')));

            // Get post permalink
            $post_permalink = get_permalink();
            
            echo de_book_block(
                $sanitized_title,
                $sanitized_thumbnail_url,
                $category,
                $category_link,
                $publish_year,
                $sanitized_excerpt,
                $sanitized_author_name,
                $author_link,
                $post_permalink,
            );
        }
    } else {
        echo "<p>No Books Found</p>";
    }
    echo '</section>';
    // Reset post data
    wp_reset_postdata();

    // Rest of the shortcode content
    $output = ob_get_clean();
    return $output;
}

function load_books_ajax() {
    // Use the counter as the instance identifier
    global $books_shortcode_instance;

    $unique_instance_identifier = 'books-instance-' . $books_shortcode_instance;
    $unique_instance_identifier = esc_attr($unique_instance_identifier);

    // Increment the counter for the next instance
    $books_shortcode_instance++;

    // The Requested Category
    // validate
    $categories = isset($_GET['category']) ? $_GET['category'] : null;
    // check length
    $categories = strlen($categories) > 0 ? $categories : null;

    echo books_shortcode_body($categories, $unique_instance_identifier);

    die();
}
add_action('wp_ajax_load_books', 'load_books_ajax');
add_action('wp_ajax_nopriv_load_books', 'load_books_ajax');
