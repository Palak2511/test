<?php
/**
 * The template for including the acf in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
/* Custom Post type start */

function demotheme_custom_post_types() {
//custom post type
    $custom_posttype_supports = array(
        'title',
        'editor',
        'author',
        'thumbnail',
        'excerpt',
        'custom-fields',
        'comments',
        'revisions',
        'post-formats',
    );

    $custom_posttype_labels = array(
        'name' => _x('Custom Post Type', 'plural'),
        'singular_name' => _x('Custom Post Type', 'singular'),
        'menu_name' => _x('Custom Post Type', 'admin menu'),
        'name_admin_bar' => _x('Custom Post Type', 'admin bar'),
        'add_new' => _x('Add New', 'add new'),
        'add_new_item' => __('Add New Custom Post Type'),
        'new_item' => __('New Custom Post Type'),
        'edit_item' => __('Edit Custom Post Type'),
        'view_item' => __('View Custom Post Type'),
        'all_items' => __('All Custom Post Types'),
        'search_items' => __('Search Custom Post Type'),
        'not_found' => __('No news found.'),
    );

    $custom_posttype_args = array(
        'supports' => $custom_posttype_supports,
        'labels' => $custom_posttype_labels,
        'public' => true,
        'query_var' => true,
        'rewrite' => array('slug' => DEMOTHEME_CUSTOM_POST_POST_TYPE),
        'has_archive' => true,
        'hierarchical' => false,
    );

    register_post_type(DEMOTHEME_CUSTOM_POST_POST_TYPE, $custom_posttype_args);

    $category_labels = array(
        'name' => _x('Categories', 'taxonomy general name'),
        'singular_name' => _x('Category', 'taxonomy singular name'),
        'search_items' => __('Search Categories'),
        'popular_items' => __('Popular Categories'),
        'all_items' => __('All Categories'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Categories'),
        'update_item' => __('Update Categories'),
        'add_new_item' => __('Add New Category'),
        'new_item_name' => __('New Category Name'),
        'add_or_remove_items' => __('Add or remove Category'),
        'menu_name' => __('Categories'),
    );
    register_taxonomy(
            DEMOTHEME_CUSTOM_TAXONOMY_CUSTOM_POST_TYPE,
            DEMOTHEME_CUSTOM_POST_POST_TYPE,
            array(
                'hierarchical' => true,
                'labels' => $category_labels,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array(
                    'slug' => DEMOTHEME_CUSTOM_TAXONOMY_CUSTOM_POST_TYPE,
                    'with_front' => true
                )
            )
    );
    // Add new taxonomy, NOT hierarchical (like tags)
    $tag_labels = array(
        'name' => _x('Tags', 'taxonomy general name'),
        'singular_name' => _x('Tag', 'taxonomy singular name'),
        'search_items' => __('Search Tags'),
        'popular_items' => __('Popular Tags'),
        'all_items' => __('All Tags'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Tag'),
        'update_item' => __('Update Tag'),
        'add_new_item' => __('Add New Tag'),
        'new_item_name' => __('New Tag Name'),
        'separate_items_with_commas' => __('Separate tags with commas'),
        'add_or_remove_items' => __('Add or remove tags'),
        'choose_from_most_used' => __('Choose from the most used tags'),
        'menu_name' => __('Tags'),
    );

    register_taxonomy(DEMOTHEME_CUSTOM_TAG_CUSTOM_POST_TYPE, DEMOTHEME_CUSTOM_POST_POST_TYPE, array(
        'hierarchical' => false,
        'labels' => $tag_labels,
        'show_admin_column' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array('slug' => DEMOTHEME_CUSTOM_TAG_CUSTOM_POST_TYPE),
    ));
}

add_action('init', 'demotheme_custom_post_types');

