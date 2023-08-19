<?php
add_shortcode('book_display', 'book_display_shortcode');
function book_display_shortcode($atts) {
    ob_start();
    require_once plugin_dir_path(__FILE__) . '../shortcode-ui/template-functions.php';

    // Parse shortcode attributes
    $atts = shortcode_atts(array('categories' => null,), $atts);
    $req_categories = $atts['categories'];

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
        'posts_per_page' => 10,
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

    // Query books
    $books_query = new WP_Query($args);

    // Output books
    echo '<section style="
        position: relative;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        align-content: center;
        row-gap: 10px;
        column-gap: 10px;
    ">';
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
    echo '</section>';

    // Reset post data
    wp_reset_postdata();

    $output = ob_get_clean();
    return $output;
}