<?php
/**
 * Class Responsive Ready Sites WXR Importer
 *
 * @since  1.0.0
 * @package Responsive Addon
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Responsive_Ready_Sites_WXR_Importer' ) ) :
	/**
	 * Class Responsive_Ready_Sites_WXR_Importer
	 *
	 * @since  1.0.0
	 */
	class Responsive_Ready_Sites_WXR_Importer {

		/**
		 * Instance of Responsive_Ready_Sites_WXR_Importer
		 *
		 * @since  1.0.0
		 * @var Responsive_Ready_Sites_WXR_Importer
		 */
		private static $instance = null;

		/**
		 * Instantiate Responsive_Ready_Sites_WXR_Importer
		 *
		 * @since  1.0.0
		 * @return (Object) Responsive_Ready_Sites_WXR_Importer.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since  1.0.0
		 */
		private function __construct() {

			require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';

			$responsive_ready_sites_wxr_importers_dir = plugin_dir_path( __FILE__ );

			require_once $responsive_ready_sites_wxr_importers_dir . 'class-wp-importer-logger.php';
			require_once $responsive_ready_sites_wxr_importers_dir . 'class-wp-importer-logger-serversentevents.php';
			require_once $responsive_ready_sites_wxr_importers_dir . 'class-wxr-importer.php';
			require_once $responsive_ready_sites_wxr_importers_dir . 'class-wxr-import-info.php';

			add_filter( 'upload_mimes', array( $this, 'add_mime_type_xml_and_json' ) );
			add_action( 'wp_ajax_responsive-wxr-import', array( $this, 'stream_import' ) );
			add_filter( 'wxr_importer.pre_process.user', '__return_null' );

			if ( version_compare( get_bloginfo( 'version' ), '5.1.0', '>=' ) ) {
				add_filter( 'wp_check_filetype_and_ext', array( $this, 'real_mime_types_5_1_0' ), 10, 5 );
			} else {
				add_filter( 'wp_check_filetype_and_ext', array( $this, 'real_mime_types' ), 10, 4 );
			}

		}

		/**
		 * Real Mime Type
		 *
		 * @since 1.0.0
		 *
		 * @param array  $defaults File data array.
		 * @param string $file                      Full path to the file.
		 * @param string $filename                  The name of the file.
		 * @param array  $mimes                     Key is the file extension with value as the mime type.
		 * @param string $real_mime                Real MIME type of the uploaded file.
		 */
		public function real_mime_types_5_1_0( $defaults, $file, $filename, $mimes, $real_mime ) {
			return $this->real_mimes( $defaults, $filename );
		}

		/**
		 * Real Mime Type
		 *
		 * @since 1.0.0
		 *
		 * @param array  $defaults File data array.
		 * @param string $file                      Full path to the file.
		 * @param string $filename                  The name of the file.
		 * @param array  $mimes                     Key is the file extension with value as the mime type.
		 */
		public function real_mime_types( $defaults, $file, $filename, $mimes ) {
			return $this->real_mimes( $defaults, $filename );
		}

		/**
		 * Real Mime Type
		 *
		 * @since 1.0.0
		 *
		 * @param array  $defaults File data array.
		 * @param string $filename                  The name of the file.
		 */
		public function real_mimes( $defaults, $filename ) {

			// Set EXT and real MIME type only for the file name `wxr.xml`.
			if ( 'wxr.xml' === $filename ) {
				$defaults['ext']  = 'xml';
				$defaults['type'] = 'text/xml';
			}

			// Set EXT and real MIME type only for the file name `wpforms.json`.
			if ( 'wpforms.json' === $filename ) {
				$defaults['ext']  = 'json';
				$defaults['type'] = 'text/plain';
			}

			return $defaults;
		}

		/**
		 * Importer
		 *
		 * @since  1.0.0
		 */
		public function stream_import() {

			check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( __( 'User does not have permission!', 'responsive-addons' ) );
			}
			// Start the event stream.
			header( 'Content-Type: text/event-stream, charset=UTF-8' );

			// Turn off PHP output compression.
			$previous = error_reporting( error_reporting() ^ E_WARNING ); // phpcs:ignore
			ini_set( 'output_buffering', 'off' ); // phpcs:ignore
			ini_set( 'zlib.output_compression', false ); //// phpcs:ignore
			error_reporting( $previous ); //// phpcs:ignore

			if ( $GLOBALS['is_nginx'] ) {
				// Setting this header instructs Nginx to disable fastcgi_buffering
				// and disable gzip for this request.
				header( 'X-Accel-Buffering: no' );
				header( 'Content-Encoding: none' );
			}

			$xml_url = urldecode( $_REQUEST['xml_path'] ); //phpcs:ignore
			if ( empty( $xml_url ) ) {
				exit;
			}

			// 2KB padding for IE
			echo ':' . str_repeat( ' ', 2048 ) . "\n\n"; //phpcs:ignore

			// Time to run the import!
			set_time_limit( 0 );

			// Ensure we're not buffered.
			wp_ob_end_flush_all();
			flush();

			// Are we allowed to create users?
			add_filter( 'wxr_importer.pre_process.user', '__return_null' );

			// Keep track of our progress.
			add_action( 'wxr_importer.processed.post', array( $this, 'imported_post' ), 10, 2 );
			add_action( 'wxr_importer.process_failed.post', array( $this, 'imported_post' ), 10, 2 );
			add_action( 'wxr_importer.process_already_imported.post', array( $this, 'already_imported_post' ), 10, 2 );
			add_action( 'wxr_importer.process_skipped.post', array( $this, 'already_imported_post' ), 10, 2 );
			add_action( 'wxr_importer.processed.comment', array( $this, 'imported_comment' ) );
			add_action( 'wxr_importer.process_already_imported.comment', array( $this, 'imported_comment' ) );
			add_action( 'wxr_importer.processed.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.process_failed.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.process_already_imported.term', array( $this, 'imported_term' ) );
			add_action( 'wxr_importer.processed.user', array( $this, 'imported_user' ) );
			add_action( 'wxr_importer.process_failed.user', array( $this, 'imported_user' ) );

			// Keep track of our progress.
			add_action( 'wxr_importer.processed.post', array( $this, 'track_post' ) );
			add_action( 'wxr_importer.processed.term', array( $this, 'track_term' ) );

			// Flush once more.
			flush();

			$importer = $this->get_importer();
			$response = $importer->import( $xml_url );

			// Let the browser know we're done.
			$complete = array(
				'action' => 'complete',
				'error'  => false,
			);
			if ( is_wp_error( $response ) ) {
				$complete['error'] = $response->get_error_message();
			}

			$this->emit_sse_message( $complete );
			exit;
		}

		/**
		 * Add .xml files as supported format in the uploader.
		 *
		 * @param array $mimes Already supported mime types.
		 */
		public function add_mime_type_xml_and_json( $mimes ) {

			// Allow XML files.
			$mimes['xml'] = 'text/xml';

			// Allow JSON files.
			$mimes['json'] = 'application/json';

			return $mimes;
		}

		/**
		 * Start the xml import.
		 *
		 * @since  1.0.0
		 *
		 * @param  (String) $path Absolute path to the XML file.
		 */
		public function get_xml_data( $path ) {

			$args = array(
				'action'      => 'responsive-wxr-import',
				'id'          => '1',
				'xml_path'    => $path,
				'_ajax_nonce' => wp_create_nonce( 'responsive-addons' ),
			);
			$url  = add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php' ) );

			$data = $this->get_data( $path );

			return array(
				'count'   => array(
					'posts'    => $data->post_count,
					'media'    => $data->media_count,
					'users'    => count( $data->users ),
					'comments' => $data->comment_count,
					'terms'    => $data->term_count,
				),
				'url'     => $url,
				'strings' => array(
					'complete' => __( 'Import complete!', 'responsive-addons' ),
				),
			);
		}

		/**
		 * Get XML data.
		 *
		 * @since 1.0.0
		 * @param  string $url Downloaded XML file absolute URL.
		 * @return array  XML file data.
		 */
		public function get_data( $url ) {
			$importer = $this->get_importer();
			$data     = $importer->get_preliminary_information( $url );
			if ( is_wp_error( $data ) ) {
				return $data;
			}
			return $data;
		}

		/**
		 * Get Importer
		 *
		 * @since 1.0.0
		 * @return object   Importer object.
		 */
		public function get_importer() {
			$options = apply_filters(
				'responsive_sites_xml_import_options',
				array(
					'fetch_attachments' => true,
					'default_author'    => get_current_user_id(),
				)
			);

			$importer = new WXR_Importer( $options );
			$logger   = new WP_Importer_Logger_ServerSentEvents();

			$importer->set_logger( $logger );
			return $importer;
		}

		/**
		 * Send message when a post has been imported.
		 *
		 * @since 1.0.0
		 * @param int   $id Post ID.
		 * @param array $data Post data saved to the DB.
		 */
		public function imported_post( $id, $data ) {
			$this->emit_sse_message(
				array(
					'action' => 'updateDelta',
					'type'   => ( 'attachment' === $data['post_type'] ) ? 'media' : 'posts',
					'delta'  => 1,
				)
			);
		}

		/**
		 * Send message when a post is marked as already imported.
		 *
		 * @since 1.0.0
		 * @param array $data Post data saved to the DB.
		 */
		public function already_imported_post( $data ) {
			$this->emit_sse_message(
				array(
					'action' => 'updateDelta',
					'type'   => ( 'attachment' === $data['post_type'] ) ? 'media' : 'posts',
					'delta'  => 1,
				)
			);
		}

		/**
		 * Send message when a comment has been imported.
		 *
		 * @since 1.0.0
		 */
		public function imported_comment() {
			$this->emit_sse_message(
				array(
					'action' => 'updateDelta',
					'type'   => 'comments',
					'delta'  => 1,
				)
			);
		}

		/**
		 * Send message when a term has been imported.
		 *
		 * @since 1.0.0
		 */
		public function imported_term() {
			$this->emit_sse_message(
				array(
					'action' => 'updateDelta',
					'type'   => 'terms',
					'delta'  => 1,
				)
			);
		}

		/**
		 * Send message when a user has been imported.
		 *
		 * @since 1.0.0
		 */
		public function imported_user() {
			$this->emit_sse_message(
				array(
					'action' => 'updateDelta',
					'type'   => 'users',
					'delta'  => 1,
				)
			);
		}

		/**
		 * Emit a Server-Sent Events message.
		 *
		 * @since 1.0.0
		 * @param mixed $data Data to be JSON-encoded and sent in the message.
		 */
		public function emit_sse_message( $data ) {
			echo "event: message\n";
			echo 'data: ' . wp_json_encode( $data ) . "\n\n";

			// Extra padding.
			echo ':' . str_repeat( ' ', 2048 ) . "\n\n"; //phpcs:ignore

			flush();
		}

		/**
		 * Track Imported Post
		 *
		 * @param  int $post_id Post ID.
		 * @return void
		 */
		public function track_post( $post_id ) {
			Responsive_Ready_Sites_Importer_Log::add( 'Inserted - Post ' . $post_id . ' - ' . get_post_type( $post_id ) . ' - ' . get_the_title( $post_id ) );
			update_post_meta( $post_id, '_responsive_ready_sites_imported_post', true );
		}

		/**
		 * Track Imported Term
		 *
		 * @param  int $term_id Term ID.
		 * @return void
		 */
		public function track_term( $term_id ) {
			$term = get_term( $term_id );
			if ( $term ) {
				Responsive_Ready_Sites_Importer_Log::add( 'Inserted - Term ' . $term_id . ' - ' . wp_json_encode( $term ) );
			}
			update_term_meta( $term_id, '_responsive_ready_sites_imported_term', true );
		}

	}

	/**
	 * Initialized by calling 'instance()' method
	 */
	Responsive_Ready_Sites_WXR_Importer::instance();

endif;
