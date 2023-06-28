<?php
/**
 * Demo Theme header template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <title>Demo Theme</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="format-detection" content="telephone=no">       
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>

