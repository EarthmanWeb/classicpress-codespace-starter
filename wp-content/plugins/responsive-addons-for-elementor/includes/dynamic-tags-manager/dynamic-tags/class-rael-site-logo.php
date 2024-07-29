<?php
/**
 * Theme builder widget- Site Logo
 *
 * @package Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

use Elementor\Utils;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for making site logo tag.
 */
class RAEL_Site_Logo extends Data_Tag {

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
		return 'rael-site-logo';
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
		return __( 'Site Logo', 'responsive-addons-for-elementor' );
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
		return array( Module::IMAGE_CATEGORY );
	}

	/**
	 * Retrieves the value of the element.
	 *
	 * Retrieves the value of the element, which is the URL of the custom logo. If a custom logo is set
	 * in the theme customizer, it retrieves the ID and URL of the custom logo. If no custom logo is
	 * set, it falls back to a placeholder image.
	 *
	 * @param array $options An array of options (default: empty).
	 * @return array An array containing the custom logo ID and URL.
	 */
	public function get_value( array $options = array() ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$url = wp_get_attachment_image_src( $custom_logo_id, 'full' )[0];
		} else {
			$url = Utils::get_placeholder_image_src();
		}

		return array(
			'id'  => $custom_logo_id,
			'url' => $url,
		);
	}
}
