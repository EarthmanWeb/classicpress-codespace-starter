<?php
/**
 * Responsive Ready Sites Importer Log
 *
 * @since   2.2.0
 * @package Responsive Ready Sites
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Responsive_Ready_Sites_Importer_Log' ) ) :

	/**
	 * Responsive Ready Sites Importer
	 */
	class Responsive_Ready_Sites_Importer_Log {
		/**
		 * Instance
		 *
		 * @since 2.2.0
		 * @var (Object) Class object
		 */
		private static $_instance = null;

		/**
		 * Log File
		 *
		 * @since 2.2.0
		 * @var (Object) Class object
		 */
		private static $log_file = null;

		/**
		 * Set Instance
		 *
		 * @since 2.2.0
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 2.2.0
		 */
		private function __construct() {

			// Check file read/write permissions.
			add_action( 'admin_init', array( $this, 'has_file_read_write' ) );

		}

		/**
		 * Check file read/write permissions and process.
		 *
		 * @since 2.2.0
		 * @return null
		 */
		public function has_file_read_write() {

			// Get user credentials for WP file-system API.
			$responsive_ready_sites_import = wp_nonce_url( admin_url( 'themes.php?page=responsive-add-ons' ), 'responsive-add-ons' );
			$creds                         = request_filesystem_credentials( $responsive_ready_sites_import, '', false, false, null );
			if ( false === $creds ) {
				return;
			}

			// Set log file.
			self::set_log_file();

			// Initial AJAX Import Hooks.
			add_action( 'responsive_ready_sites_import_start', array( $this, 'start' ), 10, 2 );
		}

		/**
		 * Add log file URL in UI response.
		 *
		 * @since 2.2.0
		 */
		public static function add_log_file_url() {

			$upload_dir   = self::log_dir();
			$upload_path  = trailingslashit( $upload_dir['url'] );
			$file_abs_url = get_option( 'responsive_ready_sites_recent_import_log_file', self::$log_file );
			$file_url     = $upload_path . basename( $file_abs_url );

			return array(
				'abs_url' => $file_abs_url,
				'url'     => $file_url,
			);
		}

		/**
		 * Current Time for log.
		 *
		 * @since 2.2.0
		 * @return string Current time with time zone.
		 */
		public static function current_time() {
			return gmdate( 'H:i:s' ) . ' ' . date_default_timezone_get();
		}

		/**
		 * Import Start
		 *
		 * @since 2.2.0
		 * @param  array  $data         Import Data.
		 * @param  string $demo_api_uri Import site API URL.
		 * @return void
		 */
		public function start( $data = array(), $demo_api_uri = '' ) {

			self::add( 'Started Import Process' );

			self::add( '# System Details: ' );
			self::add( "Debug Mode \t\t: " . self::get_debug_mode() );
			self::add( "Operating System \t: " . self::get_os() );
			self::add( "Software \t\t: " . self::get_software() );
			self::add( "MySQL version \t\t: " . self::get_mysql_version() );
			self::add( "XML Reader \t\t: " . self::get_xmlreader_status() );
			self::add( "PHP Version \t\t: " . self::get_php_version() );
			self::add( "PHP Max Input Vars \t: " . self::get_php_max_input_vars() );
			self::add( "PHP Max Post Size \t: " . self::get_php_max_post_size() );
			self::add( "PHP Extension GD \t: " . self::get_php_extension_gd() );
			self::add( "PHP Max Execution Time \t: " . self::get_max_execution_time() );
			self::add( "Max Upload Size \t: " . size_format( wp_max_upload_size() ) );
			self::add( "Memory Limit \t\t: " . self::get_memory_limit() );
			self::add( "Timezone \t\t: " . self::get_timezone() );
			self::add( PHP_EOL . '-----' . PHP_EOL );
			self::add( 'Importing Started! - ' . self::current_time() );

			self::add( '---' . PHP_EOL );
		}

		/**
		 * Get an instance of WP_Filesystem_Direct.
		 *
		 * @since 2.2.0
		 * @return object A WP_Filesystem_Direct instance.
		 */
		public static function get_filesystem() {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		}

		/**
		 * Get Log File
		 *
		 * @since 2.2.0
		 * @return string log file URL.
		 */
		public static function get_log_file() {
			return self::$log_file;
		}

		/**
		 * Log file directory
		 *
		 * @since 2.2.0
		 * @param  string $dir_name Directory Name.
		 * @return array    Uploads directory array.
		 */
		public static function log_dir( $dir_name = 'responsive-add-ons' ) {

			$upload_dir = wp_upload_dir();

			// Build the paths.
			$dir_info = array(
				'path' => $upload_dir['basedir'] . '/' . $dir_name . '/',
				'url'  => $upload_dir['baseurl'] . '/' . $dir_name . '/',
			);

			$files = array(
				array(
					'base'    => $upload_dir['basedir'] . '/' . $dir_name,
					'file'    => '.htaccess',
					'content' => 'deny from all',
				),
			);

			foreach ( $files as $file ) {
				if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
					$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_read_fopen
					if ( $file_handle ) {
						fwrite( $file_handle, $file['content'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
						fclose( $file_handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
					}
				}
			}

			return $dir_info;
		}

		/**
		 * Set log file
		 *
		 * @since 2.2.0
		 */
		public static function set_log_file() {

			$upload_dir = self::log_dir();

			$upload_path = trailingslashit( $upload_dir['path'] );

			// File format e.g. 'import-31-Oct-2017-06-39-12.txt'.
			self::$log_file = $upload_path . 'import-' . gmdate( 'd-M-Y-h-i-s' ) . '.txt';

			if ( ! get_option( 'responsive_ready_sites_recent_import_log_file', false ) ) {
				update_option( 'responsive_ready_sites_recent_import_log_file', self::$log_file );
			}
		}

		/**
		 * Write content to a file.
		 *
		 * @since 2.2.0
		 * @param string $content content to be saved to the file.
		 */
		public static function add( $content ) {

			if ( get_option( 'responsive_ready_sites_recent_import_log_file', false ) ) {
				$log_file = get_option( 'responsive_ready_sites_recent_import_log_file', self::$log_file );
			} else {
				$log_file = self::$log_file;
			}

			$existing_data = '';
			if ( file_exists( $log_file ) ) {
				$existing_data = self::get_filesystem()->get_contents( $log_file );
			}

			// Style separator.
			$separator = PHP_EOL;

			self::get_filesystem()->put_contents( $log_file, $existing_data . $separator . $content, FS_CHMOD_FILE );
		}

		/**
		 * Debug Mode
		 *
		 * @since 2.2.0
		 * @return string Enabled for Debug mode ON and Disabled for Debug mode Off.
		 */
		public static function get_debug_mode() {
			if ( WP_DEBUG ) {
				return __( 'Enabled', 'responsive-addons' );
			}

			return __( 'Disabled', 'responsive-addons' );
		}

		/**
		 * Memory Limit
		 *
		 * @since 2.2.0
		 * @return string Memory limit.
		 */
		public static function get_memory_limit() {

			$required_memory                = '64M';
			$memory_limit_in_bytes_current  = wp_convert_hr_to_bytes( WP_MEMORY_LIMIT );
			$memory_limit_in_bytes_required = wp_convert_hr_to_bytes( $required_memory );

			if ( $memory_limit_in_bytes_current < $memory_limit_in_bytes_required ) {
				return sprintf(
				/* translators: %1$s Memory Limit, %2$s Recommended memory limit. */
					_x( 'Current memory limit %1$s. We recommend setting memory to at least %2$s.', 'Recommended Memory Limit', 'responsive-addons' ),
					WP_MEMORY_LIMIT,
					$required_memory
				);
			}

			return WP_MEMORY_LIMIT;
		}

		/**
		 * Timezone
		 *
		 * @since 2.2.0
		 * @see https://codex.wordpress.org/Option_Reference/
		 *
		 * @return string Current timezone.
		 */
		public static function get_timezone() {
			$timezone = get_option( 'timezone_string' );

			if ( ! $timezone ) {
				return get_option( 'gmt_offset' );
			}

			return $timezone;
		}

		/**
		 * XML Reader
		 *
		 * @since 2.2.0
		 * @return string Current XML Reader status.
		 */
		public static function get_xmlreader_status() {

			if ( class_exists( 'XMLReader' ) ) {
				return __( 'Yes', 'responsive-addons' );
			}

			return __( 'No', 'responsive-addons' );
		}

		/**
		 * Operating System
		 *
		 * @since 2.2.0
		 * @return string Current Operating System.
		 */
		public static function get_os() {
			return PHP_OS;
		}

		/**
		 * Server Software
		 *
		 * @since 2.2.0
		 * @return string Current Server Software.
		 */
		public static function get_software() {
			if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
				return sanitize_key( $_SERVER['SERVER_SOFTWARE'] );
			}
		}

		/**
		 * MySql Version
		 *
		 * @since 2.2.0
		 * @return string Current MySql Version.
		 */
		public static function get_mysql_version() {
			global $wpdb;
			return $wpdb->db_version();
		}

		/**
		 * PHP Version
		 *
		 * @since 2.2.0
		 * @return string Current PHP Version.
		 */
		public static function get_php_version() {
			if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
				return _x( 'We recommend to use php 5.4 or higher', 'PHP Version', 'responsive-addons' );
			}
			return PHP_VERSION;
		}

		/**
		 * PHP Max Input Vars
		 *
		 * @since 2.2.0
		 * @return string Current PHP Max Input Vars
		 */
		public static function get_php_max_input_vars() {
			return ini_get( 'max_input_vars' ); // phpcs:disable PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
		}

		/**
		 * PHP Max Post Size
		 *
		 * @since 2.2.0
		 * @return string Current PHP Max Post Size
		 */
		public static function get_php_max_post_size() {
			return ini_get( 'post_max_size' );
		}

		/**
		 * PHP Max Execution Time
		 *
		 * @since 2.2.0
		 * @return string Current Max Execution Time
		 */
		public static function get_max_execution_time() {
			return ini_get( 'max_execution_time' );
		}

		/**
		 * PHP GD Extension
		 *
		 * @since 2.2.0
		 * @return string Current PHP GD Extension
		 */
		public static function get_php_extension_gd() {
			if ( extension_loaded( 'gd' ) ) {
				return __( 'Yes', 'responsive-addons' );
			}

			return __( 'No', 'responsive-addons' );
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Responsive_Ready_Sites_Importer_Log::get_instance();
	endif;
