<?php
namespace Responsive_Addons_Pro\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Responsive Pro user report.
 *
 * Responsive Pro system report handler class responsible for generating a report for
 * the user.
 *
 * @since 2.0.5
 */
class User extends Base {

	/**
	 * Get user reporter title.
	 *
	 * Retrieve user reporter title.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'User';
	}

	/**
	 * Get user report fields.
	 *
	 * Retrieve the required fields for the user report.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return array(
			'role'   => 'Role',
			'locale' => 'WP Profile lang',
			'agent'  => 'User Agent',
		);
	}

	/**
	 * Get user role.
	 *
	 * Retrieve the user role.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The user role.
	 * }
	 */
	public function get_role() {
		$role = null;

		$current_user = wp_get_current_user();
		if ( ! empty( $current_user->roles ) ) {
			$role = $current_user->roles[0];
		}

		return array(
			'value' => $role,
		);
	}

	/**
	 * Get user profile language.
	 *
	 * Retrieve the user profile language.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value User profile language.
	 * }
	 */
	public function get_locale() {
		return array(
			'value' => get_locale(),
		);
	}

	/**
	 * Get user agent.
	 *
	 * Retrieve user agent.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value HTTP user agent.
	 * }
	 */
	public function get_agent() {
		return array(
			'value' => esc_html( $_SERVER['HTTP_USER_AGENT'] ),
		);
	}
}
