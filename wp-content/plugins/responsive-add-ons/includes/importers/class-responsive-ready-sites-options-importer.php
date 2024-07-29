<?php
/**
 * Site options importer class.
 *
 * @since  1.0.0
 * @package  Responsive Addon
 */

defined( 'ABSPATH' ) || exit;

/**
 * Site options importer class.
 *
 * @since  2.0.0
 */
class Responsive_Ready_Sites_Options_Importer {

	/**
	 * Instance of Responsive_Ready_Sites_Options_Importer
	 *
	 * @since  1.0.8
	 * @var (Object) Responsive_Ready_Sites_Options_Importer
	 */
	private static $instance = null;

	/**
	 * Instanciate Responsive_Ready_Sites_Options_Importer
	 *
	 * @since  1.0.8
	 * @return (Object) Responsive_Ready_Sites_Options_Importer
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Site Options
	 *
	 * @since 2.0.0
	 *
	 * @return array    List of defined array.
	 */
	private static function site_options() {
		return array(
			'custom_logo',
			'nav_menu_locations',
			'show_on_front',
			'page_on_front',
			'page_for_posts',

			// Plugin: WPForms.
			'wpforms_settings',

			// Plugin: Elementor.
			'elementor_container_width',
			'elementor_cpt_support',
			'elementor_css_print_method',
			'elementor_default_generic_fonts',
			'elementor_disable_color_schemes',
			'elementor_disable_typography_schemes',
			'elementor_editor_break_lines',
			'elementor_exclude_user_roles',
			'elementor_global_image_lightbox',
			'elementor_page_title_selector',
			'elementor_scheme_color',
			'elementor_scheme_color-picker',
			'elementor_scheme_typography',
			'elementor_space_between_widgets',
			'elementor_stretched_section_container',
			'elementor_load_fa4_shim',
			'elementor_active_kit',

			// Plugin: Elementor Pro.
			'elementor_pro_theme_builder_conditions',

			// Plugin: WooCommerce.
			// Pages.
			'woocommerce_shop_page_title',
			'woocommerce_cart_page_title',
			'woocommerce_checkout_page_title',
			'woocommerce_myaccount_page_title',
			'woocommerce_edit_address_page_title',
			'woocommerce_view_order_page_title',
			'woocommerce_change_password_page_title',
			'woocommerce_logout_page_title',

			// Account & Privacy.
			'woocommerce_enable_guest_checkout',
			'woocommerce_enable_checkout_login_reminder',
			'woocommerce_enable_signup_and_login_from_checkout',
			'woocommerce_enable_myaccount_registration',
			'woocommerce_registration_generate_username',

			// Categories.
			'woocommerce_product_cat',

			// Plugin: EventOn.
			'evcal_options_evcal_1',
		);
	}

	/**
	 * Import site options.
	 *
	 * @since  1.0.8
	 *
	 * @param  (Array) $options Array of site options to be imported from the demo.
	 */
	public function import_options( $options = array() ) {

		if ( ! isset( $options ) ) {
			return;
		}

		foreach ( $options as $option_name => $option_value ) {

			// Is option exist in defined array site_options()?
			if ( null !== $option_value ) {

				// Is option exist in defined array site_options()?
				if ( in_array( $option_name, self::site_options(), true ) ) {

					switch ( $option_name ) {

						case 'page_for_posts':
						case 'page_on_front':
							$this->update_page_id_by_option_value( $option_name, $option_value );
							break;

						// insert logo.
						case 'custom_logo':
							$post_guid = get_post_field( 'guid', $option_value );
							$this->insert_logo( $post_guid );
							break;

						// Set WooCommerce page ID by page Title.
						case 'woocommerce_shop_page_title':
						case 'woocommerce_cart_page_title':
						case 'woocommerce_checkout_page_title':
						case 'woocommerce_myaccount_page_title':
						case 'woocommerce_edit_address_page_title':
						case 'woocommerce_view_order_page_title':
						case 'woocommerce_change_password_page_title':
						case 'woocommerce_logout_page_title':
							$this->update_woocommerce_page_id_by_option_value( $option_name, $option_value );
							break;

						// import WooCommerce category images.
						case 'woocommerce_product_cat':
							$this->set_woocommerce_product_cat( $option_value );
							break;

						default:
							update_option( $option_name, $option_value );
							break;
					}
				}
			}
		}
	}

	/**
	 * Update post option
	 *
	 * @since 2.0.0
	 *
	 * @param  string $option_name  Option name.
	 * @param  mixed  $option_value Option value.
	 * @return void
	 */
	private function update_page_id_by_option_value( $option_name, $option_value ) {
		$page = get_page_by_title( $option_value );
		if ( is_object( $page ) ) {
			update_option( $option_name, $page->ID );
		}
	}

	/**
	 * In WP nav menu is stored as ( 'menu_location' => 'menu_id' );
	 * In export we send 'menu_slug' like ( 'menu_location' => 'menu_slug' );
	 * In import we set 'menu_id' from menu slug like ( 'menu_location' => 'menu_id' );
	 *
	 * @since 2.0.0
	 * @param array $nav_menu_locations Array of nav menu locations.
	 */
	private function set_nav_menu_locations( $nav_menu_locations = array() ) {

		$menu_locations = array();

		// Update menu locations.
		if ( isset( $nav_menu_locations ) ) {

			foreach ( $nav_menu_locations as $menu => $value ) {

				$term = get_term_by( 'slug', $value, 'nav_menu' );

				if ( is_object( $term ) ) {
					$menu_locations[ $menu ] = $term->term_id;
				}
			}

			set_theme_mod( 'nav_menu_locations', $menu_locations );
		}
	}

	/**
	 * Insert Logo By URL
	 *
	 * @since 2.0.0
	 * @param  string $image_url Logo URL.
	 * @return void
	 */
	private function insert_logo( $image_url = '' ) {
		$attachment_id = $this->download_image( $image_url );
		if ( $attachment_id ) {
			set_theme_mod( 'custom_logo', $attachment_id );
		}
	}

	/**
	 * Download image by URL
	 *
	 * @since 2.0.0
	 *
	 * @param  string $image_url Logo URL.
	 * @return mixed false|Attachment ID
	 */
	private function download_image( $image_url = '' ) {
		$data = (object) self::sideload_image( $image_url );

		if ( ! is_wp_error( $data ) ) {
			if ( isset( $data->attachment_id ) && ! empty( $data->attachment_id ) ) {
				return $data->attachment_id;
			}
		}

		return false;
	}

	/**
	 *  Download the image by URL
	 *
	 * @since 2.0.0
	 * @param string $file Image URL.
	 * @return int|object|stdClass|string|WP_Error
	 */
	public static function sideload_image( $file ) {
		$data = new stdClass();

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		if ( ! empty( $file ) ) {

			// Set variables for storage, fix file filename for query strings.
			preg_match( '/[^\?]+\.(jpe?g|jpe|svg|gif|png)\b/i', $file, $matches );
			$file_array         = array();
			$file_array['name'] = basename( $matches[0] );

			// Download file to temp location.
			$file_array['tmp_name'] = download_url( $file );

			// If error storing temporarily, return the error.
			if ( is_wp_error( $file_array['tmp_name'] ) ) {
				return $file_array['tmp_name'];
			}

			// Do the validation and storage stuff.
			$id = media_handle_sideload( $file_array, 0 );

			// If error storing permanently, unlink.
			if ( is_wp_error( $id ) ) {
				unlink( $file_array['tmp_name'] );
				return $id;
			}

			// Build the object to return.
			$meta                = wp_get_attachment_metadata( $id );
			$data->attachment_id = $id;
			$data->url           = wp_get_attachment_url( $id );
			$data->thumbnail_url = wp_get_attachment_thumb_url( $id );
			$data->height        = $meta['height'];
			$data->width         = $meta['width'];
		}

		return $data;
	}

	/**
	 * Update WooCommerce page ids.
	 *
	 * @since 2.0.5
	 *
	 * @param  string $option_name  Option name.
	 * @param  mixed  $option_value Option value.
	 * @return void
	 */
	private function update_woocommerce_page_id_by_option_value( $option_name, $option_value ) {
		$option_name = str_replace( '_title', '_id', $option_name );
		$this->update_page_id_by_option_value( $option_name, $option_value );
	}

	/**
	 * Set WooCommerce category images.
	 *
	 * @since 2.0.5
	 *
	 * @param array $cats Array of categories.
	 */
	private function set_woocommerce_product_cat( $cats = array() ) {

		$menu_locations = array();

		if ( isset( $cats ) && is_array( $cats ) ) {

			foreach ( $cats as $key => $cat ) {

				if ( ! empty( $cat['slug'] ) && ! empty( $cat['thumbnail_src'] ) ) {

					$image = (object) self::sideload_image( $cat['thumbnail_src'] );

					if ( ! is_wp_error( $image ) ) {

						if ( isset( $image->attachment_id ) && ! empty( $image->attachment_id ) ) {

							$term = get_term_by( 'slug', $cat['slug'], 'product_cat' );

							if ( is_object( $term ) ) {
								update_term_meta( $term->term_id, 'thumbnail_id', $image->attachment_id );
							}
						}
					}
				}
			}
		}
	}
}
