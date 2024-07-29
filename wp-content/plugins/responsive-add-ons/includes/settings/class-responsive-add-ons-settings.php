<?php
/**
 * Class Settings file.
 *
 * @package Responsive_Addons
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles Settings Operation
 *
 * @class       Settings
 * @version     3.0.0
 * @package     Responsive Addons
 */
class Responsive_Add_Ons_Settings {
	/**
	 * Data array, with defaults.
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Instance of the current class
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Return the current instance of the class
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->data = $this->get_defaults();
	}

	/**
	 * Get default plugin settings
	 *
	 * @return array
	 */
	public function get_defaults() {
		return array(
			'site'    => array(
				'url'       => get_site_url(),
				'installed' => time(),
			),
			'api'     => array(
				'token' => '',
			),
			'account' => array(
				'email'     => '',
				'id'        => '',
				'connected' => false,
				'plan'      => 'free',
				'site_key'  => '',
				'product_id' => '',
			),
		);
	}

	/**
	 * Get settings
	 *
	 * @param string $group Name of the group.
	 * @param string $key Name of the key.
	 * @return array
	 */
	public function get( $group = '', $key = '' ) {
		$settings = get_option( 'reads_app_settings', $this->data );
		if ( empty( $key ) && empty( $group ) ) {
			return $settings;
		} elseif ( ! empty( $key ) && ! empty( $group ) ) {
			$settings = isset( $settings[ $group ] ) ? $settings[ $group ] : array();
			return isset( $settings[ $key ] ) ? $settings[ $key ] : array();
		} else {
			return isset( $settings[ $group ] ) ? $settings[ $group ] : array();
		}
		return $settings;
	}

	/**
	 * Update settings to database.
	 *
	 * @param array $data Array of settings data.
	 * @return void
	 */
	public function update( $data ) {
		$settings = get_option( 'reads_app_settings', $this->data );
		if ( empty( $settings ) ) {
			$settings = $this->data;
		}
		$settings = $data;
		update_option( 'reads_app_settings', $settings );
	}

	// Getter Functions.

	/**
	 * Get account token for authentication.
	 *
	 * @return string
	 */
	public function get_token() {
		return $this->get( 'api', 'token' );
	}

	/**
	 * Get the website key
	 *
	 * @return string
	 */
	public function get_website_key() {
		return $this->get( 'account', 'site_key' );
	}

	/**
	 * Get the id
	 *
	 * @return string
	 */
	public function get_user_id() {
		return $this->get( 'account', 'id' );
	}

	/**
	 * Get website plan
	 *
	 * @return string
	 */
	public function get_plan() {
		return $this->get( 'account', 'plan' );
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function get_email() {
		return $this->get( 'account', 'email' );
	}

	/**
	 * Update the value of the setting
	 *
	 * @return void
	 */
	public function update_raddons_app_settings( $key, $sub_key, $value ) {
		// Get the current settings from the database.
		$raddons_app_settings = get_option( 'reads_app_settings', $this->data );

		$raddons_app_settings[ $key ][ $sub_key ] = $value;

		// Update the option in the database.
		update_option( 'reads_app_settings', $raddons_app_settings );
	}
}
