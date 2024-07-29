<?php
/**
 * All helper function for customizer
 *
 * @package Responsive Addons pro
 */

if ( ! function_exists( 'responsive_footer_elements_positioning' ) ) {
	/**
	 * Returns footer elements positioning
	 *
	 * @since 0.2
	 */
	function responsive_footer_elements_positioning() {

		// Default sections.
		$sections = array( 'social_icons', 'footer_menu', 'copy_right_text' );

		// Get sections from Customizer.
		$sections = get_theme_mod( 'responsive_footer_elements_positioning', $sections );

		// Turn into array if string.
		if ( $sections && ! is_array( $sections ) ) {
			$sections = explode( ',', $sections );
		}

		// Apply filters for easy modification.
		$sections = apply_filters( 'responsive_footer_elements_positioning', $sections );

		// Return sections.
		return $sections;

	}
}
if ( ! function_exists( 'responsive_blog_pagination' ) ) {
	/**
	 * Returns blog pagination option
	 *
	 * @since 0.2
	 */
	function responsive_blog_pagination() {

		$blog_pagination = get_theme_mod( 'blog_pagination' );

		return $blog_pagination;

	}
}

if ( ! function_exists( 'responsive_date_box_toggle_value' ) ) {
	/**
	 * Returns date box option
	 */
	function responsive_date_box_toggle_value() {
		$date_box_toggle_value = get_theme_mod( 'responsive_date_box_toggle' );
		return $date_box_toggle_value;
	}
}

if ( ! function_exists( 'required_font_color_value' ) ) {
	/**
	 * Returns needed font color value for date box
	 *
	 * @param [type] $color [description] Needed font color value of the date box.
	 */
	function required_font_color_value( $color ) {
		list($r, $g, $b) = sscanf( $color, "#%02x%02x%02x" );
		$red             = $r * 299;
		$green           = $g * 587;
		$blue            = $b * 114;
		$sum             = round( ( $red + $green + $blue ) / 1000 );
		if ( $sum > 125 ) {
			$font_color_needed = "black";
		} else {
			$font_color_needed = "white";
		}
		return $font_color_needed;
	}
}

if ( ! function_exists( 'responsive_addons_menu_search_icon' ) ) {
	function responsive_addons_menu_search_icon() {
		$menu_last_item = get_theme_mod( 'responsive_menu_last_item', 'none' );
		if ( 'search' === $menu_last_item ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'responsive_addons_checkbox_control' ) ) {
	/**
	 * [responsive_addons_checkbox_control description]
	 *
	 * @param  [type] $wp_customize [description].
	 * @param  [type] $element      [description].
	 * @param  [type] $label        [description].
	 * @param  [type] $section      [description].
	 * @param  [type] $priority     [description].
	 * @param  [type] $default      [description].
	 * @param  [type] $active_call  [description].
	 * @return void               [description].
	 */
	function responsive_addons_checkbox_control( $wp_customize, $element, $label, $section, $priority, $default, $active_call = null, $transport = 'refresh', $desc = '' ) {

		$wp_customize->add_setting(
			'responsive_' . $element,
			array(
				'default'           => $default,
				'sanitize_callback' => 'responsive_checkbox_validate',
				'transport'         => $transport,
			)
		);
		$wp_customize->add_control(
			new Responsive_Customizer_Checkbox_Control(
				$wp_customize,
				'responsive_' . $element,
				array(
					'label'           => $label,
					'section'         => $section,
					'settings'        => 'responsive_' . $element,
					'priority'        => $priority,
					'active_callback' => $active_call,
					'description'     => $desc,
				)
			)
		);
	}
}

if ( ! function_exists( 'responsive_addons_radio_button_control' ) ) {
	/**
	 * [responsive_radio_button_control description].
	 *
	 * @param  [type] $wp_customize [description].
	 * @param  [type] $element      [description].
	 * @param  [type] $label        [description].
	 * @param  [type] $section      [description].
	 * @param  [type] $priority     [description].
	 * @param  [type] $default      [description].
	 * @param  [type] $choices      [description].
	 * @return void               [description].
	 */
	function responsive_addons_radio_button_control( $wp_customize, $element, $label, $section, $priority, $default, $choices = '', $transport = 'refresh' ) {

		$wp_customize->add_setting(
			'responsive_' . $element,
			array(
				'default'   => $default,
				'transport' => $transport,
			)
		);
		$wp_customize->add_control(
			'responsive_' . $element,
			array(
				'label'    => $label,
				'section'  => $section,
				'settings' => 'responsive_' . $element,
				'type'     => 'radio',
				'priority' => $priority,
				'choices'  => $choices,
			)
		);
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

if ( ! function_exists( 'responsive_addons_color_control' ) ) {

	/**
	 * Responsive_meta_styles description
	 *
	 * @param  object  $wp_customize [description].
	 * @param  string  $element      [description].
	 * @param  string  $label      [description].
	 * @param  string  $section      [description].
	 * @param  integer $priority     [description].
	 * @param  integer $default     [description].
	 * @param  bool    $active_call     [description].
	 * @param  string  $desc     [description].
	 * @return void               [description].
	 */
	function responsive_addons_color_control( $wp_customize, $element, $label, $section, $priority, $default, $active_call = null, $desc = '' ) {
		// Menu Background Color.
		$wp_customize->add_setting(
			'responsive_' . $element . '_color',
			array(
				'default'           => $default,
				'type'              => 'theme_mod',
				'sanitize_callback' => 'responsive_sanitize_background',
				'transport'         => 'postMessage',
			)
		);
		$wp_customize->add_control(
			new Responsive_Customizer_Color_Control(
				$wp_customize,
				'responsive_' . $element . '_color',
				array(
					'label'           => $label,
					'section'         => $section,
					'settings'        => 'responsive_' . $element . '_color',
					'priority'        => $priority,
					'active_callback' => $active_call,
					'description'     => $desc,
				)
			)
		);

	}
}

if ( ! function_exists( 'responsive_addons_drag_number_control' ) ) {
	/**
	 * [responsive_drag_number_control description]
	 *
	 * @param  [type]  $wp_customize [description].
	 * @param  [type]  $element      [description].
	 * @param  [type]  $label        [description].
	 * @param  [type]  $section      [description].
	 * @param  [type]  $priority     [description].
	 * @param  [type]  $default      [description].
	 * @param  [type]  $active_call  [description].
	 * @param  integer $max          [description].
	 * @param  integer $min          [description].
	 * @return void                [description].
	 */
	function responsive_addons_drag_number_control( $wp_customize, $element, $label, $section, $priority, $default, $active_call = null, $max = 4096, $min = 1, $transport = 'refresh' ) {

		/**
		 * Main Container Width
		 */
		$wp_customize->add_setting(
			'responsive_' . $element,
			array(
				'transport'         => $transport,
				'default'           => $default,
				'sanitize_callback' => 'responsive_sanitize_number',
			)
		);

		$wp_customize->add_control(
			new Responsive_Customizer_Range_Control(
				$wp_customize,
				'responsive_' . $element,
				array(
					'label'           => $label,
					'section'         => $section,
					'settings'        => 'responsive_' . $element,
					'priority'        => $priority,
					'active_callback' => $active_call,
					'input_attrs'     => array(
						'min'  => $min,
						'max'  => $max,
						'step' => 1,
					),
				)
			)
		);

	}
}

if ( ! function_exists( 'responsive_addons_padding_control' ) ) {
	/**
	 * responsive_addons_padding_control.
	 *
	 * @param  object  $wp_customize  [description].
	 * @param  integer $element  [description].
	 * @param  string  $section  [description].
	 * @param  integer $priority [description].
	 * @param  integer $default_values_y [description].
	 * @param  integer $default_values_x [description].
	 * @param  bool    $active_call [description].
	 * @param  string  $label [description].
	 * @return void
	 */
	function responsive_addons_padding_control( $wp_customize, $element, $section, $priority, $default_values_y = '', $default_values_x = '', $active_call = null, $label = 'Padding (px)', $max = 100 ) {
		/**
		 *  Padding control.
		 */
		$wp_customize->add_setting(
			'responsive_' . $element . '_top_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_left_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);

		$wp_customize->add_setting(
			'responsive_' . $element . '_bottom_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_right_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_tablet_top_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_tablet_right_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_tablet_bottom_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_tablet_left_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);

		$wp_customize->add_setting(
			'responsive_' . $element . '_mobile_top_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_mobile_right_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_mobile_bottom_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_y,
			)
		);
		$wp_customize->add_setting(
			'responsive_' . $element . '_mobile_left_padding',
			array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'responsive_sanitize_number',
				'default'           => $default_values_x,
			)
		);
		$wp_customize->add_control(
			new Responsive_Customizer_Dimensions_Control(
				$wp_customize,
				'responsive_' . $element . '_padding',
				array(
					'label'           => $label,
					'section'         => $section,
					'settings'        => array(
						'desktop_top'    => 'responsive_' . $element . '_top_padding',
						'desktop_right'  => 'responsive_' . $element . '_right_padding',
						'desktop_bottom' => 'responsive_' . $element . '_bottom_padding',
						'desktop_left'   => 'responsive_' . $element . '_left_padding',
						'tablet_top'     => 'responsive_' . $element . '_tablet_top_padding',
						'tablet_right'   => 'responsive_' . $element . '_tablet_right_padding',
						'tablet_bottom'  => 'responsive_' . $element . '_tablet_bottom_padding',
						'tablet_left'    => 'responsive_' . $element . '_tablet_left_padding',
						'mobile_top'     => 'responsive_' . $element . '_mobile_top_padding',
						'mobile_right'   => 'responsive_' . $element . '_mobile_right_padding',
						'mobile_bottom'  => 'responsive_' . $element . '_mobile_bottom_padding',
						'mobile_left'    => 'responsive_' . $element . '_mobile_left_padding',
					),
					'priority'        => $priority,
					'active_callback' => $active_call,
					'input_attrs'     => array(
						'min'  => '',
						'max'  => $max,
						'step' => 1,
					),
				)
			)
		);
	}
}

if ( ! function_exists( 'responsive_addons_rst_text_control' ) ) {
	/**
	 * [responsive_addons_rst_text_control description]
	 *
	 * @param  [type] $wp_customize [description].
	 * @param  [type] $element      [description].
	 * @param  [type] $label        [description].
	 * @param  [type] $section      [description].
	 * @param  [type] $priority     [description].
	 * @param  [type] $default      [description].
	 * @param  [type] $active_call      [description].
	 * @return void               [description].
	 */
	function responsive_addons_rst_text_control( $wp_customize, $element, $label, $section, $priority, $default, $active_call = null, $sanitize_function = 'sanitize_text_field', $type = 'text', $transport = 'refresh' ) {

		// Add Twitter Setting.
		$wp_customize->add_setting(
			'responsive_' . $element,
			array(
				'default'           => $default,
				'sanitize_callback' => $sanitize_function,
				'transport'         => $transport,
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'responsive_' . $element,
				array(
					'active_callback' => $active_call,
					'label'           => $label,
					'priority'        => $priority,
					'section'         => $section,
					'settings'        => 'responsive_' . $element,
					'type'            => $type,
				)
			)
		);
	}
}

if ( ! function_exists( 'responsive_border_css' ) ) {
	/**
	 * Return border values for customizer
	 *
	 * @param  integer $top    Top width.
	 * @param  integer $right  Right width.
	 * @param  integer $bottom bottom width.
	 * @param  integer $left   Left width.
	 * @return integer
	 */
	function responsive_border_css( $top, $right, $bottom, $left ) {

		// Add px and 0 if no value.
		$s_top    = ( isset( $top ) && '' !== $top ) ? intval( $top ) . 'px ' : '1px ';
		$s_right  = ( isset( $right ) && '' !== $right ) ? intval( $right ) . 'px ' : '1px ';
		$s_bottom = ( isset( $bottom ) && '' !== $bottom ) ? intval( $bottom ) . 'px ' : '1px ';
		$s_left   = ( isset( $left ) && '' !== $left ) ? intval( $left ) . 'px ' : '1px';

		// Return one value if it is the same on every inputs.
		if ( ( intval( $s_top ) === intval( $s_right ) )
			&& ( intval( $s_right ) === intval( $s_bottom ) )
			&& ( intval( $s_bottom ) === intval( $s_left ) ) ) {
			return $s_left;
		}

		// Return.
		return $s_top . $s_right . $s_bottom . $s_left;
	}
}

if ( ! function_exists( 'responsive_spacing_css' ) ) {
	/**
	 * Return padding/margin values for customizer
	 *
	 * @param  integer $top    Top width.
	 * @param  integer $right  Right width.
	 * @param  integer $bottom bottom width.
	 * @param  integer $left   Left width.
	 * @return integer
	 */
	function responsive_spacing_css( $top, $right, $bottom, $left ) {

		// Add px and 0 if no value.
		$s_top    = ( isset( $top ) && '' !== $top ) ? intval( $top ) . 'px ' : '0px ';
		$s_right  = ( isset( $right ) && '' !== $right ) ? intval( $right ) . 'px ' : '0px ';
		$s_bottom = ( isset( $bottom ) && '' !== $bottom ) ? intval( $bottom ) . 'px ' : '0px ';
		$s_left   = ( isset( $left ) && '' !== $left ) ? intval( $left ) . 'px ' : '0px';

		// Return one value if it is the same on every inputs.
		if ( ( intval( $s_top ) === intval( $s_right ) )
			&& ( intval( $s_right ) === intval( $s_bottom ) )
			&& ( intval( $s_bottom ) === intval( $s_left ) ) ) {
			return $s_left;
		}

		// Return.
		return $s_top . $s_right . $s_bottom . $s_left;
	}
}

if ( ! function_exists( 'responsive_popup_elements_positioning' ) ) {

	/**
	 * Returns popup elements positioning
	 */
	function responsive_popup_elements_positioning() {

		// Default elements.
		$sections = array( 'title', 'content', 'buttons', 'bottom_text' );

		// Get elements from Customizer.
		$sections = get_theme_mod( 'responsive_popup_elements_positioning', $sections );

		// Turn into array if string.
		if ( $sections && ! is_array( $sections ) ) {
			$sections = explode( ',', $sections );
		}

		// Apply filters for easy modification.
		$sections = apply_filters( 'responsive_popup_elements_positioning', $sections );

		// Return sections.
		return $sections;

	}
}
// Add shortcode at initialisation.
add_action( 'init', 'responsive_addons_register_woo_cart_shortcodes' );

if ( ! function_exists( 'responsive_addons_register_woo_cart_shortcodes' ) ) {
	/**
	 * Register woo cart shortcodes
	 */
	function responsive_addons_register_woo_cart_shortcodes() {
		// Register Shortcode returns cart item count.
		add_shortcode( 'responsive_woo_cart_items', 'responsive_addons_woo_cart_items_count_function' );
		// Register Shortcode returns total.
		add_shortcode( 'responsive_woo_total_cart', 'responsive_addons_woo_cart_total_function' );
		// Register Shortcode returns total.
		add_shortcode( 'responsive_woo_free_shipping_left', 'woo_free_shipping_shortcode' );
	}
}

if ( ! function_exists( 'responsive_addons_woo_cart_items_count_function' ) ) {
	/**
	 * Shortcode returns cart item count
	 */
	function responsive_addons_woo_cart_items_count_function() {
		if ( ! class_exists( 'WooCommerce' )
				|| is_admin() ) {
			return;
		}
		// return if in elementor, avoid errors.
		if ( class_exists( 'Elementor\Plugin' )
				&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'responsive_addons_pro' );
		}
		$output  = '<span class="responsive-woo-cart-count">';
		$output .= WC()->cart->get_cart_contents_count();
		$output .= '</span>';

		return $output;
	}
}

if ( ! function_exists( 'responsive_addons_woo_cart_total_function' ) ) {
	/**
	 * Shortcode returns cart total
	 */
	function responsive_addons_woo_cart_total_function() {
		if ( ! class_exists( 'WooCommerce' )
				|| is_admin() ) {
			return;
		}
		// return if in elementor, avoid errors.
		if ( class_exists( 'Elementor\Plugin' )
				&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return esc_html__( 'This shortcode only works in front end', 'responsive_addons_pro' );
		}

		$output  = '<span class="responsive-woo-total">';
		$output .= WC()->cart->get_total();
		$output .= '</span>';

		return $output;
	}
}

/**
 * Free shipping left
 *
 */
if ( ! function_exists( 'woo_free_shipping_left' ) ) {
	/**
	 * Check free shipping left
	 */
	function woo_free_shipping_left( $content, $content_reached, $multiply_by = 1 ) {

		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		if ( class_exists( 'Elementor\Plugin' )
			&& \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		if ( empty( $content ) ) {
			$content = esc_html__( 'Buy for %left_to_free% more and get free shipping', 'responsive_addons_pro' );
		}

		if ( empty( $content_reached ) ) {
			$content_reached = esc_html__( 'You have Free delivery!', 'responsive_addons_pro' );
		}

		$min_free_shipping_amount = 0;

		$free_shipping = new WC_Shipping_Legacy_Free_Shipping();
		if ( 'yes' === $free_shipping->enabled ) {
			if ( in_array( $lfree_shipping->requires, array( 'min_amount', 'either', 'both' ) ) ) {
				$min_free_shipping_amount = $free_shipping->min_amount;
			}
		}
		if ( 0 === $min_free_shipping_amount ) {
			if ( function_exists( 'WC' ) && ( $wc_shipping = WC()->shipping ) && ( $wc_cart = WC()->cart ) ) {
				if ( $wc_shipping->enabled ) {
					$packages = $wc_cart->get_shipping_packages();
					if ( $packages ) {
						$methods = $wc_shipping->load_shipping_methods( $packages[0] );
						foreach ( $methods as $method ) {
							if ( 'yes' === $method->enabled && 0 != $method->instance_id ) {
								if ( 'WC_Shipping_Free_Shipping' === get_class( $method ) ) {
									if ( in_array( $method->requires, array( 'min_amount', 'either', 'both' ) ) ) {
										$min_free_shipping_amount = $method->min_amount;
										break;
									}
								}
							}
						}
					}
				}
			}
		}

		if ( 0 !== $min_free_shipping_amount ) {
			if ( isset( WC()->cart->cart_contents_total ) ) {
				$total = ( WC()->cart->prices_include_tax ) ? ( WC()->cart->cart_contents_total + WC()->cart->get_cart_contents_tax() ) : WC()->cart->cart_contents_total;
				if ( $total >= $min_free_shipping_amount ) {
					return do_shortcode( $content_reached );
				} else {
					$content = str_replace( '%left_to_free%', '<span class="responsive-woo-left-to-free">' . wc_price( ( $min_free_shipping_amount - $total ) * $multiply_by ) . '</span>', $content );
					$content = str_replace( '%free_shipping_min_amount%', '<span class="responsive-woo-left-to-free">' . wc_price( ( $min_free_shipping_amount ) * $multiply_by ) . '</span>', $content );
					return $content;
				}
			}
		}

	}
}

if ( ! function_exists( 'woo_free_shipping_shortcode' ) ) {
	/**
	 * Free shipping shortcode
	 */
	function woo_free_shipping_shortcode( $atts, $content ) {

		// Return if WooCommerce is not enabled.
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Call the script.
		wp_enqueue_script( 'responsive-woo-popup' );

		// Initiation data on data attr on span.
		$content_data    = '';
		$content_reached = '';
		if ( ! empty( $atts ) ) {
			if ( isset( $atts['content'] ) ) {
				$content_data = $atts['content'];
			}
			if ( isset( $atts['content_reached'] ) ) {
				$content_reached = $atts['content_reached'];
			}
		}

		$x = str_replace( '%', '+', $content_data );

		extract(
			shortcode_atts(
				array(
					'content'         => esc_html__( 'Buy for %left_to_free% more and get free shipping', 'responsive_addons_pro' ),
					'content_reached' => esc_html__( 'You have Free delivery!', 'responsive_addons_pro' ),
					'multiply_by'     => 1,
				),
				$atts
			)
		);

		return woo_free_shipping_left( "<span class='responsive-woo-free-shipping' data-content='$x' data-reach='$content_reached'>" . $content . '</span>', '<span class="responsive-woo-free-shipping">' . $content_reached . '</span>', $multiply_by );

	}
}


if ( ! function_exists( 'update_responsive_woo_free_shipping_left_shortcode' ) ) {
	/**
	 * Ajax replay the refresh fragemnt
	 *
	 * @return void
	 */
	function update_responsive_woo_free_shipping_left_shortcode() {
		$atts = array();

		if ( ( isset( $_POST['content'] )
			&& ( '' !== $_POST['content'] ) )
				|| ( isset( $_POST['content_rech_data'] )
					&& ( '' !== $_POST['content_rech_data'] ) ) ) {

			$atts['content_reached'] = $_POST['content_rech_data'];
			$content                 = str_replace( '+', '%', $_POST['content'] );
			$atts['content']         = $content;
			$return_shortcode_value  = woo_free_shipping_shortcode( $atts, '' );
			wp_send_json( $return_shortcode_value );

		} else {

			$return_shortcode_value = woo_free_shipping_shortcode( $atts, '' );
			wp_send_json( $return_shortcode_value );

		}
	}
}

add_action( 'wp_ajax_update_responsive_woo_free_shipping_left_shortcode', 'update_responsive_woo_free_shipping_left_shortcode' );
add_action( 'wp_ajax_nopriv_update_responsive_woo_free_shipping_left_shortcode', 'update_responsive_woo_free_shipping_left_shortcode' );

if ( ! function_exists( 'responsive_addons_distraction_free_woocommerce' ) ) {
	/**
	 * [responsive_addons_distraction_free_woocommerce description]
	 *
	 * @return [type] [description]
	 */
	function responsive_addons_distraction_free_woocommerce() {
		return ( 1 === get_theme_mod( 'responsive_distraction_free_woocommerce', 0 ) ) ? true : false;
	}
}
