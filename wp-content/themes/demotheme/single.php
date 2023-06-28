<?php
/**
 * Demo Theme Single page template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
get_header();

$id = get_the_ID();
$title = get_the_title($id);
$thumb = get_the_post_thumbnail_url($id);
$content = get_the_content($id);
?>
<section class="news">
    <div class="container">
        <h1><?php echo $title; ?></h1>
        <?php if (!empty($thumb)) { ?>
            <img src=<?php echo $thumb; ?> alt="alt" height='100%' width='100%'/>
        <?php }  if (!empty($thumb)) { ?>
            <p><?php echo $content; ?></p>
        <?php } ?>
    </div>
</section>
<?php
get_footer();
