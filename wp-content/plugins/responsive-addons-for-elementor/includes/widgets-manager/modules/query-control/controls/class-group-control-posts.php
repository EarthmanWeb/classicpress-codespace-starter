<?php
/**
 * RAEL Group Control Posts.
 *
 * @package  Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Modules\QueryControl\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;
use Elementor\Utils as ElementorUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Group_Control_Posts
 */
class Group_Control_Posts extends Group_Control_Base {

	const INLINE_MAX_RESULTS = 15;

	/**
	 * Fields
	 *
	 * @var array
	 */
	protected static $fields;

	/**
	 * Get Type
	 */
	public static function get_type() {
		return 'rael-posts';
	}

	/**
	 * Remove Setting From Element on Export
	 *
	 * @param array  $element Element.
	 * @param string $control_id Control Id of widget.
	 */
	public static function on_export_remove_setting_from_element( $element, $control_id ) {

		unset( $element['settings'][ $control_id . '_posts_ids' ] );
		unset( $element['settings'][ $control_id . '_authors' ] );

		$post_types = self::get_public_post_types();
		foreach ( $post_types as $post_type => $label ) {
			$taxonomy_filter_args = array(
				'show_in_nav_menus' => true,
				'object_type'       => array( $post_type ),
			);

			$taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

			foreach ( $taxonomies as $taxonomy => $object ) {
				unset( $element['settings'][ $control_id . '_' . $taxonomy . '_ids' ] );
			}
		}

		return $element;
	}

	/**
	 * Init Fields.
	 */
	protected function init_fields() {
		$fields = array();

		$fields['post_type'] = array(
			'label' => __( 'Source', 'responsive-addons-for-elementor' ),
			'type'  => Controls_Manager::SELECT,
		);

		$fields['posts_ids'] = array(
			'label'       => __( 'Search & Select', 'responsive-addons-for-elementor' ),
			'type'        => 'rael-query',
			'post_type'   => '',
			'options'     => array(),
			'label_block' => true,
			'multiple'    => true,
			'filter_type' => 'by_id',
			'condition'   => array(
				'post_type' => 'by_id',
			),
			'export'      => false,
		);

		$fields['authors'] = array(
			'label'       => __( 'Author', 'responsive-addons-for-elementor' ),
			'label_block' => true,
			'type'        => 'rael-query',
			'multiple'    => true,
			'default'     => array(),
			'options'     => array(),
			'filter_type' => 'author',
			'condition'   => array(
				'post_type!' => array(
					'by_id',
					'current_query',
				),
			),
			'export'      => false,
		);

		return $fields;
	}

	/**
	 * Prepare Fields.
	 *
	 * @param array $fields Fields.
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		$post_type_args = array();
		if ( ! empty( $args['post_type'] ) ) {
			$post_type_args['post_type'] = $args['post_type'];
		}

		$post_types = self::get_public_post_types( $post_type_args );

		$post_types_options = $post_types;

		$post_types_options['by_id']         = __( 'Manual Selection', 'responsive-addons-for-elementor' );
		$post_types_options['current_query'] = __( 'Current Query', 'responsive-addons-for-elementor' );

		$fields['post_type']['options'] = $post_types_options;

		$fields['post_type']['default'] = key( $post_types );

		$fields['posts_ids']['object_type'] = array_keys( $post_types );

		$taxonomy_filter_args = array(
			'show_in_nav_menus' => true,
		);

		$taxonomies = get_taxonomies( $taxonomy_filter_args, 'objects' );

		// bypass bug in WP_List_Util::filter() causing wrong array comparison
		// when a taxonomy belongs to several post-types (e.g. when using woocommerce-product-add-ons)
		// ( using simple '==' rather than in_array() or array_intersect() ).
		$filtered_taxonomies = array();
		if ( ! empty( $args['post_type'] ) ) {
			foreach ( $taxonomies as $taxonomy => $obj ) {
				$tax_array = (array) $obj;
				if ( in_array( $args['post_type'], $tax_array['object_type'], true ) ) {
					$filtered_taxonomies[ $taxonomy ] = $obj;
				}
			}
		} else {
			$filtered_taxonomies = $taxonomies;
		}

		foreach ( $filtered_taxonomies as $taxonomy => $object ) {
			$taxonomy_args = array(
				'label'       => $object->label,
				'type'        => 'rael-query',
				'label_block' => true,
				'multiple'    => true,
				'object_type' => $taxonomy,
				'options'     => array(),
				'condition'   => array(
					'post_type' => $object->object_type,
				),
				'export'      => false,
			);

			$count = wp_count_terms( $taxonomy );

			$options = array();

			// For large websites, use Ajax to search.
			if ( $count > self::INLINE_MAX_RESULTS ) {
				$taxonomy_args['type'] = 'rael-query';

				$taxonomy_args['filter_type'] = 'taxonomy';
			} else {
				$taxonomy_args['type'] = Controls_Manager::SELECT2;

				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
					)
				);

				foreach ( $terms as $term ) {
					$options[ $term->term_id ] = $term->name;
				}

				$taxonomy_args['options'] = $options;
			}

			$fields[ $taxonomy . '_ids' ] = $taxonomy_args;
		}

		return parent::prepare_fields( $fields );
	}

	/**
	 * Get Default Options.
	 */
	protected function get_default_options() {
		return array(
			'popover' => false,
		);
	}

	/**
	 * Fix Offset.
	 *
	 * @param array $query_args Query Object.
	 * @param array $settings Control Id of widget.
	 * @param array $prefix Prefix.
	 */
	protected function fix_offset( $query_args, $settings, $prefix = '' ) {
		if ( 0 < $settings[ $prefix . 'offset' ] ) {
			/**
			 * Due to a WordPress bug, the offset will be set later, in $this->fix_query_offset()
			 *
			 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			$query_args['offset_to_fix'] = $settings[ $prefix . 'offset' ];
		}

		return $query_args;
	}

	/**
	 * Build Query Args
	 *
	 * @param array  $settings Settings.
	 * @param string $control_id_prefix Control Id of Prefix.
	 */
	protected function build_query_args( $settings, $control_id_prefix ) {

		$prefix = $control_id_prefix . '_';

		$post_type = $settings[ $prefix . 'post_type' ];

		$query_args = array(
			'orderby'             => $settings['orderby'],
			'order'               => $settings['order'],
			'ignore_sticky_posts' => 1,
			'post_status'         => 'publish', // Hide drafts/private posts for admins.
		);

		if ( 'by_id' === $post_type ) {
			$post_types = self::get_public_post_types();

			$query_args['post_type']      = array_keys( $post_types );
			$query_args['posts_per_page'] = -1;

			$query_args['post__in'] = $settings[ $prefix . 'posts_ids' ];

			if ( empty( $query_args['post__in'] ) ) {
				// If no selection - return an empty query.
				$query_args['post__in'] = array( 0 );
			}
		} else {
			$query_args['post_type']      = $post_type;
			$query_args['posts_per_page'] = $settings['posts_per_page'];
			$query_args['tax_query']      = array(); //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query

			$query_args = $this->fix_offset( $query_args, $settings );

			$taxonomies = get_object_taxonomies( $post_type, 'objects' );

			foreach ( $taxonomies as $object ) {
				$setting_key = $prefix . $object->name . '_ids';

				if ( ! empty( $settings[ $setting_key ] ) ) {
					$query_args['tax_query'][] = array(
						'taxonomy' => $object->name,
						'field'    => 'term_id',
						'terms'    => $settings[ $setting_key ],
					);
				}
			}
		}

		if ( ! empty( $settings[ $prefix . 'authors' ] ) ) {
			$query_args['author__in'] = $settings[ $prefix . 'authors' ];
		}

		$post__not_in = array();
		if ( ! empty( $settings['exclude'] ) ) {
			if ( in_array( 'current_post', $settings['exclude'], true ) ) {
				if ( ElementorUtils::is_ajax() && ! empty( $_REQUEST['post_id'] ) ) { // phpcs:ignore
					$post__not_in[] = $_REQUEST['post_id']; // phpcs:ignore
				} elseif ( is_singular() ) {
					$post__not_in[] = get_queried_object_id();
				}
			}

			if ( in_array( 'manual_selection', $settings['exclude'], true ) && ! empty( $settings['exclude_ids'] ) ) {
				$post__not_in = array_merge( $post__not_in, $settings['exclude_ids'] );
			}
		}

		if ( ! empty( $settings['avoid_duplicates'] ) && 'yes' === $settings['avoid_duplicates'] ) {
			$post__not_in = array_merge( $post__not_in, Module::$displayed_ids );
		}

		$query_args['post__not_in'] = $post__not_in;

		return $query_args;
	}

	/**
	 * Get Query Args.
	 *
	 * @param string $control_id_prefix Control Id Prefix.
	 * @param array  $settings Settings.
	 */
	public function get_query_args( $control_id_prefix, $settings ) {

		$defaults = array(
			$control_id_prefix . '_post_type' => 'post',
			$control_id_prefix . '_posts_ids' => array(),
			'orderby'                         => 'date',
			'order'                           => 'desc',
			'posts_per_page'                  => 3,
			'offset'                          => 0,
		);

		$settings = wp_parse_args( $settings, $defaults );

		$post_type = $settings[ $control_id_prefix . '_post_type' ];

		if ( 'current_query' === $post_type ) {
			$current_query_vars = $GLOBALS['wp_query']->query_vars;

			/**
			 * Current query variables.
			 *
			 * Filters the query variables for the current query.
			 *
			 * @since 1.0.0
			 *
			 * @param array $current_query_vars Current query variables.
			 */
			$current_query_vars = apply_filters( 'rael_core_elements_query_control_get_query_args_current_query', $current_query_vars );

			return $current_query_vars;
		}

		return $this->build_query_args( $settings, $control_id_prefix );
	}

	/**
	 * Get public post type list.
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	public static function get_public_post_types( $args = array() ) {
		$post_type_args = array(
			// Default is the value $public.
			'show_in_nav_menus' => true,
		);

		// Keep for backwards compatibility.
		if ( ! empty( $args['post_type'] ) ) {
			$post_type_args['name'] = $args['post_type'];
			unset( $args['post_type'] );
		}

		$post_type_args = wp_parse_args( $post_type_args, $args );

		$_post_types = get_post_types( $post_type_args, 'objects' );

		$post_types = array();

		foreach ( $_post_types as $post_type => $object ) {
			$post_types[ $post_type ] = $object->label;
		}

		return apply_filters( 'rael_core_elements_get_public_post_types', $post_types );
	}
}
