<?php
/**
 * Plugin Name:     Responsive Pro
 * Plugin URI:      https://www.cyberchimps.com/responsive-pro
 * Description:     Adds pro features to your Responsive theme. Import pro Ready Sites. Get private, priority support.
 * Author:          CyberChimps
 * Author URI:      https://www.cyberchimps.com/
 * Text Domain:     responsive-addons-pro
 * Domain Path:     /languages
 * Version:         2.6.6
 *
 * @package         Responsive_Addons_Pro
 */

// Your code starts here.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Set Constants.
if ( ! defined( 'RESPONSIVE_ADDONS_PRO_FILE' ) ) {
	define( 'RESPONSIVE_ADDONS_PRO_FILE', __FILE__ );
}

if ( ! defined( 'RESPONSIVE_ADDONS_PRO_DIR' ) ) {
	define( 'RESPONSIVE_ADDONS_PRO_DIR', plugin_dir_path( RESPONSIVE_ADDONS_PRO_FILE ) );
}

if ( ! defined( 'RESPONSIVE_ADDONS_PRO_URI' ) ) {
	define( 'RESPONSIVE_ADDONS_PRO_URI', plugins_url( '/', RESPONSIVE_ADDONS_PRO_FILE ) );
}

if ( ! defined( 'RESPONSIVE_ADDONS_PRO_VERSION' ) ) {
	define( 'RESPONSIVE_ADDONS_PRO_VERSION', '2.6.6' );
}

if ( ! defined( 'RESPONSIVE_ADDONS_PRO_BASE' ) ) {
	define( 'RESPONSIVE_ADDONS_PRO_BASE', plugin_basename( __FILE__ ) );
}

/**
 * Load WC_AM_Client class if it exists.
 */
if ( ! class_exists( 'WC_AM_Client_2_7_PRO' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'wc-am-client-pro.php';
}

/*
 * Instantiate WC_AM_Client class object if the WC_AM_Client class is loaded.
 */
if ( class_exists( 'WC_AM_Client_2_7_PRO' ) ) {

	$wcam_lib_responsive_pro = new WC_AM_Client_2_7_PRO( __FILE__, '', '2.6.6', 'plugin', 'https://www.cyberchimps.com/', 'Responsive Pro', 'responsive-addons-pro' );
}

// Responsive Addons Pro plugin's main file.
require plugin_dir_path( __FILE__ ) . 'includes/class-responsive-addons-pro.php';

/**
 * The code that runs during plugin activation.
 */
function activate_responsive_addons_pro() {

	$settings = get_option( 'rpro_elementor_settings' );

	if ( is_array( $settings ) ) {
		$settings['hide_wl_settings'] = 'off';

	}

	update_option( 'rpro_elementor_settings', $settings );

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-responsive-addons-pro-activator.php';
	Responsive_Addons_Pro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_responsive_addons_pro() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-responsive-addons-pro-deactivator.php';
	Responsive_Addons_Pro_Deactivator::deactivate();
}


register_activation_hook( __FILE__, 'activate_responsive_addons_pro' );
register_deactivation_hook( __FILE__, 'deactivate_responsive_addons_pro' );

/**
 * Begins execution of the plugin.
 */
function run_responsive_addons_pro() {

	$plugin = new Responsive_Addons_Pro();
	$plugin->run();

}
run_responsive_addons_pro();
