<?php
/**
 * Theme builder widget- Site URL
 *
 * @package Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Class for making site url tag.
 */
class RAEL_Site_URL extends Data_Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'rael-site-url';
	}

	/**
	 * Get Title
	 *
	 * Returns the Title of the tag
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Site URL', 'responsive-addons-for-elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_group() {
		return 'rael-site-group';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @since 1.4.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( Module::URL_CATEGORY );
	}

	/**
	 * Retrieves the value of the element.
	 *
	 * Retrieves the home URL of the WordPress site.
	 *
	 * @param array $options An array of options (default: empty).
	 * @return string The home URL of the WordPress site.
	 */
	public function get_value( array $options = array() ) {
		return home_url();
	}
}
