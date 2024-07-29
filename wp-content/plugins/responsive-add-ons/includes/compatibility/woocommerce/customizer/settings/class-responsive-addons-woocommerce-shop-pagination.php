<?php
/**
 * Footer Customizer Options
 *
 * @package Responsive Addons Pro Plugin Woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Addons_Woocommerce_Shop_Pagination' ) ) :
	/**
	 * Footer Customizer Options
	 */
	class Responsive_Addons_Woocommerce_Shop_Pagination {
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
			require_once RESPONSIVE_ADDONS_DIR . 'includes/compatibility/woocommerce/customizer/class-responsive-addons-woocommerce-ext.php';

			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'partials/class-responsive-addons-customizer-ext-woocommerce-partials.php';
			$theme = wp_get_theme();
			if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {

				$wp_customize->add_setting(
					'shop_pagination',
					array(
						'default'           => 'default',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'shop_pagination',
						array(
							'label'    => __( 'Shop Pagination', 'responsive' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'shop_pagination',
							'priority' => 51,
							'choices'  => array(
								'default'  => esc_html__( 'Default', 'responsive' ),
								'infinite' => esc_html__( 'Infinite', 'responsive' ),
							),
						)
					)
				);

				/*
				------------------------------------------------------------------
				// Quick View
				-------------------------------------------------------------------
				*/
				$wp_customize->add_setting(
					'shop_pagination_quick_view',
					array(
						'default'           => 'disabled',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'shop_pagination_quick_view',
						array(
							'label'    => __( 'Shop Quick View', 'responsive-addons-pro' ),
							'section'  => 'responsive_woocommerce_shop_layout',
							'settings' => 'shop_pagination_quick_view',
							'priority' => 54,
							'choices'  => array(
								'disabled'       => esc_html__( 'Disabled', 'responsive-addons-pro' ),
								'on-image'       => esc_html__( 'On Image', 'responsive-addons-pro' ),
								'on-image-click' => esc_html__( 'On Image Click', 'responsive-addons-pro' ),
								'after-summary'  => esc_html__( 'After Summary', 'responsive-addons-pro' ),
							),
						)
					)
				);

				/*
				------------------------------------------------------------------
				// Shop Pagination Style
				-------------------------------------------------------------------
				*/
				$wp_customize->add_setting(
					'shop_pagination_style',
					array(
						'default'           => 'square',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'shop_pagination_style',
						array(
							'active_callback' => 'responsive_addons_pagination_callbacks',
							'label'           => __( 'Shop Pagination Style', 'responsive-addons-pro' ),
							'section'         => 'responsive_woocommerce_shop_layout',
							'settings'        => 'shop_pagination_style',
							'priority'        => 52,
							'choices'         => array(
								'square' => __( 'Square', 'responsive-addons-pro' ),
								'circle' => __( 'Circle', 'responsive-addons-pro' ),
							),
						)
					)
				);

				$wp_customize->add_setting(
					'shop-infinite-scroll-event',
					array(
						'default'           => 'scroll',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					new Responsive_Customizer_Select_Control(
						$wp_customize,
						'shop-infinite-scroll-event',
						array(
							'active_callback' => 'responsive_addons_pagination_trigger',
							'label'           => __( 'Event to Trigger Infinite Loading', 'responsive-addons-pro' ),
							'section'         => 'responsive_woocommerce_shop_layout',
							'settings'        => 'shop-infinite-scroll-event',
							'priority'        => 52,
							'choices'         => array(
								'scroll' => __( 'Scroll', 'responsive-addons-pro' ),
								'click'  => __( 'Click', 'responsive-addons-pro' ),
							),
						)
					)
				);

				$wp_customize->add_setting(
					'shop-load-more-text',
					array(
						'default'           => 'Load More',
						'transport'         => 'refresh',
						'sanitize_callback' => 'responsive_sanitize_select',
					)
				);
				$wp_customize->add_control(
					'shop-load-more-text',
					array(
						'active_callback' => 'responsive_addons_load_more_callback',
						'label'           => __( 'Load More Text', 'responsive-addons-pro' ),
						'section'         => 'responsive_woocommerce_shop_layout',
						'settings'        => 'shop-load-more-text',
						'type'            => 'text',
						'priority'        => 53,
						'partial'         => array(
							'selector'            => '.responsive-shop-pagination-infinite .responsive-load-more',
							'container_inclusive' => false,
							'render_callback'     => array( 'Responsive_Customizer_Ext_WooCommerce_Partials', '_render_shop_load_more' ),
						),
					)
				);

					/*
					------------------------------------------------------------------
						// Checkout Width
					-------------------------------------------------------------------
					*/

					$wp_customize->add_setting(
						'responsive_checkout_width',
						array(
							'transport'         => 'refresh',
							'default'           => '960',
							'sanitize_callback' => 'responsive_sanitize_number',
						)
					);

					$wp_customize->add_control(
						new Responsive_Customizer_Range_Control(
							$wp_customize,
							'responsive_checkout_width',
							array(
								'label'       => __( 'Checkout Form Width (px)', 'responsive-addons-pro' ),
								'section'     => 'woocommerce_checkout',
								'settings'    => 'responsive_checkout_width',
								'priority'    => 10,
								'input_attrs' => array(
									'min'  => 0,
									'max'  => 4096,
									'step' => 1,
								),
							)
						)
					);
			}
		}
	}

endif;

return new Responsive_Addons_Woocommerce_Shop_Pagination();
