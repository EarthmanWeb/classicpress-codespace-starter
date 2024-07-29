<?php
/**
 * Woocommerce Plugin Customizer
 *
 * @package woocommerce
 */

if ( ! function_exists( 'responsive_addons_pagination_callbacks' ) ) {
	/**
	 * Function for sanitizing
	 */
	function responsive_addons_pagination_callbacks() {
		$shop_scroll_style = get_theme_mod( 'shop_pagination', 'default' );
		if ( 'default' === $shop_scroll_style ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'responsive_addons_pagination_trigger' ) ) {
	function responsive_addons_pagination_trigger() {
		$shop_scroll_style = get_theme_mod( 'shop_pagination', 'default' );
		if ( 'infinite' === $shop_scroll_style ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'responsive_addons_load_more_callback' ) ) {
	function responsive_addons_load_more_callback() {
		$shop_infinite_loading = get_theme_mod( 'shop-infinite-scroll-event', 'scroll' );
		$shop_scroll_style     = get_theme_mod( 'shop_pagination', 'default' );
		if ( 'infinite' === $shop_scroll_style && 'click' === $shop_infinite_loading ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'responsive_addons_separator_control' ) ) {
	/**
	 * [responsive_addons_separator_control description].
	 *
	 * @param  [type] $wp_customize [description].
	 * @param  [type] $element      [description].
	 * @param  [type] $label        [description].
	 * @param  [type] $section      [description].
	 * @param  [type] $priority     [description].
	 * @param  [type] $active_call     [description].
	 *
	 * @return void               [description].
	 */
	function responsive_addons_separator_control( $wp_customize, $element, $label, $section, $priority, $active_call = null ) {

		/**
		 *  Heading.
		 */
		$wp_customize->add_setting(
			'responsive_' . $element,
			array(
				'sanitize_callback' => 'wp_kses',
			)
		);

		$wp_customize->add_control(
			new Responsive_Customizer_Heading_Control(
				$wp_customize,
				'responsive_' . $element,
				array(
					'label'           => $label,
					'section'         => $section,
					'priority'        => $priority,
					'active_callback' => $active_call,
				)
			)
		);
	}
}
