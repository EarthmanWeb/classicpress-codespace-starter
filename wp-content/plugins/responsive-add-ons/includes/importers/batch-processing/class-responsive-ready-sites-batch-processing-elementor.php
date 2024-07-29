<?php
/**
 * Elementor Importer
 *
 * @package Responsive Addons
 */

namespace Elementor\TemplateLibrary;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// If plugin - 'Elementor' not exist then return.
if ( ! class_exists( '\Elementor\Plugin' ) ) {
	return;
}

use Elementor\Core\Base\Document;
use Elementor\DB;
use Elementor\Core\Settings\Page\Manager as PageSettingsManager;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Model;
use Elementor\Editor;
use Elementor\Plugin;
use Elementor\Settings;
use Elementor\Utils;

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 1.2.13 Added compatibility for Elemetnor v2.5.0
 * @since 1.0.0
 */
class Responsive_Ready_Sites_Batch_Processing_Elementor extends Source_Local {

	/**
	 * Import
	 *
	 * @since 1.0.14
	 * @return void
	 */
	public function import() {

		$post_types = \Responsive_Ready_Sites_Batch_Processing::get_post_types_supporting( 'elementor' );
		if ( empty( $post_types ) && ! is_array( $post_types ) ) {
			return;
		}

		$post_ids = \Responsive_Ready_Sites_Batch_Processing::get_pages( $post_types );
		if ( empty( $post_ids ) && ! is_array( $post_ids ) ) {
			return;
		}

		foreach ( $post_ids as $post_id ) {
			$is_elementor_post = get_post_meta( $post_id, '_elementor_version', true );
			if ( $is_elementor_post ) {
				$this->import_single_post( $post_id );
			}
		}
	}
	/**
	 * Update post meta.
	 *
	 * @since 1.0.14
	 * @param  integer $post_id Post ID.
	 * @return void
	 */
	public function import_single_post( $post_id = 0 ) {

		if ( ! empty( $post_id ) ) {

			$hotlink_imported = get_post_meta( $post_id, '_responsive_sites_hotlink_imported', true );

			if ( empty( $hotlink_imported ) ) {

				$data = get_post_meta( $post_id, '_elementor_data', true );

				if ( ! empty( $data ) ) {

					// Update WP form IDs.
					$ids_mapping = get_option( 'responsive_sites_wpforms_ids_mapping', array() );
					if ( $ids_mapping ) {
						foreach ( $ids_mapping as $old_id => $new_id ) {
							$data = str_replace( '[wpforms id=\"' . $old_id, '[wpforms id=\"' . $new_id, $data );
						}
					}

					if ( ! is_array( $data ) ) {
						$data = json_decode( $data, true );
					}

					$term_ids_mapping = get_option( 'responsive_sites_term_ids_mapping', array() );
					$term_ids_mapping = maybe_unserialize( $term_ids_mapping );

					array_walk( $data, array( $this, 'traverse_array_recursive' ), $term_ids_mapping );

					$document = Plugin::$instance->documents->get( $post_id );
					if ( $document ) {
						$data = $document->get_elements_raw_data( $data, true );
					}

					// Import the data.
					$data = $this->process_export_import_content( $data, 'on_import' );

					$current_page_api = get_option( 'current_page_api' );
					if ( isset( $current_page_api ) ) {
						$data = wp_json_encode( $data, true );
						if ( ! empty( $data ) ) {
							$site_url         = get_site_url();
							$site_url         = str_replace( '/', '\/', $site_url );
							$current_page_api = str_replace( '/', '\/', $current_page_api );
							$data             = str_replace( $current_page_api, $site_url, $data );
							$data             = json_decode( $data, true );
						}
					}

					// Update processed meta.
					update_metadata( 'post', $post_id, '_elementor_data', $data );
					update_metadata( 'post', $post_id, '_responsive_sites_hotlink_imported', true );

					// !important, Clear the cache after images import.
					Plugin::$instance->files_manager->clear_cache();

				}
			}
		}
	}

	/**
	 * Traverse the array recursively based on a condition.
	 *
	 * @since 2.6.1
	 * @param string|array $value Reference Value of the array.
	 * @param string       $key Key/Index of the array.
	 * @param array        $term_ids_mapping Term IDs mapping array.
	 * @return void
	 */
	protected function traverse_array_recursive( &$value, $key, $term_ids_mapping ) {
		// This condition is specifically for Product categories related widget.
		if ( 'rea_query_include_categories' !== $key && 'rea_query_exclude_categories' !== $key && 'rea-posts_include_term_ids' !== $key && 'category_ids' !== $key && is_array( $value ) ) {
			array_walk( $value, array( $this, 'traverse_array_recursive' ), $term_ids_mapping );
		} else {
			if ( is_array( $value ) ) {
				foreach ( $value as $index => $term_id ) {
					$value[ $index ] = $term_ids_mapping[ $term_id ];
				}
			}
		}
	}

	/**
	 * Update post meta.
	 *
	 * @since 2.7.3
	 * @param  integer $post_id Post ID.
	 * @param  array   $data Elementor Data.
	 * @return array   $data Elementor Imported Data.
	 */
	public function responsive_import_post_meta( $post_id = 0, $data = array() ) {

		if ( ! empty( $post_id ) && ! empty( $data ) ) {

			$data = wp_json_encode( $data, true );

			// Update WP form IDs.
			$ids_mapping = get_option( 'responsive_sites_wpforms_ids_mapping', array() );

			if ( $ids_mapping ) {
				foreach ( $ids_mapping as $old_id => $new_id ) {
					$data = str_replace( '[wpforms id=\"' . $old_id, '[wpforms id=\"' . $new_id, $data );
					$data = str_replace( '"select_form":"' . $old_id, '"select_form":"' . $new_id, $data );
				}
			}

			$data = json_decode( $data, true );

			// Import the data.
			$data = $this->process_export_import_content( $data, 'on_import' );

			// Replace the site urls.
			$demo_data = get_option( 'responsive_ready_sites_import_data', array() );

			if ( isset( $demo_data ) ) {
				$site_url      = get_site_url();
				$site_url      = str_replace( '/', '\/', $site_url );
				$demo_site_url = 'https:' . $demo_data;
				$demo_site_url = str_replace( '/', '\/', $demo_site_url );
				if ( ! is_array( $data ) ) {
					$data = str_replace( $demo_site_url, $site_url, $data );
				} else {
					$data = wp_json_encode( $data, true );
					if ( ! empty( $data ) ) {
						$data          = str_replace( $demo_site_url, $site_url, $data );
						$data          = json_decode( $data, true );
					}
				}
			}

			// Update processed meta.
			update_metadata( 'post', $post_id, '_elementor_data', $data );

			// !important, Clear the cache after images import.
			Plugin::$instance->posts_css_manager->clear_cache();

			return $data;
		}

		return array();
	}

}
