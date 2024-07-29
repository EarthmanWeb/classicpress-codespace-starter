<?php
/**
 * Plugin install helper.
 *
 * @package responsive-addons-for-elementor
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Responsive_Addons_For_Elementor_RST_Install_Helper
 */
class Responsive_Addons_For_Elementor_RST_Install_Helper {


	/**
	 * Instance of class.
	 *
	 * @var bool $instance instance variable.
	 */
	private static $instance;


	/**
	 * Check if instance already exists.
	 *
	 * @return Responsive_Addons_For_Elementor_RST_Install_Helper
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Responsive_Addons_For_Elementor_RST_Install_Helper ) ) {
			self::$instance = new Responsive_Addons_For_Elementor_RST_Install_Helper();
		}

		return self::$instance;
	}

	/**
	 * Get plugin path based on plugin slug.
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return string
	 */
	public static function get_plugin_path( $slug ) {
		return $slug . '/' . $slug . '.php';
	}

	/**
	 * Generate action button html.
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return string
	 */
	public function get_button_html( $slug ) {
		$button   = '';
		$redirect = admin_url( 'admin.php?page=responsive_add_ons' );
		$state    = $this->check_plugin_state( $slug );
		if ( empty( $slug ) ) {
			return '';
		}

		$additional = '';

		if ( 'activated' === $state ) {
			$additional = ' action_button active';
		}

		$plugin_link_suffix = self::get_plugin_path( $slug );

		$nonce = add_query_arg(
			array(
				'action'        => 'activate',
				'plugin'        => rawurlencode( $plugin_link_suffix ),
				'plugin_status' => 'all',
				'paged'         => '1',
				'_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $plugin_link_suffix ),
			),
			network_admin_url( 'plugins.php' )
		);
		switch ( $state ) {
			case 'install':
				$button .= '<a data-redirect="' . esc_url( $redirect ) . '" data-slug="' . esc_attr( $slug ) . '" class="responsive-addons-for-elementor-rst-button install-now button" href="' . esc_url( $nonce ) . '" data-name="' . esc_attr( $slug ) . '" aria-label="Install ' . esc_attr( $slug ) . '">' . __( 'Install & Activate', 'responsive-addons-for-elementor' ) . '</a>';
				break;

			case 'activate':
				$button .= '<a  data-redirect="' . esc_url( $redirect ) . '" data-slug="' . esc_attr( $slug ) . '" class="responsive-addons-for-elementor-rst-button activate-now button button-primary" href="' . esc_url( $nonce ) . '" aria-label="Activate ' . esc_attr( $slug ) . '">' . esc_html__( 'Activate', 'responsive-addons-for-elementor' ) . '</a>';
				break;

			case 'activated':
				$button .= '<button class="responsive-addons-for-elementor-rst-button-disabled" aria-label="Activated ' . esc_attr( $slug ) . '">' . esc_html__( 'Activated', 'responsive-addons-for-elementor' ) . '</button>';
				break;
		} // End switch.
		return $button;
	}

	/**
	 * Check plugin state.
	 *
	 * @param string $slug plugin slug.
	 *
	 * @return bool
	 */
	public function check_plugin_state( $slug ) {
		$plugin_link_suffix = self::get_plugin_path( $slug );

		if ( file_exists( ABSPATH . 'wp-content/plugins/' . $plugin_link_suffix ) ) {
			$needs = is_plugin_active( $plugin_link_suffix ) ? 'activated' : 'activate';
			if ( 'activated' === $needs && ! post_type_exists( 'portfolio' ) && 'jetpack' === $slug ) {
				return 'enable_cpt';
			}

			return $needs;
		} else {
			return 'install';
		}
	}
}
