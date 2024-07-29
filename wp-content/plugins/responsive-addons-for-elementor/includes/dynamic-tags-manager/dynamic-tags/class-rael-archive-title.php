<?php
/**
 * Theme Builder- Archive Title Widget
 *
 * @package Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\DynamicTagsManager\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;
use Elementor\Controls_Manager;

/**
 * Class for making post title tag.
 */
class RAEL_Archive_Title extends Tag {
	/**
	 * Get Name
	 *
	 * Returns the Name of the tag
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'rael-archive-title';
	}

	/**
	 * Get Title
	 *
	 * Returns the Title of the tag
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Archive Title', 'responsive-addons-for-elementor' );
	}

	/**
	 * Get Group
	 *
	 * Returns the Group of the tag
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_group() {
		return 'rael-archive-group';
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
	 * Render Archive title frontend output.
	 */
	public function render() {
		$include_context = 'yes' === $this->get_settings( 'rael_include_context' );

		$title = $this->get_page_title( $include_context );

		echo esc_html( $title );
	}
	/**
	 * Register controls for archive title widget
	 */
	protected function register_controls() {
		$this->add_control(
			'rael_include_context',
			array(
				'label'   => __( 'Include Context', 'responsive-addons-for-elementor' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);
	}

	/**
	 * Get Page title
	 *
	 * Returns a string with page title prefixed by its context if needed
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param bool $include_context includes context if needed.
	 *
	 * @return string
	 */
	public static function get_page_title( $include_context = true ) {
		$title = '';

		if ( is_singular() ) {
			/* translators: %s: Search term. */
			$title = get_the_title();

			if ( $include_context ) {
				$post_type_obj = get_post_type_object( get_post_type() );
				$title         = sprintf( '%s: %s', $post_type_obj->labels->singular_name, $title );
			}
		} elseif ( is_search() ) {
			/* translators: %s: Search term. */
			$title = sprintf( __( 'Search Results for: %s', 'responsive-addons-for-elementor' ), get_search_query() );

			if ( get_query_var( 'paged' ) ) {
				/* translators: %s is the page number. */
				$title .= sprintf( __( '&nbsp;&ndash; Page %s', 'responsive-addons-for-elementor' ), get_query_var( 'paged' ) );
			}
		} elseif ( is_category() ) {
			$title = single_cat_title( '', false );

			if ( $include_context ) {
				/* translators: Category archive title. 1: Category name */
				$title = sprintf( __( 'Category: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
			if ( $include_context ) {
				/* translators: Tag archive title. 1: Tag name */
				$title = sprintf( __( 'Tag: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';

			if ( $include_context ) {
				/* translators: Author archive title. 1: Author name */
				$title = sprintf( __( 'Author: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_year() ) {
			$title = get_the_date( _x( 'Y', 'yearly archives date format', 'responsive-addons-for-elementor' ) );

			if ( $include_context ) {
				/* translators: Yearly archive title. 1: Year */
				$title = sprintf( __( 'Year: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_month() ) {
			$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'responsive-addons-for-elementor' ) );

			if ( $include_context ) {
				/* translators: Monthly archive title. 1: Month name and year */
				$title = sprintf( __( 'Month: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_day() ) {
			$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'responsive-addons-for-elementor' ) );

			if ( $include_context ) {
				/* translators: Daily archive title. 1: Date */
				$title = sprintf( __( 'Day: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = _x( 'Asides', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = _x( 'Galleries', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = _x( 'Images', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = _x( 'Videos', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = _x( 'Quotes', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = _x( 'Links', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = _x( 'Statuses', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = _x( 'Audio', 'post format archive title', 'responsive-addons-for-elementor' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = _x( 'Chats', 'post format archive title', 'responsive-addons-for-elementor' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );

			if ( $include_context ) {
				/* translators: Post type archive title. 1: Post type name */
				$title = sprintf( __( 'Archives: %s', 'responsive-addons-for-elementor' ), $title );
			}
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );

			if ( $include_context ) {
				$tax = get_taxonomy( get_queried_object()->taxonomy );
				/* translators: Taxonomy term archive title. 1: Taxonomy singular name, 2: Current taxonomy term */
				$title = sprintf( __( '%1$s: %2$s', 'responsive-addons-for-elementor' ), $tax->labels->singular_name, $title );
			}
		} elseif ( is_archive() ) {
			$title = __( 'Archives', 'responsive-addons-for-elementor' );
		} elseif ( is_404() ) {
			$title = __( 'Page Not Found', 'responsive-addons-for-elementor' );
		}

		return $title;
	}
}
