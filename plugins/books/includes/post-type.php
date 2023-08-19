<?php
// Registering th custom BOOKS post type
add_action('init', 'books_custom_post_type_registration');
function books_custom_post_type_registration() {
    $plural_name = 'Books';
    $singular_name = 'Book';
    $slug = 'books';
    
    $labels = array(
        'name' => $plural_name,
        'all_items' => "All $plural_name",
        'singular_name' => $singular_name,
        'add_new' => 'Add New',
        'add_new_item' => "Add New $singular_name",
        'edit_item' => "Edit $singular_name",
        'new_item' => "New $singular_name",
        'view_item' => "View $singular_name",
        'search_items' => "Search $plural_name",
        'not_found' => "No $plural_name found",
        'not_found_in_trash' => "No $plural_name found in Trash",
        'parent_item_colon' => "Parent $singular_name",
        'menu_name' => $plural_name,
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Books custom post type.',
        'supports' => array('thumbnail', 'title', 'editor', 'excerpt', 'author'),
        'public' => true,
        'menu_position' => 5,
        'has_archive' => true,
        'rewrite' => array('slug' => 'books'),
    );
    error_log('Custom post type plugin is being executed.');

	register_post_type($slug, $args);
}

// Registering the Category Taxonomy for the Books custom post type
add_action( 'init', 'books_category_taxonomy_registration' );
function books_category_taxonomy_registration() {
    $plural_name = 'Categories';
    $singular_name = 'Category';
    $slug = 'book-category';

    $labels = array(
        'name' => _x( $plural_name, "$singular_name taxonomy label" ),
        'singular_name' => _x( $singular_name, "$singular_name taxonomy singular label" ),
        'search_items' => __( "Search $plural_name" ),
        'all_items' => __( "All $plural_name" ),
        'parent_item' => __( "Parent $singular_name"),
        'parent_item_colon' => __( "Parent $singular_name:" ),
        'edit_item' => __( "Edit $singular_name" ),
        'update_item' => __( "Update $singular_name" ),
        'add_new_item' => __( "Add New $singular_name" ),
        'new_item_name' => __( "New $singular_name Name" ),
        'menu_name' => __( $plural_name ),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud' => false,
        'rewrite' => [ 'slug' => $slug ],
    );

    register_taxonomy( $slug, [ 'books' ], $args );
}

require_once plugin_dir_path(__FILE__) . './publication-year-field.php';
