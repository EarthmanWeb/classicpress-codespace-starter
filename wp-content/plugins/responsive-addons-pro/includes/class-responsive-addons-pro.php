<?php
/**
 * Core plugin class.
 *
 * @link       https://www.cyberchimps.com
 * @since      1.0.0
 *
 * @package    Responsive_Addons_Pro
 * @subpackage Responsive_Addons_Pro/includes
 */

/**
 * The core plugin class Responsive_Addons_Pro.
 *
 * @since      1.0.0
 * @package    Responsive_Addons_Pro
 * @subpackage Responsive_Addons_Pro/includes
 * @author     CyberChimps <support@cyberchimps.com>
 */
class Responsive_Addons_Pro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Responsive_Addons_Pro_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Holds the values to be used in the fields callbacks
	 *
	 * @var array $license_options License Options.
	 */
	protected $license_options;

	/**
	 * Parent Menu Slug
	 *
	 * @since  1.0.0
	 * @var (string) $parent_menu_slug
	 */
	protected $parent_menu_slug = 'themes.php';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'RESPONSIVE_ADDONS_PRO_VERSION' ) ) {
			$this->version = RESPONSIVE_ADDONS_PRO_VERSION;
		} else {
			$this->version = '2.6.6';
		}
		$this->plugin_name = 'responsive-addons-pro';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->load_responsive_sites_importer_pro();

		add_action( 'responsive_addons_pro_license_page', array( $this, 'display_license_form' ) );

		add_action( 'admin_init', array( $this, 'load_license_page_settings' ) );

		// Add admin Notice.
		add_action( 'admin_notices', array( $this, 'add_free_plugin_and_theme_installation_notice' ) );

		// Add admin Notice.
		add_action( 'admin_notices', array( $this, 'add_activation_guide_notice' ) );

		add_filter( 'responsive_sites_api_params', array( $this, 'api_request_params' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'responsive_pro_css' ) );

		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {
			add_action( 'after_setup_theme', array( $this, 'load_woocommerce' ) );
		}

		add_action( 'wp_footer', array( $this, 'responsive_pro_fixed_menu_onscroll' ) );

		add_action( 'admin_menu', array( $this, 'register_custom_fonts_menu' ), 101 );
		add_action( 'admin_head', array( $this, 'custom_fonts_menu_highlight' ) );

		add_filter( 'manage_edit-' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug . '_columns', array( $this, 'manage_columns' ) );

		add_action( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug . '_add_form_fields', array( $this, 'add_new_taxonomy_data' ) );
		add_action( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug . '_edit_form_fields', array( $this, 'edit_taxonomy_data' ) );

		add_action( 'edited_' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug, array( $this, 'save_metadata' ) );
		add_action( 'create_' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug, array( $this, 'save_metadata' ) );

		add_filter( 'upload_mimes', array( $this, 'add_fonts_to_allowed_mimes' ) );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'update_mime_types' ), 10, 3 );

		add_filter( 'plugin_action_links_' . RESPONSIVE_ADDONS_PRO_BASE, array( $this, 'plugin_action_links' ) );

		if ( is_admin() ) {
				add_action( 'wp_ajax_responsive-ready-sites-install-required-pro-plugins', array( $this, 'install_pro_plugin' ) );

				// Check if Responsive Addons pro plugin is active.
				add_action( 'wp_ajax_check-responsive-add-ons-pro-installed', array( $this, 'is_responsive_pro_is_installed' ) );

				// Check if Responsive Addons pro license is active.
				add_action( 'wp_ajax_check-responsive-add-ons-pro-license-active', array( $this, 'is_responsive_pro_license_is_active' ) );

				// Activate responsive theme.
				add_action( 'wp_ajax_responsive-pro-activate-responsive-theme', array( $this, 'activate_theme' ) );

				add_action( 'wp_ajax_responsive-pro-activate-responsive-addons-plugin', array( $this, 'activate_responsive_adddons' ) );

				add_action( 'wp_ajax_responsive-pro-api-key-deactivate', array( $this, 'responsive_pro_api_key_deactivate' ) );

				add_action( 'wp_ajax_nopriv_responsive-pro-api-key-deactivate', array( $this, 'responsive_pro_api_key_deactivate' ) );

				add_action( 'wp_ajax_responsive-pro-api-key-activate', array( $this, 'responsive_pro_api_key_activate' ) );

				add_action( 'wp_ajax_nopriv_responsive-pro-api-key-activate', array( $this, 'responsive_pro_api_key_activate' ) );

		}
		$theme = wp_get_theme();
		if ( 'Responsive' === $theme->name ) {
			$theme                    = wp_get_theme( 'responsive' );
			$responsive_theme_version = $theme->version;
			$compare                  = version_compare( $responsive_theme_version, '4.6.3' );
			if ( -1 === $compare ) {
				add_action( 'admin_notices', array( $this, 'responsive_upgrade_theme_react' ), 20 );
			}
		}
	}

	/**
	 * Deactivates the API Key.
	 *
	 * Fires when the ajax request send from the responsive dashboard settings tab.
	 *
	 * @since    2.5.1
	 */
	public function responsive_pro_api_key_deactivate() {

		check_ajax_referer( 'deactivate_rpro_license', '_nonce' );

		global $wcam_lib_responsive_pro;
		$activation_status = get_option( $wcam_lib_responsive_pro->wc_am_activated_key );

		$args = array(
			'api_key' => $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ],
		);

		if ( 'Activated' === $activation_status && '' !== $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ] ) {
			// deactivates API Key key activation.
			$activate_results = json_decode( $wcam_lib_responsive_pro->deactivate( $args ), true );

			if ( true === $activate_results['success'] && true === $activate_results['deactivated'] ) {
				if ( ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
					update_option( $wcam_lib_responsive_pro->wc_am_activated_key, 'Deactivated' );
				}

				wp_send_json_success(
					array(
						'activate_results' => $activate_results,
						'error'            => false,
						'message'          => $activate_results['activations_remaining'],
					)
				);
			}

			if ( isset( $activate_results['data']['error_code'] ) && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
				update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
				wp_send_json_error(
					array(
						'activate_results' => $activate_results,
						'error'            => true,
						'message'          => $activate_results['data']['error'],
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'activate_results' => false,
					'error'            => true,
					'message'          => 'License Already Activated',
				)
			);
		}
	}

	/**
	 * Activates the API Key.
	 *
	 * Fires when the ajax request send from the responsive dashboard settings tab.
	 *
	 * @since    2.5.1
	 */
	public function responsive_pro_api_key_activate() {

		check_ajax_referer( 'activate_rpro_license', '_nonce' );

		global $wcam_lib_responsive_pro;

		$api_key = isset( $_POST['apiKey'] ) ? sanitize_text_field( wp_unslash( $_POST['apiKey'] ) ) : '';
		if ( empty( $api_key ) || '' === $api_key ) {
			wp_send_json_error(
				array(
					'message' => 'Please Enter Valid API Key.',
				),
			);
		}
		$product_id = isset( $_POST['productId'] ) ? sanitize_text_field( wp_unslash( $_POST['productId'] ) ) : '';
		if ( empty( $product_id ) || '' === $product_id ) {
			wp_send_json_error(
				array(
					'message' => 'Please Enter Valid Product ID Key.',
				),
			);
		}

		$args = array(
			'api_key' => $api_key,
		);

		update_option( $wcam_lib_responsive_pro->wc_am_product_id, $product_id );

		update_option(
			$wcam_lib_responsive_pro->data_key,
			array(
				$wcam_lib_responsive_pro->data_key . '_api_key' => $api_key,
			)
		);

		$activate_results = json_decode( $wcam_lib_responsive_pro->activate( $args, $product_id ), true );

		if ( true === $activate_results['success'] && true === $activate_results['activated'] ) {
			update_option( $wcam_lib_responsive_pro->wc_am_activated_key, 'Activated' );
			update_option( $wcam_lib_responsive_pro->wc_am_deactivate_checkbox_key, 'off' );
			wp_send_json_success(
				array(
					'activate_results' => $activate_results,
					'message'          => $activate_results['message'],
					'error'            => false,
				)
			);
		}

		if ( ( false === $activate_results || is_null( $activate_results ) ) && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
			update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
			wp_send_json_error(
				array(
					'activate_results' => $activate_results,
					'message'          => 'Connection failed to the License Key API server. Try again later. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate the plugin/theme.',
					'error'            => true,
				)
			);
		}

		if ( isset( $activate_results['data']['error_code'] ) && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
			update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
			wp_send_json_error(
				array(
					'activate_results' => $activate_results,
					'message'          => $activate_results['data']['error'],
					'error'            => true,
				)
			);
		}
	}

	/**
	 * Adding stylesheet of responsive pro plugin using handle of responsive theme stylesheet.
	 */
	public function responsive_pro_css() {
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'responsive-pro-style', plugin_dir_url( __FILE__ ) . "css/style{$suffix}.css", array( 'responsive-style' ), RESPONSIVE_ADDONS_PRO_VERSION );

	}

	/**
	 * Verify if the version of responsive pro is greater or not.
	 *
	 * @since 2.6.4
	 */
	public function is_pro_version_greater() {
		$is_pro_version_greater = false;
		if ( version_compare( RESPONSIVE_ADDONS_PRO_VERSION, '2.6.3', '>' ) ) {
			$is_pro_version_greater = true;
		}
		return $is_pro_version_greater;
	}

	/**
	 * Verify if the version of responsive theme is greater or not.
	 *
	 * @param string $theme_version Current version of the theme.
	 * @since 2.6.4
	 */
	public function is_responsive_version_greater( $theme_version ) {
		$is_theme_version_greater = false;
		if ( version_compare( $theme_version, '4.9.6', '>' ) ) {
			$is_theme_version_greater = true;
		}
		return $is_theme_version_greater;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Responsive_Addons_Pro_Loader. Orchestrates the hooks of the plugin.
	 * - Responsive_Addons_Pro_i18n. Defines internationalization functionality.
	 * - Responsive_Addons_Pro_Admin. Defines all hooks for the admin area.
	 * - Responsive_Addons_Pro_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-responsive-addons-pro-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-responsive-addons-pro-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-responsive-addons-pro-admin.php';

		/**
		 * Responsive Addons Pro Customizer Controls.
		 */
		require plugin_dir_path( __FILE__ ) . 'customizer/class-responsive-addons-pro-customizer-controls.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-responsive-addons-pro-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/custom-fonts/class-responsive-pro-custom-fonts-taxonomy.php';
		/**
		 * The class responsible for loading the footer customizer options
		 */

		$theme = wp_get_theme(); // gets the current theme.

		if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {

			if ( 'Responsive' === $theme->parent_theme ) {
				$theme = wp_get_theme( 'responsive' );
			}

			if ( version_compare( $theme['Version'], '4.0.5', '>' ) ) {

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-footer-customizer.php';

				if (
					( ! $this->is_pro_version_greater() ) ||
					( $this->is_pro_version_greater() && ! $this->is_responsive_version_greater( $theme['Version'] ) )
				) {
					if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) :
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-background-image-customizer.php';
					endif;
				}

				/**
				 * The class responsible for loading the blog customizer options
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-blog-customizer.php';

				if (
					( ! $this->is_pro_version_greater() ) ||
					( $this->is_pro_version_greater() && ! $this->is_responsive_version_greater( $theme['Version'] ) )
				) {
					/**
					 * The class responsible for loading the Responsive Pro Typography options
					 */
					if ( 'on' === get_option( 'rpro_typography_enable' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-typography-customizer.php';
					}
				}

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-sticky-header-customizer.php';

				if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {
					/**
					 * The class responsible for loading the Woocommerce Typography options
					 */
					if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Typography' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/settings/class-responsive-addons-pro-woocommerce-typography.php';
					}

					/**
					 * The class responsible for loading the Shop Pagination options
					 */
					if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Shop_Pagination' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/settings/class-responsive-addons-pro-woocommerce-shop-pagination.php';
					}

					/**
					 * The class responsible for loading the Breadcrumb and Toolbar disable options
					 */
					if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Product_Catalog' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/settings/class-responsive-addons-pro-woocommerce-product-catalog.php';
					}

					/**
					 * The class responsible for loading the Header Cart Icon options
					 */
					if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Cart' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/settings/class-responsive-addons-pro-woocommerce-cart.php';
					}

					/**
					 * The class responsible for loading the Woocommerce Typography options
					 */
					if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Single_Product' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/settings/class-responsive-addons-pro-woocommerce-single-product.php';
					}
				}
				/**
				 * The class responsible for loading the Custom Styles
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/custom-styles.php';

				/**
				 * The class responsible for loading the footer customizer options
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/customizer.php';

				/**
				 * The class responsible for loading the helper functions for Customizer
				 */
				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/helper.php';
				require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/classes/class-responsive-pro-blog-markup.php';

				if ( ( class_exists( 'Responsive_Add_Ons' ) && version_compare( RESPONSIVE_ADDONS_VER, '2.9.3', '>' ) ) || ( ! class_exists( 'Responsive_Add_Ons' ) ) ) {
					if ( ! class_exists( 'Responsive_Custom_Nav_Walker' ) ) {
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/megamenu/class-responsive-nav-walker.php';
						require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/megamenu/class-responsive-custom-nav-walker.php';
					}
				}

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-menu-layout.php';

				if ( $this->is_pro_version_greater() && ! $this->is_responsive_version_greater( $theme['Version'] ) ) {
					if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) :
						require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-container-spacing.php';
					endif;
				} else {
					require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/customizer/settings/class-responsive-addons-pro-container-spacing.php';
				}
			}
		}

		$this->load_responsive_pro_system_info();

		$this->loader = new Responsive_Addons_Pro_Loader();

	}

	/**
	 * Load Responsive Pro Reports
	 *
	 * @since 2.0.5
	 */
	public function load_responsive_pro_system_info() {
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/helpers/class-report-helper.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-base.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-mu-plugins.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-network-plugins.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-plugins.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-server.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-theme.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-user.php';
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/reporters/class-wordpress.php';

		require_once RESPONSIVE_ADDONS_PRO_DIR . 'admin/system-info/class-module.php';
	}

	/**
	 * Load woocommerce files.
	 */
	public function load_woocommerce() {
		if ( ! class_exists( 'Responsive_Woocommerce_Ext' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/compatibility/woocommerce/customizer/class-responsive-woocommerce-ext.php';
		}
	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 2.5.1
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {

		if ( 'Responsive' === get_option( 'current_theme' ) ) {
			$settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=responsive#settings' ), esc_html__( 'Settings', 'responsive-addons' ) );
			array_unshift( $links, $settings_link );
		}
		return $links;
	}

	/**
	 * Shows fixed header on scroll if sticky-header is enabled
	 */
	public function responsive_pro_fixed_menu_onscroll() {
		$responsive_options = wp_parse_args( get_option( 'responsive_theme_options', array() ) );

		if ( isset( $responsive_options['sticky-header'] ) && 1 === $responsive_options['sticky-header'] ) {

			if ( get_theme_mod( 'responsive_shrink_sticky_header' ) ) {
				?>
				<script type="text/javascript">
					document.getElementById("masthead").classList.add( 'shrink' );
				</script>
				<?php } else { ?>
				<script type="text/javascript">
					document.getElementById("masthead").classList.remove( 'shrink' );
				</script>
			<?php }
            if ( get_theme_mod( 'responsive_sticky_header_logo_option' ) ) {?>
                <script type="text/javascript">
                    document.getElementById("masthead").classList.add( 'sticky-logo' );
                </script>
            <?php } else { ?>
                <script type="text/javascript">
                    document.getElementById("masthead").classList.remove( 'sticky-logo' );
                </script>
            <?php }?>

		<script type="text/javascript">
			window.addEventListener("scroll", responsiveStickyHeader);

			function responsiveStickyHeader() {
				var height = document.getElementById("masthead").offsetHeight;
				if (document.documentElement.scrollTop > 0 ) {
					document.getElementById("masthead").classList.add( 'sticky-header' );
					if (document.getElementById("wrapper") ) {
						document.getElementById("wrapper").style.marginTop = height+'px';
					}
					if (document.getElementsByClassName("elementor")[0] ) {
						document.getElementsByClassName("elementor")[0].style.marginTop = height+'px';
					}

					let container = document.getElementById( 'site-navigation' );
					let button = container.getElementsByTagName( 'button' )[0];
					let menu = container.getElementsByTagName( 'ul' )[0];
					let icon = button.getElementsByTagName( 'i' )[0];
					container.classList.remove( 'toggled' );
					menu.setAttribute( 'aria-expanded', 'false' );
					button.setAttribute( 'aria-expanded', 'false' );
					icon.setAttribute( 'class', 'icon-bars' );
					if(document.getElementById("sidebar-menu-overlay")) {
						document.getElementById("sidebar-menu-overlay").style.display = "none";
					}

				} else {
					document.getElementById("masthead").classList.remove( 'sticky-header' );
					if (document.getElementById("wrapper") ) {
						document.getElementById("wrapper").style.marginTop = '0px';
					}
					if (document.getElementsByClassName("elementor")[0] ) {
						document.getElementsByClassName("elementor")[0].style.marginTop = '0px';
					}
				}
			}
		</script>
			<?php
		}
	}
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Responsive_Addons_Pro_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Responsive_Addons_Pro_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Responsive_Addons_Pro_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'responsive_pro_admin_menu' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Responsive_Addons_Pro_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Responsive_Addons_Pro_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Install Pro plugins.
	 *
	 * @since     1.0.0
	 */
	public function install_pro_plugin() {

		check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Error: You don\'t have the required permissions to install plugins.', 'responsive-addons' ),
				)
			);
		}

		$pro_plugins    = ( isset( $_POST['pro_plugin'] ) ) ? wp_unslash( $_POST['pro_plugin'] ) : array();
		$license_key    = ( isset( $_POST['license_key'] ) ) ? sanitize_text_field( wp_unslash( $_POST['license_key'] ) ) : '';
		$cyberchips_url = ( isset( $_POST['request_url'] ) ) ? esc_url_raw( wp_unslash( $_POST['request_url'] ) ) : '';
		$product_id     = ( isset( $_POST['product_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : '';

		$pro_plugins_installation_data = $this->get_cc_api_response( $license_key, $cyberchips_url, $product_id, $pro_plugins );

		if ( $pro_plugins_installation_data ) {

			foreach ( $pro_plugins as $plugin ) {

				$plugin_slug = $plugin['slug'];
				$plugin_init = $plugin['init'];

				if ( self::is_plugin_installed( $plugin_init ) ) {
					if ( ! is_plugin_active( $plugin_init ) ) {
						if ( 'responsive-elementor-addons' === $plugin_slug ) {
							$activate = activate_plugin( $plugin_init, '', false, false );
						} else {
							$activate = activate_plugin( $plugin_init, '', false, true );
						}
					}
				} else {
					if ( 'responsive-elementor-addons' === $plugin_slug ) {
						$plugin_zip = $pro_plugins_installation_data['data'][ $plugin_slug ]['package_url'];
						$installed  = self::install_plugin( $plugin_zip );
						if ( $installed ) {
							if ( ! function_exists( 'activate_plugin' ) ) {
								require_once ABSPATH . 'wp-admin/includes/plugin.php';
							}
							$activate = activate_plugin( $plugin_init, '', false, false );
						}
					}
				}

				if ( 'responsive-elementor-addons' === $plugin_slug && ! $this->is_responsive_elementor_addons_license_active() ) {
					$this->activate_responsive_elementor_addons_license();
				}
			}

			wp_send_json_success(
				array(
					'pro_plugins_install' => true,
				)
			);
		} else {
			wp_send_json_error(
				array(
					'pro_plugins_install' => false,
				)
			);
		}
	}


	/**
	 * Activate the license for Responsive Elementor Addons
	 */
	public function activate_responsive_elementor_addons_license() {
		global $wcam_lib_responsive_elementor_addons;
		global $wcam_lib_responsive_pro;

		$api_key = $wcam_lib_responsive_pro->data['wc_am_client_responsive_addons_pro_api_key'];

		$args = array(
			'api_key' => $api_key,
		);

		$wcam_lib_responsive_elementor_addons->wc_am_instance_id = get_option( $wcam_lib_responsive_elementor_addons->wc_am_instance_key );

		$activate_results = json_decode( $wcam_lib_responsive_elementor_addons->activate( $args ), true );

		$api_key_array = array(
			$wcam_lib_responsive_elementor_addons->wc_am_api_key_key => $api_key,
		);
		update_option( $wcam_lib_responsive_elementor_addons->data_key, $api_key_array );

		if ( true === $activate_results['success'] && true === $activate_results['activated'] ) {
			update_option( $wcam_lib_responsive_elementor_addons->wc_am_activated_key, 'Activated' );
			update_option( $wcam_lib_responsive_elementor_addons->wc_am_deactivate_checkbox_key, 'off' );
		}

		if ( false == $activate_results && ! empty( $wcam_lib_responsive_elementor_addons->data ) && ! empty( $wcam_lib_responsive_elementor_addons->wc_am_activated_key ) ) {
			update_option( $wcam_lib_responsive_elementor_addons->data[ $wcam_lib_responsive_elementor_addons->wc_am_activated_key ], 'Deactivated' );
		}

		if ( isset( $activate_results['data']['error_code'] ) && ! empty( $wcam_lib_responsive_elementor_addons->data ) && ! empty( $wcam_lib_responsive_elementor_addons->wc_am_activated_key ) ) {
			update_option( $wcam_lib_responsive_elementor_addons->data[ $wcam_lib_responsive_elementor_addons->wc_am_activated_key ], 'Deactivated' );
		}
	}

	/**
	 * Check if Responsive Addons Pro is installed.
	 */
	public function is_responsive_pro_is_installed() {

		check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Error: You are not allowed to perform this action.', 'responsive-addons' ),
				)
			);
		}

		$responsive_pro_slug = 'responsive-addons-pro/responsive-addons-pro.php';
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if ( ! empty( $all_plugins[ $responsive_pro_slug ] ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Check if Responsive Addons Pro License is Active.
	 */
	public function is_responsive_pro_license_is_active() {

		check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Error: You are not allowed to perform this action.', 'responsive-addons' ),
				)
			);
		}

		global $wcam_lib_responsive_pro;
		$license_status = $wcam_lib_responsive_pro->license_key_status();

		if ( ! empty( $license_status['data']['activated'] ) && $license_status['data']['activated'] ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Check if Responsive Addons Pro License is Active.
	 *
	 * @since 1.0.1
	 */
	public function is_responsive_pro_license_active() {

		global $wcam_lib_responsive_pro;
		$license_status = $wcam_lib_responsive_pro->license_key_status();

		if ( ! empty( $license_status['data']['activated'] ) && $license_status['data']['activated'] ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if Responsive Addons Pro License is Active.
	 *
	 * @since 2.2.3
	 */
	public function is_responsive_elementor_addons_license_active() {

		global $wcam_lib_responsive_elementor_addons;

		if ( is_null( $wcam_lib_responsive_elementor_addons ) ) {
			return false;
		}

		if ( 'Activated' === get_option( $wcam_lib_responsive_elementor_addons->wc_am_activated_key ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get license response from cyberchimps.com
	 *
	 * @param string $license_key License Key.
	 * @param string $cyberchips_url cyberchimps url.
	 * @param int    $product_id Product Id.
	 * @param array  $pro_plugins Pro Plugins.
	 * @return bool
	 */
	public function get_cc_api_response( $license_key = '', $cyberchips_url = '', $product_id = '', $pro_plugins = array() ) {
		$args = array(
			'api_key'              => $license_key,
			'wc_cc_action'         => 'get_third_party_plugins_data',
			'site_url'             => str_ireplace( array( 'http://', 'https://' ), '', home_url() ),
			'product_id'           => $product_id,
			'required_pro_plugins' => $pro_plugins,
			'wc_am_instance_id'    => self::get_instance_id(),
		);

		// Check for a plugin update.
		$response = json_decode( $this->send_query( $args, $cyberchips_url ), true );

		if ( false !== $response && true === $response['success'] ) {
			return $response;
		}
		return false;

	}

	/**
	 * Sends and receives data to and from the server API
	 *
	 * @since  1.0.0
	 *
	 * @param array  $args Arguments.
	 * @param string $cyberchips_url Cyberchimps site url.
	 *
	 * @return bool|string
	 */
	public function send_query( $args, $cyberchips_url ) {
		$target_url = esc_url_raw( add_query_arg( 'wc-api', 'resp-pro-install', $cyberchips_url ) . '&' . http_build_query( $args ) );
		$request    = wp_safe_remote_post( $target_url, array( 'timeout' => 15 ) );

		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) !== 200 ) {
			return false;
		}

		$response = wp_remote_retrieve_body( $request );

		return ! empty( $response ) ? $response : false;
	}

	/**
	 * Check is plugin is installed.
	 *
	 * @param (String) $plugin_init Plugin Init.
	 * @since     1.0.0
	 */
	public function is_plugin_installed( $plugin_init ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if ( ! empty( $all_plugins[ $plugin_init ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Install Plugin.
	 *
	 * @param (String) $plugin_zip Plugin zip.
	 * @since     1.0.0
	 */
	public function install_plugin( $plugin_zip ) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		wp_cache_flush();

		$upgrader  = new Plugin_Upgrader();
		$installed = $upgrader->install( $plugin_zip );

		return $installed;
	}

	/**
	 * Load Responsive Ready Sites Importer Pro
	 *
	 * @since 1.0.0
	 */
	public function load_responsive_sites_importer_pro() {
		require_once RESPONSIVE_ADDONS_PRO_DIR . 'includes/importers/class-responsive-ready-sites-importer-pro.php';
	}

	/**
	 * Display License form.
	 *
	 * @since 1.0.0
	 */
	public function display_license_form() {
		?>
		<div class="responsive-addons-tabs">
			<div id="responsive-addons-license" class="tab-content active">
		<?php
		$this->license_options = get_option( 'my_option_name' );
		?>
				<form method="post" action="options.php">
		<?php
		settings_fields( 'responsive_license_group' );
		do_settings_sections( 'responsive-license-setting' );
		?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="submit button button-primary" value="Validate & Activate">
				</p>
				</form>
			</div>
			<div id="responsive-addons-license-info">
				<form method="post" action="options.php">
				<?php settings_fields( 'responsive_license_info_group' ); ?>

			<?php do_settings_sections( 'responsive-license-deactivation-setting' ); ?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="submit button button-primary" value="De-activate">
				</p>
				</form>
			</div>
		</div>
		<?php
	}


	/**
	 * Submit License form.
	 *
	 * @param (Array) $args request parameters.
	 * @since 1.0.0
	 */
	public function submit_license_form( $args ) {
		global $wcam_lib_responsive_pro;
		$activate_results = json_decode( $wcam_lib_responsive_pro->activate( $args ), true );

		if ( true === $activate_results['success'] && true === $activate_results['activated'] ) {
			add_settings_error( 'activate_text', 'activate_msg', sprintf( __( '%s activated. ', 'responsive-addons-pro' ), esc_attr( $wcam_lib_responsive_pro->software_title ) ) . esc_attr( "{$activate_results['message']}." ), 'updated' );
			update_option( $wcam_lib_responsive_pro->wc_am_activated_key, 'Activated' );
			update_option( $wcam_lib_responsive_pro->wc_am_deactivate_checkbox_key, 'off' );
		}

		if ( false === $activate_results && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
			add_settings_error( 'api_key_check_text', 'api_key_check_error', esc_html__( 'Connection failed to the License Key API server. Try again later. There may be a problem on your server preventing outgoing requests, or the store is blocking your request to activate the plugin/theme.', 'responsive-addons-pro' ), 'error' );
			update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
		}

		if ( isset( $activate_results['data']['error_code'] ) && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
			add_settings_error( 'wc_am_client_error_text', 'wc_am_client_error', esc_attr( "{$activate_results['data']['error']}" ), 'error' );
			update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
		}
	}

	/**
	 * Register and add settings.
	 */
	public function load_license_page_settings() {
		register_setting(
			'responsive_license_group',
			'responsive_license_option',
			array( $this, 'submit_license_form' )
		);

		add_settings_section(
			'responsive_license_section',
			'',
			array( $this, 'print_section_info' ),
			'responsive-license-setting'
		);

		add_settings_field(
			'api_key',
			'License key:',
			array( $this, 'api_key_callback' ),
			'responsive-license-setting',
			'responsive_license_section'
		);

		register_setting(
			'responsive_license_info_group',
			'responsive_license_info',
			array( $this, 'deactivate_license' )
		);

		add_settings_section(
			'responsive_license_deactivation_section',
			'',
			array( $this, 'print_deactivation_section_info' ),
			'responsive-license-deactivation-setting'
		);

		add_settings_field(
			'api_key',
			'License key:',
			array( $this, 'api_key_callback' ),
			'responsive-license-deactivation-setting',
			'responsive_license_deactivation_section'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function sanitize( $input ) {
		$new_input = array();
		if ( isset( $input['api_key'] ) ) {
			$new_input['api_key'] = absint( $input['api_key'] );
		}

		if ( isset( $input['title'] ) ) {
			$new_input['title'] = sanitize_text_field( $input['title'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		echo '<div class="description">Please validate & activate your license key to get pro updates & support. You can get your license key from the My Account area on CyberChimps.com.</div>';
	}

	/**
	 * Print the Deactivation Section text
	 */
	public function print_deactivation_section_info() {
		echo '<div class="description">Please find your Responsive license details below.</div>';
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function api_key_callback() {
		printf(
			'<input type="text" id="api_key" class="responsive_api_key" name="responsive_license_option[api_key]" value="%s" />',
			isset( $this->options['api_key'] ) ? esc_attr( $this->options['api_key'] ) : ''
		);
	}

	/**
	 * Deactivates the API Key.
	 *
	 * @param array $input Contains all settings fields as array keys.
	 */
	public function deactivate_license( $input ) {

		global $wcam_lib_responsive_pro;
		$activation_status = get_option( $wcam_lib_responsive_pro->wc_am_activated_key );
		$options           = ( 'on' === $input ? 'on' : 'off' );

		$args = array(
			'api_key' => $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ],
		);

		if ( 'on' === $options && 'Activated' === $activation_status && '' !== $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ] ) {
			// deactivates API Key key activation.
			$activate_results = json_decode( $wcam_lib_responsive_pro->deactivate( $args ), true );

			if ( true === $activate_results['success'] && true === $activate_results['deactivated'] ) {
				if ( ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
					update_option( $wcam_lib_responsive_pro->wc_am_activated_key, 'Deactivated' );
					add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg', esc_html__( 'API Key deactivated. ', 'responsive-addons-pro' ) . esc_attr( "{$activate_results['activations_remaining']}." ), 'updated' );
				}

				return $options;
			}

			if ( isset( $activate_results['data']['error_code'] ) && ! empty( $wcam_lib_responsive_pro->data ) && ! empty( $wcam_lib_responsive_pro->wc_am_activated_key ) ) {
				add_settings_error( 'wc_am_client_error_text', 'wc_am_client_error', esc_attr( "{$activate_results['data']['error']}" ), 'error' );
				update_option( $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_activated_key ], 'Deactivated' );
			}
		} else {

			return $options;
		}

		return false;
	}

	/**
	 * Add Admin Notice.
	 *
	 * @since 1.0.1
	 */
	public function add_free_plugin_and_theme_installation_notice() {
		$theme = wp_get_theme();

		if ( ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme || $this->is_activation_theme_notice_expired() ) && is_plugin_active( 'responsive-add-ons/responsive-add-ons.php' ) ) {
			return;
		}

		$class = 'responsive-notice notice notice-error';

		$theme_status  = 'responsive-theme-' . $this->get_theme_status();
		$plugin_status = 'responsive-addons-' . $this->get_responsive_addons_status();

		$image_path = RESPONSIVE_ADDONS_PRO_URI . 'admin/images/responsive-thumbnail.jpg';
		?>
		<div id="responsive-theme-addons-activation" class="<?php echo esc_html( $class ); ?>">
			<div class="responsive-addons-pro-message-inner">
				<div class="responsive-addons-pro-message-icon">
					<div class="">
						<img src="<?php echo $image_path; ?>" alt="Responsive Pro">
					</div>
				</div>
				<div class="responsive-addons-pro-message-content">
					<p><?php echo esc_html( 'Responsive Pro needs the Responsive theme & the Responsive Starter Templates plugin to function.' ); ?> </p>
					<p class="responsive-addons-pro-message-actions">
						<input type="hidden" id="responsive-theme-status" value="<?php echo esc_html( $theme_status ); ?>">
						<input type="hidden" id="responsive-addons-status" value="<?php echo esc_html( $plugin_status ); ?>">
						<a href="#" class="responsive-install-theme-and-addons button button-primary">Install & Activate Now</a>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Add activation guide notice.
	 *
	 * @since 1.0.1
	 */
	public function add_activation_guide_notice() {
		global $pagenow;
		if ( 'options-general.php' === $pagenow && ! $this->is_responsive_pro_license_active() ) {
				echo '<div class="notice notice-info">
          <p>The API key can be found in your My Account > API Keys area on CyberChimps. <a href="https://cyberchimps.com/my-account/api-keys/" target="_blank">Get it here</a></p>
         </div>';
		}
	}

	/**
	 * Is notice expired?
	 *
	 * @since 1.0.1
	 *
	 * @return boolean
	 */
	public static function is_activation_theme_notice_expired() {

		// Check the user meta status if current notice is dismissed.
		$meta_status = get_user_meta( get_current_user_id(), 'responsive-theme-activation', true );

		if ( empty( $meta_status ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get theme install, active or inactive status.
	 *
	 * @since 1.0.1
	 *
	 * @return string Theme status
	 */
	public function get_theme_status() {

		$theme = wp_get_theme();

		// Theme installed and activate.
		if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {
			return 'installed-and-active';
		}

		// Theme installed but not activate.
		foreach ( (array) wp_get_themes() as $theme_dir => $theme ) {
			if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {
				return 'installed-but-inactive';
			}
		}

		return 'not-installed';
	}

	/**
	 * Get Responsive Addons install, active or inactive status.
	 *
	 * @since 1.0.1
	 *
	 * @return string Theme status
	 */
	public function get_responsive_addons_status() {

		$responsive_addons_slug = 'responsive-add-ons/responsive-add-ons.php';
		if ( is_plugin_active( $responsive_addons_slug ) ) {
			return 'installed-and-active';
		} elseif ( self::is_plugin_installed( $responsive_addons_slug ) ) {
			return 'installed-but-inactive';
		} else {
			return 'not-installed';
		}
	}

	/**
	 * Activate theme
	 *
	 * @since 1.0.1
	 * @return void
	 */
	public function activate_theme() {

		check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

		if ( ! current_user_can( 'switch_themes' ) ) {
			wp_send_json_error( __( 'You are not allowed to activate the Theme', 'responsive-addons' ) );
		}

		switch_theme( 'responsive' );

		wp_send_json_success(
			array(
				'success' => true,
				'message' => __( 'Theme Activated', 'responsive-addons-pro' ),
			)
		);
	}

	/**
	 * Activate Responsive Addons
	 *
	 * @since 1.0.0
	 */
	public function activate_responsive_adddons() {

		check_ajax_referer( 'responsive-addons', '_ajax_nonce' );

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Error: You don\'t have the required permissions to install plugins.', 'responsive-addons' ),
				)
			);
		}

		if ( ! isset( $_POST['init'] ) || ! $_POST['init'] ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Plugins data is missing.', 'responsive-addons' ),
				)
			);
		}

		$plugin_init = ( isset( $_POST['init'] ) ) ? esc_attr( $_POST['init'] ) : '';

		$activate = activate_plugin( $plugin_init, '', false, true );

		if ( is_wp_error( $activate ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => $activate->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'success' => true,
				'message' => __( 'Plugin Activated', 'responsive-addons-pro' ),
			)
		);

	}

	/**
	 * API Request Params
	 *
	 * @since 1.0.3
	 *
	 * @param  array $args API request arguments.
	 * @return arrray       Filtered API request params.
	 */
	public function api_request_params( $args = array() ) {

		$args['api_key']                = self::get_api_key();
		$args['wc_am_instance_id']      = self::get_instance_id();
		$args['product_id']             = self::get_product_id();
		$args['responsive_pro_version'] = RESPONSIVE_ADDONS_PRO_VERSION;
		$args['responsive_addons_ver']  = RESPONSIVE_ADDONS_VER;

		return $args;
	}

	/**
	 * Get Product ID
	 *
	 * @since 1.0.4
	 */
	public function get_product_id() {
		global $wcam_lib_responsive_pro;
		$product_id = get_option( $wcam_lib_responsive_pro->wc_am_product_id );
		return $product_id;
	}

	/**
	 * Get Api key
	 *
	 * @since 1.0.3
	 */
	public function get_api_key() {
		global $wcam_lib_responsive_pro;
		$license_key = $wcam_lib_responsive_pro->data[ $wcam_lib_responsive_pro->wc_am_api_key_key ];
		return $license_key;
	}

	/**
	 * Get Instance Id
	 *
	 * @since 1.0.3
	 */
	public function get_instance_id() {
		global $wcam_lib_responsive_pro;
		$instance_id = $wcam_lib_responsive_pro->wc_am_instance_id;
		return $instance_id;
	}

	/**
	 * Register custom font menu
	 *
	 * @since 1.0.0
	 */
	public function register_custom_fonts_menu() {

		$title = apply_filters( 'responsive_custom_fonts_menu_title', __( 'Custom Fonts', 'responsive-addons' ) );
		add_submenu_page(
			$this->parent_menu_slug,
			$title,
			$title,
			Responsive_Pro_Custom_Fonts_Taxonomy::$capability,
			'edit-tags.php?taxonomy=' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug
		);

	}

	/**
	 * Highlight custom font menu
	 *
	 * @since 1.0.0
	 */
	public function custom_fonts_menu_highlight() {
		global $parent_file, $submenu_file;

		if ( 'edit-tags.php?taxonomy=' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug === $submenu_file ) {
			$parent_file = $this->parent_menu_slug; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
		if ( get_current_screen()->id != 'edit-' . Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ) {
			return;
		}

		?><style>#addtag div.form-field.term-slug-wrap, #edittag tr.form-field.term-slug-wrap { display: none; }
			#addtag div.form-field.term-description-wrap, #edittag tr.form-field.term-description-wrap { display: none; }</style><script>jQuery( document ).ready( function( $ ) {
				var $wrapper = $( '#addtag, #edittag' );
				$wrapper.find( 'tr.form-field.term-name-wrap p, div.form-field.term-name-wrap > p' ).text( '<?php esc_html_e( 'The name of the font as it appears in the customizer options.', 'responsive-addons' ); ?>' );
			} );</script>
			<?php
	}

	/**
	 * Manage Columns
	 *
	 * @since 1.0.0
	 * @param array $columns default columns.
	 * @return array $columns updated columns.
	 */
	public function manage_columns( $columns ) {

		$screen = get_current_screen();
		// If current screen is add new custom fonts screen.
		if ( isset( $screen->base ) && 'edit-tags' == $screen->base ) {

			$old_columns = $columns;
			$columns     = array(
				'cb'   => $old_columns['cb'],
				'name' => $old_columns['name'],
			);

		}
		return $columns;
	}

	/**
	 * Add new Taxonomy data
	 *
	 * @since 1.0.0
	 */
	public function add_new_taxonomy_data() {
		$this->font_file_new_field( 'font_woff_2', __( 'Upload Font', 'responsive-addons' ), __( 'Allowed Font types are .woff2, .woff, .ttf, .eot, .svg, .otf', 'responsive-addons' ) );

		$this->select_new_field(
			'font-display',
			__( 'Font Display', 'responsive-addons' ),
			__( 'Select font-display property for this font', 'responsive-addons' ),
			array(
				'auto'     => 'auto',
				'block'    => 'block',
				'swap'     => 'swap',
				'fallback' => 'fallback',
				'optional' => 'optional',
			)
		);
	}

	/**
	 * Edit Taxonomy data
	 *
	 * @since 1.0.0
	 * @param object $term taxonomy terms.
	 */
	public function edit_taxonomy_data( $term ) {

		$data = Responsive_Pro_Custom_Fonts_Taxonomy::get_font_links( $term->term_id );
		$this->font_file_edit_field( 'font_woff_2', __( 'Upload Font', 'responsive-addons' ), $data['font_woff_2'], __( 'Allowed Font types are .woff2, .woff, .ttf, .eot, .svg, .otf', 'responsive-addons' ) );

		$this->select_edit_field(
			'font-display',
			__( 'Font Display', 'responsive-addons' ),
			$data['font-display'],
			__( 'Select font-display property for this font', 'responsive-addons' ),
			array(
				'auto'     => 'Auto',
				'block'    => 'Block',
				'swap'     => 'Swap',
				'fallback' => 'Fallback',
				'optional' => 'Optional',
			)
		);
	}

	/**
	 * Add Taxonomy data field
	 *
	 * @since 1.0.0
	 * @param int    $id current term id.
	 * @param string $title font type title.
	 * @param string $description title font type description.
	 * @param string $value title font type meta values.
	 */
	protected function font_file_new_field( $id, $title, $description, $value = '' ) {
		?>
		<div class="responsive-custom-fonts-file-wrap form-field term-<?php echo esc_attr( $id ); ?>-wrap" >

			<label for="font-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			<input type="text" id="font-<?php echo esc_attr( $id ); ?>" class="responsive-custom-fonts-link <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $value ); ?>" />
			<a href="#" class="responsive-custom-fonts-upload button" data-upload-type="<?php echo esc_attr( $id ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
				<path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
				<path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
			</svg>
			</a>
			<p><?php echo esc_html( $description ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render select field for the new font screen.
	 *
	 * @param String $id Field ID.
	 * @param String $title Field Title.
	 * @param String $description Field Description.
	 * @param Array  $select_fields Select fields as Array.
	 * @return void
	 */
	protected function select_new_field( $id, $title, $description, $select_fields ) {
		?>
		<div class="responsive-custom-fonts-file-wrap form-field term-<?php echo esc_attr( $id ); ?>-wrap" >
			<label for="font-<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></label>
			<select type="select" id="font-<?php echo esc_attr( $id ); ?>" class="responsive-custom-font-select-field <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ); ?>[<?php echo esc_attr( $id ); ?>]" />
				<?php
				foreach ( $select_fields as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>;
				<?php } ?>
			</select>
		</div>
		<?php
	}

	/**
	 * Render select field for the edit font screen.
	 *
	 * @param String $id Field ID.
	 * @param String $title Field Title.
	 * @param String $saved_val Field Value.
	 * @param String $description Field Description.
	 * @param Array  $select_fields Select fields as Array.
	 * @return void
	 */
	private function select_edit_field( $id, $title, $saved_val, $description, $select_fields ) {
		?>
		<tr class="responsive-custom-fonts-file-wrap form-field term-<?php echo esc_attr( $id ); ?>-wrap ">
			<th scope="row">
				<label for="metadata-<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $title ); ?>
				</label>
			</th>
			<td>
			<select type="select" id="font-<?php echo esc_attr( $id ); ?>" class="responsive-custom-font-select-field <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ); ?>[<?php echo esc_attr( $id ); ?>]" />
				<?php
				foreach ( $select_fields as $key => $value ) {
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $saved_val ); ?>><?php echo esc_html( $value ); ?></option>;
				<?php } ?>
			</select>
				<p><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Add Taxonomy data field
	 *
	 * @since 1.0.0
	 * @param int    $id current term id.
	 * @param string $title font type title.
	 * @param string $value title font type meta values.
	 * @param string $description title font type description.
	 */
	protected function font_file_edit_field( $id, $title, $value, $description ) {
		?>
		<tr class="responsive-custom-fonts-file-wrap form-field term-<?php echo esc_attr( $id ); ?>-wrap ">
			<th scope="row">
				<label for="metadata-<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $title ); ?>
				</label>
			</th>
			<td>
				<input id="metadata-<?php echo esc_attr( $id ); ?>" type="text" class="responsive-custom-fonts-link <?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ); ?>[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $value ); ?>" />
				<a href="#" class="responsive-custom-fonts-upload button" data-upload-type="<?php echo esc_attr( $id ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
				  <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
				  <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
					</svg>
				</a>
				<p><?php echo esc_html( $description ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save Taxonomy meta data value
	 *
	 * @since 1.0.0
	 * @param int $term_id current term id.
	 */
	public function save_metadata( $term_id ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST[ Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ] ) ) {// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$value = array_map( 'esc_attr', $_POST[ Responsive_Pro_Custom_Fonts_Taxonomy::$register_taxonomy_slug ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			Responsive_Pro_Custom_Fonts_Taxonomy::update_font_links( $value, $term_id );
		}
	}

	/**
	 * Allowed mime types and file extensions
	 *
	 * @since 1.0.0
	 * @param array $mimes Current array of mime types.
	 * @return array $mimes Updated array of mime types.
	 */
	public function add_fonts_to_allowed_mimes( $mimes ) {
		$mimes['woff']  = 'application/x-font-woff';
		$mimes['woff2'] = 'application/x-font-woff2';
		$mimes['ttf']   = 'application/x-font-ttf';
		$mimes['svg']   = 'image/svg+xml';
		$mimes['eot']   = 'application/vnd.ms-fontobject';
		$mimes['otf']   = 'font/otf';

		return $mimes;
	}

	/**
	 * Correct the mome types and extension for the font types.
	 *
	 * @param array  $defaults File data array containing 'ext', 'type', and
	 *                                          'proper_filename' keys.
	 * @param string $file                      Full path to the file.
	 * @param string $filename                  The name of the file (may differ from $file due to
	 *                                          $file being in a tmp directory).
	 * @return Array File data array containing 'ext', 'type', and
	 */
	public function update_mime_types( $defaults, $file, $filename ) {
		if ( 'ttf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			$defaults['type'] = 'application/x-font-ttf';
			$defaults['ext']  = 'ttf';
		}

		if ( 'otf' === pathinfo( $filename, PATHINFO_EXTENSION ) ) {
			$defaults['type'] = 'application/x-font-otf';
			$defaults['ext']  = 'otf';
		}

		return $defaults;
	}

	public function responsive_upgrade_theme_react() {
		?>
		<div class="notice notice-error">
			<p>Please update to the latest version of <strong>Responsive</strong> theme <strong>(4.6.3 or higher)</strong> to be compatible with <strong>Responsive Pro</strong>. Download the latest <strong>Responsive</strong> theme from the <a href="https://wordpress.org/themes/responsive">WordPress.org</a> Repository.</p>
		</div>
		<?php
	}


}
