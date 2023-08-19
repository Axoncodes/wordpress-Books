<?php
function enqueue_shortcode_assets() {
    if (is_singular() && has_shortcode(get_the_content(), 'book_display')) {
        wp_enqueue_style('book-display-style', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_script('book-display-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_shortcode_assets');