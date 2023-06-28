<?php
/**
 * The template for including the acf in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
//add options setting
if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Theme General Settings',
        'menu_title' => 'Theme Settings',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));
}

//add favicon to admin site
function add_site_favicon() {
    if (class_exists('acf')) {
        $favicon = get_field('iF_favicon', 'options');
        $favicon_url = ($favicon ? $favicon['url'] : '');
        echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
    }
}

add_action('login_head', 'add_site_favicon');
add_action('admin_head', 'add_site_favicon');