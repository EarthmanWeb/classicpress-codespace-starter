<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.cyberchimps.com
 * @since      1.0.0
 *
 * @package    Responsive_Addons_Pro
 * @subpackage Responsive_Addons_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Responsive_Addons_Pro
 * @subpackage Responsive_Addons_Pro/admin
 * @author     CyberChimps <support@cyberchimps.com>
 */
class Responsive_Addons_Pro_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * RST Blocks API Url
	 *
	 * @since 2.6.3
	 * @var   string API Url
	 */
	public static $rst_blocks_api_url;

	/**
	 * Api url to get the ready sites.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string $api_url Api url to get the ready sites.
	 */
	public static $api_url;

	/**
	 * Api url to verify Licenses.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string $cc_api_url Api url to verify Licenses.
	 */
	public static $cc_api_url;

	/**
	 * Member Varible
	 *
	 * @var string $font_css
	 */
	protected $font_css = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		self::set_api_url();
		self::set_rst_blocks_api_url();
		self::set_cc_api_url();
		$settings = self::get_settings();

		// Enqueue the custom fonts.
		add_action( 'responsive_render_fonts', array( $this, 'render_fonts' ) );
		add_action( 'responsive_customizer_font_list', array( $this, 'add_customizer_font_list' ) );
		// Add font files style.
		add_action( 'wp_head', array( $this, 'add_style' ) );
		add_action( 'elementor/editor/footer', array( $this, 'responsive_ready_sites_register_widget_scripts' ), 98 );
		if ( is_admin() ) {
			add_action( 'enqueue_block_assets', array( $this, 'add_style' ) );
			add_action( 'wp_ajax_responsive-pro-white-label-settings', array( $this, 'responsive_pro_white_label_settings' ) );
			add_action( 'wp_ajax_nopriv_responsive-pro-white-label-settings', array( $this, 'responsive_pro_white_label_settings' ) );
			add_action( 'wp_ajax_responsive-pro-enable-megamenu', array( $this, 'responsive_pro_enable_megamenu' ) );
			add_action( 'wp_ajax_nopriv_responsive-pro-enable-megamenu', array( $this, 'responsive_pro_enable_megamenu' ) );
			add_action( 'wp_ajax_responsive-pro-enable-woocommerce', array( $this, 'responsive_pro_enable_woocommerce' ) );
			add_action( 'wp_ajax_nopriv_responsive-pro-enable-woocommerce', array( $this, 'responsive_pro_enable_woocommerce' ) );
			add_action( 'wp_ajax_responsive-pro-enable-typography', array( $this, 'responsive_pro_enable_typography' ) );
			add_action( 'wp_ajax_nopriv_responsive-pro-enable-typography', array( $this, 'responsive_pro_enable_typography' ) );
			add_action( 'wp_ajax_responsive-pro-enable-colors-backgrounds', array( $this, 'responsive_pro_enable_colors_backgrounds' ) );
			add_action( 'wp_ajax_nopriv_responsive-pro-enable-colors-backgrounds', array( $this, 'responsive_pro_enable_colors_backgrounds' ) );

			add_filter( 'wp_prepare_themes_for_js',  __CLASS__ . '::responsive_theme_white_label_update_branding');
			add_filter( 'update_right_now_text', array( $this, 'admin_dashboard_page' ) );
			add_filter( 'gettext', array( $this, 'theme_gettext' ), 20, 3 );

			if ( !empty( $settings['theme_icon_url'] ) ) {
				add_filter( 'responsive_admin_menu_icon', array( $this, 'update_admin_brand_logo' ) );
				add_filter( 'responsive_admin_menu_footer_icon', array( $this, 'update_admin_brand_logo' ) );
			}
		}
		if ( !empty( $settings['theme_name'] ) ) {
			add_filter( 'responsive_theme_footer_theme_text', array( $this, 'white_label_theme_powered_by_text' ) );
		}
		if ( !empty( $settings['plugin_website_uri'] ) ) {
			add_filter( 'responsive_theme_footer_link', array( $this, 'white_label_theme_powered_by_link' ) );
		}

		// adding hooks for white label.
		add_filter( 'all_plugins', __CLASS__ . '::responsive_pro_white_label_update_branding' );
	}

	/**
	 * Save White Label Settings.
	 *
	 * @since 2.5.1
	 * @access public
	 */
	public function responsive_pro_white_label_settings() {

		check_ajax_referer( 'white_label_settings', '_nonce' );

		$settings = self::get_settings();

		$settings['plugin_author']        = isset( $_POST['authorName'] ) ? sanitize_text_field( wp_unslash( $_POST['authorName'] ) ) : '';
		$settings['plugin_name']          = isset( $_POST['pluginName'] ) ? sanitize_text_field( wp_unslash( $_POST['pluginName'] ) ) : '';
		$settings['plugin_desc']          = isset( $_POST['pluginDesc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['pluginDesc'] ) ) : '';
		$settings['plugin_uri']           = isset( $_POST['pluginURL'] ) ? sanitize_text_field( wp_unslash( $_POST['pluginURL'] ) ) : '';
		$settings['plugin_website_uri']   = isset( $_POST['websiteURL'] ) ? sanitize_text_field( wp_unslash( $_POST['websiteURL'] ) ) : '';
		$settings['hide_wl_settings']     = isset( $_POST['hideSettings'] ) ? sanitize_text_field( wp_unslash( $_POST['hideSettings'] ) ) : '';
		$settings['theme_name']           = isset( $_POST['themeName'] ) ? sanitize_text_field( wp_unslash( $_POST['themeName'] ) ) : '';
		$settings['theme_desc']           = isset( $_POST['themeDesc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['themeDesc'] ) ) : '';
		$settings['theme_screenshot_url'] = isset( $_POST['themeScreenshotURL'] ) ? sanitize_textarea_field( wp_unslash( $_POST['themeScreenshotURL'] ) ) : '';
		$settings['theme_icon_url']       = isset( $_POST['themeIconURL'] ) ? sanitize_textarea_field( wp_unslash( $_POST['themeIconURL'] ) ) : '';

		update_option( 'rpro_elementor_settings', $settings );

		wp_send_json_success( array( 'msg' => 'Settings Saved' ) );

	}

	/**
	 * Enable/Disables the MegaMenu Feature on switch toggle.
	 *
	 * @since 2.5.2
	 * @access public
	 */
	public function responsive_pro_enable_megamenu() {

		check_ajax_referer( 'rpro_toggle_megamenu', '_nonce' );

		$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

		update_option( 'rpo_megamenu_enable', $value );

		wp_send_json_success();

	}

	/**
	 * Enable/Disables the Woocommerce customizer settings on switch toggle.
	 *
	 * @since 2.5.4
	 * @access public
	 */
	public function responsive_pro_enable_woocommerce() {

		check_ajax_referer( 'rpro_toggle_woocommerce', '_nonce' );

		$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

		update_option( 'rpro_woocommerce_enable', $value );

		wp_send_json_success();

	}

	/**
	 * Enable/Disables the Typography customizer settings on switch toggle.
	 *
	 * @since 2.5.4
	 * @access public
	 */
	public function responsive_pro_enable_typography() {

		check_ajax_referer( 'rpro_toggle_typography', '_nonce' );

		$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

		update_option( 'rpro_typography_enable', $value );

		wp_send_json_success();

	}

	/**
	 * Enable/Disables the Colors & Backgrounds customizer settings on switch toggle.
	 *
	 * @since 2.5.4
	 * @access public
	 */
	public function responsive_pro_enable_colors_backgrounds() {

		check_ajax_referer( 'rpro_toggle_colors_backgrounds', '_nonce' );

		$value = isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

		update_option( 'rpro_colors_backgrounds_enable', $value );

		wp_send_json_success();

	}

	/**
	 * Setter for $api_url
	 *
	 * @since  1.0.0
	 */
	public static function set_api_url() {
		self::$api_url = apply_filters( 'responsive_ready_sites_api_url', 'https://ccreadysites.cyberchimps.com/wp-json/wp/v2/' );
	}

	/**
	 * Setter for rst blocks $rst_blocks_api_url
	 *
	 * @since  2.6.3
	 */
	public static function set_rst_blocks_api_url() {
		self::$rst_blocks_api_url = apply_filters( 'rst_blocks_api_url', 'https://ccreadysites.cyberchimps.com/ccblocks/wp-json/wp/v2/' );
	}

	/**
	 * Setter for $api_url
	 *
	 * @since  1.0.0
	 */
	public static function set_cc_api_url() {
		self::$cc_api_url = apply_filters( 'cyberchimps_api_url', 'https://www.cyberchimps.com/' );
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/responsive-addons-pro-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'responsive-ready-sites-pro-admin-js', RESPONSIVE_ADDONS_PRO_URI . 'admin/js/responsive-addons-pro-admin.js', array( 'jquery', 'wp-util', 'updates' ), '1.0.0', true );

		$data = apply_filters(
			'responsive_sites_localize_vars',
			array(
                'debug'             => ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || isset( $_GET['debug'] ) ) ? true : false, //phpcs:ignore
				'ajaxurl'           => esc_url( admin_url( 'admin-ajax.php' ) ),
				'siteURL'           => site_url(),
				'_ajax_nonce'       => wp_create_nonce( 'responsive-addons' ),
				'XMLReaderDisabled' => ! class_exists( 'XMLReader' ) ? true : false,
				'required_plugins'  => array(),
				'ApiURL'            => self::$api_url,
				'CcURL'             => self::$cc_api_url,
				'license_key'       => self::get_api_key(),
				'product_id'        => self::get_api_product_id(),
			)
		);

		wp_localize_script( 'responsive-ready-sites-pro-admin-js', 'responsiveSitesProAdmin', $data );

		wp_enqueue_script( 'install-responsive-theme-and-addons', RESPONSIVE_ADDONS_PRO_URI . 'admin/js/install-responsive-theme-addons.js', array( 'jquery', 'updates' ), '1.0.1', true );
		$data = apply_filters(
			'responsive_sites_install_theme_and_addons_localize_vars',
			array(
				'installing'  => __( 'Installing..', 'responsive-addons-pro' ),
				'ajaxurl'     => esc_url( admin_url( 'admin-ajax.php' ) ),
				'_ajax_nonce' => wp_create_nonce( 'responsive-addons' ),
			)
		);
		wp_localize_script( 'install-responsive-theme-and-addons', 'responsiveInstallThemeAddonsVars', $data );
		wp_enqueue_media();
		wp_enqueue_script( 'responsive-custom-fonts-js', RESPONSIVE_ADDONS_PRO_URI . 'includes/custom-fonts/assets/js/responsive-pro-custom-fonts.js', array(), '1.0.1', true );
	}

	/**
	 * Get Api key from database
	 *
	 * @since 1.0.0
	 */
	public function get_api_key() {
		global $wcam_lib_responsive_pro;
		$license_key = '';
		if ( $wcam_lib_responsive_pro->data ) {
			$license_key = $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ];
		}
		return $license_key;
	}

	/**
	 * Get Api key from database
	 *
	 * @since 2.0.0
	 */
	public function get_api_product_id() {
		global $wcam_lib_responsive_pro;
		$product_id = $wcam_lib_responsive_pro->product_id;
		return $product_id;
	}

	/**
	 * Responsive pro admin menu
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function responsive_pro_admin_menu() {

		add_submenu_page(
			'responsive_add_ons',
			'',
			__( 'Priority Support', 'responsive-addons' ),
			'manage_options',
			'responsive_add_ons_priority_support',
			array( $this, 'responsive_add_ons_priority_support' ),
			30
		);

	}

	/**
	 * Go to Responsive Pro support.
	 *
	 * @since 2.0.5
	 * @access public
	 */
	public function responsive_add_ons_priority_support() {
		if ( empty( $_GET['page'] ) ) {
			return;
		}
		wp_redirect( 'https://cyberchimps.com/my-account/orders/' );
		die;
	}

	/**
	 * Enqueue Scripts
	 *
	 * @since 1.0.4
	 */
	public function add_style() {
		$fonts = Responsive_Pro_Custom_Fonts_Taxonomy::get_fonts();
		if ( ! empty( $fonts ) ) {
			foreach ( $fonts  as $load_font_name => $load_font ) {
				$this->render_font_css( $load_font_name );
			}
			?>
			<style type="text/css">
				<?php echo wp_strip_all_tags( $this->font_css ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</style>
			<?php
		}
	}

	/**
	 * Add Custom Font list into customizer.
	 *
	 * @since  1.0.0
	 * @param string $value selected font family.
	 */
	public function add_customizer_font_list( $value ) {

		$fonts = Responsive_Pro_Custom_Fonts_Taxonomy::get_fonts();

		echo '<optgroup label="' . esc_attr( 'Custom Fonts' ) . '">';

		foreach ( $fonts as $font => $links ) {
			echo '<option value="' . esc_attr( $font ) . '" ' . selected( $font, $value, false ) . '>' . esc_attr( $font ) . '</option>';
		}
	}

	/**
	 * Enqueue Render Fonts
	 *
	 * @since 1.0.0
	 * @param array $load_fonts fonts.
	 */
	public function render_fonts( $load_fonts ) {

		$fonts = Responsive_Pro_Custom_Fonts_Taxonomy::get_fonts();

		foreach ( $load_fonts  as $load_font_name => $load_font ) {
			if ( array_key_exists( $load_font_name, $fonts ) ) {
				unset( $load_fonts[ $load_font_name ] );
			}
		}
		return $load_fonts;
	}

	/**
	 * Create css for font-face
	 *
	 * @since 1.0.0
	 * @param array $font selected font from custom font list.
	 */
	private function render_font_css( $font ) {
		$fonts = Responsive_Pro_Custom_Fonts_Taxonomy::get_links_by_name( $font );

		foreach ( $fonts as $font => $links ) :
			$css  = '@font-face { font-family:' . esc_attr( $font ) . ';';
			$css .= 'src:';
			$arr  = array();
			if ( $links['font_woff_2'] ) {
				$arr[] = 'url(' . esc_url( $links['font_woff_2'] ) . ") format('woff2')";
			}
			if ( $links['font_woff'] ) {
				$arr[] = 'url(' . esc_url( $links['font_woff'] ) . ") format('woff')";
			}
			if ( $links['font_ttf'] ) {
				$arr[] = 'url(' . esc_url( $links['font_ttf'] ) . ") format('truetype')";
			}
			if ( $links['font_otf'] ) {
				$arr[] = 'url(' . esc_url( $links['font_otf'] ) . ") format('opentype')";
			}
			if ( $links['font_svg'] ) {
				$arr[] = 'url(' . esc_url( $links['font_svg'] ) . '#' . esc_attr( strtolower( str_replace( ' ', '_', $font ) ) ) . ") format('svg')";
			}
			$css .= join( ', ', $arr );
			$css .= ';';
			$css .= 'font-display: ' . esc_attr( $links['font-display'] ) . ';';
			$css .= '}';
		endforeach;

		$this->font_css .= $css;
	}

	/**
	 * Include Elementor Admin JS.
	 *
	 * @since 2.4.6
	 */
	public function responsive_ready_sites_register_widget_scripts() {

		if ( class_exists( 'Responsive_Add_Ons' ) ) {
			wp_enqueue_script( 'responsive-elementor-admin', RESPONSIVE_ADDONS_PRO_URI . 'admin/js/responsive-elementor-admin.js', array( 'jquery', 'wp-util', 'updates', 'jquery-ui-autocomplete', 'masonry', 'imagesloaded' ), RESPONSIVE_ADDONS_PRO_VERSION, true );

			wp_add_inline_script( 'responsive-elementor-admin', sprintf( 'var pagenow = "%s";', 'Responsive Starter Templates' ), 'after' );

			$responsive_add_ons = new Responsive_Add_Ons();

			/* translators: %s are link. */
			$license_msg = sprintf( __( 'License key is not activated. <a href="%s" target="_blank">Read More</a>.', 'responsive-addons' ), 'https://docs.cyberchimps.com/responsive-elementor-addons/' );

			$data = apply_filters(
				'responsive_sites_render_localize_vars',
				array(
					'plugin_name'                 => 'Responsive Starter Templates',
					'version'                     => RESPONSIVE_ADDONS_PRO_VERSION,
					'default_page_builder'        => 'elementor',
					'license_status'              => $responsive_add_ons->responsive_pro_license_is_active(),
					'ajaxurl'                     => esc_url( admin_url( 'admin-ajax.php' ) ),
					'default_page_builder_sites'  => $responsive_add_ons->get_sites_by_elementor(),
					'default_page_builder_blocks' => $responsive_add_ons->get_rst_blocks_by_elementor(),
					'ApiURL'                      => self::$api_url,
					'_ajax_nonce'                 => wp_create_nonce( 'responsive-addons' ),
					'isPro'                       => defined( 'RESPONSIVE_ADDONS_PRO_VERSION' ) ? true : false,
					'dismiss_text'                => esc_html__( 'Dismiss', 'responsive-addons' ),
					'noPlugins'                   => __( 'No Plugins Required' ),
					'syncCompleteMessage'         => __( 'Template library refreshed!', 'responsive-addons' ),
					'getProText'                  => __( 'Get Responsive Pro!', 'responsive-addons' ),
					'getProURL'                   => esc_url( 'https://cyberchimps.com/responsive-go-pro/?utm_source=free-to-pro&utm_medium=responsive-add-ons&utm_campaign=responsive-pro&utm_content=preview-ready-site' ),
					'getREAURL'                   => esc_url( 'https://cyberchimps.com/elementor-widgets/docs/how-to-install-activate-the-responsive-elementor-addons/' ),
					'siteURL'                     => site_url(),
					'template'                    => esc_html__( 'Template', 'responsive-addons' ),
					'install_plugin_text'         => esc_html__( 'Install Required Plugins', 'responsive-addons' ),
					'license_key'                 => self::get_api_key(),
					'CcURL'                       => self::$cc_api_url,
					'product_id'                  => self::get_api_product_id(),
					'isREAActivated'              => $this->is_rea_activated(),
					'license_msg'                 => $license_msg,
					'license_block_msg'           => $license_msg,
					'blockSiteURL'                => self::$rst_blocks_api_url,
					'blockCategories'             => $responsive_add_ons->block_categories(),
				)
			);

			wp_localize_script( 'responsive-elementor-admin', 'responsiveElementorProSites', $data );
		}
	}

	/**
	 * Check if REA is activated.
	 *
	 * @since 2.4.6
	 */
	public function is_rea_activated() {
		$rea_slug = 'responsive-elementor-addons/responsive-elementor-addons.php';
		if ( is_plugin_active( $rea_slug ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get plugin settings.
	 *
	 * @since 2.4.8
	 * @return array
	 */
	public static function get_settings() {
		$default_settings = array(
			'plugin_name'       => '',
			'plugin_short_name' => '',
			'plugin_desc'       => '',
			'plugin_author'     => '',
			'plugin_uri'        => '',
			'admin_label'       => '',
			'support_link'      => '',
			'hide_support'      => 'off',
			'hide_wl_settings'  => 'off',
			'theme_name'        => '',
			'theme_desc'        => '',
			'theme_screenshot_url' => '',
			'theme_icon_url' => '',

		);

		$settings = get_option( 'rpro_elementor_settings' );

		if ( ! is_array( $settings ) || empty( $settings ) ) {
			$settings = $default_settings;
		}

		if ( is_array( $settings ) && ! empty( $settings ) ) {
			$settings = array_merge( $default_settings, $settings );
		}

		return apply_filters( 'rpro_elements_admin_settings', $settings );
	}

	/**
	 * Set the WHite Label branding data to plugin.
	 *
	 * @since 2.4.8
	 * @return array
	 */
	public static function responsive_pro_white_label_update_branding( $all_plugins ) {
		if ( ! is_array( $all_plugins ) || empty( $all_plugins ) || ! isset( $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ] ) ) {
			return $all_plugins;
		}

		$settings = self::get_settings();

		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Name']        = ! empty( $settings['plugin_name'] ) ? $settings['plugin_name'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Name'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['PluginURI']   = ! empty( $settings['plugin_uri'] ) ? $settings['plugin_uri'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['PluginURI'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Description'] = ! empty( $settings['plugin_desc'] ) ? $settings['plugin_desc'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Description'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Author']      = ! empty( $settings['plugin_author'] ) ? $settings['plugin_author'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Author'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['AuthorURI']   = ! empty( $settings['plugin_website_uri'] ) ? $settings['plugin_website_uri'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['AuthorURI'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Title']       = ! empty( $settings['plugin_name'] ) ? $settings['plugin_name'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['Title'];
		$all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['AuthorName']  = ! empty( $settings['plugin_author'] ) ? $settings['plugin_author'] : $all_plugins[ RESPONSIVE_ADDONS_PRO_BASE ]['AuthorName'];

		return $all_plugins;
	}

	/**
	 * Set the White Label branding data to theme.
	 *
	 * @since 2.5.4
	 * @return array
	 */
	public static function responsive_theme_white_label_update_branding( $all_themes ) {

		$settings = self::get_settings();

		$theme_slug = 'responsive';
		// Check if the theme exists
		if (isset($all_themes[$theme_slug])) {

			// Update theme details
			if ( !empty($settings['theme_name']) ) {

				$all_themes['responsive']['name'] = $settings['theme_name'];

				foreach ( $all_themes as $key => $theme ) {
					if ( isset( $theme['parent'] ) && 'Responsive' == $theme['parent'] ) {
						$all_themes[ $key ]['parent'] = $settings['theme_name'];
					}
				}
			}

			$all_themes['responsive']['description'] = !empty($settings['theme_desc']) ? $settings['theme_desc'] : $all_themes['responsive']['description'];

			if(!empty($settings['plugin_author'])){
				$all_themes['responsive']['author']  = $settings['plugin_author'];
				$author_url                          = ( !empty($settings['plugin_website_uri']) ? $settings['plugin_website_uri'] : "#" );
				$all_themes['responsive']['authorAndUri']  = '<a href="' . esc_url( $author_url ) . '">' . $all_themes['responsive']['author'] . '</a>';
			}

			$all_themes['responsive']['screenshot']        = !empty($settings['theme_screenshot_url']) ? array( $settings['theme_screenshot_url'] ) : $all_themes['responsive']['screenshot'];

		}

		return $all_themes;
	}

	/**
	 * White labels the theme on the dashboard 'At a Glance' metabox
	 *
	 * @param mixed $content Content.
	 * @return array
	 */
	public function admin_dashboard_page( $content ) {
		$settings = self::get_settings();
		if ( is_admin() && 'Responsive' == wp_get_theme() && !empty($settings['theme_name']) ) {
			return sprintf( $content, get_bloginfo( 'version', 'display' ), '<a href="themes.php">' . $settings['theme_name'] . '</a>' );
		}
		return $content;
	}

	/**
	 * White labels the theme using the gettext filter
	 * to cover areas that we can't access like the Customizer.
	 *
	 * @param string $text  Translated text.
	 * @param string $original         Text to translate.
	 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
	 * @return string
	 */
	public function theme_gettext( $text, $original, $domain ) {
		$settings = self::get_settings();
		if ( !empty($settings['theme_name']) ) {
			if ( 'Responsive' == $original && $domain == 'responsive' ) {
				$text = $settings['theme_name'];
			}
		}
		return $text;
	}

	/**
	 * Get whitelabelled icon for admin dashboard.
	 *
	 * @since 2.5.4
	 * @param string $logo Default icon.
	 * @return string URL for updated whitelabelled icon.
	 */
	public function update_admin_brand_logo( $logo ) {
		$settings=self::get_settings();
		$logo = $settings['theme_icon_url'];
		return esc_url( $logo );
	}

	/**
	 * Get whitelabelled website url for footer.
	 *
	 * @since 2.5.4
	 * @param string $link Default url.
	 * @return string URL for updated whitelabelled icon.
	 */
	public function white_label_theme_powered_by_link( $link ) {
		$settings=self::get_settings();
		$link = $settings['plugin_website_uri'];
		return esc_url( $link );
	}

	/**
	 * Get whitelabelled theme name for footer.
	 *
	 * @since 2.5.4
	 * @param string $text Default text.
	 * @return string text for updated whitelabelled theme name.
	 */
	public function white_label_theme_powered_by_text( $text ) {
		$settings=self::get_settings();
		$text = $settings['theme_name'];
		return $text;
	}

}
