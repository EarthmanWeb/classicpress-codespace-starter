<?php
/**
 * Batch Processing
 *
 * @package Responsive Addons
 * @since 1.0.14
 */

if ( ! class_exists( 'Responsive_Ready_Sites_Batch_Processing' ) ) :

	/**
	 * Responsive_Ready_Sites_Batch_Processing
	 *
	 * @since 2.0.0
	 */
	class Responsive_Ready_Sites_Batch_Processing {

		/**
		 * Instance
		 *
		 * @since 1.0.14
		 * @var object Class object.
		 * @access private
		 */
		private static $instance;

		/**
		 * Process All
		 *
		 * @since 1.0.14
		 * @var object Class object.
		 * @access public
		 */
		public static $process_all;

		/**
		 * Process Single Page
		 *
		 * @since 2.0.8
		 * @var object Class object.
		 * @access public
		 */
		public static $process_single;

		/**
		 * API Url
		 *
		 * @since 2.5.0
		 * @var   string API Url
		 */
		public static $api_url;

		/**
		 * Last Export Checksums
		 *
		 * @since 2.5.0
		 * @var object Class object.
		 * @access public
		 */
		public $last_xml_export_checksums;

		/**
		 * Initiator
		 *
		 * @since 1.0.14
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
		 * @since 1.0.14
		 */
		public function __construct() {

			// Core Helpers - Image.
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$responsive_ready_sites_batch_processing = plugin_dir_path( __FILE__ );

			require_once $responsive_ready_sites_batch_processing . 'helpers/class-responsive-ready-sites-image-importer.php';

			// Core Helpers - Batch Processing.
			require_once $responsive_ready_sites_batch_processing . 'helpers/class-wp-async-request.php';
			require_once $responsive_ready_sites_batch_processing . 'helpers/class-wp-background-process.php';
			require_once $responsive_ready_sites_batch_processing . 'helpers/class-wp-background-process-responsive.php';
			require_once $responsive_ready_sites_batch_processing . 'helpers/class-wp-background-process-responsive-single.php';

			// Prepare Page Builders.
			require_once $responsive_ready_sites_batch_processing . 'class-responsive-ready-sites-batch-processing-elementor.php';
			require_once $responsive_ready_sites_batch_processing . 'class-responsive-ready-sites-batch-processing-gutenberg.php';

			// Batch Processing Importer.
			require_once $responsive_ready_sites_batch_processing . 'class-responsive-ready-sites-batch-processing-importer.php';

			// Menu fix.
			require_once $responsive_ready_sites_batch_processing . 'class-responsive-ready-sites-batch-processing-menu.php';

			self::$process_all    = new WP_Background_Process_Responsive();
			self::$process_single = new WP_Background_Process_Responsive_Single();

			// Start image importing after site import complete.
			add_action( 'responsive_ready_sites_import_complete', array( $this, 'start_process' ) );

			add_action( 'responsive_ready_sites_process_template', array( $this, 'start_process_page' ) );

			add_action( 'wp_ajax_responsive-ready-sites-import-sites', array( $this, 'import_sites' ) );

			add_action( 'wp_ajax_responsive-sites-get-sites-request-count', array( $this, 'ready_sites_requests_count' ) );

			add_action( 'wp_ajax_responsive-ready-sites-update-templates-library', array( $this, 'update_sites_library' ) );

			add_action( 'wp_ajax_responsive-ready-sites-update-templates-library-complete', array( $this, 'update_templates_library_complete' ) );

			add_action( 'wp_ajax_rst-blocks-requests-count', array( $this, 'rst_blocks_requests_count' ) );

			add_action( 'wp_ajax_rst-import-blocks', array( $this, 'import_blocks' ) );

			add_action( 'admin_head', array( $this, 'initialize_ready_sites_templates_importer' ) );

			self::set_api_url();
		}

		/**
		 * Start Image Import
		 *
		 * @since 1.0.14
		 *
		 * @return void
		 */
		public function start_process() {

			Responsive_Ready_Sites_Importer_Log::add( 'Batch Process Started!' );

			// Add "misc" in import [queue].
			self::$process_all->push_to_queue( Responsive_Ready_Sites_Batch_Processing_Menu::get_instance() );
			
			// Add "gutenberg" in import queue.
			self::$process_all->push_to_queue( Responsive_Ready_Sites_Batch_Processing_Gutenberg::get_instance() );

			// Add "elementor" in import [queue].
			// @todo Remove required `allow_url_fopen` support.
			if ( ini_get( 'allow_url_fopen' ) ) {
				if ( is_plugin_active( 'elementor/elementor.php' ) ) {
					$import = new \Elementor\TemplateLibrary\Responsive_Ready_Sites_Batch_Processing_Elementor();
					self::$process_all->push_to_queue( $import );
				}
			}

			// Dispatch Queue.
			self::$process_all->save()->dispatch();
		}

		/**
		 * Start Single Page Import
		 *
		 * @param  int $page_id Page ID .
		 * @since 2.0.8
		 * @return void
		 */
		public function start_process_page( $page_id ) {

			// Add "gutenberg" in import queue.
			self::$process_single->push_to_queue(
				array(
					'page_id'  => $page_id,
					'instance' => Responsive_Ready_Sites_Batch_Processing_Gutenberg::get_instance(),
				)
			);

			if ( is_plugin_active( 'elementor/elementor.php' ) ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();

				$import = new \Elementor\TemplateLibrary\Responsive_Ready_Sites_Batch_Processing_Elementor();
				self::$process_single->push_to_queue(
					array(
						'page_id'  => $page_id,
						'instance' => $import,
					)
				);
			}

			// Dispatch Queue.
			self::$process_single->save()->dispatch();
		}

		/**
		 * Get all post id's
		 *
		 * @since 1.0.14
		 *
		 * @param  array $post_types Post types.
		 * @return array
		 */
		public static function get_pages( $post_types = array() ) {

			if ( $post_types ) {
				$args = array(
					'post_type'      => $post_types,

					// Query performance optimization.
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				);

				$query = new WP_Query( $args );

				// Have posts?
				if ( $query->have_posts() ) :

					return $query->posts;

				endif;
			}

			return null;
		}

		/**
		 * Get Supporting Post Types..
		 *
		 * @since 1.3.7
		 * @param  integer $feature Feature.
		 * @return array
		 */
		public static function get_post_types_supporting( $feature ) {
			global $_wp_post_type_features;

			$post_types = array_keys(
				wp_filter_object_list( $_wp_post_type_features, array( $feature => true ) )
			);

			return $post_types;
		}

		/**
		 * Import Sites
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function import_sites() {
			$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( $page_no ) {
				$sites_and_pages = Responsive_Ready_Sites_Batch_Processing_Importer::get_instance()->import_sites( $page_no );

				wp_send_json_success( $sites_and_pages );
			}

			wp_send_json_error();
		}

		/**
		 * Import Blocks
		 *
		 * @since 2.9.1
		 * @return void
		 */
		public function import_blocks() {
			$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( $page_no ) {
				$blocks = Responsive_Ready_Sites_Batch_Processing_Importer::get_instance()->import_blocks( $page_no );
				wp_send_json_success( $blocks );
			}
			wp_send_json_error();
		}

		/**
		 * Update Library Complete
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function update_templates_library_complete() {
			$this->update_latest_checksums();
			wp_send_json_success();
		}

		/**
		 * Update Latest Checksums
		 *
		 * Store latest checksum after batch complete.
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function update_latest_checksums() {
			$latest_checksums = get_site_option( 'responsive-ready-sites-last-xml-export-checksums-latest', '' );
			update_site_option( 'responsive-ready-sites-last-xml-export-checksums', $latest_checksums );
		}

		/**
		 * Get Total Requests
		 *
		 * @since 2.5.0
		 * @return integer
		 */
		public function get_total_requests() {

			$api_args = array(
				'timeout' => 60,
			);

			$api_url = self::$api_url . 'get-ready-sites-requests-count/?per_page=15';

			$response = wp_remote_get( $api_url, $api_args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {

				$total_requests = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $total_requests ) ) {

					update_site_option( 'responsive-ready-sites-requests', $total_requests );

					return $total_requests;
				}
			}

			$this->get_total_requests();
		}

		/**
		 * Get Total RST Blocks Requests
		 *
		 * @since 2.9.1
		 * @return integer
		 */
		public function get_total_rst_blocks_requests() {

			$api_args = array(
				'timeout' => 60,
			);

			$api_url = self::$api_url . 'get-rst-blocks-requests-count2/?per_page=15';

			$response = wp_remote_get( $api_url, $api_args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {

				$total_requests = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $total_requests ) ) {

					update_site_option( 'rst-blocks-requests', $total_requests );

					return $total_requests;
				}
			}

			$this->get_total_rst_blocks_requests();
		}

		/**
		 * Sites Requests Count
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function ready_sites_requests_count() {

			// Get count.
			$total_requests = $this->get_total_requests();
			if ( $total_requests ) {
				wp_send_json_success( $total_requests );
			}

			wp_send_json_error();
		}

		/**
		 * RST Blocks Requests Count
		 *
		 * @since 2.9.1
		 * @return void
		 */
		public function rst_blocks_requests_count() {

			// Get count.
			$total_requests = $this->get_total_rst_blocks_requests();
			if ( $total_requests ) {
				wp_send_json_success( $total_requests );
			}

			wp_send_json_error();
		}

		/**
		 * Setter for $api_url
		 *
		 * @since  2.5.0
		 */
		public static function set_api_url() {
			self::$api_url = apply_filters( 'responsive_ready_sites_api_url', 'https://ccreadysites.cyberchimps.com/wp-json/wp/v2/' );
		}

		/**
		 * Update Sites Library
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function update_sites_library() {

			if ( 'no' === $this->get_last_export_checksums() ) {
				wp_send_json_success( 'updated' );
			}

			$import_with = 'ajax';

			wp_send_json_success( $import_with );
		}

		/**
		 * Get Last Exported Checksum Status
		 *
		 * @since 2.5.0
		 * @return string Checksums Status.
		 */
		public function get_last_export_checksums() {

			$old_last_export_checksums = get_site_option( 'responsive-ready-sites-last-xml-export-checksums', '' );

			$new_last_export_checksums = $this->set_last_export_checksums();

			$checksums_status = 'no';

			if ( empty( $old_last_export_checksums ) ) {
				$checksums_status = 'yes';
			}

			if ( $new_last_export_checksums !== $old_last_export_checksums ) {
				$checksums_status = 'yes';
			}

			return apply_filters( 'responsive_ready_sites_checksums_status', $checksums_status );
		}

		/**
		 * Set Last Exported Checksum
		 *
		 * @since 2.5.0
		 * @return string Checksums Status.
		 */
		public function set_last_export_checksums() {

			if ( ! empty( $this->last_export_checksums ) ) {
				return $this->last_export_checksums;
			}

			$api_args = array(
				'timeout' => 60,
			);

			$response = wp_remote_get( self::$api_url . 'get-last-xml-export-checksum2', $api_args );

			if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
				$result = json_decode( wp_remote_retrieve_body( $response ), true );

				// Set last export checksums.
				if ( ! empty( $result['last_xml_export_checksums'] ) ) {
					update_site_option( 'responsive-ready-sites-last-xml-export-checksums-latest', $result['last_xml_export_checksums'] );

					$this->last_xml_export_checksums = $result['last_xml_export_checksums'];
				}
			}

			return $this->last_xml_export_checksums;
		}

		/**
		 * Start Importer
		 *
		 * @since 2.5.0
		 * @return void
		 */
		public function initialize_ready_sites_templates_importer() {

			$is_fresh_site = get_site_option( 'responsive-ready-sites-fresh-site', '' );

			// Process initially for the fresh user.
			if ( isset( $_GET['reset'] ) ) {
				// Process Ready sites data import.
				$this->process_batch();
			} elseif ( empty( $is_fresh_site ) ) {
				$this->process_batch();
				update_site_option( 'responsive-ready-sites-fresh-site', 'yes', 'no' );
			} else {
				$current_screen = get_current_screen();
				// return if not Responsive Ready Sites grid page.
				if ( ! is_object( $current_screen ) && null === $current_screen ) {
					return;
				}
				if ( 'toplevel_page_responsive_add_ons' === $current_screen->id ) {
					// Process import.
					$this->process_ready_sites_templates_data_import();
				}
			}
		}

		/**
		 * Process Import
		 *
		 * @since 2.5.0
		 *
		 * @return mixed Null if process is already started.
		 */
		public function process_ready_sites_templates_data_import() {

			// Batch is already started? Then return.
			$status = get_site_option( 'responsive-ready-sites-batch-status' );
			if ( 'in-process' === $status ) {
				return;
			}

			// Check batch expiry.
			$expired = get_transient( 'responsive-ready-sites-import-templates-check' );
			if ( false !== $expired ) {
				return;
			}

			// For 1 week.
			set_transient( 'responsive-ready-sites-import-templates-check', 'true', WEEK_IN_SECONDS );

			update_site_option( 'responsive-ready-sites-batch-status', 'in-process' );

			// Process batch.
			$this->process_batch();
		}

		/**
		 * Process Batch
		 *
		 * @since 2.5.0
		 * @return mixed
		 */
		public function process_batch() {

			if ( 'no' === $this->get_last_export_checksums() ) {
				return;
			}

			$dir = RESPONSIVE_ADDONS_DIR . 'includes/json/';

			if ( file_exists( $dir . 'responsive-sites-request.json' ) ) {
				$responsive_sites_request = (int) trim( file_get_contents( $dir . 'responsive-sites-request.json' ) );
				if ( 16 < (int) trim( $responsive_sites_request ) ) {
					return;
				}
				// Get count.
				if ( $responsive_sites_request ) {
					for ( $page = 1; $page <= $responsive_sites_request; $page++ ) {
						$sites_and_pages = array();
						if ( file_exists( $dir . 'responsive-ready-sites-and-pages-page-' . $page . '.json' ) ) {
							$file_contents   = file_get_contents( $dir . 'responsive-ready-sites-and-pages-page-' . $page . '.json' );
							$sites_and_pages = json_decode( $file_contents, true );
							update_site_option( 'responsive-ready-sites-and-pages-page-' . $page, $sites_and_pages );
						}
					}
				}
			}

			if ( file_exists( $dir . 'rst-blocks-requests.json' ) ) {
				$responsive_blocks_request = (int) trim( file_get_contents( $dir . 'rst-blocks-requests.json' ) );
				if ( $responsive_blocks_request ) {
					update_site_option( 'rst-blocks-requests', $responsive_blocks_request );
					for ( $page = 1; $page <= $responsive_blocks_request; $page++ ) {
						$blocks_pages = array();
						if ( file_exists( $dir . 'rst-blocks-page-' . $page . '.json' ) ) {
							$file_contents = file_get_contents( $dir . 'rst-blocks-page-' . $page . '.json' );
							$blocks_pages  = json_decode( $file_contents, true );
							update_site_option( 'rst-blocks-page-' . $page, $blocks_pages );
						}
					}
				}
			}

			delete_site_option( 'responsive-ready-sites-batch-status' );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Responsive_Ready_Sites_Batch_Processing::get_instance();

endif;
