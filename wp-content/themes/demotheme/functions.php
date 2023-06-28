 <?php
/**
 * Demo Theme functions and definitions
 *
 * @package WordPress
 * @subpackage Demo Theme
 * @since Demo Theme 1.0
 */

if (!function_exists('demotheme_setup')) {
    function demotheme_setup() {

        add_theme_support('title-tag');

        add_theme_support(
                'post-formats',
                array(
                    'link',
                    'aside',
                    'gallery',
                    'image',
                    'quote',
                    'status',
                    'video',
                    'audio',
                    'chat',
                )
        );

        add_theme_support('post-thumbnails');
        set_post_thumbnail_size(1568, 9999);
        add_theme_support('menus');
        register_nav_menus(
                array(
                    'primary' => esc_html__('Primary menu', 'demotheme'),
                    'footer' => __('Footer menu', 'demotheme'),
                )
        );

        add_theme_support(
                'html5',
                array(
                    'search-form',
                    'comment-form',
                    'comment-list',
                    'gallery',
                    'caption',
                    'style',
                    'script',
                    'navigation-widgets',
                )
        );

        $logo_width = 300;
        $logo_height = 100;

        add_theme_support(
                'custom-logo',
                array(
                    'height' => $logo_height,
                    'width' => $logo_width,
                    'flex-width' => true,
                    'flex-height' => true,
                    'unlink-homepage-logo' => true,
                )
        );

        show_admin_bar( true );
    }

}
add_action('after_setup_theme', 'demotheme_setup');


/**
 * Register widget areas.
 *
 * @since Demo Theme 1.0
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function demotheme_sidebar_registration() {

	// Arguments used in all register_sidebar() calls.
	$shared_args = array(
		'before_title'  => '<h2 class="widget-title subheading heading-size-3">',
		'after_title'   => '</h2>',
		'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'after_widget'  => '</div></div>',
	);

	// Footer #1.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #1', 'demotheme' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'demotheme' ),
			)
		)
	);

	// Footer #2.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #2', 'demotheme' ),
				'id'          => 'sidebar-2',
				'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'demotheme' ),
			)
		)
	);

}

add_action( 'widgets_init', 'demotheme_sidebar_registration' );
//include main include file
require get_template_directory() . '/includes/custom-includes.php';


