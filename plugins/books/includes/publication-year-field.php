<?php

// Registering the "publication year" custom field beside the autofill check
// The autofill checkbox will make the field read-only and fill the field with the year from the post date

function add_publication_year_field() {
    add_meta_box(
        'publication_year_field',
        'Publication Year',
        'publication_year_field_callback',
        'books',
        'side',
        'default'
    );
}

function publication_year_field_callback($post) {
    $publication_year = get_post_meta($post->ID, 'publication_year', true);
    $auto_fill = get_post_meta($post->ID, 'publication_year_auto_fill', true);
    if ($auto_fill === '' || $auto_fill === '1') { // Check if empty or set to '1'
        $auto_fill_checked = 'checked';
    } else {
        $auto_fill_checked = '';
    }
    ?>
    <label for="publication_year">Publication Year:</label>
    <input type="text" id="publication_year" name="publication_year" value="<?php echo esc_attr($publication_year); ?>" <?php if ($auto_fill) echo 'readonly'; ?>>
    <br>
    <label>
        <input type="checkbox" name="publication_year_auto_fill" value="1" <?php checked($auto_fill, 1); ?>>
        Auto Fill with Post Date
    </label>
    <?php
}

function save_publication_year_field($post_id) {
    if (array_key_exists('publication_year', $_POST)) {
        $publication_year = sanitize_text_field($_POST['publication_year']);
        update_post_meta(
            $post_id,
            'publication_year',
            $publication_year
        );
    }

    if (array_key_exists('publication_year_auto_fill', $_POST)) {
        $auto_fill = sanitize_text_field($_POST['publication_year_auto_fill']);
        update_post_meta(
            $post_id,
            'publication_year_auto_fill',
            $auto_fill
        );
    }
}

function auto_fill_publication_year($post_id) {
    $auto_fill = get_post_meta($post_id, 'publication_year_auto_fill', true);

    if ($auto_fill) {
        $post_date = get_the_date('Y', $post_id);
        update_post_meta($post_id, 'publication_year', $post_date);
    }
}


// Make the field visible on the post type posts column
function add_publication_year_column($columns) {
    return array(
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'author' => $columns['author'],
        'taxonomy-book-category' => $columns['taxonomy-book-category'],
        'publication_year' => 'Publication Year',
        'date' => $columns['date'],
    );
}

function display_publication_year_column($column, $post_id) {
    if ($column === 'publication_year') {
        $publication_year = get_post_meta($post_id, 'publication_year', true);
        echo esc_html($publication_year);
    }
}

function make_publication_year_column_sortable($columns) {
    $columns['publication_year'] = 'publication_year';
    return $columns;
}

function sort_publication_year_column($query) {
    if (!is_admin()) return;

    $orderby = $query->get('orderby');
    if ($orderby === 'publication_year') {
        $query->set('meta_key', 'publication_year');
        $query->set('orderby', 'meta_value');
    }
}


add_action('add_meta_boxes', 'add_publication_year_field');
add_action('save_post_books', 'save_publication_year_field');
add_action('save_post_books', 'auto_fill_publication_year');
add_filter('manage_edit-books_columns', 'add_publication_year_column');
add_action('manage_books_posts_custom_column', 'display_publication_year_column', 10, 2);
add_filter('manage_edit-books_sortable_columns', 'make_publication_year_column_sortable');
add_action('pre_get_posts', 'sort_publication_year_column');
