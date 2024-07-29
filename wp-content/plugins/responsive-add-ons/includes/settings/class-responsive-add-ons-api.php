<?php
/**
 * Class Api file.
 *
 * @package Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Responsive_Add_Ons_Api.
 */
class Responsive_Add_Ons_Api extends WP_REST_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'cc/v1';

	/**
	 * Route Settings base.
	 *
	 * @var string
	 */
	protected $rest_settings_base = 'settings';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 10 );
	}

	/**
	 * Register the routes for app.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_settings_base,
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_items' ),
					'permission_callback' => array( $this, 'create_items_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_items( $request ) {
		require_once RESPONSIVE_ADDONS_DIR . 'includes/settings/class-responsive-add-ons-settings.php';
		$settings = new Responsive_Add_Ons_Settings();
		$data     = $request->get_param( 'data' );

		$settings->update( $data );
		return rest_ensure_response( array( 'success' => true ) );
	}

	/**
	 * Check if a given request has access to read items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function create_items_permissions_check( $request ) {

		$request_origin   = $request->get_header( 'Source' );
		$permission_check = false;
		$token            = $request->get_param( 'token' );
		$request_platform = $request->get_param( 'platform' );

		if ( CC_APP_URL === $request_origin && isset( $token ) && $request_platform === 'wordpress' ) {
			return true;
		} else {
			return new WP_Error( 'rest_forbidden', esc_html__( 'Invalid Authorization.', 'responsive-addons' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return $permission_check;
	}
}
