<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACF_Admin') ) :

class ACF_Admin {
	
	/**
	 * __construct
	 *
	 * Sets up the class functionality.
	 *
	 * @date	23/06/12
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	 function __construct() {
		
		// Add hooks.
		add_action( 'admin_menu', 				array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts',	array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_body_class', 		array( $this, 'admin_body_class' ) );
		add_action( 'current_screen',			array( $this, 'current_screen' ) );
	}
	
	/**
	 * admin_menu
	 *
	 * Adds the ACF menu item.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	 function admin_menu() {
		global $parent_file;
		
		// Bail early if ACF is hidden.
		if( !acf_get_setting('show_admin') ) {
			return;
		}
		
		// Vars.
		$slug = 'edit.php?post_type=acf-field-group';
		$cap = acf_get_setting('capability');
		
		// Add menu items.
		add_menu_page( __("Custom Fields",'acf'), __("Custom Fields",'acf'), $cap, $slug, false, 'dashicons-welcome-widgets-menus', '80.025' );
		add_submenu_page( $slug, __('Field Groups','acf'), __('Field Groups','acf'), $cap, $slug );
		add_submenu_page( $slug, __('Add New','acf'), __('Add New','acf'), $cap, 'post-new.php?post_type=acf-field-group' );
		
		// Only register info page when needed.
		if( isset($_GET['page']) && $_GET['page'] === 'acf-settings-info' ) {
			add_submenu_page( $slug, __('Info','acf'), __('Info','acf'), $cap,'acf-settings-info', array($this,'info_page_html') );
		}
	}
	
	/**
	 * admin_enqueue_scripts
	 *
	 * Enqueues global admin styling.
	 *
	 * @date	28/09/13
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	function admin_enqueue_scripts() {
		
		// Enqueue global style. To-do: Change to admin.
		wp_enqueue_style( 'acf-global' );
	}
	
	/**
	 * admin_body_class
	 *
	 * Appends the determined body_class.
	 *
	 * @date	5/11/19
	 * @since	5.8.7
	 *
	 * @param	string $classes Space-separated list of CSS classes.
	 * @return	string
	 */
	function admin_body_class( $classes ) {
		global $wp_version;
		
		// Determine body class version.
		$wp_minor_version = floatval( $wp_version );
		if( $wp_minor_version >= 5.3 ) {
			$classes .= ' acf-admin-5-3';
		} else {
			$classes .= ' acf-admin-3-8';
		}
		
		// Add browser for specific CSS.
		$classes .= ' acf-browser-' . acf_get_browser();
		
		// Append and return.
		return $classes;
	}
	
	/**
	 * info_page_html
	 *
	 * Renders the Info page HTML.
	 *
	 * @date	5/11/19
	 * @since	5.8.7
	 *
	 * @param	void
	 * @return	void
	 */
	function info_page_html() {
		
		// Vars.
		$view = array(
			'version'		=> acf_get_setting('version'),
			'have_pro'		=> acf_get_setting('pro'),
			'tabs'			=> array(
				'new'			=> __("What's New", 'acf'),
				'changelog'		=> __("Changelog", 'acf')
			),
			'active'		=> 'new'
		);
		
		// Find active tab.
		if( isset($_GET['tab']) && $_GET['tab'] === 'changelog' ) {
			$view['active'] = 'changelog';
		}		
		
		// Load view.
		acf_get_view('settings-info', $view);
	}
	
	/**
	 * Adds custom functionality to "ACF" admin pages.
	 *
	 * @date	7/4/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	function current_screen( $screen ) {
		
		// Determine if the current page being viewed is "ACF" related.
		if( isset( $screen->post_type ) && $screen->post_type === 'acf-field-group' ) {
			add_action( 'in_admin_header',		array( $this, 'in_admin_header' ) );
			add_filter( 'admin_footer_text',	array( $this, 'admin_footer_text' ) );
		}
	}
	
	/**
	 * Renders the admin navigation element.
	 *
	 * @date	27/3/20
	 * @since	5.9.0
	 *
	 * @param	void
	 * @return	void
	 */
	function in_admin_header() {
		acf_get_view( 'html-admin-navigation' );
	}
	
	/**
	 * Modifies the admin footer text.
	 *
	 * @date	7/4/20
	 * @since	5.9.0
	 *
	 * @param	string $text The admin footer text.
	 * @return	string
	 */
	function admin_footer_text( $text ) {
		// Use RegExp to append "ACF" after the <a> element allowing translations to read correctly.
		return preg_replace( '/(<a[\S\s]+?\/a>)/', '$1 ' . __('and', 'acf') . ' <a href="https://www.advancedcustomfields.com" target="_blank">ACF</a>', $text, 1 );
	}
}

// Instantiate.
acf_new_instance('ACF_Admin');

endif; // class_exists check
