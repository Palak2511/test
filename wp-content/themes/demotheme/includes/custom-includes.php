<?php
/**
 * The template for including the important files in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
/**
 * Demo Theme includes and definitions
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
require get_stylesheet_directory() . '/includes/custom-define.php';
if (class_exists('acf')) {
    require get_stylesheet_directory() . '/includes/custom-acf.php';
}
//include functions file
require get_stylesheet_directory() . '/includes/custom-functions.php';
//include post types file
require get_stylesheet_directory() . '/includes/custom-posttypes.php';
//include scripts and styles file
require get_stylesheet_directory() . '/includes/custom-scripts.php';
