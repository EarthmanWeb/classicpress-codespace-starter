<?php
/**
 * Blog Customizer Options
 *
 * @package Responsive Addons Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Addons_Pro_Menu_Layout_Customizer' ) ) :
	/**
	 * Blog Customizer Options
	 */
	class Responsive_Addons_Pro_Menu_Layout_Customizer {

		/**
		 * Constructor
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

			$wp_customize->add_setting(
				'search_style',
				array(
					'default'           => 'default',
					'transport'         => 'refresh',
					'sanitize_callback' => 'responsive_sanitize_select',
				)
			);
			$wp_customize->add_control(
				new Responsive_Customizer_Select_Control(
					$wp_customize,
					'search_style',
					array(
						'label'           => __( 'Search Style', 'responsive' ),
						'section'         => 'responsive_header_menu_layout',
						'priority'        => 31,
						'settings'        => 'search_style',
						'active_callback' => 'menu_search_icon',
						'choices'         => array(
							'search'      => esc_html__( 'Default', 'responsive' ),
							'full-screen' => esc_html__( 'Full Screen Search', 'responsive' ),
						),
					)
				)
			);

			// Mobile Menu Border width.

			$mobile_menu_border_width_label = __( 'Menu Toggle Border Width (px)', 'responsive' );
			responsive_addons_padding_control( $wp_customize, 'mobile_menu_border', 'responsive_header_menu_layout', 35, 1, 1, 'responsive_toggle_border_color', $mobile_menu_border_width_label );
		}

	}

endif;

return new Responsive_Addons_Pro_Menu_Layout_Customizer();
