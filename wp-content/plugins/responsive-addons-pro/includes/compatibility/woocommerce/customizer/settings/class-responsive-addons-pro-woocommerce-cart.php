<?php
/**
 * Footer Customizer Options
 *
 * @package Responsive Addons Pro Plugin Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Addons_Pro_Woocommerce_Cart' ) ) :
	/**
	 * Footer Customizer Options
	 */
	class Responsive_Addons_Pro_Woocommerce_Cart {
		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customizer_options' ) );
		}

		/**
		 * Customizer options
		 *
		 * @param  object $wp_customize WordPress customization option.
		 */
		public function customizer_options( $wp_customize ) {
			$theme = wp_get_theme();

			/*
			------------------------------------------------------------------
				// Cart Icon
			-------------------------------------------------------------------
			*/
			if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {

				$wp_customize->add_setting(
					'responsive_cart_icon',
					array(
						'default'           => 'icon-opencart',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'responsive_cart_icon',
						array(
							'label'    => __( 'Icon', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_cart_layout',
							'settings' => 'responsive_cart_icon',
							'priority' => 20,
							'choices'  => array(
								'icon-opencart'        => esc_html__( 'Default', 'responsive-addons-pro' ),
								'icon-shopping-cart'   => esc_html__( 'Cart', 'responsive-addons-pro' ),
								'icon-shopping-bag'    => esc_html__( 'Bag', 'responsive-addons-pro' ),
								'icon-shopping-basket' => esc_html__( 'Basket', 'responsive-addons-pro' ),
							),
						)
					)
				);

				/*
				------------------------------------------------------------------
					// Header Cart Style
				-------------------------------------------------------------------
				*/

				$wp_customize->add_setting(
					'responsive_cart_style',
					array(
						'default'           => 'none',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'responsive_cart_style',
						array(
							'label'    => __( 'Style', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_cart_layout',
							'settings' => 'responsive_cart_style',
							'priority' => 30,
							'choices'  => array(
								'none'    => esc_html__( 'None', 'responsive-addons-pro' ),
								'outline' => esc_html__( 'Outline', 'responsive-addons-pro' ),
								'fill'    => esc_html__( 'Fill', 'responsive-addons-pro' ),
							),
						)
					)
				);

				/*
				------------------------------------------------------------------
					// Header Cart Background Color
				-------------------------------------------------------------------
				*/

				$wp_customize->add_setting(
					'responsive_cart_color',
					array(
						'default'           => '#000000',
						'transport'         => 'refresh',
						'type'              => 'theme_mod',
						'sanitize_callback' => 'responsive_sanitize_color',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Color_Control(
						$wp_customize,
						'responsive_cart_color',
						array(
							'label'    => __( 'Color', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_cart_layout',
							'settings' => 'responsive_cart_color',
							'required' => array( get_theme_mod( 'responsive_cart_style' ), '!=', 'none' ),
							'priority' => 40,
						)
					)
				);

				/*
				------------------------------------------------------------------
					// Display Cart Title
				-------------------------------------------------------------------
				*/

				$wp_customize->add_setting(
					'responsive_cart_title',
					array(
						'transport'         => 'refresh',
						'sanitize_callback' => 'Responsive\Customizer\\responsive_sanitize_checkbox',
					)
				);

				$wp_customize->add_control(
					new Responsive_Customizer_Checkbox_Control(
						$wp_customize,
						'responsive_cart_title',
						array(
							'label'    => __( 'Display Cart Title', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_cart_layout',
							'settings' => 'responsive_cart_title',
							'priority' => 50,
						)
					)
				);

				/*
				------------------------------------------------------------------
					// Display Count
				-------------------------------------------------------------------
				*/

				$wp_customize->add_setting(
					'responsive_cart_count',
					array(
						'transport'         => 'refresh',
						'sanitize_callback' => 'Responsive\Customizer\\responsive_sanitize_checkbox',
					)
				);

				$wp_customize->add_control(
					new Responsive_Customizer_Checkbox_Control(
						$wp_customize,
						'responsive_cart_count',
						array(
							'label'    => __( 'Display Cart Total', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_cart_layout',
							'settings' => 'responsive_cart_count',
							'priority' => 50,
						)
					)
				);
			}
		}
	}

endif;

return new Responsive_Addons_Pro_Woocommerce_Cart();
