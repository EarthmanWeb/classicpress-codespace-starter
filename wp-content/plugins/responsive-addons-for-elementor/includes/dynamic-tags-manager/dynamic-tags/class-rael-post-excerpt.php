<?php
/**
 * Theme builder Widget - Post excerpt
 *
 * @package Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
/**
 * Class for making post excerpt tag.
 */
class RAEL_Post_Excerpt extends Tag {

	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'rael-post-excerpt';
	}
	/**
	 * Get Title
	 *
	 * Returns the Title of the tag
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Post Excerpt', 'responsive-addons-for-elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
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
	 * @access public
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( Module::TEXT_CATEGORY );
	}

	/**
	 * Render function
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @access public
	 *
	 * @return void
	 */
	public function render() {
		// Allow only a real `post_excerpt` and not the trimmed `post_content` from the `get_the_excerpt` filter.
		$post = get_post();

		if ( ! $post || empty( $post->post_excerpt ) ) {
			return;
		}

		echo wp_kses_post( $post->post_excerpt );
	}
}
