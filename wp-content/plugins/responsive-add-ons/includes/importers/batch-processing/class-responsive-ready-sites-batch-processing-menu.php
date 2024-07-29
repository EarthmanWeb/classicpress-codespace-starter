<?php
/**
 * Menu fix batch task.
 *
 * @package Responsive Addons
 * @since 2.0.3
 */

if ( ! class_exists( 'Responsive_Ready_Sites_Batch_Processing_Menu' ) ) :

	/**
	 * Responsive_Ready_Sites_Batch_Processing_Menu
	 *
	 * @since 2.0.3
	 */
	class Responsive_Ready_Sites_Batch_Processing_Menu {

		/**
		 * Instance
		 *
		 * @since 2.0.3
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.0.3
		 * @return object initialized object of class.
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.0.3
		 */
		public function __construct() {}

		/**
		 * Import
		 *
		 * @since 2.0.3
		 * @return void
		 */
		public function import() {

			self::fix_nav_menus();
		}

		/**
		 * Fix Menu.
		 */
		public static function fix_nav_menus() {
			$header_menu_term_id = term_exists( 'menu1' );
			if ( $header_menu_term_id ) {
				$theme_nav_menu_locations                = get_theme_mod( 'nav_menu_locations' );
				$theme_nav_menu_locations['header-menu'] = $header_menu_term_id;
				set_theme_mod( 'nav_menu_locations', $theme_nav_menu_locations );
			}

			$footer_menu_term_id = term_exists( 'menu2' );
			if ( $footer_menu_term_id ) {
				$theme_nav_menu_locations                = get_theme_mod( 'nav_menu_locations' );
				$theme_nav_menu_locations['footer-menu'] = $footer_menu_term_id;
				set_theme_mod( 'nav_menu_locations', $theme_nav_menu_locations );
			}
		}

	}

	Responsive_Ready_Sites_Batch_Processing_Menu::get_instance();

endif;
