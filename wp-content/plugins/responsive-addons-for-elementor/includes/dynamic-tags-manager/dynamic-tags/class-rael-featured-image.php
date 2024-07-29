<?php
/**
 * Theme builder widget- Featured Image
 *
 * @package Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * Class for making featured image tag.
 */
class RAEL_Featured_Image extends Data_Tag {
	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'rael-post-featured-image';
	}

	/**
	 * Get Title
	 *
	 * Returns the Title of the tag
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Featured Image', 'responsive-addons-for-elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return array
	 */
	public function get_group() {
		return 'rael-post-group';
	}

	/**
	 * Get Categories
	 *
	 * Returns an array of tag categories
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( Module::TEXT_CATEGORY, Module::IMAGE_CATEGORY );
	}

	/**
	 * Register controls
	 */
	protected function register_controls() {
		$this->add_control(
			'rael_fallback',
			array(
				'label' => __( 'Fallback', 'responsive-addons-for-elementor' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
	}

	/**
	 * Retrieves the value of the element.
	 *
	 * Retrieves the value of the element based on the given options. If a post has a thumbnail,
	 * it retrieves the thumbnail image ID and its URL. If no thumbnail is found, it falls back
	 * to the specified fallback settings.
	 *
	 * @param array $options An array of options (default: empty).
	 * @return array An array containing the image data, including the image ID and URL.
	 */
	public function get_value( array $options = array() ) {
		$thumbnail_id = get_post_thumbnail_id();

		if ( $thumbnail_id ) {
			$image_data = array(
				'id'  => $thumbnail_id,
				'url' => wp_get_attachment_image_src( $thumbnail_id, 'full' )[0],
			);
		} else {
			$image_data = $this->get_settings( 'rael_fallback' );
		}

		return $image_data;
	}
}
