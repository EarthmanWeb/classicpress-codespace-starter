<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the App Conncetion Setting item.
 *
 * @link       https://cyberchimps.com/
 * @since      2.6.6
 *
 * @package    Responsive Ready Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once RESPONSIVE_ADDONS_DIR . 'includes/class-responsive-add-ons-app-auth.php';
$cc_app_auth = new Responsive_Add_Ons_App_Auth();
?>
<div id="responsive-theme-setting-app-connection-section">
	<div class="responsive-theme-single-setting-section">
		<div id="responsive-addons-settings-tab-content" class="responsive-addons-settings-tab-content responsive-addons-switch-tab-content">
			<?php if ( $cc_app_auth->has_auth() ) : ?>
				<?php
					require_once RESPONSIVE_ADDONS_DIR . 'includes/settings/class-responsive-add-ons-settings.php';
					$settings = new Responsive_Add_Ons_Settings();
				?>
				<div class="connection-status">
					<div class="settings-success-content">
						<h2 class="rst-success-connect-status">
							<img src="<?php echo esc_url( RESPONSIVE_ADDONS_URI . 'admin/images/svgs/connect-success.svg' ); ?>">
							<?php esc_html_e( 'Your website is connected to Cyberchimps Responsive', 'responsive-addons' ); ?>
						</h2>
						<p><?php esc_html_e( 'You can access all the plugin settings on the web and unlock new features.', 'responsive-addons' ); ?></p>
						<div class="app-connect-info">
							<p><strong><?php esc_html_e( 'Email: ', 'responsive-addons' ); ?></strong><?php echo $settings->get_email(); ?> </p>
							<p><strong><?php esc_html_e( 'Site Key: ', 'responsive-addons' ); ?></strong><?php echo $settings->get_website_key(); ?> </p>
							<p><strong><?php esc_html_e( 'Plan: ', 'responsive-addons' ); ?></strong><?php echo $settings->get_plan(); ?> </p>
						</div>
					</div>
					<div class="rst_app-after-connect-action-btns">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=responsive_add_ons' ) ); ?>">
								<button type="button" class="rst-go-to-templates"><?php esc_html_e( 'Start Importing Templates', 'responsive-addons' ); ?></button>
							</a>
							<button type="button" class="rst-delete-auth"><span id="loader"></span><?php esc_html_e( 'Disconnect', 'responsive-addons' ); ?></button>
					</div>
				</div>
			<?php else : ?>
				<div class="container">
					<div class="row">
						<div class="content-container">
							<div>
								<h2><?php esc_html_e( 'Connect Your Website to Cyberchimps Responsive', 'responsive-addons' ); ?></h2>
								<p><?php esc_html_e( 'Create a free account to connect with Cyberchimps Responsive. After connecting, you can get access to all the Starter Templates and additional features for the Cyberchimps Responsive theme like:', 'responsive-addons' ); ?></p>
								<ul>
									<li><strong><?php esc_html_e( 'Mega Menu:', 'responsive-addons' ); ?></strong><?php esc_html_e( ' Adds menu options such as mega menus, highlight tags, icons, etc.', 'responsive-addons' ); ?></li>
									<li><strong><?php esc_html_e( 'White Label:', 'responsive-addons' ); ?></strong><?php esc_html_e( ' White Label the theme name & settings with the Pro Plugin.' ); ?></li>
									<li><strong><?php esc_html_e( 'Woocommerce:', 'responsive-addons' ); ?></strong><?php esc_html_e( ' Adds enhanced set of options in the WooCommerce store customizer.', 'responsive-addons' ); ?></li>
								</ul>
								<p><?php esc_html_e( 'You can continue using the plugin without connecting to the web app if you wish so. Please note that the standalone version of the plugin doesn’t provide some advanced features.', 'responsive-addons' ); ?></p>
							</div>
						</div>
					</div>
						<div class="rst_app-connect-action-btns">
							<button type="button" class="rst-start-auth rst-start-auth-new"><?php esc_html_e( 'New? Create a free account', 'responsive-addons' ); ?><span id="loader"></span></button>
							<button type="button" class="rst-start-auth rst-start-auth-exist"><?php esc_html_e( 'Connect your existing account', 'responsive-addons' ); ?><span id="loader"></span></button>
						</div>
				</div>
				<?php endif; ?>
		</div>
	</div>
</div>
