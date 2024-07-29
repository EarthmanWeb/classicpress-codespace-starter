<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.cyberchimps.com
 * @since      1.0.0
 *
 * @package    Responsive_Add_Ons
 * @subpackage Responsive_Add_Ons/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Responsive_Add_Ons
 * @subpackage Responsive_Add_Ons/includes
 * @author     CyberChimps <support@cyberchimps.com>
 */
class Responsive_Add_Ons_Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		set_transient( 'responsive_add_ons_activation_redirect', true, MINUTE_IN_SECONDS );

		update_option( 'ra_first_time_activation', true );
	}
}
