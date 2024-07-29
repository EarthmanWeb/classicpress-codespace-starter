<?php
/**
 * Provide a admin area view for the plugin
 *
 * Getting Started Settings Tab
 *
 * @link       https://cyberchimps.com/
 * @since      4.8.8
 *
 * @package responsive
 */

?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="col-md-2">
	<div class="responsive-theme-setting-list">
		<div tabindex="0" class="responsive-theme-setting-item d-flex" id="responsive-setting-item-app-connection-tab" role="button">
				<span class="responsive-theme-setting-item-icon dashicons dashicons-admin-users responsive-theme-setting-active-tab"></span>
				<p class="responsive-theme-setting-item-title responsive-theme-setting-active-tab"><?php esc_html_e( 'Connect Account', 'responsive-addons' ); ?></p>
		</div>
		<?php
		if ( empty( $wl_settings ) || ( ! empty( $wl_settings ) && 'off' === $wl_settings['hide_wl_settings'] ) ) {
			?>
		<div tabindex="1" class="responsive-theme-setting-item d-flex" id="responsive-theme-setting-wl-tab" role="button"
		>
			<span class="responsive-theme-setting-item-icon dashicons dashicons-edit-page"></span>
			<p class="responsive-theme-setting-item-title"><?php esc_html_e( 'White Label', 'responsive-add-ons' ); ?></p>
		</div>
			<?php
		}
		?>
	</div>
</div>
