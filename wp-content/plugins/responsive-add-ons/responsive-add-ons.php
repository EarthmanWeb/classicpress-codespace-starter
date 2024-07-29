<?php
/**
 * Plugin Name: Responsive Plus - Starter Templates
 * Plugin URI: http://wordpress.org/plugins/responsive-add-ons/
 * Description: Responsive Plus - Starter Templates offers you a library of premium Elementor and block templates so you can launch your website quickly. It also offers advanced features for the Responsive theme like mega menu, white label, WooCommerce features and custom fonts.
 * Version: 3.0.7
 * Author: Cyberchimps
 * Author URI: https://cyberchimps.com
 * License: GPL2
 *
 * @package         Responsive_Add_Ons
 */

/*
Copyright 2013  CyberChimps  (email : support@cyberchimps.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Set Constants.
if ( ! defined( 'RESPONSIVE_ADDONS_FILE' ) ) {
	define( 'RESPONSIVE_ADDONS_FILE', __FILE__ );
}

if ( ! defined( 'RESPONSIVE_ADDONS_DIR' ) ) {
	define( 'RESPONSIVE_ADDONS_DIR', plugin_dir_path( RESPONSIVE_ADDONS_FILE ) );
}

if ( ! defined( 'RESPONSIVE_ADDONS_DIR_URL' ) ) {
	define( 'RESPONSIVE_ADDONS_DIR_URL', plugin_dir_url( RESPONSIVE_ADDONS_FILE ) );
}

if ( ! defined( 'RESPONSIVE_ADDONS_URI' ) ) {
	define( 'RESPONSIVE_ADDONS_URI', plugins_url( '/', RESPONSIVE_ADDONS_FILE ) );
}

if ( ! defined( 'RESPONSIVE_ADDONS_VER' ) ) {
	define( 'RESPONSIVE_ADDONS_VER', '3.0.7' );
}

if ( ! defined( 'CC_APP_URL' ) ) {
	define( 'CC_APP_URL', 'https://app.cyberchimps.com' );
}

/**
 * Load WC_AM_Client class if it exists.
 */
if ( ! class_exists( 'WC_AM_Client_2_7_Responsive_Addons' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'wc-am-client-addons.php';
}

/*
 * Instantiate WC_AM_Client class object if the WC_AM_Client class is loaded.
 */
if ( class_exists( 'WC_AM_Client_2_7_Responsive_Addons' ) ) {

	$wcam_lib_responsive_addons = new WC_AM_Client_2_7_Responsive_Addons( __FILE__, '', '3.0.7', 'plugin', CC_APP_URL, 'Responsive Addons', 'responsive-addons' );
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-responsive-add-ons.php';

/**
 * The code that runs during plugin activation.
 */
function activate_responsive_addons() {

	$settings = get_option( 'rpro_elementor_settings' );

	if ( is_array( $settings ) ) {
		$settings['hide_wl_settings'] = 'off';
	}
	update_option( 'rpro_elementor_settings', $settings );

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-responsive-add-ons-activator.php';
	Responsive_Add_Ons_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_responsive_addons() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-responsive-add-ons-deactivator.php';
	Responsive_Add_Ons_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_responsive_addons' );
register_deactivation_hook( __FILE__, 'deactivate_responsive_addons' );

/**
 * Initialize Plugin
 */
if ( class_exists( 'Responsive_Add_Ons' ) ) {

	// Initialise Class.
	$responsive = new Responsive_Add_Ons();
}

// load the latest sdk version from the active Responsive theme.
if ( ! function_exists( 'responsive_sdk_load_latest' ) ) :
	/**
	 * Always load the latest sdk version.
	 */
	function responsive_sdk_load_latest() {
		/**
		 * Don't load the library if we are on < 5.4.
		 */
		if ( version_compare( PHP_VERSION, '5.4.32', '<' ) ) {
			return;
		}
		require_once dirname( __FILE__ ) . '/admin/rollback/start.php';
	}
endif;
add_action( 'init', 'responsive_sdk_load_latest' );


if ( ! function_exists( 'responsive_addon_load_sdk' ) ) {
	/**
	 * Loads products array.
	 *
	 * @param array $products All products.
	 *
	 * @return array Products array.
	 */
	function responsive_addon_load_sdk( $products ) {
		$theme_name = wp_get_theme();
		if ( 'Responsive' === $theme_name->get( 'Name' ) ) {
			$products[] = get_template_directory() . '/style.css';
		}
		return $products;
	}
}
add_filter( 'responsive_sdk_products', 'responsive_addon_load_sdk' );
