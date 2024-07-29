<?php
/**
 * Breadcrumb Toolbar Disable
 *
 * @package Responsive Addons Pro Plugin Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Product_Catalog' ) ) :
	/**
	 * Product Catalog Loader
	 *
	 * @since 1.0.0
	 */
	class Responsive_Addons_Pro_Woocommerce_Product_Catalog {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'customize_register', array( $this, 'customizer_options' ) );
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'product_flip_image' ), 10 );
		}

		/**
		 * Customizer options
		 *
		 * @param object $wp_customize WordPress customizer options.
		 */
		public function customizer_options( $wp_customize ) {
			$theme = wp_get_theme();
			if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {

				$wp_customize->add_setting(
					'breadcrumbs_options',
					array(
						'default'           => 1,
						'sanitize_callback' => 'Responsive\Customizer\\responsive_sanitize_checkbox',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Checkbox_Control(
						$wp_customize,
						'breadcrumbs_options',
						array(
							'label'    => __( 'Breadcrumbs', 'responsive' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'breadcrumbs_options',
						)
					)
				);

				$wp_customize->add_setting(
					'toolbar_options',
					array(
						'default'           => 1,
						'sanitize_callback' => 'Responsive\Customizer\\responsive_sanitize_checkbox',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Checkbox_Control(
						$wp_customize,
						'toolbar_options',
						array(
							'label'    => __( 'Toolbar', 'responsive' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'toolbar_options',
						)
					)
				);
				$wp_customize->add_setting(
					'content_alignment_options',
					array(
						'default'           => 'left',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Button_Control(
						$wp_customize,
						'content_alignment_options',
						array(
							'label'    => esc_html__( 'Content Alignment', 'responsive' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'content_alignment_options',
							'priority' => 10,
							'choices'  => array(
								'left'   => esc_html__( 'dashicons-editor-alignleft', 'responsive' ),
								'center' => esc_html__( 'dashicons-editor-aligncenter', 'responsive' ),
								'right'  => esc_html__( 'dashicons-editor-alignright', 'responsive' ),
							),
						)
					)
				);
				$wp_customize->add_setting(
					'box_shadow_options',
					array(
						'type'              => 'theme_mod',
						'sanitize_callback' => 'responsive_sanitize_number',
						'transport'         => 'refresh',
						'default'           => 0,
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Range_Control(
						$wp_customize,
						'box_shadow_options',
						array(
							'label'       => esc_html__( 'Box Shadow', 'responsive' ),
							'section'     => 'responsive_woocommerce_shop_layout',
							'settings'    => 'box_shadow_options',
							'priority'    => 10,
							'input_attrs' => array(
								'min'  => 0,
								'max'  => 5,
								'step' => 1,
							),
						)
					)
				);
				$wp_customize->add_setting(
					'box_shadow_hover_options',
					array(
						'type'              => 'theme_mod',
						'sanitize_callback' => 'responsive_sanitize_number',
						'transport'         => 'refresh',
						'default'           => 0,
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Range_Control(
						$wp_customize,
						'box_shadow_hover_options',
						array(
							'label'       => esc_html__( 'Box Hover Shadow', 'responsive' ),
							'section'     => 'responsive_woocommerce_shop_layout',
							'settings'    => 'box_shadow_hover_options',
							'priority'    => 10,
							'input_attrs' => array(
								'min'  => 0,
								'max'  => 5,
								'step' => 1,
							),
						)
					)
				);
				$wp_customize->add_setting(
					'product_image_hover_style_options',
					array(
						'default'           => 'none',
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'product_image_hover_style_options',
						array(
							'label'    => esc_html__( 'Product Image Hover Style', 'responsive' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'product_image_hover_style_options',
							'priority' => 10,
							'choices'  => array(
								'none'        => esc_html__( 'None', 'responsive' ),
								'swap-images' => esc_html__( 'Swap Images', 'responsive' ),
								'fade'        => esc_html__( 'Fade', 'responsive' ),
								'zoom'        => esc_html__( 'Zoom', 'responsive' ),
								'zoom-fade'   => esc_html__( 'Zoom Fade', 'responsive' ),
							),
						)
					)
				);
				/**
				 * Native Cart Popup
				*/
				$native_cart_popup_separator = esc_html__( 'Native Cart Popup', 'responsive-addons-pro' );
				responsive_addons_separator_control( $wp_customize, 'native_cart_popup_separator', $native_cart_popup_separator, 'responsive_woocommerce_shop_layout', 150 );

				// Enable Popup.
				$enable_popup = esc_html__( 'Enable Popup', 'responsive-addons-pro' );
				responsive_addons_checkbox_control( $wp_customize, 'enable_native_cart_popup', $enable_popup, 'responsive_woocommerce_shop_layout', 150, 0, null, 'refresh' );

				// Display Popup in customizer.
				$display_popup = esc_html__( 'Preview Popup In Customizer', 'responsive-addons-pro' );
				$desc          = 'This checkbox is just to allow you to display the popup in the customizer preview.';
				responsive_addons_checkbox_control( $wp_customize, 'native_cart_popup_display', $display_popup, 'responsive_woocommerce_shop_layout', 150, 0, null, 'postMessage', $desc );

				// Positioning of popup elements.
				$elements = apply_filters(
					'responsive_popup_elements',
					array(
						'title'       => esc_html__( 'Title', 'responsive-addons-pro' ),
						'content'     => esc_html__( 'Content', 'responsive-addons-pro' ),
						'buttons'     => esc_html__( 'Buttons', 'responsive-addons-pro' ),
						'bottom_text' => esc_html__( 'Bottom Text', 'responsive-addons-pro' ),
					)
				);

				$wp_customize->add_setting(
					'responsive_popup_elements_positioning',
					array(
						'default'           => array( 'title', 'content', 'buttons', 'bottom_text' ),
						'sanitize_callback' => 'responsive_sanitize_multi_choices',
						'transport'         => 'refresh',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Sortable_Control(
						$wp_customize,
						'responsive_popup_elements_positioning',
						array(
							'label'    => esc_html__( 'Elements Positioning', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'responsive_popup_elements_positioning',
							'priority' => 150,
							'choices'  => $elements,
						)
					)
				);

				// Popup Title Text .
				$popup_title_text = __( 'Title Text', 'responsive-addons-pro' );
				responsive_addons_text_control( $wp_customize, 'popup_title_text', $popup_title_text, 'responsive_woocommerce_shop_layout', 150, 'Item added to your cart', null, 'sanitize_text_field', 'text', 'postMessage' );

				// Popup Content .
				$default_content = esc_html__( '[responsive_woo_cart_items] items in the cart ([responsive_woo_total_cart])', 'responsive-addons-pro' );
				$popup_content   = __( 'Content', 'responsive-addons-pro' );
				responsive_addons_text_control( $wp_customize, 'popup_content', $popup_content, 'responsive_woocommerce_shop_layout', 150, $default_content, null, 'sanitize_text_field', 'textarea', 'postMessage' );

				// Continue Button Text.
				$popup_continue_btn_text = __( 'Continue Button Text', 'responsive-addons-pro' );
				responsive_addons_text_control( $wp_customize, 'popup_continue_btn_text', $popup_continue_btn_text, 'responsive_woocommerce_shop_layout', 150, 'Continue Shopping', null, 'sanitize_text_field', 'text', 'postMessage' );

				// Go cart Button Text.
				$popup_cart_btn_text = __( 'Go Cart Button Text', 'responsive-addons-pro' );
				responsive_addons_text_control( $wp_customize, 'popup_cart_btn_text', $popup_cart_btn_text, 'responsive_woocommerce_shop_layout', 150, 'Go To The Cart', null, 'sanitize_text_field', 'text', 'postMessage' );

				// Bottom Text.
				$default_bottom_text = esc_html__( '[responsive_woo_free_shipping_left]', 'responsive-addons-pro' );
				$popup_bottom_text   = __( 'Bottom Text', 'responsive-addons-pro' );
				responsive_addons_text_control( $wp_customize, 'popup_bottom_text', $popup_bottom_text, 'responsive_woocommerce_shop_layout', 150, $default_bottom_text, null, 'sanitize_text_field', 'text', 'postMessage' );

				// Styling.
				$native_cart_popup_styling_separator = esc_html__( 'Native Cart Popup Styling', 'responsive-addons-pro' );
				responsive_addons_separator_control( $wp_customize, 'native_cart_popup_styling_separator', $native_cart_popup_styling_separator, 'responsive_woocommerce_shop_layout', 160 );

				// Main Content Width.

				// Desktop.
				$popup_width = esc_html__( 'Popup Width (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_width', $popup_width, 'responsive_woocommerce_shop_layout', 160, 600, null, 5000, 20, 'postMessage' );

				// Tablet.
				$popup_width_tablet = esc_html__( 'Popup Tablet Width (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_width_tablet', $popup_width_tablet, 'responsive_woocommerce_shop_layout', 160, 600, null, 5000, 20, 'postMessage' );

				// Mobile.
				$popup_width_mobile = esc_html__( 'Popup Mobile Width (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_width_mobile', $popup_width_mobile, 'responsive_woocommerce_shop_layout', 160, 600, null, 5000, 20, 'postMessage' );

				// Main Content Height.

				// Desktop.
				$popup_height = esc_html__( 'Popup Height (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_height', $popup_height, 'responsive_woocommerce_shop_layout', 160, 600, null, 5000, 20, 'postMessage' );

				// Tablet.
				$popup_height_tablet = esc_html__( 'Popup Tablet Height (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_height_tablet', $popup_height_tablet, 'responsive_woocommerce_shop_layout', 160, 350, null, 5000, 20, 'postMessage' );

				// Mobile.
				$popup_height_mobile = esc_html__( 'Popup Mobile Height (px)', 'responsive-addons-pro' );
				responsive_addons_drag_number_control( $wp_customize, 'popup_height_mobile', $popup_height_mobile, 'responsive_woocommerce_shop_layout', 160, 450, null, 5000, 20, 'postMessage' );

				// Popup Padding.
				$popup_padding = esc_html__( 'Popup Padding (px)', 'responsive-addons-pro' );
				responsive_addons_padding_control( $wp_customize, 'popup', 'responsive_woocommerce_shop_layout', 160, 50, 25, null, $popup_padding );

				// Popup radius.
				$popup_radius = esc_html__( 'Popup Border Radius (px)', 'responsive-addons-pro' );
				responsive_addons_padding_control( $wp_customize, 'popup_radius', 'responsive_woocommerce_shop_layout', 160, 600, 600, null, $popup_radius, 600 );

				// Popup Background.
				$popup_bg = __( 'Popup Background', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_bg', $popup_bg, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Popup overlay color.
				$popup_overlay = __( 'Popup Overlay color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_overlay', $popup_overlay, 'responsive_woocommerce_shop_layout', 165, 'rgba(0,0,0,0.7)' );

				// Check Mark Background color.
				$popup_checkmark_bg = __( 'Check Mark Background', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_checkmark_bg', $popup_checkmark_bg, 'responsive_woocommerce_shop_layout', 165, '#5bc142' );

				// Check Mark Color.
				$popup_checkmark = __( 'Check Mark Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_checkmark', $popup_checkmark, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Title Color.
				$popup_title_color = __( 'Title Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_title', $popup_title_color, 'responsive_woocommerce_shop_layout', 165, '#333333' );

				// Content Color.
				$popup_content = __( 'Content Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_content', $popup_content, 'responsive_woocommerce_shop_layout', 165, '#777777' );

				// Continue Button Background.
				$popup_continue_btn_bg = __( 'Continue Button Background', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn_bg', $popup_continue_btn_bg, 'responsive_woocommerce_shop_layout', 165, '#0066CC' );

				// Continue Button Color.
				$popup_continue_btn_color = __( 'Continue Button Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn', $popup_continue_btn_color, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Continue Button Border Color.
				$popup_continue_btn_border = __( 'Continue Button Border Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn_border', $popup_continue_btn_border, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Continue Button Background: Hover.
				$popup_continue_btn_hover_bg = __( 'Continue Button Background: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn_hover_bg', $popup_continue_btn_hover_bg, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Continue Button Color: Hover.
				$popup_continue_btn_hover = __( 'Continue Button Color: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn_hover', $popup_continue_btn_hover, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Continue Button Border Color: Hover.
				$popup_continue_btn_hover_border = __( 'Continue Button Border Color: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_continue_btn_hover_border', $popup_continue_btn_hover_border, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Cart Button Background.
				$popup_cart_btn_bg = __( 'Cart Button Background', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn_bg', $popup_cart_btn_bg, 'responsive_woocommerce_shop_layout', 165, '#0066CC' );

				// Cart Button Color.
				$popup_cart_btn = __( 'Cart Button Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn', $popup_cart_btn, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Cart Button Border Color.
				$popup_cart_btn_border = __( 'Cart Button Border Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn_border', $popup_cart_btn_border, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Cart Button Background: Hover.
				$popup_cart_btn_hover_bg = __( 'Cart Button Background: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn_hover_bg', $popup_cart_btn_hover_bg, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Cart Button Color: Hover.
				$popup_cart_btn_hover = __( 'Cart Button Color: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn_hover', $popup_cart_btn_hover, 'responsive_woocommerce_shop_layout', 165, '#ffffff' );

				// Cart Button Border Color: Hover.
				$popup_cart_btn_hover_border = __( 'Cart Button Border Color: Hover', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_cart_btn_hover_border', $popup_cart_btn_hover_border, 'responsive_woocommerce_shop_layout', 165, '#10659C' );

				// Bottom Text Color.
				$popup_text_color = __( 'Bottom Text Color', 'responsive-addons-pro' );
				responsive_addons_color_control( $wp_customize, 'popup_text', $popup_text_color, 'responsive_woocommerce_shop_layout', 165, '#777777' );

			}
		}

		/**
		 * Product Flip Image
		 */
		public function product_flip_image() {

			global $product;

			$hover_style = get_theme_mod( 'product_image_hover_style_options' );

			if ( 'swap-images' === $hover_style ) {

				$attachment_ids = $product->get_gallery_image_ids();

				if ( $attachment_ids ) {

					$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );

					echo ( wp_get_attachment_image( reset( $attachment_ids ), $image_size, false, array( 'class' => 'show-on-hover' ) ) );
				}
			}
		}
	}

endif;

return new Responsive_Addons_Pro_Woocommerce_Product_Catalog();
