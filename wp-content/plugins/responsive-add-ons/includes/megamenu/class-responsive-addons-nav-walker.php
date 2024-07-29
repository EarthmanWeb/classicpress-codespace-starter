<?php
/**
 * Starting new Responsive_Addons_Nav_Walker class.
 *
 * @package Responsive WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to make changes in default WordPress menus.
 *
 * @since 2.3.0
 */
class Responsive_Addons_Nav_Walker {

	/**
	 * Constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		global $pagenow;
		// Add custom fields to menu.
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 4 );

		// Edit menu walker.
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_walker' ), 10, 2 );
		add_filter( 'responsive_nav_menu_arg', array( $this, 'responsive_nav_menu_arguments' ), 11, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_theme_script' ), 999 );
		if ( 'nav-menus.php' === $pagenow ) {
			add_action( 'admin_footer', array( $this, 'responsive_pro_mega_menu_modal' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_css' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts' ) );
		}
		add_action( 'rest_api_init', array( $this, 'responive_pro_rest_routes' ) );
	}

	/**
	 * Add custom megamenu fields data to the menu.
	 *
	 * @param [type] $id    [description].
	 * @param [type] $item  [description].
	 * @param [type] $depth [description].
	 * @param [type] $args  [description].
	 */
	public function add_custom_fields( $id, $item, $depth, $args ) {
		$item_title = isset( $item->title ) ? $item->title : '';
		$parent_id  = isset( $item->menu_item_parent ) ? $item->menu_item_parent : '';
		if ( 'on' === get_option( 'rpo_megamenu_enable' ) ) {
			?>
		<p class="description description-wide">
			<div id="responsive-megamenu-btn">
				<a class="button button-secondary button-large resp-pro-megamenu-button" data-depth="<?php echo esc_attr( $depth ); ?>" data-menu-id="<?php echo esc_attr( $id ); ?>" data-menu-title="<?php echo esc_attr( $item_title ); ?>" data-parent-id="<?php echo esc_attr( $parent_id ); ?>">
			<?php esc_html_e( 'Mega Menu Settings', 'responsive-add-ons' ); ?>
				</a>
			</div>
		</p>
			<?php
		}
	}

	/**
	 * Function to replace normal edit nav walker.
	 *
	 * @return string Class name of new navwalker
	 */
	public function edit_walker() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . '/megamenu/class-responsive-addons-walker-nav-menu-edit-custom.php';
		return 'Responsive_Addons_Walker_Nav_Menu_Edit_Custom';
	}

	/**
	 * [responsive_nav_menu_arg description].
	 *
	 * @return array [description].
	 */
	public function responsive_nav_menu_arguments() {

		return array(
			'container'      => false,
			'menu_id'        => 'header-menu',
			'fallback_cb'    => 'responsive_fallback_menu',
			'theme_location' => 'header-menu',
			'walker'         => new Responsive_Addons_Custom_Nav_Walker(),
		);
	}

	/**
	 * Frontend scripts.
	 *
	 * @since 1.0
	 *
	 * @return void.
	 */
	public function enqueue_frontend_scripts() {

		$js_gen_path = plugin_dir_url( __FILE__ ) . 'assets/';
		if ( 'on' === get_option( 'rpo_megamenu_enable' ) ) {
			if ( $this->is_megamenu_meta_active() ) {
				wp_enqueue_script( 'navigation-pro-scripts', $js_gen_path . 'navigation-pro.js', array( 'jquery' ), RESPONSIVE_ADDONS_VER, true );
				$mobile_menu_breakpoint = array( 'mobileBreakpoint' => get_theme_mod( 'responsive_mobile_menu_breakpoint', 767 ) );
				wp_localize_script( 'navigation-pro-scripts', 'responsive_breakpoint', $mobile_menu_breakpoint );
			}
			wp_enqueue_script( 'responsive-mega-menu', $js_gen_path . 'mega-menu.js', array( 'jquery' ), RESPONSIVE_ADDONS_VER, true );
		}
	}

	/**
	 * Frontend styles.
	 *
	 * @since 2.5.2
	 *
	 * @return void.
	 */
	public function enqueue_frontend_styles() {
		if ( 'on' === get_option( 'rpo_megamenu_enable' ) && $this->is_megamenu_meta_active() ) {
			wp_enqueue_style( 'responsive-pro-mega-menu-frontend-style', plugin_dir_url( __FILE__ ) . 'assets/megamenu-frontend.css', array(), RESPONSIVE_ADDONS_VER );
		}
		wp_enqueue_style( 'responsive-pro-mega-menu-style', plugin_dir_url( __FILE__ ) . 'assets/megamenu.css', array(), RESPONSIVE_ADDONS_VER );
	}

	/**
	 * Dequeue responsive theme navigation scripts.
	 *
	 * @since 2.5.2
	 *
	 * @return void.
	 */
	public function dequeue_theme_script() {
		if ( $this->is_megamenu_meta_active() ) {
			wp_dequeue_script( 'navigation-scripts' );
		}
	}

	/**
	 * Checks if megamenu is activated for atleast one menu.
	 *
	 * @since 2.5.2
	 *
	 * @return boolean.
	 */
	public function is_megamenu_meta_active() {
		global $wpdb;
		$meta_key   = '_menu_item_megamenu_resp_enable_megamenu';
		$meta_value = '1';
		$result     = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		return $result ? true : false;
	}

	/**
	 * Frontend scripts.
	 *
	 * @since 1.0
	 *
	 * @return void.
	 */
	public function enqueue_backend_css() {

		/* Directory and Extension */
		$suffix = '.min';

		if ( SCRIPT_DEBUG ) {
			$suffix = '';
		}

		wp_enqueue_style( 'responsive-pro-mega-menu-style', plugin_dir_url( __FILE__ ) . 'assets/menus.css', array( 'wp-components', 'dashicons' ), RESPONSIVE_ADDONS_VER );

	}

	/**
	 * Backend scripts.
	 *
	 * @since 2.5.2
	 *
	 * @return void.
	 */
	public function enqueue_backend_scripts() {
		if ( 'on' === get_option( 'rpo_megamenu_enable' ) ) {
			wp_enqueue_script( 'responsive-mega-menu-admin', RESPONSIVE_ADDONS_URI . 'dist/responsive-pro-megamenu.js', array( 'lodash', 'react', 'react-dom', 'wp-api-fetch', 'wp-blob', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-data', 'wp-date', 'wp-dom-ready', 'wp-edit-post', 'wp-editor', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-keycodes', 'wp-plugins', 'wp-polyfill', 'wp-rich-text', 'wp-token-list', 'wp-url', 'jquery' ), RESPONSIVE_ADDONS_VER, true );
			$rpro_megamenu = array(
				'nonce' => wp_create_nonce( 'rpro_megamenu' ),
			);
			wp_localize_script( 'responsive-mega-menu-admin', 'rpro_megamenu', $rpro_megamenu );
		}
	}

	/**
	 * Modal HTML.
	 *
	 * @since 2.5.2
	 *
	 * @return void.
	 */
	public function responsive_pro_mega_menu_modal() {
		?>
		<div id="responsive-pro-modal-app" class="resp-pro-megamenu-modal"></div>
		<?php
	}

	/**
	 * Enable/Disables the MegaMenu Feature on switch toggle.
	 *
	 * @since 2.5.2
	 * @access public
	 */
	public function responive_pro_rest_routes() {

		register_rest_route(
			'responsive_pro/v1',
			'/rpro_mega_menu/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'responsive_pro_get_mega_menu_option' ),
				'permission_callback' => array( $this, 'responsive_pro_get_mega_menu_option_permission' ),
			)
		);

		register_rest_route(
			'responsive_pro/v1',
			'/rpro_mega_menu',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'responsive_pro_set_mega_menu_option' ),
				'permission_callback' => array( $this, 'responsive_pro_set_mega_menu_option_permission' ),
			)
		);

	}

	/**
	 * Checking permissions
	 *
	 * @since 2.5.2
	 * @return bool
	 */
	public function responsive_pro_get_mega_menu_option_permission() {
		return true;
	}

	/**
	 * Checking permissions
	 *
	 * @since 2.5.2
	 */
	public function responsive_pro_set_mega_menu_option_permission() {
		return current_user_can( 'edit_theme_options' );
	}


	/**
	 * Save megamenu values in database.
	 *
	 * @param object $req Megamenu request payload.
	 * @return string
	 * @since 2.5.2
	 */
	public function responsive_pro_set_mega_menu_option( $req ) {

		if ( ! current_user_can( 'edit_theme_options' ) ) {
			$response = rest_ensure_response(
				array(
					'msg' => 'No Priviledges Granted',
				),
			);
			$response->set_status( 401 );
			return $response;
		}
		$body    = json_decode( $req->get_body(), true );
		$menu_id = (int) $body['id'];
		$options = $body['options'];
		foreach ( $options as $key => $option ) {

			if ( 'resp_custom_text' === $key ) {
				$value = wp_kses_post( $option['value'] );
			} else {
				$value = sanitize_text_field( $option['value'] );
			}
			update_post_meta( $menu_id, '_menu_item_megamenu_' . $key, $value );
		}

		$response = rest_ensure_response(
			array(
				'msg' => 'Success',
			)
		);
		$response->set_status( 200 );
		return $response;

	}

	/**
	 * Mega menu configs
	 *
	 * @param WP_REST_Request $request Megamenu id.
	 * @return array
	 * @since 2.5.2
	 */
	public function responsive_pro_get_mega_menu_option( $request ) {

		$menu_item_id                   = $request->get_param( 'id' );
		$prefix                         = '_menu_item_megamenu_resp_';
		$resp_megamenu_width            = get_post_meta( $menu_item_id, $prefix . 'megamenu_width', true );
		$resp_menu_item_icon_type       = get_post_meta( $menu_item_id, $prefix . 'menu_item_icon_type', true );
		$resp_background_image          = get_post_meta( $menu_item_id, $prefix . 'background_image', true );
		$resp_submenu_item_icon_type    = get_post_meta( $menu_item_id, $prefix . 'submenu_item_icon_type', true );
		$resp_content_source            = get_post_meta( $menu_item_id, $prefix . 'content_source', true );
		$resp_menu_item_icon_size       = get_post_meta( $menu_item_id, $prefix . 'menu_item_icon_size', true );
		$resp_menu_item_icon_spacing    = get_post_meta( $menu_item_id, $prefix . 'menu_item_icon_spacing', true );
		$resp_submenu_item_icon_size    = get_post_meta( $menu_item_id, $prefix . 'submenu_item_icon_size', true );
		$resp_submenu_item_icon_spacing = get_post_meta( $menu_item_id, $prefix . 'submenu_item_icon_spacing', true );
		$config                         = array(
			'resp_enable_megamenu'                  => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'enable_megamenu', true ),
				'default_value' => false,
			),
			'resp_megamenu_width'                   => array(
				'value' => '' === $resp_megamenu_width ? 'content' : $resp_megamenu_width,
			),
			'resp_megamenu_custom_width'            => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'megamenu_custom_width', true ),
				'default_value' => 600,
			),
			'resp_highlight_label'                  => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'highlight_label', true ),
				'default_value' => '',
			),
			'resp_menu_item_icon_type'              => array(
				'value' => '' === $resp_menu_item_icon_type ? 'none' : $resp_menu_item_icon_type,
			),
			'resp_menu_item_icon_type_upload'       => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'menu_item_icon_type_upload', true ),
			),
			'resp_menu_item_icon_position'          => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'menu_item_icon_position', true ),
			),
			'resp_menu_item_icon_size'              => array(
				'value' => '' === $resp_menu_item_icon_size ? 20 : $resp_menu_item_icon_size,
			),
			'resp_menu_item_icon_spacing'           => array(
				'value' => '' === $resp_menu_item_icon_spacing ? 5 : $resp_menu_item_icon_spacing,
			),
			'resp_background_type'                  => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'background_type', true ),
				'default_value' => 'color',
			),
			'resp_background_color'                 => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'background_color', true ),
				'default_value' => '#045CB4',
			),
			'resp_background_image'                 => array(
				'value' => '' === $resp_background_image ? 'none' : $resp_background_image,
			),
			'resp_background_image_position'        => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'background_image_position', true ),
				'default_value' => 'left top',
			),
			'resp_background_image_size'            => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'background_image_size', true ),
				'default_value' => 'auto',
			),
			'resp_background_image_repeat'          => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'background_image_repeat', true ),
				'default_value' => 'no-repeat',
			),
			'resp_color_text_or_link'               => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'color_text_or_link', true ),
				'default_value' => '',
			),
			'resp_highlight_position'               => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'highlight_position', true ),
				'default_value' => '',
			),
			'resp_highlight_icon_size'              => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'highlight_icon_size', true ),
				'default_value' => '20',
			),
			'resp_highlight_color'                  => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'highlight_color', true ),
				'default_value' => '',
			),
			'resp_highlight_bg_color'               => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'highlight_bg_color', true ),
				'default_value' => '',
			),
			'resp_set_heading'                      => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'set_heading', true ),
				'default_value' => false,
			),
			'resp_hide_menu_label'                  => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'hide_menu_label', true ),
				'default_value' => false,
			),
			'resp_disable_link'                     => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'disable_link', true ),
				'default_value' => false,
			),
			'resp_submenu_item_icon_type'           => array(
				'value' => '' === $resp_submenu_item_icon_type ? 'none' : $resp_submenu_item_icon_type,
			),
			'resp_submenu_item_icon_type_upload'    => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'submenu_item_icon_type_upload', true ),
			),
			'resp_submenu_item_icon_position'       => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'submenu_item_icon_position', true ),
			),
			'resp_submenu_item_icon_size'           => array(
				'value' => '' === $resp_submenu_item_icon_size ? 20 : $resp_submenu_item_icon_size,
			),
			'resp_submenu_item_icon_spacing'        => array(
				'value' => '' === $resp_submenu_item_icon_spacing ? 5 : $resp_submenu_item_icon_spacing,
			),
			'resp_content_source'                   => array(
				'value' => '' === $resp_content_source ? 'default' : $resp_content_source,
			),
			'resp_custom_text'                      => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'custom_text', true ),
			),
			'resp_submenu_highlight_label'          => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'submenu_highlight_label', true ),
				'default_value' => '',
			),
			'resp_set_heading_separator_color'      => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'set_heading_separator_color', true ),
				'default_value' => false,
			),
			'resp_submenu_highlight_label_position' => array(
				'value' => get_post_meta( $menu_item_id, $prefix . 'submenu_highlight_label_position', true ),
			),
			'resp_submenu_highlight_label_color'    => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'submenu_highlight_label_color', true ),
				'default_value' => '',
			),
			'resp_submenu_highlight_label_bg_color' => array(
				'value'         => get_post_meta( $menu_item_id, $prefix . 'submenu_highlight_label_bg_color', true ),
				'default_value' => '',
			),
		);

		return rest_ensure_response( $config );
	}
}

new Responsive_Addons_Nav_Walker();
