<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://cyberchimps.com/
 * @since 1.0.0
 *
 * @package responsive-addons-for-elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<?php
require_once RAEL_DIR . 'admin/class-responsive-addons-for-elementor-rst-install-helper.php';
?>
<div class="responsive-addons-for-elementor-rst">
	<div class="responsive-addons-for-elementor-rst-content">
		<img class="responsive-addons-for-elementor-logo" src="<?php echo esc_url( RAEL_URL ) . 'admin/images/responsive-starter-templates-thumbnail.svg'; ?>">
		<div class="responsive-addons-for-elementor-rst-text">
			<p class="responsive-addons-for-elementor-rst-name"><?php esc_html_e( 'Responsive Starter Templates', 'responsive-addons-for-elementor' ); ?></p>
			<p class="responsive-addons-for-elementor-rst-desc"><?php esc_html_e( 'Browse 150+ fully-functional ready site templates by installing the free Responsive Starter Templates plugin. Click the button below to get started.', 'responsive-addons-for-elementor' ); ?></p>
		</div>
		<div class="responsive-addons-for-elementor-rst-buttons">
			<?php echo wp_kses_post( Responsive_Addons_For_Elementor_RST_Install_Helper::instance()->get_button_html( 'responsive-add-ons' ) ); ?>
			<a class="responsive-addons-for-elementor-rst-learn-more" href="https://wordpress.org/plugins/responsive-add-ons/" target="_blank"><?php esc_html_e( 'Learn More', 'responsive-addons-for-elementor' ); ?></a>
		</div>
	</div>
</div>
