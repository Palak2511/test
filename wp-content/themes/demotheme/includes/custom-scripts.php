<?php
/**
 * The template for including the custom scripts in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
//enqueue public scripts
function demotheme_public_scripts() {
    //styles
    wp_enqueue_style('public-style', get_template_directory_uri() . '/css/public-style.css', null, time());
    wp_enqueue_style('style', get_stylesheet_uri());

    //scripts
    wp_enqueue_script('jQuery', get_template_directory_uri() . '/js/jquery.min.js', array());
    wp_enqueue_script('public-script', get_template_directory_uri() . '/js/public-script.js', array(), time());

    wp_localize_script('public-script', 'ajaxObj', array('ajax_url' => admin_url('admin-ajax.php')));
}

//enqueue admin scripts
add_action('wp_enqueue_scripts', 'demotheme_public_scripts');

function demotheme_admin_scripts() {
    //styles
    wp_enqueue_style( 'admin', get_template_directory_uri() . '/css/admin-style.css', false, time() );
    //scripts
    wp_enqueue_script( 'admin', get_template_directory_uri() . '/js/admin-script.js', false, time() );
}
add_action( 'admin_enqueue_scripts', 'demotheme_admin_scripts' );