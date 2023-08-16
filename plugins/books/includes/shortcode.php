<?php
add_shortcode('book_display', 'book_display_shortcode');
function book_display_shortcode($atts) {
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
    $output = '<ul>';
    while ($books_query->have_posts()) {
        $books_query->the_post();
        $output .= '<li>' . esc_html(get_the_title()) . ' by ' . esc_html(get_post_meta(get_the_ID(), 'author', true)) . '</li>';
    }
    $output .= '</ul>';

    // Reset post data
    wp_reset_postdata();

    return $output;
}