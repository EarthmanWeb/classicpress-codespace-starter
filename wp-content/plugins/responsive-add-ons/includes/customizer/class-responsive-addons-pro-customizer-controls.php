<?php
/**
 * Responsive Addons Customizer Controls.
 *
 * @package     Responsive_Addons
 * @author      Cyberchimps
 * @copyright   Copyright (c) 2019, Responsive
 * @link        https://cyberchimps.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$theme = wp_get_theme(); // gets the current theme.
if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {

	if ( 'Responsive' === $theme->parent_theme ) {
		$theme = wp_get_theme( 'responsive' );
	}

	if ( version_compare( $theme['Version'], '4.0.5', '>' ) ) {
		add_action( 'customize_register', 'rst_responsive_addons_register_options' );
		add_action( 'customize_preview_init', 'rst_responsive_addons_customize_preview_js' );
		add_action( 'customize_controls_enqueue_scripts', 'rst_custom_react_controls_enqueue_scripts' );

		/**
		 * Register customizer controls.
		 *
		 * @param array $wp_customize WordPress Customize settings.
		 *
		 * @since 2.0.0
		 */
		if ( ! function_exists( 'rst_responsive_addons_register_options' ) ) {
			function rst_responsive_addons_register_options( $wp_customize ) {

				// Load customize control classes.
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/palette/class-responsive-customizer-palette-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/color/class-responsive-customizer-color-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/range/class-responsive-customizer-range-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/slider/class-responsive-customizer-slider-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/sortable/class-responsive-customizer-sortable-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/text/class-responsive-customizer-text-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/typography/class-responsive-customizer-typography-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/dimensions/class-responsive-customizer-dimensions-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/heading/class-responsive-customizer-heading-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/select/class-responsive-customizer-responsive-select-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/checkbox/class-responsive-customizer-responsive-checkbox-control.php';
				require_once RESPONSIVE_ADDONS_DIR . 'includes/customizer/controls/selectbtn/class-responsive-customizer-responsive-selectbtn-control.php';
				// Register JS control types.
				$wp_customize->register_control_type( 'Responsive_Customizer_Palette_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Color_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Range_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Slider_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Sortable_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Text_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Typography_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Dimensions_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Color_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Heading_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Select_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Checkbox_Control' );
				$wp_customize->register_control_type( 'Responsive_Customizer_Select_Button_Control' );
			}
		}

		if ( ! function_exists( 'rst_responsive_addons_customize_preview_js' ) ) {
			function rst_responsive_addons_customize_preview_js() {
				wp_enqueue_script( 'responsive-padding-control', RESPONSIVE_ADDONS_URI . 'includes/customizer/assets/js/customize-preview-padding-control.js', array( 'customize-preview' ), RESPONSIVE_ADDONS_VER, true );

			}
		}

		if ( ! function_exists( 'rst_custom_react_controls_enqueue_scripts' ) ) {
			/**
			 * Enqueues rect based controls.
			 *
			 * @return void
			 */
			function rst_custom_react_controls_enqueue_scripts() {
				// Enqueue Customizer React.JS script.

				$custom_controls_react_deps = array(
					'wp-i18n',
					'wp-components',
					'wp-element',
					'wp-media-utils',
					'wp-block-editor',
				);
				wp_enqueue_script( 'responsive-pro-custom-control-react-script', RESPONSIVE_ADDONS_URI . 'includes/customizer/extend-controls/build/index.js', $custom_controls_react_deps, RESPONSIVE_ADDONS_VER, true );
			}
		}
	}
}
