<?php
/**
 * WooCommerce - Customizer Partials.
 *
 * @package Responsive Addons
 * @since 1.1.0
 */

// No direct access, please.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Customizer_Ext_WooCommerce_Partials' ) ) {
	/*
	* Responsive_Customizer_Ext_WooCommerce_Partials initial setup
	*
	* @since 1.1.0
	*/
	class Responsive_Customizer_Ext_WooCommerce_Partials {
		/*
		* Member Variable
		*
		* @var instance
		*/
		private static $instance;

		/*
		* Initiator
		*/
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/*
		* Constructor
		*/
		public function __construct() {
		}

		/*
		* Render the Below Header Section 1 for the selective refresh partial.
		*
		* @since 1.1.0
		*/
		function _render_shop_load_more() {
			return get_theme_mod( 'shop-load-more-text', 'Load More' );
		}
	}
}
new Responsive_Customizer_Ext_WooCommerce_Partials();
