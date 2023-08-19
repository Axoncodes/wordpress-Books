<?php
/*
* Plugin Name: Book Handler
* Description: Manage Books Taxonomy and Display system.
* Version: 1.0
* Author: Alireza Ataei
* Author URI: https://ceo.axoncodes.com
*/
// Initialize a global counter variable
global $books_shortcode_instance;
$books_shortcode_instance = 1; // Start from 1

require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcode.php';

