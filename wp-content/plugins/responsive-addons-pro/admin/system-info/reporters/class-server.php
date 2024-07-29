<?php
namespace Responsive_Addons_Pro\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Responsive Pro server environment report.
 *
 * Responsive Pro system report handler class responsible for generating a report for
 * the server environment.
 *
 * @since 2.0.5
 */
class Server extends Base {

	/**
	 * Get server environment reporter title.
	 *
	 * Retrieve server environment reporter title.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Server Environment';
	}

	/**
	 * Get server environment report fields.
	 *
	 * Retrieve the required fields for the server environment report.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return array(
			'os'                => 'Operating System',
			'software'          => 'Software',
			'mysql_version'     => 'MySQL version',
			'php_version'       => 'PHP Version',
			'write_permissions' => 'Write Permissions',
		);
	}

	/**
	 * Get server operating system.
	 *
	 * Retrieve the server operating system.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server operating system.
	 * }
	 */
	public function get_os() {
		return array(
			'value' => PHP_OS,
		);
	}

	/**
	 * Get server software.
	 *
	 * Retrieve the server software.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value Server software.
	 * }
	 */
	public function get_software() {
		return array(
			'value' => $_SERVER['SERVER_SOFTWARE'],
		);
	}

	/**
	 * Get PHP version.
	 *
	 * Retrieve the PHP version.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value          PHP version.
	 *    @type string $recommendation Minimum PHP version recommendation.
	 *    @type bool   $warning        Whether to display a warning.
	 * }
	 */
	public function get_php_version() {
		$result = array(
			'value' => PHP_VERSION,
		);

		if ( version_compare( $result['value'], '5.4', '<' ) ) {
			$result['recommendation'] = _x( 'We recommend to use php 5.4 or higher', 'System Info', 'responsive-addons-pro' );

			$result['warning'] = true;
		}

		return $result;
	}

	/**
	 * Get MySQL version.
	 *
	 * Retrieve the MySQL version.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value MySQL version.
	 * }
	 */
	public function get_mysql_version() {
		global $wpdb;

		$db_server_version = $wpdb->get_results( "SHOW VARIABLES WHERE `Variable_name` IN ( 'version_comment', 'innodb_version' )", OBJECT_K );

		return array(
			'value' => $db_server_version['version_comment']->Value . ' v' . $db_server_version['innodb_version']->Value,
		);
	}

	/**
	 * Get write permissions.
	 *
	 * Check whether the required folders has writing permissions.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value   Writing permissions status.
	 *    @type bool   $warning Whether to display a warning. True if some required
	 *                          folders don't have writing permissions, False otherwise.
	 * }
	 */
	public function get_write_permissions() {
		$paths_to_check = array(
			ABSPATH => 'WordPress root directory',
		);

		$write_problems = array();

		$wp_upload_dir = wp_upload_dir();

		if ( $wp_upload_dir['error'] ) {
			$write_problems[] = 'WordPress root uploads directory';
		}

		$responsive_pro_uploads_path = $wp_upload_dir['basedir'] . '/responsive-add-ons';

		if ( is_dir( $responsive_pro_uploads_path ) ) {
			$paths_to_check[ $responsive_pro_uploads_path ] = 'Responsive uploads directory';
		}

		$htaccess_file = ABSPATH . '/.htaccess';

		if ( file_exists( $htaccess_file ) ) {
			$paths_to_check[ $htaccess_file ] = '.htaccess file';
		}

		foreach ( $paths_to_check as $dir => $description ) {
			if ( ! is_writable( $dir ) ) {
				$write_problems[] = $description;
			}
		}

		if ( $write_problems ) {
			$value = 'There are some writing permissions issues with the following directories/files:' . "\n\t\t - ";

			$value .= implode( "\n\t\t - ", $write_problems );
		} else {
			$value = 'All right';
		}

		return array(
			'value'   => $value,
			'warning' => ! ! $write_problems,
		);
	}
}
