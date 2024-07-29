<?php
/**
 * Batch Processing Importer
 *
 * @package Responsive Addons
 * @since 2.5.0
 */

if ( ! class_exists( 'Responsive_Ready_Sites_Batch_Processing_Importer' ) ) :

	/**
	 * Responsive Ready Sites Batch Processing Importer
	 *
	 * @since 2.5.0
	 */
	class Responsive_Ready_Sites_Batch_Processing_Importer {

		/**
		 * Instance
		 *
		 * @since 2.5.0
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * API Url
		 *
		 * @since 2.5.0
		 * @var   string API Url
		 */
		public static $api_url;

		/**
		 * Initiator
		 *
		 * @since 2.5.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.5.0
		 */
		public function __construct() {
			self::set_api_url();
		}

		/**
		 * Import
		 *
		 * @since 2.5.0
		 *
		 * @param  integer $page Page number.
		 * @return array
		 */
		public function import_sites( $page = 1 ) {

			$api_args        = array(
				'timeout' => 60,
			);
			$sites_and_pages = array();

			$query_args = apply_filters(
				'cyb_sites_import_sites_query_args',
				array(
					'per_page' => 15,
					'page'     => $page,
				)
			);

			$api_url = add_query_arg( $query_args, self::$api_url . 'cyberchimps-sites' );

			$response = wp_remote_get( $api_url, $api_args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$sites_and_pages = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $sites_and_pages['code'] ) ) {
					$message = isset( $sites_and_pages['message'] ) ? $sites_and_pages['message'] : '';
					return $message;
				} else {

					foreach ( $sites_and_pages as $key => $site ) {
						$sites_and_pages[ 'id-' . $site['id'] ] = $site;
						unset( $sites_and_pages[ $key ] );
					}
					update_site_option( 'responsive-ready-sites-and-pages-page-' . $page, $sites_and_pages );
				}
			} else {
				error_log( 'API Error: ' . $response->get_error_message() );
			}

			return $sites_and_pages;
		}

		/**
		 * Import Blocks.
		 *
		 * @since 2.5.0
		 *
		 * @param  integer $page Page number.
		 * @return array
		 */
		public function import_blocks( $page = 1 ) {

			$api_args = array(
				'timeout' => 60,
			);
			$blocks   = array();

			$query_args = apply_filters(
				'rst_import_block_query_args',
				array(
					'per_page' => 15,
					'page'     => $page,
				)
			);

			$api_url = add_query_arg( $query_args, self::$api_url . 'cyberchimps-blocks' );

			$response = wp_remote_get( $api_url, $api_args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$blocks = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $blocks['code'] ) ) {
					$message = isset( $blocks['message'] ) ? $blocks['message'] : '';
					return $message;
				} else {
					foreach ( $blocks as $key => $site ) {
						$blocks[ 'id-' . $site['id'] ] = $site;
						unset( $blocks[ $key ] );
					}
					update_site_option( 'rst-blocks-page-' . $page, $blocks );
				}
			} else {
				error_log( 'API Error: ' . $response->get_error_message() );
			}

			return $blocks;
		}

		/**
		 * Get an instance of WP_Filesystem_Direct.
		 *
		 * @since 2.5.0
		 * @return object A WP_Filesystem_Direct instance.
		 */
		public static function get_filesystem() {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		}

		/**
		 * Setter for $api_url
		 *
		 * @since  2.5.0
		 */
		public static function set_api_url() {
			self::$api_url = apply_filters( 'responsive_ready_sites_api_url', 'https://ccreadysites.cyberchimps.com/wp-json/wp/v2/' );
		}
	}

	/**
	 * Initiating by calling 'get_instance()' method
	 */
	Responsive_Ready_Sites_Batch_Processing_Importer::get_instance();

endif;
