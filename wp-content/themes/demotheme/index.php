<?php
/**
 * Demo Theme main index template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
get_header();

if (have_posts()) {
    ?>
    <div class="container">
        <h1>Demo</h1>
        <?php
        while (have_posts()) {
            the_post();
            $id = get_the_ID();
            $title = get_the_title($id);
            $date = get_the_date('d F, Y');
            $thumbnail = get_the_post_thumbnail_url($id);
            $permalink = get_the_permalink($id);
            ?>
            <div class="block">
                <?php if (!empty($permalink)) { ?>
                    <a href="<?php echo $permalink; ?>">
                        <?php if (!empty($thumbnail)) { ?>
                            <img src="<?php echo $thumbnail; ?>" alt="">
                        <?php } if (!empty($title)) { ?>
                            <h6><?php echo $title; ?></h6>
                        <?php } if (!empty($date)) { ?>
                            <span><?php echo $date; ?></span>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>
            <?php
        }wp_reset_query();
        wp_reset_postdata();
        ?>
    </div>
    <?php
}
get_footer();
