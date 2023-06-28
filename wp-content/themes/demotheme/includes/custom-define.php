<?php

/**
 * The template for including the definition in the Demo Theme.
 *
 * @package WordPress
 * @subpackage Demo Theme
 */
//define post post type
if (!define('DEMOTHEME_POST_POST_TYPE')) {
    define('DEMOTHEME_POST_POST_TYPE', 'post');
}
//define custom post type
if (!define('DEMOTHEME_CUSTOM_POST_POST_TYPE')) {
    define('DEMOTHEME_CUSTOM_POST_POST_TYPE', 'custom-post-type');
}
//define custom post type taxonomy
if (!define('DEMOTHEME_CUSTOM_TAXONOMY_CUSTOM_POST_TYPE')) {
    define('DEMOTHEME_CUSTOM_TAXONOMY_CUSTOM_POST_TYPE', 'custom-taxonomy');
}
//define custom post type tag
if (!define('DEMOTHEME_CUSTOM_TAG_CUSTOM_POST_TYPE')) {
    define('DEMOTHEME_CUSTOM_TAG_CUSTOM_POST_TYPE', 'custom-tag');
}