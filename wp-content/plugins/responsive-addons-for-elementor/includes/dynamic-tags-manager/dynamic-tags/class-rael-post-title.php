<?php
/**
 * Theme Builder Widget- Post Title
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

/**
 * Class for making post title tag.
 */
class RAEL_Post_Title extends Tag {

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
		return 'rael-post-title';
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
		return __( 'Post Title', 'responsive-addons-for-elementor' );
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
		return array( Module::TEXT_CATEGORY );
	}

	/**
	 * Render function
	 *
	 * Prints out the value of the Dynamic tag
	 *
	 * @since 1.3.2
	 * @access public
	 *
	 * @return void
	 */
	public function render() {
		echo esc_html( get_the_title() );
	}
}
