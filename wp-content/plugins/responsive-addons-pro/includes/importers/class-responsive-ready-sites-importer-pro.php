<?php
/** Pro
 * Responsive Ready Sites Importer
 *
 * @since   1.0.0
 * @package Responsive Ready Sites
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Responsive_Ready_Sites_Importer_Pro' ) ) :

	/**
	 * Responsive Ready Sites Importer
	 */
	class Responsive_Ready_Sites_Importer_Pro {


		/**
		 * Instance
		 *
		 * @since 1.0.0
		 * @var   (Object) Class object
		 */
		public static $instance = null;

		/**
		 * Set Instance
		 *
		 * @since 1.0.0
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( is_admin() ) {
				// Import AJAX.
				add_action( 'wp_ajax_responsive-ready-sites-import-set-site-data-pro', array( $this, 'import_start' ) );
				add_action( 'wp_ajax_responsive-ready-sites-required-plugins-pro', array( $this, 'required_plugin' ) );
			}
		}

		/**
		 * Start Site Import
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function import_start() {

			check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error( __( 'User does not have permission!', 'responsive-addons' ) );
			}

            $demo_api_uri = isset( $_POST['api_url'] ) ? esc_url( $_POST['api_url'] ) : ''; //phpcs:ignore

			if ( ! empty( $demo_api_uri ) ) {

				$demo_data = self::get_responsive_single_demo( $demo_api_uri );
				if ( ! $demo_data['success'] ) {
					wp_send_json( $demo_data );
				}

				update_option( 'responsive_ready_sites_import_data', $demo_data );

				if ( is_wp_error( $demo_data ) ) {
					wp_send_json_error( $demo_data->get_error_message() );
				} else {
					do_action( 'responsive_ready_sites_import_start', $demo_data, $demo_api_uri );
				}

				wp_send_json_success( $demo_data );

			} else {
				wp_send_json_error( __( 'Request site API URL is empty. Try again!', 'responsive-addons' ) );
			}

		}

		/**
		 * Get single demo.
		 *
		 * @since 1.0.0
		 *
		 * @param (String) $demo_api_uri API URL of a demo.
		 *
		 * @return (Array) $responsive_demo_data demo data for the demo.
		 */
		public static function get_responsive_single_demo( $demo_api_uri ) {

			// default values.
			$remote_args = array();
			$defaults    = array(
				'id'                   => '',
				'xml_path'             => '',
				'wpforms_path'         => '',
				'site_customizer_data' => '',
				'required_plugins'     => '',
				'required_plugins'     => '',
				'site_widgets_data'    => '',
				'slug'                 => '',
				'site_options_data'    => '',
			);

			$api_args = apply_filters(
				'responsive_sites_api_args',
				array(
					'timeout' => 15,
				)
			);

			// Use this for pro demos.
			$request_params = apply_filters(
				'responsive_sites_api_params',
				array(
					'site_url' => site_url(),
				)
			);

			$demo_api_uri = add_query_arg( $request_params, $demo_api_uri );

			// API Call.
			$response = wp_remote_get( $demo_api_uri, $api_args );

			if ( is_wp_error( $response ) || ( isset( $response->status ) && 0 === $response->status ) ) {
				if ( isset( $response->status ) ) {
					$data = json_decode( $response, true );
				} else {
					return new WP_Error( 'api_invalid_response_code', $response->get_error_message() );
				}
			} else {
				$data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! $data['success'] ) {
					return $data;
				}
			}

			$rbea_plugin = array(
				'name' => 'Responsive Block Editor Addons',
				'slug' => 'responsive-block-editor-addons',
				'init' => 'responsive-block-editor-addons/responsive-block-editor-addons.php',
			);
			$plugin_exists = false;
			foreach ($data['required_plugins'] as $existing_plugin) {
				if ($existing_plugin['slug'] === $rbea_plugin['slug']) {
					$plugin_exists = true;
					break;
				}
			}
			if (!$plugin_exists) {
				$data['required_plugins'][] = $rbea_plugin;
			}

			if ( ! isset( $data['code'] ) ) {
				$remote_args['id']                   = $data['id'];
				$remote_args['xml_path']             = $data['xml_path'];
				$remote_args['wpforms_path']         = $data['wpforms_path'];
				$remote_args['site_customizer_data'] = $data['site_customizer_data'];
				$remote_args['required_plugins']     = $data['required_plugins'];
				$remote_args['required_pro_plugins'] = $data['required_pro_plugins'];
				$remote_args['site_widgets_data']    = json_decode( $data['site_widgets_data'] );
				$remote_args['site_options_data']    = $data['site_options_data'];
				$remote_args['slug']                 = $data['slug'];
				$remote_args['featured_image_url']   = $data['featured_image_url'];
				$remote_args['title']                = $data['title']['rendered'];
				$remote_args['success']              = true;
			}

			// Merge remote demo and defaults.
			return wp_parse_args( $remote_args, $defaults );
		}

		/**
		 * Required Plugin
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function required_plugin() {

			// Verify Nonce.
			check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

			$response = array(
				'active'       => array(),
				'inactive'     => array(),
				'notinstalled' => array(),
				'proplugins'   => array(),
			);

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( $response );
			}

			$required_plugins     = ( isset( $_POST['required_plugins'] ) ) ? $_POST['required_plugins'] : array();
			$required_pro_plugins = ( isset( $_POST['required_pro_plugins'] ) ) ? $_POST['required_pro_plugins'] : array();

			if ( is_array( $required_plugins ) && count( $required_plugins ) > 0 ) {
				foreach ( $required_plugins as $key => $plugin ) {

					if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) && is_plugin_inactive( $plugin['init'] ) ) {

						$response['inactive'][] = $plugin;

					} elseif ( ! file_exists( WP_PLUGIN_DIR . '/' . $plugin['init'] ) ) {

						$response['notinstalled'][] = $plugin;

					} else {
						$response['active'][] = $plugin;
					}
				}
			}

			if ( is_array( $required_pro_plugins ) && count( $required_pro_plugins ) > 0 ) {
				foreach ( $required_pro_plugins as $key => $plugin ) {
					$response['proplugins'][] = $plugin;
				}
			}

			// Send response.
			wp_send_json_success(
				array(
					'required_plugins' => $response,
				)
			);
		}
	}

	/**
	 * Initialized by calling 'get_instance()' method
	 */
	Responsive_Ready_Sites_Importer_Pro::get_instance();

endif;
