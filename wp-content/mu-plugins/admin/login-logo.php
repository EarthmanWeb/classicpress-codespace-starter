<?php
/**
 * Plugin Name: EM Custom Login Logo and Styles
 * Description: Customizes the login page with a custom logo and styles.
 * Author: Terrance Orletsky / ChatGPT
 * Version: 2.3.4
 */

add_action( 'login_enqueue_scripts', 'em_custom_login_styles' );

/**
 * Enqueues custom styles for the login page.
 *
 * @return void
 */
function em_custom_login_styles() {
    $logo_url           = get_theme_mod( 'em_login_logo' );
    $background_color   = get_theme_mod( 'em_login_background_color', '#ffffff' );
    $font_color         = get_theme_mod( 'em_login_font_color', '#000000' );
    $font_color_2       = get_theme_mod( 'em_login_font_color_2', '#000000' );
    $background_color_2 = get_theme_mod( 'em_login_background_color_2', '' );
    echo "<style type='text/css' id='em-custom-login-logo'>
        .login .message, .login .notice, .login .success {
            border-left: 4px solid " . esc_attr( $font_color ) . ' !important;
        }
        body.login {
            background-color: ' . esc_attr( $background_color ) . ';'
            . ( ! empty( $background_color_2 ) ? 'background: linear-gradient(' . esc_attr( $background_color ) . ', ' . esc_attr( $background_color_2 ) . ');' : '' ) .
        '}
        .login #nav a, .login #backtoblog a {
            color: ' . esc_attr( $font_color_2 ) . ' !important;
        }
        #login h1 a, .login h1 a {
			' . ( $logo_url ? 'background-image: url(' . esc_url( $logo_url ) . ');' : '' ) . '
            height: 85px;
            width: 320px;
            background-repeat: no-repeat;
            padding-bottom: 30px;
            background-size: contain;
			background-position: center center;
        }
        .login form {
            color: ' . esc_attr( $font_color ) . ";
        }
        .login label[for='g-recaptcha'] {
            display: none !important;
        }
		#loginform .g-recaptcha iframe {
			height: 77px !important;
		}
    </style>";
}

add_filter( 'login_headerurl', 'em_custom_login_url' );

/**
 * Changes the login page URL to the home page.
 *
 * @return URL The home page URL.
 */
function em_custom_login_url() {
    return home_url();
}

add_action( 'customize_register', 'em_customizer_settings' );

/**
 * Adds custom settings to the Customizer for the login page.
 *
 * @param [type] $wp_customize The Customizer object.
 * @return void
 */
function em_customizer_settings( $wp_customize ) {
    $wp_customize->add_section(
        'em_login_customizer_section',
        array(
			'title'    => __( 'Login Page', 'theme_name' ),
			'priority' => 30,
		)
    );
    $wp_customize->add_setting(
        'em_login_logo',
        array(
			'default'   => '',
			'transport' => 'refresh',
		)
    );
    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize,
            'em_login_logo',
            array(
				'label'    => __( 'Login Page Logo (320 x 85)', 'theme_name' ),
				'section'  => 'em_login_customizer_section',
				'settings' => 'em_login_logo',
            )
        )
    );
    $wp_customize->add_setting(
        'em_login_background_color',
        array(
			'default'   => '#ffffff',
			'transport' => 'refresh',
		)
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'em_login_background_color',
            array(
				'label'    => __( 'Top Background Color', 'theme_name' ),
				'section'  => 'em_login_customizer_section',
				'settings' => 'em_login_background_color',
            )
        )
    );
    $wp_customize->add_setting(
        'em_login_background_color_2',
        array(
			'default'   => '',
			'transport' => 'refresh',
		)
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'em_login_background_color_2',
            array(
				'label'    => __( 'Bottom Background Color', 'theme_name' ),
				'section'  => 'em_login_customizer_section',
				'settings' => 'em_login_background_color_2',
            )
        )
    );
    $wp_customize->add_setting(
        'em_login_font_color',
        array(
			'default'   => '#000000',
			'transport' => 'refresh',
		)
    );
    $wp_customize->add_setting(
        'em_login_font_color_2',
        array(
			'default'   => '#000000',
			'transport' => 'refresh',
		)
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'em_login_font_color',
            array(
				'label'    => __( 'Form Text Color', 'theme_name' ),
				'section'  => 'em_login_customizer_section',
				'settings' => 'em_login_font_color',
            )
        )
    );
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'em_login_font_color_2',
            array(
				'label'    => __( 'Background Text Color', 'theme_name' ),
				'section'  => 'em_login_customizer_section',
				'settings' => 'em_login_font_color_2',
            )
        )
    );
}


add_action( 'customize_controls_enqueue_scripts', 'em_custom_login_customizer_js' );

/**
 * Enqueue JavaScript for Customizer control.
 *
 * @return void
 */
function em_custom_login_customizer_js() {
    $script = sprintf(
        "(function($) {
            wp.customize.section('em_login_customizer_section', function(section) {
                section.expanded.bind(function(isExpanded) {
                    if (isExpanded) {
                        var url = '%s';
                        // Append customizer-specific query args to ensure the login screen can be previewed correctly
                        url += '?customize_changeset_uuid=' + wp.customize.settings.changeset.uuid + '&customize_theme=' + wp.customize.settings.theme.stylesheet + '&customize_messenger_channel=' + wp.customize.previewer.channel() + '&customize_autosaved=on';
						wp.customize.previewer.previewUrl.set(url);
                    }
                });
            });
        })(jQuery);",
		esc_url( wp_login_url() )
    );

    wp_add_inline_script( 'customize-controls', $script, 'after' );
}


add_action( 'customize_controls_enqueue_scripts', 'em_custom_default_page_customizer_js' );

/**
 * Enqueue JavaScript for Customizer control to restore the default page.
 *
 * @return void
 */
function em_custom_default_page_customizer_js() {
    $script = "
        (function($) {
            wp.customize.section('em_login_customizer_section', function(section) {
                section.expanded.bind(function(isExpanded) {
                    if (!isExpanded) {
                        // Restore the default preview URL when the section is closed
                        console.log('restoring default preview');
                        var defaultUrl = wp.customize.settings.url.home;
                        wp.customize.previewer.previewUrl.set(defaultUrl);
                    }
                });
            });
        })(jQuery);";

    wp_add_inline_script( 'customize-controls', $script, 'after' );
}

add_action( 'init', 'em_customize_login_redirect_control' );

/**
 * Prevents the login page from redirecting to the Customizer preview when 'customize_changeset_uuid' GET parameter is present.
 *
 * @return void
 */
function em_customize_login_redirect_control() {
    if ( isset( $_GET['customize_changeset_uuid'] ) ) {
        remove_action( 'login_init', 'wp_admin_bar_customize_menu' );
    }
}

add_action( 'login_init', 'em_custom_login_footer_content' );

/**
 * Adds the footer content to the login page when 'customize_changeset_uuid' is present.
 *
 * @return void
 */
function em_custom_login_footer_content() {
    if ( isset( $_GET['customize_changeset_uuid'] ) ) {
        add_action( 'login_footer', 'wp_footer' );
    }
}

add_action( 'login_init', 'em_prevent_login_shake_and_errors' );

/**
 * Prevents the login page from shaking and displaying errors when 'customize_changeset_uuid' GET parameter is present.
 *
 * @return void
 */
function em_prevent_login_shake_and_errors() {
    if ( isset( $_GET['customize_changeset_uuid'] ) ) {
        // Suppress the shake script.
        remove_action( 'login_footer', 'wp_shake_js', 12 );

        // Clear any errors early.
        add_filter(
            'wp_login_errors',
            function( $errors, $redirect_to ) {
				// Clear all errors.
				$errors = new WP_Error();
				return $errors;
			},
            10,
            2
        );

    }
}
