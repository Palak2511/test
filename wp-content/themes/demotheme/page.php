<?php
/**
 * Demo Theme page template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
get_header();

$id = get_the_ID();
?>
<div class="container">
    <h1><?php echo get_the_title($id); ?></h1>
</div>

<?php
get_footer();
