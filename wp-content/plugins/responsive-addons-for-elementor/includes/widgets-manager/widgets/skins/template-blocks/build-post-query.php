<?php
/**
 * RAEL Build Post Query.
 *
 * @package RAEL
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Skins\TemplateBlocks;

use Responsive_Addons_For_Elementor\Helper\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Posts
 */
class Build_Post_Query {


	/**
	 * Loop query counter
	 *
	 * @since 1.9.5
	 * @var int $loop_counter
	 */
	public static $loop_counter = 0;

	/**
	 * Query object
	 *
	 * @since 1.7.0
	 * @var object $query
	 */
	public $query = '';

	/**
	 * Filter
	 *
	 * @since 1.7.0
	 * @var object $filter
	 */
	public static $filter = '';

	/**
	 * Settings
	 *
	 * @since 1.7.0
	 * @var object $settings
	 */
	public $settings = '';

	/**
	 * Skin
	 *
	 * @since 1.7.0
	 * @var object $skin
	 */
	public $skin = '';

	/**
	 * Cache the custom pagination data.
	 * Format:
	 *      array(
	 *          'current_page' => '',
	 *          'current_loop' => '',
	 *          'paged' => ''
	 *      )
	 *
	 * @since 1.7.0
	 * @var array
	 */
	public static $custom_paged_data = array();

	/**
	 * Initiator
	 *
	 * @param string $skin Skin.
	 * @param array  $settings Settimgs.
	 * @param string $filter Filter taxonomy.
	 */
	public function __construct( $skin, $settings, $filter ) {

		$this->settings = $settings;
		$this->skin     = $skin;
		self::$filter   = str_replace( '.', '', $filter );
	}

	/**
	 * Set Query Posts.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function query_posts_args() {

		$skin_id = $this->skin;

		$settings = $this->settings;

		$query_args = self::get_query_posts( $skin_id, $settings );

		$query_args['posts_per_page'] = ( '' === $settings[ $skin_id . '_posts_per_page' ] ) ? -1 : $settings[ $skin_id . '_posts_per_page' ];
		if ( 'none' !== $settings['pagination_type'] ) {
			$query_args['paged'] = $this->get_paged();
		} else {
			$query_args['paged'] = '1';
		}

		return ( $query_args );
	}

	/**
	 * Get query posts based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @param string $control_id Settings control id.
	 * @param array  $settings Settings array for the widget.
	 * @since 1.7.0
	 * @access public
	 */
	public static function get_query_posts( $control_id, $settings ) {

		if ( '' !== $control_id ) {
			$control_id = $control_id . '_';
		}

		if ( '' !== $_POST['data']['paged'] ) {
			$paged = self::get_paged();
		} else {
			$paged = '1';
		}

		$tax_count = 0;

		$post_type = ( isset( $settings['rael-posts_post_type'] ) && '' !== $settings['rael-posts_post_type'] ) ? $settings['rael-posts_post_type'] : 'post';

		$query_args = array(
			'post_type'        => $post_type,
			'posts_per_page'   => ( '' === $settings[ $control_id . 'posts_per_page' ] ) ? -1 : $settings[ $control_id . 'posts_per_page' ],
			'paged'            => $paged,
			'post_status'      => 'publish',
			'suppress_filters' => false,
		);

		$query_args['orderby']             = $settings['rael-posts_orderby'];
		$query_args['order']               = $settings['rael-posts_order'];
		$query_args['ignore_sticky_posts'] = ( isset( $settings['rael-posts_ignore_sticky_posts'] ) && 'yes' === $settings['rael-posts_ignore_sticky_posts'] ) ? 1 : 0;

		// Get all the taxonomies associated with the post type.
		$taxonomy = Helper::rael_get_taxonomies( $post_type );

		if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {
			// Get all taxonomy values under the taxonomy.

			$tax_count = 0;
			foreach ( $taxonomy as $index => $tax ) {
				if ( ! empty( $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ] ) ) {
					$operator = $settings[ $index . '_' . $post_type . '_filter_rule' ];

					$query_args['tax_query'][] = array(
						'taxonomy' => $index,
						'field'    => 'slug',
						'terms'    => $settings[ 'tax_' . $index . '_' . $post_type . '_filter' ],
						'operator' => $operator,
					);
					$tax_count++;
				}
			}
		}

		if ( 0 < $settings['rael-posts_offset'] ) {
			/**
			 * Offser break the pagination. Using WordPress's work around
			 *
			 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			$query_args['offset'] = $settings['rael-posts_offset'];
		}

		if ( '' !== self::$filter && '*all' !== self::$filter ) {
			$query_args['tax_query'][ $tax_count ]['taxonomy'] = $settings['rael_ft_tax_filter'];
			$query_args['tax_query'][ $tax_count ]['field']    = 'slug';
			$query_args['tax_query'][ $tax_count ]['terms']    = self::$filter;
			$query_args['tax_query'][ $tax_count ]['operator'] = 'IN';
		}

		return apply_filters( 'rael_posts_query_args', $query_args, $settings );
	}

	/**
	 * Set Query Posts.
	 *
	 * @since 1.7.0
	 * @access public
	 */
	public function query_posts() {

		$settings = $this->settings;

		if ( isset( $settings['query_type'] ) && 'main' === $settings['query_type'] ) {
			global $wp_query;

			$main_query = clone $wp_query;

			$this->query = $main_query;
		} else {
			$query_args  = $this->query_posts_args();
			$this->query = new \WP_Query( $query_args );
		}
	}

	/**
	 * Returns the paged number for the query.
	 *
	 * @since 1.7.0
	 * @return int
	 */
	public static function get_paged() {

		global $wp_the_query, $paged;

		if ( isset( $_POST['data']['paged'] ) && '' !== $_POST['data']['paged'] ) {
			return $_POST['data']['paged'];
		}

		// Check the 'paged' query var.
		$paged_qv = $wp_the_query->get( 'paged' );

		if ( is_numeric( $paged_qv ) ) {
			return $paged_qv;
		}

		// Check the 'page' query var.
		$page_qv = $wp_the_query->get( 'page' );

		if ( is_numeric( $page_qv ) ) {
			return $page_qv;
		}

		// Check the $paged global?
		if ( is_numeric( $paged ) ) {
			return $paged;
		}

		return 0;
	}

	/**
	 * Render current query.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function get_query() {

		return $this->query;
	}
}
