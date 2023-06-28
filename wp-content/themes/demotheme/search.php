<?php
/**
 * Demo Theme search template
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */
get_header();
?>
<div class="container">
    <?php
    _e("<h2 style='font-weight:bold;color:#000'>Search Results for: " . get_query_var('s') . "</h2>");
    ?>
    <div class="search-result-count">
        <?php
        $found = $wp_query->found_posts;
        echo '<h3>We found ' . $found . ' result for your search.</h3>';
        ?>
    </div>
    <?php
    if (have_posts()) {

        while (have_posts()) {
            the_post();
            $id = get_the_ID();
            $title = get_the_title($id);
            $date = get_the_date('d F, Y');
            $thumbnail = get_the_post_thumbnail_url($id);
            $content = get_the_content($id);
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
                        <?php } if (!empty($content)) { ?>
                            <span><?php echo $content; ?></span>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>
            <?php
        }wp_reset_query();
        wp_reset_postdata();
    } else {
        ?>
        <div class="search" style='text-align: center;'>
            <h3 style='font-weight:bold;color:#000'>Search Form</h3>
    <?php echo get_search_form(); ?>
        </div>

    <?php
}
?>
</div>

<?php
get_footer();

