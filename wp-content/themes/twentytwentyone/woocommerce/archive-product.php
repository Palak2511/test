<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */
defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
?>
<header class="woocommerce-products-header">
    <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
        <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
    <?php endif; ?>

    <?php
    /**
     * Hook: woocommerce_archive_description.
     *
     * @hooked woocommerce_taxonomy_archive_description - 10
     * @hooked woocommerce_product_archive_description - 10
     */
    do_action('woocommerce_archive_description');
    ?>
</header>
<?php
if (woocommerce_product_loop()) {

    /**
     * Hook: woocommerce_before_shop_loop.
     *
     * @hooked woocommerce_output_all_notices - 10
     * @hooked woocommerce_result_count - 20
     * @hooked woocommerce_catalog_ordering - 30
     */
    ?>
    <div class="row">
        <div class="twelve columns">
            <?php
            do_action('woocommerce_before_shop_loop');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="twelve columns">

            <ul class="courses columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?>" id="mix-wrapper">
                <?php
                if (wc_get_loop_prop('total')) {
                    $order = 1;
                    while (have_posts()) {
                        the_post();
                        $order++;

                        /**
                         * Hook: woocommerce_shop_loop.
                         */
                        do_action('woocommerce_shop_loop');
                        ?>
                        <li <?php echo 'class="' . esc_attr(implode(' ', wc_get_product_class($class, $product))) . ' mix-target"'; ?> data-order="<?php echo $order; ?>" style="display:none;">
                            <?php
                            /**
                             * Hook: woocommerce_before_shop_loop_item.
                             *
                             * @hooked woocommerce_template_loop_product_link_open - 10
                             */
                            do_action('woocommerce_before_shop_loop_item');

                            /**
                             * Hook: woocommerce_before_shop_loop_item_title.
                             *
                             * @hooked woocommerce_show_product_loop_sale_flash - 10
                             * @hooked woocommerce_template_loop_product_thumbnail - 10
                             */
                            do_action('woocommerce_before_shop_loop_item_title');

                            /**
                             * Hook: woocommerce_shop_loop_item_title.
                             *
                             * @hooked woocommerce_template_loop_product_title - 10
                             */
                            do_action('woocommerce_shop_loop_item_title');

                            /**
                             * Hook: woocommerce_after_shop_loop_item_title.
                             *
                             * @hooked woocommerce_template_loop_rating - 5
                             * @hooked woocommerce_template_loop_price - 10
                             */
                            do_action('woocommerce_after_shop_loop_item_title');

                            /**
                             * Hook: woocommerce_after_shop_loop_item.
                             *
                             * @hooked woocommerce_template_loop_product_link_close - 5
                             * @hooked woocommerce_template_loop_add_to_cart - 10
                             */
                            do_action('woocommerce_after_shop_loop_item');
                            ?>
                        </li>
                        <?php
                    }
                }

                woocommerce_product_loop_end();
                ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/jquery.mixitup/latest/jquery.mixitup.min.js"></script>
    <script>
        jQuery('#mix-wrapper').mixItUp({
            load: {
                sort: 'order:asc'
            },
            animation: {
                "duration": 500,
                "nudge": true,
                "reverseOut": true,
                "effects": "fade translateX(20%) translateY(20%) translateZ(-100px) rotateX(90deg) rotateY(90deg) rotateZ(180deg) stagger(30ms)"
            },
            selectors: {
                target: '.mix-target',
                filter: '.filter-btn',
                sort: '.sort-btn'
            },
            callbacks: {
                onMixEnd: function (state) {
                    console.log(state)
                }
            },

        });
    </script>
    <?php
    /**
     * Hook: woocommerce_after_shop_loop.
     *
     * @hooked woocommerce_pagination - 10
     */
//	do_action( 'woocommerce_after_shop_loop' );
} else {
    /**
     * Hook: woocommerce_no_products_found.
     *
     * @hooked wc_no_products_found - 10
     */
    do_action('woocommerce_no_products_found');
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');

get_footer('shop');

