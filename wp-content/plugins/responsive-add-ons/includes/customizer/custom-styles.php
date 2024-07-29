<?php
/**
 * Outputs the customizer styles.
 *
 * @package Responsive Addons Pro Plugin Woocommerce
 * @since 0.2
 */

if ( ! function_exists( 'check_is_pro_version_greater' ) ) {
	/**
	 * Verify if the version of responsive pro is greater or not.
	 *
	 * @since 2.6.4
	 */
	function check_is_pro_version_greater() {
		$is_pro_version_greater = false;
		if ( class_exists( 'Responsive_Addons_Pro' ) ) {
			if ( version_compare( RESPONSIVE_ADDONS_PRO_VERSION, '2.6.3', '>' ) ) {
				$is_pro_version_greater = true;
			}
		}
		return $is_pro_version_greater;
	}
}

if ( ! function_exists( 'is_responsive_version_greater' ) ) {
	/**
	 * Verify if the version of responsive theme is greater or not.
	 *
	 * @since 2.6.4
	 */
	function is_responsive_version_greater() {
		$theme = wp_get_theme();
		if ( 'Responsive' === $theme->name || 'Responsive' === $theme->parent_theme ) {
			if ( 'Responsive' === $theme->parent_theme ) {
				$theme = wp_get_theme( 'responsive' );
			}
		}
		$is_theme_version_greater = false;
		if ( version_compare( $theme['Version'], '4.9.6', '>' ) ) {
			$is_theme_version_greater = true;
		}
		return $is_theme_version_greater;
	}
}

if ( ! function_exists( 'responsive_addons_custom_theme_styles' ) ) {
	/**
	 * Outputs the custom styles for the woocommerce plugin.
	 *
	 * @return void
	 */
	function responsive_addons_custom_theme_styles() {
		$breadcrumb_flag = get_theme_mod( 'breadcrumbs_options', 1 );
		$toolbar_flag    = get_theme_mod( 'toolbar_options', 1 );

		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {
			$content_alignment          = get_theme_mod( 'content_alignment_options' );
			$box_shadow_flag            = get_theme_mod( 'box_shadow_options' );
			$box_shadow_hover_flag      = get_theme_mod( 'box_shadow_hover_options' );
			$product_image_hover_flag   = get_theme_mod( 'product_image_hover_style_options' );
			$checkout_width             = get_theme_mod( 'responsive_checkout_width' );
			$single_product_image_width = esc_html( get_theme_mod( 'responsive_single_product_image_width', 48 ) );
			$cart_style                 = get_theme_mod( 'responsive_cart_style', 'none' );
			$cart_color                 = get_theme_mod( 'responsive_cart_color', '#000000' );
			$shop_pagination_style      = get_theme_mod( 'shop_pagination_style', 'square' );
		}
		if ( 'off' === get_option( 'rpro_woocommerce_enable' ) ) {
			$breadcrumb_flag   = 1;
			$toolbar_flag      = 1;
			$content_alignment = 'left';
		}

		$sticky_header_background_color             = get_theme_mod( 'responsive_sticky_header_background_color' );
		$sticky_header_site_title_color             = get_theme_mod( 'responsive_sticky_header_site_title_color' );
		$sticky_header_site_title_hover_color       = get_theme_mod( 'responsive_sticky_header_site_title_hover_color' );
		$sticky_header_text_color                   = get_theme_mod( 'responsive_sticky_header_text_color' );
		$sticky_header_menu_background_color        = get_theme_mod( 'responsive_sticky_header_menu_background_color' );
		$sticky_header_active_menu_background_color = get_theme_mod( 'responsive_sticky_header_active_menu_background_color' );
		$sticky_header_menu_link_color              = get_theme_mod( 'responsive_sticky_header_menu_link_color' );
		$sticky_header_menu_link_hover_color        = get_theme_mod( 'responsive_sticky_header_menu_link_hover_color' );
		$sticky_header_sub_menu_background_color    = get_theme_mod( 'responsive_sticky_header_sub_menu_background_color' );
		$sticky_header_sub_menu_link_color          = get_theme_mod( 'responsive_sticky_header_sub_menu_link_color' );
		$sticky_header_sub_menu_link_hover_color    = get_theme_mod( 'responsive_sticky_header_sub_menu_link_hover_color' );

		if ( check_is_pro_version_greater() && ! is_responsive_version_greater() ) {
			if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) {

				// Outside Container Spacing.
				$outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_right_padding', 15 ) );
				$outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_left_padding', 15 ) );
				$outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_top_padding', 0 ) );
				$outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_bottom_padding', 0 ) );

				$outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_tablet_right_padding', 15 ) );
				$outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_tablet_left_padding', 15 ) );
				$outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_tablet_top_padding', 0 ) );
				$outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_tablet_bottom_padding', 0 ) );

				$outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_mobile_right_padding', 15 ) );
				$outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_mobile_left_padding', 15 ) );
				$outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_mobile_top_padding', 0 ) );
				$outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_mobile_bottom_padding', 0 ) );

				// Outside Container Spacing.
				$blog_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_right_padding', 15 ) );
				$blog_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_left_padding', 15 ) );
				$blog_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_top_padding', 15 ) );
				$blog_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_bottom_padding', 15 ) );

				$blog_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_right_padding', 15 ) );
				$blog_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_left_padding', 15 ) );
				$blog_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_top_padding', 15 ) );
				$blog_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_bottom_padding', 15 ) );

				$blog_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_right_padding', 15 ) );
				$blog_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_left_padding', 15 ) );
				$blog_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_top_padding', 15 ) );
				$blog_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_bottom_padding', 15 ) );

				// Inside Container Spacing.
				$blog_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_right_padding', 15 ) );
				$blog_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_left_padding', 15 ) );
				$blog_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_top_padding', 15 ) );
				$blog_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_bottom_padding', 15 ) );

				$blog_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_right_padding', 15 ) );
				$blog_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_left_padding', 15 ) );
				$blog_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_top_padding', 15 ) );
				$blog_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_bottom_padding', 15 ) );

				$blog_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_right_padding', 15 ) );
				$blog_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_left_padding', 15 ) );
				$blog_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_top_padding', 15 ) );
				$blog_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_bottom_padding', 15 ) );

				// Outside Container Spacing.
				$single_blog_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_right_padding', 15 ) );
				$single_blog_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_left_padding', 15 ) );
				$single_blog_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_top_padding', 15 ) );
				$single_blog_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_bottom_padding', 15 ) );

				$single_blog_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_right_padding', 15 ) );
				$single_blog_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_left_padding', 15 ) );
				$single_blog_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_top_padding', 15 ) );
				$single_blog_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_bottom_padding', 15 ) );

				$single_blog_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_right_padding', 15 ) );
				$single_blog_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_left_padding', 15 ) );
				$single_blog_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_top_padding', 15 ) );
				$single_blog_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_bottom_padding', 15 ) );

				// Inside Container Spacing.
				$single_blog_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_right_padding', 15 ) );
				$single_blog_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_left_padding', 15 ) );
				$single_blog_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_top_padding', 15 ) );
				$single_blog_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_bottom_padding', 15 ) );

				$single_blog_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_right_padding', 15 ) );
				$single_blog_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_left_padding', 15 ) );
				$single_blog_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_top_padding', 15 ) );
				$single_blog_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_bottom_padding', 15 ) );

				$single_blog_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_right_padding', 15 ) );
				$single_blog_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_left_padding', 15 ) );
				$single_blog_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_top_padding', 15 ) );
				$single_blog_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_bottom_padding', 15 ) );

				// Outside Container Spacing.
				$sidebar_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_right_padding', 15 ) );
				$sidebar_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_left_padding', 15 ) );
				$sidebar_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_top_padding', 0 ) );
				$sidebar_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_bottom_padding', 0 ) );

				$sidebar_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_right_padding', 15 ) );
				$sidebar_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_left_padding', 15 ) );
				$sidebar_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_top_padding', 0 ) );
				$sidebar_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_bottom_padding', 0 ) );

				$sidebar_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_right_padding', 15 ) );
				$sidebar_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_left_padding', 15 ) );
				$sidebar_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_top_padding', 0 ) );
				$sidebar_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_bottom_padding', 0 ) );

				// Inside Container Spacing.
				$sidebar_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_right_padding', 28 ) );
				$sidebar_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_left_padding', 28 ) );
				$sidebar_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_top_padding', 28 ) );
				$sidebar_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_bottom_padding', 28 ) );

				$sidebar_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_right_padding', 28 ) );
				$sidebar_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_left_padding', 28 ) );
				$sidebar_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_top_padding', 28 ) );
				$sidebar_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_bottom_padding', 28 ) );

				$sidebar_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_right_padding', 28 ) );
				$sidebar_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_left_padding', 28 ) );
				$sidebar_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_top_padding', 28 ) );
				$sidebar_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_bottom_padding', 28 ) );
			}
		} else {
			// Outside Container Spacing.
			$outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_right_padding', 15 ) );
			$outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_left_padding', 15 ) );
			$outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_top_padding', 0 ) );
			$outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_bottom_padding', 0 ) );

			$outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_tablet_right_padding', 15 ) );
			$outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_tablet_left_padding', 15 ) );
			$outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_tablet_top_padding', 0 ) );
			$outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_tablet_bottom_padding', 0 ) );

			$outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_outside_container_mobile_right_padding', 15 ) );
			$outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_outside_container_mobile_left_padding', 15 ) );
			$outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_outside_container_mobile_top_padding', 0 ) );
			$outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_outside_container_mobile_bottom_padding', 0 ) );

			// Outside Container Spacing.
			$blog_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_right_padding', 15 ) );
			$blog_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_left_padding', 15 ) );
			$blog_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_top_padding', 15 ) );
			$blog_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_bottom_padding', 15 ) );

			$blog_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_right_padding', 15 ) );
			$blog_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_left_padding', 15 ) );
			$blog_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_top_padding', 15 ) );
			$blog_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_tablet_bottom_padding', 15 ) );

			$blog_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_right_padding', 15 ) );
			$blog_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_left_padding', 15 ) );
			$blog_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_top_padding', 15 ) );
			$blog_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_outside_container_mobile_bottom_padding', 15 ) );

			// Inside Container Spacing.
			$blog_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_right_padding', 15 ) );
			$blog_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_left_padding', 15 ) );
			$blog_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_top_padding', 15 ) );
			$blog_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_bottom_padding', 15 ) );

			$blog_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_right_padding', 15 ) );
			$blog_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_left_padding', 15 ) );
			$blog_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_top_padding', 15 ) );
			$blog_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_tablet_bottom_padding', 15 ) );

			$blog_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_right_padding', 15 ) );
			$blog_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_left_padding', 15 ) );
			$blog_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_top_padding', 15 ) );
			$blog_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_blog_inside_container_mobile_bottom_padding', 15 ) );

			// Outside Container Spacing.
			$single_blog_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_right_padding', 15 ) );
			$single_blog_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_left_padding', 15 ) );
			$single_blog_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_top_padding', 15 ) );
			$single_blog_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_bottom_padding', 15 ) );

			$single_blog_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_right_padding', 15 ) );
			$single_blog_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_left_padding', 15 ) );
			$single_blog_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_top_padding', 15 ) );
			$single_blog_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_tablet_bottom_padding', 15 ) );

			$single_blog_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_right_padding', 15 ) );
			$single_blog_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_left_padding', 15 ) );
			$single_blog_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_top_padding', 15 ) );
			$single_blog_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_outside_container_mobile_bottom_padding', 15 ) );

			// Inside Container Spacing.
			$single_blog_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_right_padding', 15 ) );
			$single_blog_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_left_padding', 15 ) );
			$single_blog_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_top_padding', 15 ) );
			$single_blog_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_bottom_padding', 15 ) );

			$single_blog_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_right_padding', 15 ) );
			$single_blog_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_left_padding', 15 ) );
			$single_blog_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_top_padding', 15 ) );
			$single_blog_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_tablet_bottom_padding', 15 ) );

			$single_blog_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_right_padding', 15 ) );
			$single_blog_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_left_padding', 15 ) );
			$single_blog_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_top_padding', 15 ) );
			$single_blog_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_single_blog_inside_container_mobile_bottom_padding', 15 ) );

			// Outside Container Spacing.
			$sidebar_outside_container_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_right_padding', 15 ) );
			$sidebar_outside_container_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_left_padding', 15 ) );
			$sidebar_outside_container_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_top_padding', 0 ) );
			$sidebar_outside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_bottom_padding', 0 ) );

			$sidebar_outside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_right_padding', 15 ) );
			$sidebar_outside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_left_padding', 15 ) );
			$sidebar_outside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_top_padding', 0 ) );
			$sidebar_outside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_tablet_bottom_padding', 0 ) );

			$sidebar_outside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_right_padding', 15 ) );
			$sidebar_outside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_left_padding', 15 ) );
			$sidebar_outside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_top_padding', 0 ) );
			$sidebar_outside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_outside_container_mobile_bottom_padding', 0 ) );

			// Inside Container Spacing.
			$sidebar_inside_container_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_right_padding', 28 ) );
			$sidebar_inside_container_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_left_padding', 28 ) );
			$sidebar_inside_container_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_top_padding', 28 ) );
			$sidebar_inside_container_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_bottom_padding', 28 ) );

			$sidebar_inside_container_tablet_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_right_padding', 28 ) );
			$sidebar_inside_container_tablet_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_left_padding', 28 ) );
			$sidebar_inside_container_tablet_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_top_padding', 28 ) );
			$sidebar_inside_container_tablet_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_tablet_bottom_padding', 28 ) );

			$sidebar_inside_container_mobile_padding_right  = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_right_padding', 28 ) );
			$sidebar_inside_container_mobile_padding_left   = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_left_padding', 28 ) );
			$sidebar_inside_container_mobile_padding_top    = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_top_padding', 28 ) );
			$sidebar_inside_container_mobile_padding_bottom = esc_html( get_theme_mod( 'responsive_sidebar_inside_container_mobile_bottom_padding', 28 ) );
		}

		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {

			if ( $breadcrumb_flag ) {
				$breadcrumb_display = 'block';
			} else {
				$breadcrumb_display = 'none';
			}

			if ( $toolbar_flag ) {
				$toolbar_display = 'block';
			} else {
				$toolbar_display = 'none';
			}
			if ( 0 !== $box_shadow_flag || 0 !== $box_shadow_hover_flag ) {
				$box_shadow_padding = '0 20px 20px 20px';
			} else {
				$box_shadow_padding = '0px';
			}

			switch ( $box_shadow_flag ) {
				case 1:
					$box_shadow = '0 1px 3px -2px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.1)';
					break;
				case 2:
					$box_shadow = '0 3px 6px -5px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1))';
					break;
				case 3:
					$box_shadow = '0 10px 20px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1)';
					break;
				case 4:
					$box_shadow = '0 14px 28px rgba(0, 0, 0, 0.12), 0 10px 10px rgba(0, 0, 0, 0.12)';
					break;
				case 5:
					$box_shadow = '0 20px 30px 0 rgba(0, 0, 0, 0.2);';
					break;
				default:
					$box_shadow = 'none';
			}
			switch ( $box_shadow_hover_flag ) {
				case 1:
					$box_shadow_hover = '0 1px 3px -2px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.1)';
					break;
				case 2:
					$box_shadow_hover = '0 3px 6px -5px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1))';
					break;
				case 3:
					$box_shadow_hover = '0 10px 20px rgba(0, 0, 0, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1)';
					break;
				case 4:
					$box_shadow_hover = '0 14px 28px rgba(0, 0, 0, 0.12), 0 10px 10px rgba(0, 0, 0, 0.12)';
					break;
				case 5:
					$box_shadow_hover = '0 20px 30px 0 rgba(0, 0, 0, 0.2);';
					break;
				default:
					$box_shadow_hover = 'none';
			}
		}
		$custom_css = '';
		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {

			$custom_css = "
				.woocommerce .woocommerce-breadcrumb{
					display: {$breadcrumb_display};
				}
				.single-product .site-content-header {
					display: {$breadcrumb_display};
				}
				.woocommerce .woocommerce-result-count,
				.woocommerce .woocommerce-ordering{
					display: {$toolbar_display};
				}
				.responsive-shop-summary-wrap{
					text-align: {$content_alignment};
					padding : {$box_shadow_padding};
					opacity : 1;
				}
				.responsive-shop-summary-wrap : hover{
					opacity : 1;
				}
				.woocommerce-checkout .content-outer{
					max-width: {$checkout_width}px;
				}

				.woocommerce ul.products li.product,
				.woocommerce-page ul.products li.product{
					box-shadow : {$box_shadow};
					transition: all 300ms ease-in-out;
				}

				.woocommerce ul.products li.product:hover,
				.woocommerce-page ul.products li.product:hover{
					box-shadow : {$box_shadow_hover};
					transition: all 300ms ease-in-out;

				}
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link {
					display: block;
					margin: 0 0 1em;
				}
				@media (min-width:769px) {
					.woocommerce div.product div.images.woocommerce-product-gallery {
						width:{$single_product_image_width}%;
					}
					.woocommerce #content div.product div.summary, .woocommerce #wrapper div.product div.summary, .woocommerce-page #content div.product div.summary, .woocommerce-page #wrapper div.product div.summary {
						width: calc(96% - {$single_product_image_width}%);
					}
				}
				";
		}
		$custom_css .= "
			#masthead.sticky-header, .res-transparent-header #masthead.sticky-header, .res-transparent-header:not(.woocommerce-cart):not(.woocommerce-checkout) #masthead.sticky-header, .res-transparent-header:not(.woocommerce-cart):not(.woocommerce-checkout) #masthead.sticky-header {
				background-color: {$sticky_header_background_color};
			}
			#masthead.sticky-header .site-title a, .res-transparent-header #masthead.sticky-header .site-title a {
				color: {$sticky_header_site_title_color};
			}
			#masthead.sticky-header .site-title a:hover, .res-transparent-header #masthead.sticky-header .site-title a:hover {
				color: {$sticky_header_site_title_hover_color};
			}
			#masthead.sticky-header .site-description, .res-transparent-header #masthead.sticky-header .site-description {
				color: {$sticky_header_text_color};
			}
			#masthead.sticky-header .main-navigation, .res-transparent-header #masthead.sticky-header .main-navigation, #masthead.sticky-header .main-navigation div, .res-transparent-header #masthead.sticky-header .main-navigation div {
				background-color: {$sticky_header_menu_background_color};
			}
			#masthead.sticky-header .main-navigation .menu > li > a, .res-transparent-header #masthead.sticky-header .main-navigation .menu > li > a {
				color: {$sticky_header_menu_link_color};
			}

			#masthead.sticky-header .main-navigation .menu .current_page_item > a,
			#masthead.sticky-header .main-navigation .menu .current-menu-item > a,
			#masthead.sticky-header .main-navigation .menu li > a:hover, .res-transparent-header #masthead.sticky-header .main-navigation .menu .current_page_item > a,
			.res-transparent-header #masthead.sticky-header .main-navigation .menu .current-menu-item > a,
			.res-transparent-header #masthead.sticky-header .main-navigation .menu li > a:hover {
				color: {$sticky_header_menu_link_hover_color};
				background-color: {$sticky_header_active_menu_background_color};
			}
			#masthead.sticky-header .main-navigation .children,
			#masthead.sticky-header .main-navigation .sub-menu, .res-transparent-header #masthead.sticky-header .main-navigation .children,
			.res-transparent-header #masthead.sticky-header .main-navigation .sub-menu {
				background-color: {$sticky_header_sub_menu_background_color};
			}
			#masthead.sticky-header .main-navigation .children li a,
			#masthead.sticky-header .main-navigation .sub-menu li a, .res-transparent-header #masthead.sticky-header .main-navigation .children li a,
			.res-transparent-header #masthead.sticky-header .main-navigation .sub-menu li a {
				color: {$sticky_header_sub_menu_link_color};
			}
			#masthead.sticky-header .main-navigation .children li a:hover,
			#masthead.sticky-header .main-navigation .sub-menu li a:hover, .res-transparent-header #masthead.sticky-header .main-navigation .children li a:hover,
			.res-transparent-header #masthead.sticky-header .main-navigation .sub-menu li a:hover {
				color: {$sticky_header_sub_menu_link_hover_color};
			}
			";
		if ( check_is_pro_version_greater() && ! is_responsive_version_greater() ) {
			if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) {
				$custom_css .= '
				.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $outside_container_padding_top, $outside_container_padding_right, $outside_container_padding_bottom, $outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $outside_container_tablet_padding_top, $outside_container_tablet_padding_right, $outside_container_tablet_padding_bottom, $outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $outside_container_mobile_padding_top, $outside_container_mobile_padding_right, $outside_container_mobile_padding_bottom, $outside_container_mobile_padding_left ) . ';
					}
				}

				.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $blog_outside_container_padding_top, $blog_outside_container_padding_right, $blog_outside_container_padding_bottom, $blog_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $blog_outside_container_tablet_padding_top, $blog_outside_container_tablet_padding_right, $blog_outside_container_tablet_padding_bottom, $blog_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $blog_outside_container_mobile_padding_top, $blog_outside_container_mobile_padding_right, $blog_outside_container_mobile_padding_bottom, $blog_outside_container_mobile_padding_left ) . ';
					}
				}

				.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
					padding: ' . responsive_spacing_css( $blog_inside_container_padding_top, $blog_inside_container_padding_right, $blog_inside_container_padding_bottom, $blog_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $blog_inside_container_tablet_padding_top, $blog_inside_container_tablet_padding_right, $blog_inside_container_tablet_padding_bottom, $blog_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $blog_inside_container_mobile_padding_top, $blog_inside_container_mobile_padding_right, $blog_inside_container_mobile_padding_bottom, $blog_inside_container_mobile_padding_left ) . ';
					}
				}

				.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $single_blog_outside_container_padding_top, $single_blog_outside_container_padding_right, $single_blog_outside_container_padding_bottom, $single_blog_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $single_blog_outside_container_tablet_padding_top, $single_blog_outside_container_tablet_padding_right, $single_blog_outside_container_tablet_padding_bottom, $single_blog_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $single_blog_outside_container_mobile_padding_top, $single_blog_outside_container_mobile_padding_right, $single_blog_outside_container_mobile_padding_bottom, $single_blog_outside_container_mobile_padding_left ) . ';
					}
				}
				.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
					padding: ' . responsive_spacing_css( $single_blog_inside_container_padding_top, $single_blog_inside_container_padding_right, $single_blog_inside_container_padding_bottom, $single_blog_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $single_blog_inside_container_tablet_padding_top, $single_blog_inside_container_tablet_padding_right, $single_blog_inside_container_tablet_padding_bottom, $single_blog_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $single_blog_inside_container_mobile_padding_top, $single_blog_inside_container_mobile_padding_right, $single_blog_inside_container_mobile_padding_bottom, $single_blog_inside_container_mobile_padding_left ) . ';
					}
				}

				#secondary.widget-area {
					padding: ' . responsive_spacing_css( $sidebar_outside_container_padding_top, $sidebar_outside_container_padding_right, $sidebar_outside_container_padding_bottom, $sidebar_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					#secondary.widget-area {
						padding: ' . responsive_spacing_css( $sidebar_outside_container_tablet_padding_top, $sidebar_outside_container_tablet_padding_right, $sidebar_outside_container_tablet_padding_bottom, $sidebar_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					#secondary.widget-area {
						padding: ' . responsive_spacing_css( $sidebar_outside_container_mobile_padding_top, $sidebar_outside_container_mobile_padding_right, $sidebar_outside_container_mobile_padding_bottom, $sidebar_outside_container_mobile_padding_left ) . ';
					}
				}
				#secondary.widget-area .widget-wrapper{
					padding: ' . responsive_spacing_css( $sidebar_inside_container_padding_top, $sidebar_inside_container_padding_right, $sidebar_inside_container_padding_bottom, $sidebar_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					#secondary.widget-area .widget-wrapper{
						padding: ' . responsive_spacing_css( $sidebar_inside_container_tablet_padding_top, $sidebar_inside_container_tablet_padding_right, $sidebar_inside_container_tablet_padding_bottom, $sidebar_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					#secondary.widget-area .widget-wrapper{
						padding: ' . responsive_spacing_css( $sidebar_inside_container_mobile_padding_top, $sidebar_inside_container_mobile_padding_right, $sidebar_inside_container_mobile_padding_bottom, $sidebar_inside_container_mobile_padding_left ) . ';
					}
				}
				';
			}
		} else {
			$custom_css .= '
				.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $outside_container_padding_top, $outside_container_padding_right, $outside_container_padding_bottom, $outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $outside_container_tablet_padding_top, $outside_container_tablet_padding_right, $outside_container_tablet_padding_bottom, $outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.responsive-site-style-content-boxed #primary.content-area, .responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $outside_container_mobile_padding_top, $outside_container_mobile_padding_right, $outside_container_mobile_padding_bottom, $outside_container_mobile_padding_left ) . ';
					}
				}

				.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $blog_outside_container_padding_top, $blog_outside_container_padding_right, $blog_outside_container_padding_bottom, $blog_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $blog_outside_container_tablet_padding_top, $blog_outside_container_tablet_padding_right, $blog_outside_container_tablet_padding_bottom, $blog_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.blog.responsive-site-style-content-boxed #primary.content-area, .blog.responsive-site-style-boxed #primary.content-area, .archive.responsive-site-style-content-boxed #primary.content-area, .archive.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $blog_outside_container_mobile_padding_top, $blog_outside_container_mobile_padding_right, $blog_outside_container_mobile_padding_bottom, $blog_outside_container_mobile_padding_left ) . ';
					}
				}

				.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
					padding: ' . responsive_spacing_css( $blog_inside_container_padding_top, $blog_inside_container_padding_right, $blog_inside_container_padding_bottom, $blog_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $blog_inside_container_tablet_padding_top, $blog_inside_container_tablet_padding_right, $blog_inside_container_tablet_padding_bottom, $blog_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.blog.responsive-site-style-content-boxed .site-content .hentry, .blog.responsive-site-style-boxed .site-content .hentry, .archive.responsive-site-style-content-boxed .site-content .hentry, .archive.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $blog_inside_container_mobile_padding_top, $blog_inside_container_mobile_padding_right, $blog_inside_container_mobile_padding_bottom, $blog_inside_container_mobile_padding_left ) . ';
					}
				}

				.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
					padding: ' . responsive_spacing_css( $single_blog_outside_container_padding_top, $single_blog_outside_container_padding_right, $single_blog_outside_container_padding_bottom, $single_blog_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $single_blog_outside_container_tablet_padding_top, $single_blog_outside_container_tablet_padding_right, $single_blog_outside_container_tablet_padding_bottom, $single_blog_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.single.single-post.responsive-site-style-content-boxed #primary.content-area, .single.single-post.responsive-site-style-boxed #primary.content-area{
						padding: ' . responsive_spacing_css( $single_blog_outside_container_mobile_padding_top, $single_blog_outside_container_mobile_padding_right, $single_blog_outside_container_mobile_padding_bottom, $single_blog_outside_container_mobile_padding_left ) . ';
					}
				}
				.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
					padding: ' . responsive_spacing_css( $single_blog_inside_container_padding_top, $single_blog_inside_container_padding_right, $single_blog_inside_container_padding_bottom, $single_blog_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $single_blog_inside_container_tablet_padding_top, $single_blog_inside_container_tablet_padding_right, $single_blog_inside_container_tablet_padding_bottom, $single_blog_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					.single.single-post.responsive-site-style-content-boxed .site-content .hentry, .single.single-post.responsive-site-style-boxed .site-content .hentry{
						padding: ' . responsive_spacing_css( $single_blog_inside_container_mobile_padding_top, $single_blog_inside_container_mobile_padding_right, $single_blog_inside_container_mobile_padding_bottom, $single_blog_inside_container_mobile_padding_left ) . ';
					}
				}

				#secondary.widget-area {
					padding: ' . responsive_spacing_css( $sidebar_outside_container_padding_top, $sidebar_outside_container_padding_right, $sidebar_outside_container_padding_bottom, $sidebar_outside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					#secondary.widget-area {
						padding: ' . responsive_spacing_css( $sidebar_outside_container_tablet_padding_top, $sidebar_outside_container_tablet_padding_right, $sidebar_outside_container_tablet_padding_bottom, $sidebar_outside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					#secondary.widget-area {
						padding: ' . responsive_spacing_css( $sidebar_outside_container_mobile_padding_top, $sidebar_outside_container_mobile_padding_right, $sidebar_outside_container_mobile_padding_bottom, $sidebar_outside_container_mobile_padding_left ) . ';
					}
				}
				#secondary.widget-area .widget-wrapper{
					padding: ' . responsive_spacing_css( $sidebar_inside_container_padding_top, $sidebar_inside_container_padding_right, $sidebar_inside_container_padding_bottom, $sidebar_inside_container_padding_left ) . ';
				}
				@media screen and ( max-width: 992px ) {
					#secondary.widget-area .widget-wrapper{
						padding: ' . responsive_spacing_css( $sidebar_inside_container_tablet_padding_top, $sidebar_inside_container_tablet_padding_right, $sidebar_inside_container_tablet_padding_bottom, $sidebar_inside_container_tablet_padding_left ) . ';
					}
				}
				@media screen and ( max-width: 576px ) {
					#secondary.widget-area .widget-wrapper{
						padding: ' . responsive_spacing_css( $sidebar_inside_container_mobile_padding_top, $sidebar_inside_container_mobile_padding_right, $sidebar_inside_container_mobile_padding_bottom, $sidebar_inside_container_mobile_padding_left ) . ';
					}
				}
				';
		}

		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {

			if ( 'fade' === $product_image_hover_flag ) {
				$custom_css .= '.woocommerce ul.products li.product img:hover,
				.woocommerce-page ul.products li.product:hover img{
					opacity : 0.5;
					transition: .5s ease;
				}';
			}
			if ( 'zoom' === $product_image_hover_flag ) {
				$custom_css .= '
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link {
					overflow: hidden !important;
				}
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link img{
					transition: transform .5s ease;
					margin:0;
				}
				.woocommerce ul.products li.product:hover img{
					transform: scale(1.2);

				}
				';

			}
			if ( 'zoom-fade' === $product_image_hover_flag ) {
				$custom_css .= '
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link {
					overflow: hidden !important;
				}
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link img{
					transition: transform .5s ease;
					margin:0;
				}
				.woocommerce ul.products li.product:hover img{
					transform: scale(1.2);
					opacity : 0.5;
				}
				';
			}
			if ( 'swap-images' === $product_image_hover_flag ) {
				$custom_css .= '
				.woocommerce ul.products li.product a.woocommerce-LoopProduct-link img.show-on-hover {
					display:none;
				}
				.woocommerce ul.products li.product:hover a.woocommerce-LoopProduct-link img{
				display : none;
				}
				.woocommerce ul.products li.product:hover a.woocommerce-LoopProduct-link img.show-on-hover{
				display : block;
				}
				';
			}
			if ( 'outline' === $cart_style ) {
				$custom_css = "
				.res-addon-cart-wrap {
					border: 1px solid {$cart_color};
					color: {$cart_color};
				}
			";
			}
			if ( 'fill' === $cart_style ) {
				$custom_css = "
				.res-addon-cart-wrap {
					background-color: {$cart_color};
				}
			";
			}
			if ( 'circle' === $shop_pagination_style ) {
				$custom_css .= '
				.woocommerce nav.woocommerce-pagination ul li span, .woocommerce nav.woocommerce-pagination ul li a {
					border-radius: 50%;
					border-width: 1px;
					border-style: solid;
				}
				.woocommerce nav.woocommerce-pagination ul, .woocommerce .woocommerce-pagination ul.page-numbers li, .woocommerce-page .woocommerce-pagination ul.page-numbers li {
					border: none;
					margin: 0 3px;
				}
			';
			}
		}
		$custom_css .= '@media (min-width: ' . get_theme_mod( 'responive_mobile_breakpoint', 992 ) . 'px) {';

		for ( $i = 10; $i <= 100; $i++ ) {
			$custom_css .= '.main-navigation li.megamenu-parent ul.megamenu.tab_width-' . $i . ' > li { width: ' . $i . '%; }';
		}

		$custom_css .= '.main-navigation li.megamenu-parent .children,
			//.main-navigation li.megamenu-parent .sub-menu {
			// height: 500px;
				//padding: 50px 10px;
			//}
			.main-navigation li.megamenu-parent .children > li,
			.main-navigation li.megamenu-parent .sub-menu > li {
				border-top: none;
			}
			.main-navigation li.megamenu-parent .children .children,
			.main-navigation li.megamenu-parent .children .sub-menu,
			.main-navigation li.megamenu-parent .sub-menu .children,
			.main-navigation li.megamenu-parent .sub-menu .sub-menu {
				border: none;
				border-right: 1px solid rgba(170, 170, 170, 0.2);
				border-left: 1px solid rgba(170, 170, 170, 0.2);
				box-shadow: none;
			}
		}';

		$box_background_color = esc_html( get_theme_mod( 'responsive_box_background_color', '' ) );

		if ( check_is_pro_version_greater( 'responsive-pro' ) && ! is_responsive_version_greater() ) {
			if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) {
				$footer_background_image                    = esc_url( get_theme_mod( 'responsive_footer_background_image' ) );
				$header_background_image                    = esc_url( get_theme_mod( 'responsive_header_background_image' ) );
				$header_widget_background_image             = esc_url( get_theme_mod( 'responsive_header_widget_background_image' ) );
				$transparent_header_widget_background_image = esc_url( get_theme_mod( 'responsive_transparent_header_widget_background_image' ) );
				$sidebar_background_image                   = esc_url( get_theme_mod( 'responsive_sidebar_background_image' ) );
				$box_background_image                       = esc_url( get_theme_mod( 'responsive_box_background_image' ) );
				$button_background_image                    = esc_url( get_theme_mod( 'responsive_button_background_image' ) );
				$inputs_background_image                    = esc_url( get_theme_mod( 'responsive_inputs_background_image' ) );

				if ( $box_background_image ) {
					if ( class_exists( 'Sensei_Main' ) ) {
						$custom_css .= '
						.responsive-site-style-content-boxed .sensei-pagination,
						.responsive-site-style-content-boxed.single-course nav.post-entries.fix,
						.responsive-site-style-boxed .sensei-pagination,
						.responsive-site-style-boxed.single-course nav.post-entries.fix,';
					}

						$custom_css .= ".page.front-page.responsive-site-style-content-boxed .custom-home-widget-section.home-widgets,
					.blog.front-page.responsive-site-style-content-boxed .custom-home-widget-section.home-widgets,
					.responsive-site-style-content-boxed .custom-home-about-section,
					.responsive-site-style-content-boxed .custom-home-feature-section,
					.responsive-site-style-content-boxed .custom-home-team-section,
					.responsive-site-style-content-boxed .custom-home-testimonial-section,
					.responsive-site-style-content-boxed .custom-home-contact-section,
					.responsive-site-style-content-boxed .custom-home-widget-section,
					.responsive-site-style-content-boxed .custom-home-featured-area,
					.responsive-site-style-content-boxed .site-content-header,
					.responsive-site-style-content-boxed .content-area-wrapper,
					.responsive-site-style-content-boxed .site-content .hentry,
					.responsive-site-style-content-boxed .give-wrap .give_forms,
					.responsive-site-style-content-boxed .navigation,
					.responsive-site-style-content-boxed .comments-area,
					.responsive-site-style-content-boxed .comment-respond,
					.responsive-site-style-boxed .custom-home-about-section,
					.responsive-site-style-boxed .custom-home-feature-section,
					.responsive-site-style-boxed .custom-home-team-section,
					.responsive-site-style-boxed .custom-home-testimonial-section,
					.responsive-site-style-boxed .custom-home-contact-section,
					.responsive-site-style-boxed .custom-home-widget-section,
					.responsive-site-style-boxed .custom-home-featured-area,
					.responsive-site-style-boxed .site-content-header,
					.responsive-site-style-boxed .site-content .hentry,
					.responsive-site-style-boxed .give-wrap .give_forms,
					.responsive-site-style-boxed .navigation,
					.responsive-site-style-boxed .comments-area,
					.responsive-site-style-boxed .comment-respond,
					.responsive-site-style-boxed .comment-respond,
					.responsive-site-style-boxed aside#secondary .widget-wrapper,
					.responsive-site-style-boxed .site-content article.product,
					.woocommerce.responsive-site-style-content-boxed .related-product-wrapper,
					.woocommerce-page.responsive-site-style-content-boxed .related-product-wrapper,
					.woocommerce-page.responsive-site-style-content-boxed .products-wrapper,
					.woocommerce.responsive-site-style-content-boxed .products-wrapper,
					.woocommerce-page:not(.responsive-site-style-flat) .woocommerce-pagination,
					.woocommerce-page.responsive-site-style-boxed ul.products li.product,
					.woocommerce.responsive-site-style-boxed ul.products li.product,
					.woocommerce-page.single-product:not(.responsive-site-style-flat) div.product,
					.woocommerce.single-product:not(.responsive-site-style-flat) div.product {
						background-color:{$box_background_color};
						background-image: linear-gradient(to right, {$box_background_color}, {$box_background_color}), url({$box_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;
					}";
				}
			}
		} else {
			$footer_background_image                    = esc_url( get_theme_mod( 'responsive_footer_background_image' ) );
			$header_background_image                    = esc_url( get_theme_mod( 'responsive_header_background_image' ) );
			$header_widget_background_image             = esc_url( get_theme_mod( 'responsive_header_widget_background_image' ) );
			$transparent_header_widget_background_image = esc_url( get_theme_mod( 'responsive_transparent_header_widget_background_image' ) );
			$sidebar_background_image                   = esc_url( get_theme_mod( 'responsive_sidebar_background_image' ) );
			$box_background_image                       = esc_url( get_theme_mod( 'responsive_box_background_image' ) );
			$button_background_image                    = esc_url( get_theme_mod( 'responsive_button_background_image' ) );
			$inputs_background_image                    = esc_url( get_theme_mod( 'responsive_inputs_background_image' ) );

			if ( $box_background_image ) {
				if ( class_exists( 'Sensei_Main' ) ) {
					$custom_css .= '
					.responsive-site-style-content-boxed .sensei-pagination,
					.responsive-site-style-content-boxed.single-course nav.post-entries.fix,
					.responsive-site-style-boxed .sensei-pagination,
					.responsive-site-style-boxed.single-course nav.post-entries.fix,';
				}

					$custom_css .= ".page.front-page.responsive-site-style-content-boxed .custom-home-widget-section.home-widgets,
				.blog.front-page.responsive-site-style-content-boxed .custom-home-widget-section.home-widgets,
				.responsive-site-style-content-boxed .custom-home-about-section,
				.responsive-site-style-content-boxed .custom-home-feature-section,
				.responsive-site-style-content-boxed .custom-home-team-section,
				.responsive-site-style-content-boxed .custom-home-testimonial-section,
				.responsive-site-style-content-boxed .custom-home-contact-section,
				.responsive-site-style-content-boxed .custom-home-widget-section,
				.responsive-site-style-content-boxed .custom-home-featured-area,
				.responsive-site-style-content-boxed .site-content-header,
				.responsive-site-style-content-boxed .content-area-wrapper,
				.responsive-site-style-content-boxed .site-content .hentry,
				.responsive-site-style-content-boxed .give-wrap .give_forms,
				.responsive-site-style-content-boxed .navigation,
				.responsive-site-style-content-boxed .comments-area,
				.responsive-site-style-content-boxed .comment-respond,
				.responsive-site-style-boxed .custom-home-about-section,
				.responsive-site-style-boxed .custom-home-feature-section,
				.responsive-site-style-boxed .custom-home-team-section,
				.responsive-site-style-boxed .custom-home-testimonial-section,
				.responsive-site-style-boxed .custom-home-contact-section,
				.responsive-site-style-boxed .custom-home-widget-section,
				.responsive-site-style-boxed .custom-home-featured-area,
				.responsive-site-style-boxed .site-content-header,
				.responsive-site-style-boxed .site-content .hentry,
				.responsive-site-style-boxed .give-wrap .give_forms,
				.responsive-site-style-boxed .navigation,
				.responsive-site-style-boxed .comments-area,
				.responsive-site-style-boxed .comment-respond,
				.responsive-site-style-boxed .comment-respond,
				.responsive-site-style-boxed aside#secondary .widget-wrapper,
				.responsive-site-style-boxed .site-content article.product,
				.woocommerce.responsive-site-style-content-boxed .related-product-wrapper,
				.woocommerce-page.responsive-site-style-content-boxed .related-product-wrapper,
				.woocommerce-page.responsive-site-style-content-boxed .products-wrapper,
				.woocommerce.responsive-site-style-content-boxed .products-wrapper,
				.woocommerce-page:not(.responsive-site-style-flat) .woocommerce-pagination,
				.woocommerce-page.responsive-site-style-boxed ul.products li.product,
				.woocommerce.responsive-site-style-boxed ul.products li.product,
				.woocommerce-page.single-product:not(.responsive-site-style-flat) div.product,
				.woocommerce.single-product:not(.responsive-site-style-flat) div.product {
					background-color:{$box_background_color};
					background-image: linear-gradient(to right, {$box_background_color}, {$box_background_color}), url({$box_background_image});
					background-repeat: no-repeat;
					background-size: cover;
					background-attachment: scroll;
				}";
			}
		}

		$sensei_button = '';
		if ( class_exists( 'Sensei_Main' ) ) {

			$sensei_button = '.course #commentform #submit,
			.course .submit,
			.course a.button,
			.course a.button:visited,
			.course a.comment-reply-link,
			.course button.button,
			.course input.button,
			.course input[type=submit],
			.course-container #commentform #submit,
			.course-container .submit,
			.course-container a.button,
			.course-container a.button:visited,
			.course-container a.comment-reply-link,
			.course-container button.button,
			.course-container input.button,
			.course-container input[type=submit],
			.lesson #commentform #submit,
			.lesson .submit,
			.lesson a.button,
			.lesson a.button:visited,
			.lesson a.comment-reply-link,
			.lesson button.button,
			.lesson input.button,
			.lesson input[type=submit],
			.quiz #commentform #submit,
			.quiz .submit,
			.quiz a.button,
			.quiz a.button:visited,
			.quiz a.comment-reply-link,
			.quiz button.button,
			.quiz input.button,
			.quiz input[type=submit],';
		}

		if ( check_is_pro_version_greater( 'responsive-pro' ) && ! is_responsive_version_greater() ) {
			if ( 'on' === get_option( 'rpro_colors_backgrounds_enable' ) ) :

				$button_background_color = esc_html( get_theme_mod( 'responsive_button_color', '#0066CC' ) );
				if ( $button_background_image ) {
					$custom_css .= $sensei_button . "
						.page.front-page .button,
						.blog.front-page .button,
						.read-more-button .hentry .read-more .more-link,
						input[type=button],
						input[type=submit],
						button,
						.button,
						.wp-block-button__link,
						body div.wpforms-container-full .wpforms-form input[type=submit],
						body div.wpforms-container-full .wpforms-form button[type=submit],
						body div.wpforms-container-full .wpforms-form .wpforms-page-button {
							background-color:{$button_background_color};
							background-image: linear-gradient(to right, {$button_background_color}, {$button_background_color}), url({$button_background_image});
							background-repeat: no-repeat;
							background-size: cover;
							background-attachment: scroll;
						}";
				}

				$inputs_background_color = esc_html( get_theme_mod( 'responsive_inputs_background_color', '#ffffff' ) );

				if ( $inputs_background_image ) {
					$custom_css .= "select,
						textarea,
						input[type=tel],
						input[type=email],
						input[type=number],
						input[type=search],
						input[type=text],
						input[type=date],
						input[type=datetime],
						input[type=datetime-local],
						input[type=month],
						input[type=password],
						input[type=range],
						input[type=time],
						input[type=url],
						input[type=week],
						body div.wpforms-container-full .wpforms-form input[type=date],
						body div.wpforms-container-full .wpforms-form input[type=datetime],
						body div.wpforms-container-full .wpforms-form input[type=datetime-local],
						body div.wpforms-container-full .wpforms-form input[type=email],
						body div.wpforms-container-full .wpforms-form input[type=month],
						body div.wpforms-container-full .wpforms-form input[type=number],
						body div.wpforms-container-full .wpforms-form input[type=password],
						body div.wpforms-container-full .wpforms-form input[type=range],
						body div.wpforms-container-full .wpforms-form input[type=search],
						body div.wpforms-container-full .wpforms-form input[type=tel],
						body div.wpforms-container-full .wpforms-form input[type=text],
						body div.wpforms-container-full .wpforms-form input[type=time],
						body div.wpforms-container-full .wpforms-form input[type=url],
						body div.wpforms-container-full .wpforms-form input[type=week],
						body div.wpforms-container-full .wpforms-form select,
						body div.wpforms-container-full .wpforms-form textarea,
						#add_payment_method table.cart td.actions .coupon .input-text,
						.woocommerce-cart table.cart td.actions .coupon .input-text,
						.woocommerce-checkout table.cart td.actions .coupon .input-text,
						.woocommerce form .form-row input.input-text,
						.woocommerce form .form-row textarea {
							background-color: ' . $inputs_background_color . ';
							background-image: linear-gradient(to right, {$inputs_background_color}, {$inputs_background_color}), url({$inputs_background_image});
							background-repeat: no-repeat;
							background-size: cover;
							background-attachment: scroll;

						}";
				}

				$sidebar_background_color = esc_html( get_theme_mod( 'responsive_sidebar_background_color', '#ffffff' ) );

				if ( $sidebar_background_image ) {
					$custom_css .= ".responsive-site-style-boxed aside#secondary .widget-wrapper {
						background-color: ' . $sidebar_background_color . ';
						background-image: linear-gradient(to right, {$sidebar_background_color}, {$sidebar_background_color}), url({$sidebar_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;
					}";
				}

				$header_background_color = esc_html( get_theme_mod( 'responsive_header_background_color', '#ffffff' ) );

				if ( $header_background_image ) {
					$custom_css .= "body:not(.res-transparent-header) .site-header {
						background-color: ' . $header_background_color . ';
						background-image: linear-gradient(to right, {$header_background_color}, {$header_background_color}), url({$header_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;
					}";
				}

				$header_widget_background_color = esc_html( get_theme_mod( 'responsive_header_widget_background_color', '#ffffff' ) );

				if ( $header_widget_background_image ) {
						$custom_css .= "body:not(.res-transparent-header) .header-widgets {
							background-color: ' . $header_widget_background_color . ';
							background-image: linear-gradient(to right, {$header_widget_background_color}, {$header_widget_background_color}), url({$header_widget_background_image});
							background-repeat: no-repeat;
							background-size: cover;
							background-attachment: scroll;
					}";
				}

				$transparent_header_widget_background_color = esc_html( get_theme_mod( 'responsive_transparent_header_widget_background_color', '' ) );

				if ( $transparent_header_widget_background_image ) {
					$custom_css .= ".res-transparent-header .header-widgets {
						background-color: ' . $transparent_header_widget_background_color . ';
						background-image: linear-gradient(to right, {$transparent_header_widget_background_color}, {$transparent_header_widget_background_color}), url({$transparent_header_widget_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;
					}";
				}

				$footer_background_color = esc_html( get_theme_mod( 'responsive_footer_background_color', '#333333' ) );

				if ( $footer_background_image ) {
					$custom_css .= ".site-footer {
						background-color: ' . $footer_background_color . ';
						background-image: linear-gradient(to right, {$footer_background_color}, {$footer_background_color}), url({$footer_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;
					}";
				}

			endif;
		} else {
			$inputs_background_color = esc_html( get_theme_mod( 'responsive_inputs_background_color', '#ffffff' ) );

			if ( $inputs_background_image ) {
				$custom_css .= "select,
					textarea,
					input[type=tel],
					input[type=email],
					input[type=number],
					input[type=search],
					input[type=text],
					input[type=date],
					input[type=datetime],
					input[type=datetime-local],
					input[type=month],
					input[type=password],
					input[type=range],
					input[type=time],
					input[type=url],
					input[type=week],
					body div.wpforms-container-full .wpforms-form input[type=date],
					body div.wpforms-container-full .wpforms-form input[type=datetime],
					body div.wpforms-container-full .wpforms-form input[type=datetime-local],
					body div.wpforms-container-full .wpforms-form input[type=email],
					body div.wpforms-container-full .wpforms-form input[type=month],
					body div.wpforms-container-full .wpforms-form input[type=number],
					body div.wpforms-container-full .wpforms-form input[type=password],
					body div.wpforms-container-full .wpforms-form input[type=range],
					body div.wpforms-container-full .wpforms-form input[type=search],
					body div.wpforms-container-full .wpforms-form input[type=tel],
					body div.wpforms-container-full .wpforms-form input[type=text],
					body div.wpforms-container-full .wpforms-form input[type=time],
					body div.wpforms-container-full .wpforms-form input[type=url],
					body div.wpforms-container-full .wpforms-form input[type=week],
					body div.wpforms-container-full .wpforms-form select,
					body div.wpforms-container-full .wpforms-form textarea,
					#add_payment_method table.cart td.actions .coupon .input-text,
					.woocommerce-cart table.cart td.actions .coupon .input-text,
					.woocommerce-checkout table.cart td.actions .coupon .input-text,
					.woocommerce form .form-row input.input-text,
					.woocommerce form .form-row textarea {
						background-color: ' . $inputs_background_color . ';
						background-image: linear-gradient(to right, {$inputs_background_color}, {$inputs_background_color}), url({$inputs_background_image});
						background-repeat: no-repeat;
						background-size: cover;
						background-attachment: scroll;

					}";
			}
		}

		// Styling for blog/archive date box.
		$responsive_date_box             = esc_html( get_theme_mod( 'responsive_date_box_toggle' ) );
		$date_box_background_color       = esc_html( get_theme_mod( 'responsive_link_color', '#0066CC' ) );
		$date_box_calculated_color_value = required_font_color_value( $date_box_background_color );
			/* Taking body font size value for all views */
		$body_font_val_desktop = ( isset( get_theme_mod( 'body_typography' )['font-size'] ) && '' !== get_theme_mod( 'body_typography' )['font-size'] ) ? get_theme_mod( 'body_typography' )['font-size'] : '16px';
		$body_font_val_desktop = str_replace( 'px', '', $body_font_val_desktop );
		$body_font_val_tablet  = ( isset( get_theme_mod( 'body_tablet_typography' )['font-size'] ) && '' !== get_theme_mod( 'body_tablet_typography' )['font-size'] ) ? get_theme_mod( 'body_tablet_typography' )['font-size'] : '16px';
		$body_font_val_tablet  = str_replace( 'px', '', $body_font_val_tablet );
		$body_font_val_mobile  = ( isset( get_theme_mod( 'body_mobile_typography' )['font-size'] ) && '' !== get_theme_mod( 'body_mobile_typography' )['font-size'] ) ? get_theme_mod( 'body_mobile_typography' )['font-size'] : '16px';
		$body_font_val_mobile  = str_replace( 'px', '', $body_font_val_mobile );
			/* Calculation for desktop view */
		$datebox_month_year_font_desktop        = $body_font_val_desktop - 2.5;
		$datebox_day_font_desktop               = $body_font_val_desktop * 2;
		$datebox_container_width_height_desktop = ( $body_font_val_desktop * 2 ) + $datebox_day_font_desktop + 20;
			/* Calculation for tablet view */
		$datebox_month_year_font_tablet        = $body_font_val_tablet - 2.5;
		$datebox_day_font_tablet               = $body_font_val_tablet * 2;
		$datebox_container_width_height_tablet = ( $body_font_val_tablet * 2 ) + $datebox_day_font_tablet + 20;
			/* Calculation for mobile view */
		$datebox_month_year_font_mobile        = $body_font_val_mobile - 2.5;
		$datebox_day_font_mobile               = $body_font_val_mobile * 2;
		$datebox_container_width_height_mobile = ( $body_font_val_mobile * 2 ) + $datebox_day_font_mobile + 20;

		if ( $responsive_date_box ) {
			$custom_css .= ".responsive-date-box {
				background-color: {$date_box_background_color};
				width: {$datebox_container_width_height_desktop}px;
				height: {$datebox_container_width_height_desktop}px;
			}
			.date-box-day {
				color: {$date_box_calculated_color_value};
				font-size: {$datebox_day_font_desktop}px;
			}
			.date-box-month {
				color: {$date_box_calculated_color_value};
				font-size: {$datebox_month_year_font_desktop}px;
			}
			.date-box-year {
				color: {$date_box_calculated_color_value};
				font-size: {$datebox_month_year_font_desktop}px;
			}
			@media (min-width: 576px) and (max-width: 768px) {
				.responsive-date-box {
					width: {$datebox_container_width_height_tablet}px;
					height: {$datebox_container_width_height_tablet}px;
				}
				.date-box-day {
					font-size: {$datebox_day_font_tablet}px;
				}
				.date-box-month,
				.date-box-year {
					font-size: {$datebox_month_year_font_tablet}px;
				}
			}
			@media (max-width: 575px) {
				.responsive-date-box {
					width: {$datebox_container_width_height_mobile}px;
					height: {$datebox_container_width_height_mobile}px;
				}
				.date-box-day {
					font-size: {$datebox_day_font_mobile}px;
				}
				.date-box-month,
				.date-box-year {
					font-size: {$datebox_month_year_font_mobile}px;
				}
			}";
		}

		// Change of blog/archive date box style.
		$responsive_date_box_style = esc_html( get_theme_mod( 'responsive_date_box_style' ) );
		if ( 'round' === $responsive_date_box_style ) {
			$custom_css .= ".responsive-date-box {
				border-radius: 100%;
			}";
		} elseif ( 'square' === $responsive_date_box_style ) {
			$custom_css .= ".responsive-date-box {
				border-radius: 0;
			}";
		} else {
			$custom_css .= ".responsive-date-box {
				border-radius: 0;
			}";
		}

		// Mobile Menu Breakpoint.
		$disable_mobile_menu    = get_theme_mod( 'responsive_disable_mobile_menu', 1 );
		$mobile_menu_breakpoint = esc_html( get_theme_mod( 'responsive_mobile_menu_breakpoint', 767 ) );

		if ( 0 === $disable_mobile_menu ) {
			$mobile_menu_breakpoint = 0;
		}

		$responsive_disable_sticky_header_mobile_menu = get_theme_mod( 'responsive_disable_sticky_header_mobile_menu', 0 );
		if ( '1' == $responsive_disable_sticky_header_mobile_menu ) {
			$custom_css .= "@media (max-width:{$mobile_menu_breakpoint}px) {
				#masthead.sticky-header, .res-transparent-header #masthead.sticky-header, .res-transparent-header:not(.woocommerce-cart):not(.woocommerce-checkout) #masthead.sticky-header {
					position: relative;
					scroll-behavior: smooth;
				}
				#wrapper.site-content {
					margin-top: 0px !important;
				}
			}";
		}
		// Menu Toggle Styles.
		$mobile_menu_border_right_width  = esc_html( get_theme_mod( 'responsive_mobile_menu_border_mobile_right_padding', 1 . 'px' ) );
		$mobile_menu_border_left_width   = esc_html( get_theme_mod( 'responsive_mobile_menu_border_mobile_left_padding', 1 . 'px' ) );
		$mobile_menu_border_top_width    = esc_html( get_theme_mod( 'responsive_mobile_menu_border_mobile_top_padding', 1 . 'px' ) );
		$mobile_menu_border_bottom_width = esc_html( get_theme_mod( 'responsive_mobile_menu_border_mobile_bottom_padding', 1 . 'px' ) );
		$mobile_border_width             = responsive_border_css( $mobile_menu_border_top_width, $mobile_menu_border_right_width, $mobile_menu_border_bottom_width, $mobile_menu_border_left_width );

		$mobile_menu_toggle_style = get_theme_mod( 'responsive_mobile_menu_toggle_style', 'fill' );
		if ( 'outline' === $mobile_menu_toggle_style ) {
			$custom_css .= "@media (max-width:{$mobile_menu_breakpoint}px) {
					.main-navigation.toggled .menu-toggle{
						border-width: {$mobile_border_width };
					}
					.main-navigation .menu-toggle{
						display: flex;
						align-items: center;
						justify-content: center;
						border-width: {$mobile_border_width };
						padding: 0.5em;
					}
			}";
		}

		/** Native Cart POPUP */
		if ( 'on' === get_option( 'rpro_woocommerce_enable' ) ) {

			$popup_width                           = get_theme_mod( 'responsive_popup_width', '600' );
			$popup_width_tablet                    = get_theme_mod( 'responsive_popup_width_tablet' );
			$popup_width_mobile                    = get_theme_mod( 'responsive_popup_width_mobile' );
			$popup_height                          = get_theme_mod( 'responsive_popup_height', '600' );
			$popup_height_tablet                   = get_theme_mod( 'responsive_popup_height_tablet', '350' );
			$popup_height_mobile                   = get_theme_mod( 'responsive_popup_height_mobile', '450' );
			$top_padding                           = get_theme_mod( 'responsive_popup_top_padding', '50' );
			$right_padding                         = get_theme_mod( 'responsive_popup_right_padding', '25' );
			$bottom_padding                        = get_theme_mod( 'responsive_popup_bottom_padding', '50' );
			$left_padding                          = get_theme_mod( 'responsive_popup_left_padding', '25' );
			$tablet_top_padding                    = get_theme_mod( 'responsive_popup_tablet_top_padding', '50' );
			$tablet_right_padding                  = get_theme_mod( 'responsive_popup_tablet_right_padding', '25' );
			$tablet_bottom_padding                 = get_theme_mod( 'responsive_popup_tablet_bottom_padding', '50' );
			$tablet_left_padding                   = get_theme_mod( 'responsive_popup_tablet_left_padding', '25' );
			$mobile_top_padding                    = get_theme_mod( 'responsive_popup_mobile_top_padding', '50' );
			$mobile_right_padding                  = get_theme_mod( 'responsive_popup_mobile_right_padding', '25' );
			$mobile_bottom_padding                 = get_theme_mod( 'responsive_popup_mobile_bottom_padding', '50' );
			$mobile_left_padding                   = get_theme_mod( 'responsive_popup_mobile_left_padding', '25' );
			$top_radius                            = get_theme_mod( 'responsive_popup_radius_top_padding', '600' );
			$right_radius                          = get_theme_mod( 'responsive_popup_radius_right_padding', '600' );
			$bottom_radius                         = get_theme_mod( 'responsive_popup_radius_bottom_padding', '600' );
			$left_radius                           = get_theme_mod( 'responsive_popup_radius_left_padding', '600' );
			$tablet_top_radius                     = get_theme_mod( 'responsive_popup_radius_tablet_top_padding', '20' );
			$tablet_right_radius                   = get_theme_mod( 'responsive_popup_radius_tablet_right_padding', '20' );
			$tablet_bottom_radius                  = get_theme_mod( 'responsive_popup_radius_tablet_bottom_padding', '20' );
			$tablet_left_radius                    = get_theme_mod( 'responsive_popup_radius_tablet_left_padding', '20' );
			$mobile_top_radius                     = get_theme_mod( 'responsive_popup_radius_mobile_top_padding', '5' );
			$mobile_right_radius                   = get_theme_mod( 'responsive_popup_radius_mobile_right_padding', '5' );
			$mobile_bottom_radius                  = get_theme_mod( 'responsive_popup_radius_mobile_bottom_padding', '5' );
			$mobile_left_radius                    = get_theme_mod( 'responsive_popup_radius_mobile_left_padding', '5' );
			$popup_bg                              = get_theme_mod( 'responsive_popup_bg_color', '#ffffff' );
			$popup_overlay_color                   = get_theme_mod( 'responsive_popup_overlay_color', 'rgba(0,0,0,0.7)' );
			$popup_checkmark_bg                    = get_theme_mod( 'responsive_popup_checkmark_bg_color', '#5bc142' );
			$popup_checkmark_color                 = get_theme_mod( 'responsive_popup_checkmark_color', '#ffffff' );
			$popup_title_color                     = get_theme_mod( 'responsive_popup_title_color', '#333333' );
			$popup_content_color                   = get_theme_mod( 'responsive_popup_content_color', '#777777' );
			$popup_continue_btn_bg                 = get_theme_mod( 'responsive_popup_continue_btn_bg_color', '#0066CC' );
			$popup_continue_btn_color              = get_theme_mod( 'responsive_popup_continue_btn_color', '#ffffff' );
			$popup_continue_btn_border_color       = get_theme_mod( 'responsive_popup_continue_btn_border_color', '#10659C' );
			$popup_continue_btn_hover_bg           = get_theme_mod( 'responsive_popup_continue_btn_hover_bg_color', '#10659C' );
			$popup_continue_btn_hover_color        = get_theme_mod( 'responsive_popup_continue_btn_hover_color', '#ffffff' );
			$popup_continue_btn_hover_border_color = get_theme_mod( 'responsive_popup_continue_btn_hover_border_color', '#10659C' );
			$popup_cart_btn_bg                     = get_theme_mod( 'responsive_popup_cart_btn_bg_color', '#0066CC' );
			$popup_cart_btn_color                  = get_theme_mod( 'responsive_popup_cart_btn_color', '#ffffff' );
			$popup_cart_btn_border_color           = get_theme_mod( 'responsive_popup_cart_btn_border_color', '#10659C' );
			$popup_cart_btn_hover_bg               = get_theme_mod( 'responsive_popup_cart_btn_hover_bg_color', '#10659C' );
			$popup_cart_btn_hover_color            = get_theme_mod( 'responsive_popup_cart_btn_hover_color', '#ffffff' );
			$popup_cart_btn_hover_border_color     = get_theme_mod( 'responsive_popup_cart_btn_hover_border_color', '#10659C' );
			$popup_text_color                      = get_theme_mod( 'responsive_popup_text_color', '#777777' );

			// Popup width.
			if ( ! empty( $popup_width ) && '600' !== $popup_width ) {
				$custom_css .= '#woo-popup-wrap #woo-popup-inner{width:' . $popup_width . 'px;}';
			}

			// Popup width tablet.
			if ( ! empty( $popup_width_tablet ) ) {
				$custom_css .= '@media (max-width: 768px){#woo-popup-wrap #woo-popup-inner{width:' . $popup_width_tablet . 'px;}}';
			}

			// Popup width mobile.
			if ( ! empty( $popup_width_mobile ) ) {
				$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{width:' . $popup_width_mobile . 'px;}}';
			}

			// Popup height.
			if ( ! empty( $popup_height ) && '600' !== $popup_height ) {
				$custom_css .= '#woo-popup-wrap #woo-popup-inner{height:' . $popup_height . 'px;}';
			}

			// Popup height tablet.
			if ( ! empty( $popup_height_tablet ) && '350' !== $popup_height_tablet ) {
				$custom_css .= '@media (max-width: 768px){#woo-popup-wrap #woo-popup-inner{height:' . $popup_height_tablet . 'px;}}';
			} else {
				$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{height: auto;}}';
			}

			// Popup height mobile.
			if ( ! empty( $popup_height_mobile ) && '450' !== $popup_height_mobile ) {
				$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{height:' . $popup_height_mobile . 'px;}}';
			} else {
				$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{height: auto;}}';
			}

			// Popup padding.
			if ( isset( $top_padding ) && '50' !== $top_padding && '' !== $top_padding
					|| isset( $right_padding ) && '25' !== $right_padding && '' !== $right_padding
					|| isset( $bottom_padding ) && '50' !== $bottom_padding && '' !== $bottom_padding
					|| isset( $left_padding ) && '25' !== $left_padding && '' !== $left_padding ) {
				$custom_css .= '#woo-popup-wrap #woo-popup-inner{padding:' . responsive_spacing_css( $top_padding, $right_padding, $bottom_padding, $left_padding ) . '}';
			}

			// Tablet popup padding.
			if ( isset( $tablet_top_padding ) && '20' !== $tablet_top_padding && '' !== $tablet_top_padding
					|| isset( $tablet_right_padding ) && '20' !== $tablet_right_padding && '' !== $tablet_right_padding
					|| isset( $tablet_bottom_padding ) && '20' !== $tablet_bottom_padding && '' !== $tablet_bottom_padding
					|| isset( $tablet_left_padding ) && '20' !== $tablet_left_padding && '' !== $tablet_left_padding ) {
					$custom_css .= '@media (max-width: 768px){#woo-popup-wrap #woo-popup-inner{padding:' . responsive_spacing_css( $tablet_top_padding, $tablet_right_padding, $tablet_bottom_padding, $tablet_left_padding ) . '}}';
			}

			// Mobile popup padding.
			if ( isset( $mobile_top_padding ) && '' !== $mobile_top_padding
					|| isset( $mobile_right_padding ) && '' !== $mobile_right_padding
					|| isset( $mobile_bottom_padding ) && '' !== $mobile_bottom_padding
					|| isset( $mobile_left_padding ) && '' !== $mobile_left_padding ) {
					$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{padding:' . responsive_spacing_css( $mobile_top_padding, $mobile_right_padding, $mobile_bottom_padding, $mobile_left_padding ) . '}}';
			}

			// Popup border radius.
			if ( isset( $top_radius ) && '600' !== $top_radius && '' !== $top_radius
					|| isset( $right_radius ) && '600' !== $right_radius && '' !== $right_radius
					|| isset( $bottom_radius ) && '600' !== $bottom_radius && '' !== $bottom_radius
					|| isset( $left_radius ) && '600' !== $left_radius && '' !== $left_radius ) {
				$custom_css .= '#woo-popup-wrap #woo-popup-inner{border-radius:' . responsive_spacing_css( $top_radius, $right_radius, $bottom_radius, $left_radius ) . '}';
			}

			// Tablet popup border radius.
			if ( isset( $tablet_top_radius ) && '' !== $tablet_top_radius
					|| isset( $tablet_right_radius ) && '' !== $tablet_right_radius
					|| isset( $tablet_bottom_radius ) && '' !== $tablet_bottom_radius
					|| isset( $tablet_left_radius ) && '' !== $tablet_left_radius ) {
				$custom_css .= '@media (max-width: 768px){#woo-popup-wrap #woo-popup-inner{border-radius:' . responsive_spacing_css( $tablet_top_radius, $tablet_right_radius, $tablet_bottom_radius, $tablet_left_radius ) . '}}';
			}

			// Mobile popup border radius.
			if ( isset( $mobile_top_radius ) && '' !== $mobile_top_radius
					|| isset( $mobile_right_radius ) && '' !== $mobile_right_radius
					|| isset( $mobile_bottom_radius ) && '' !== $mobile_bottom_radius
					|| isset( $mobile_left_radius ) && '' !== $mobile_left_radius ) {
				$custom_css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{border-radius:' . responsive_spacing_css( $mobile_top_radius, $mobile_right_radius, $mobile_bottom_radius, $mobile_left_radius ) . '}}';
			}

			// Popup background color.
			if ( ! empty( $popup_bg ) && '#ffffff' !== $popup_bg ) {
				$custom_css .= '#woo-popup-wrap #woo-popup-inner{background-color:' . $popup_bg . ';}';
			}

			// Popup check mark background.
			if ( ! empty( $popup_checkmark_bg ) && '#5bc142' !== $popup_checkmark_bg ) {
				$custom_css .= '#woo-popup-wrap .checkmark{box-shadow: inset 0 0 0 ' . $popup_checkmark_bg . '; }#woo-popup-wrap .checkmark-circle{stroke: ' . $popup_checkmark_bg . ';}@keyframes fill {100% { box-shadow: inset 0 0 0 100px ' . $popup_checkmark_bg . '; }}';
			}

			// Popup check mark color.
			if ( ! empty( $popup_checkmark_color ) && '#ffffff' !== $popup_checkmark_color ) {
				$custom_css .= '#woo-popup-wrap .checkmark-check{stroke:' . $popup_checkmark_color . ';}';
			}

			// Popup title color.
			if ( ! empty( $popup_title_color ) && '#333333' !== $popup_title_color ) {
				$custom_css .= '#woo-popup-wrap .popup-title{color:' . $popup_title_color . ';}';
			}

			// Popup content color.
			if ( ! empty( $popup_content_color ) && '#777777' !== $popup_content_color ) {
				$custom_css .= '#woo-popup-wrap .popup-content{color:' . $popup_content_color . ';}';
			}

			// Popup continue button background color.
			if ( ! empty( $popup_continue_btn_bg ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn{background-color:' . $popup_continue_btn_bg . ';}';
			}

			// Popup continue button color.
			if ( ! empty( $popup_continue_btn_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn{color:' . $popup_continue_btn_color . ';}';
			}

			// Popup continue button border color.
			if ( ! empty( $popup_continue_btn_border_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn{border-color:' . $popup_continue_btn_border_color . ';}';
			}

			// Popup continue button hover background color.
			if ( ! empty( $popup_continue_btn_hover_bg ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn:hover{background-color:' . $popup_continue_btn_hover_bg . ';}';
			}

			// Popup continue button hover color.
			if ( ! empty( $popup_continue_btn_hover_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn:hover{color:' . $popup_continue_btn_hover_color . ';}';
			}

			// Popup continue button hover border color.
			if ( ! empty( $popup_continue_btn_hover_border_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.continue-btn:hover{border-color:' . $popup_continue_btn_hover_border_color . ';}';
			}

			// Popup cart button background color.
			if ( ! empty( $popup_cart_btn_bg ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn{background-color:' . $popup_cart_btn_bg . ';}';
			}

			// Popup cart button color.
			if ( ! empty( $popup_cart_btn_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn{color:' . $popup_cart_btn_color . ';}';
			}

			// Popup cart button border color.
			if ( ! empty( $popup_cart_btn_border_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn{border-color:' . $popup_cart_btn_border_color . ';}';
			}

			// Popup cart button hover background color.
			if ( ! empty( $popup_cart_btn_hover_bg ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn:hover{background-color:' . $popup_cart_btn_hover_bg . ';}';
			}

			// Popup cart button hover color.
			if ( ! empty( $popup_cart_btn_hover_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn:hover{color:' . $popup_cart_btn_hover_color . ';}';
			}

			// Popup cart button hover border color.
			if ( ! empty( $popup_cart_btn_hover_border_color ) ) {
				$custom_css .= '#woo-popup-wrap .buttons-wrap a.cart-btn:hover{border-color:' . $popup_cart_btn_hover_border_color . ';}';
			}

			// Popup bottom text color.
			if ( ! empty( $popup_text_color ) ) {
				$custom_css .= '#woo-popup-wrap .popup-text{color:' . $popup_text_color . ';}';
			}
			// Popup bottom text color.
			if ( ! empty( $popup_overlay_color ) ) {
				$custom_css .= '.mfp-bg {background:' . $popup_overlay_color . ';}';
			}
		}

		wp_add_inline_style( 'responsive-pro-style', $custom_css );
	}
}
add_action( 'wp_enqueue_scripts', 'responsive_addons_custom_theme_styles', 99 );
