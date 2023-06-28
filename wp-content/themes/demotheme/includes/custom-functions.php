<?php
/**
 * The template for including the custom functions in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
//add SVG to allowed file uploads
function add_file_types_to_uploads($file_types) {

    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg';
    $file_types = array_merge($file_types, $new_filetypes);

    return $file_types;
}

add_action('upload_mimes', 'add_file_types_to_uploads');

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

//add selected menus to footer
add_filter('acf/load_field/name=iF_select_menu', 'iF_nav_menus_load');

function iF_nav_menus_load($field) {

    $menus = wp_get_nav_menus();

    if (!empty($menus)) {

        foreach ($menus as $menu) {
            $field['choices'][$menu->slug] = $menu->name;
        }
    }

    return $field;
}

//include post types to search query
function iF_include_custom_post_types_in_search_results($wp_query) {
    if ($wp_query->is_main_query() && $wp_query->is_search() && !is_admin()) {
        $wp_query->set('post_type', array('post', 'news', 'product'));
    }
}

add_action('pre_get_posts', 'iF_include_custom_post_types_in_search_results');

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

include(get_template_directory() . '/custom-includes/custom-shortcodes.php');

//ajax call for advertisement template news listing filter
add_action('wp_ajax_nopriv_get_filtered_news', 'get_filtered_news');
add_action('wp_ajax_get_filtered_news', 'get_filtered_news');

function get_filtered_news() {
    echo do_shortcode('[iflair_search_fitler]');
    die();
}

// Add the custom columns to the news post type
add_filter('manage_news_posts_columns', 'set_custom_edit_news_columns');

function set_custom_edit_news_columns($columns) {
    $columns['news_type'] = __('News Type', 'iFlair');

    return $columns;
}

// Add the data to the custom columns for the book post type:
add_action('manage_news_posts_custom_column', 'custom_news_column', 10, 2);

function custom_news_column($column, $post_id) {
    switch ($column) {

        case 'news_type' :
            if (class_exists('acf')) {
                $news_type = get_field('iF_news_type', $post_id);
            }
            if (!empty($news_type)) {
                echo $news_type;
            } else {
                echo '-';
            }
            break;
    }
}

//reorder columns
add_filter('manage_news_posts_columns', function ($columns) {
    $col['news_type'] = __('News Type', 'iFlair');

    $offset = array_search('author', array_keys($columns));
    return array_merge(array_slice($columns, 0, $offset), $col, array_slice($columns, $offset, null));
});
