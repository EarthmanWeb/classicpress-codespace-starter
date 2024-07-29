<?php
namespace Responsive_Addons_Pro\System_Info\Reporters;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Responsive Pro active plugins report.
 *
 * Responsive Pro system report handler class responsible for generating a report for
 * active plugins.
 *
 * @since 2.0.5
 */
class Plugins extends Base {

	/**
	 * Active plugins.
	 *
	 * Holds the sites active plugins list.
	 *
	 * @since 2.0.5
	 * @access private
	 *
	 * @var array
	 */
	private $plugins;

	/**
	 * Get active plugins.
	 *
	 * Retrieve the active plugins from the list of all the installed plugins.
	 *
	 * @since 2.0.5
	 * @access private
	 *
	 * @return array Active plugins.
	 */
	private function get_plugins() {
		if ( ! $this->plugins ) {
			// Ensure get_plugins function is loaded
			if ( ! function_exists( 'get_plugins' ) ) {
				include ABSPATH . '/wp-admin/includes/plugin.php';
			}

			$active_plugins = get_option( 'active_plugins' );
			$this->plugins  = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );
		}

		return $this->plugins;
	}

	/**
	 * Get active plugins reporter title.
	 *
	 * Retrieve active plugins reporter title.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return string Reporter title.
	 */
	public function get_title() {
		return 'Active Plugins';
	}

	/**
	 * Is enabled.
	 *
	 * Whether there are active plugins or not.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return bool True if the site has active plugins, False otherwise.
	 */
	public function is_enabled() {
		return ! ! $this->get_plugins();
	}

	/**
	 * Get active plugins report fields.
	 *
	 * Retrieve the required fields for the active plugins report.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array Required report fields with field ID and field label.
	 */
	public function get_fields() {
		return array(
			'active_plugins' => 'Active Plugins',
		);
	}

	/**
	 * Get active plugins.
	 *
	 * Retrieve the sites active plugins.
	 *
	 * @since 2.0.5
	 * @access public
	 *
	 * @return array {
	 *    Report data.
	 *
	 *    @type string $value The active plugins list.
	 * }
	 */
	public function get_active_plugins() {
		return array(
			'value' => $this->get_plugins(),
		);
	}
}
