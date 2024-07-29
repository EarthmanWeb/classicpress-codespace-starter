/**
 * Responsive Addons Pro
 *
 * @since 1.0.1
 * @package Responsive Addons Pro
 */

/**
 * Install Responsive Theme and Responsive Add-ons
 *
 * @since 1.0.1
 */
(function($){

	InstallResponsiveThemeAddons = {

		responsive_add_ons_slug : 'responsive-add-ons',
		responsive_add_ons_init: '',

		/**
		 * Init
		 */
		init: function() {
			this._bind();
		},

		/**
		 * Binds events for the Responsive Theme Installation.
		 *
		 * @since 1.0.1
		 */
		_bind: function()
		{
			$( document ).on( 'click', '.responsive-install-theme-and-addons', InstallResponsiveThemeAddons._installResponsiveTheme );
			$( document ).on( 'responsive-theme-not-installed', InstallResponsiveThemeAddons._install_and_activate_theme );
			$( document ).on( 'responsive-theme-installed-but-inactive', InstallResponsiveThemeAddons._activateTheme );
			$( document ).on( 'responsive-theme-installed-and-active', InstallResponsiveThemeAddons._responsive_theme_already_activated );
			$( document ).on( 'responsive-pro-responsive-theme-activation-completed', InstallResponsiveThemeAddons._installResponsiveAddons );
			$( document ).on( 'responsive-addons-not-installed', InstallResponsiveThemeAddons._install_and_activate_addons );
			$( document ).on( 'responsive-addons-installed-but-inactive', InstallResponsiveThemeAddons._activatePlugin );
			$( document ).on( 'responsive-addons-installed-and-active', InstallResponsiveThemeAddons._responsive_addons_already_activated );
			$( document ).on( 'responsive-pro-responsive-addons-activation-completed', InstallResponsiveThemeAddons._reloadThePage );
			$( document ).on( 'wp-theme-install-success', InstallResponsiveThemeAddons._activateTheme );
			$( document ).on( 'wp-plugin-install-success', InstallResponsiveThemeAddons._installSuccess );
		},

		/**
		 * Install responsive theme and responsive addons
		 *
		 * @since 1.0.1
		 */
		_installResponsiveTheme: function( event ){
			event.preventDefault();
			var responsive_theme_trigger = $( '#responsive-theme-status' ).val();
			var btn                      = $( event.target );

			if ( btn.hasClass( 'processing' ) ) {
				return;
			}

			btn.text( responsiveInstallThemeAddonsVars.installing ).addClass( 'processing' );
			$( '#responsive-theme-addons-activation a' ).addClass( 'processing' );

			$( document ).trigger( responsive_theme_trigger );
		},

		/**
		 * Install Responsive Addons
		 *
		 * @since 1.0.1
		 */
		_installResponsiveAddons: function( event ){
			event.preventDefault();
			var responsive_addons_trigger = $( '#responsive-addons-status' ).val();
			$( document ).trigger( responsive_addons_trigger );
		},

		/**
		 * Install Responsive Addons
		 *
		 * @since 1.0.1
		 */
		_install_and_activate_addons: function( event ) {
			event.preventDefault();
			// Add plugin activate request in Ajax queue.
			wp.updates.queue.push(
				{
					action: 'install-plugin', // Required action.
					data:   {
						slug: 'responsive-add-ons'
					}
				}
			);

			InstallResponsiveThemeAddons.responsive_add_ons_init = 'responsive-add-ons/responsive-add-ons.php';
			// Required to set queue.
			wp.updates.queueChecker();
		},

		/**
		 * Responsive Theme already activated
		 *
		 * @since 1.0.1
		 */
		_responsive_theme_already_activated: function( event ) {
			$( document ).trigger( 'responsive-pro-responsive-theme-activation-completed' );
		},

		/**
		 * Responsive Theme already activated
		 *
		 * @since 1.0.1
		 */
		_responsive_addons_already_activated: function( event ) {
			$( document ).trigger( 'responsive-pro-responsive-addons-activation-completed' );
		},

		/**
		 * Activate plugin
		 *
		 * @since 1.0.1
		 */
		_activatePlugin: function( event, response ) {

			event.preventDefault();

			InstallResponsiveThemeAddons.responsive_add_ons_init = 'responsive-add-ons/responsive-add-ons.php';
			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout(
				function () {

					$.ajax(
						{
							url: responsiveInstallThemeAddonsVars.ajaxurl,
							type: 'POST',
							data: {
								'action': 'responsive-pro-activate-responsive-addons-plugin',
								'init': InstallResponsiveThemeAddons.responsive_add_ons_init,
								'_ajax_nonce': responsiveInstallThemeAddonsVars._ajax_nonce,
							},
							}
					)
							.done(
								function (result) {

									$( document ).trigger( 'responsive-pro-responsive-addons-activation-completed' );
								}
							);

				},
				1200
			);
		},

		/**
		 * Install Success
		 *
		 * @since 1.0.1
		 */
		_installSuccess: function( event, response ) {

			if ( typeof InstallResponsiveThemeAddons.responsive_add_ons_init !== 'undefined' && InstallResponsiveThemeAddons.responsive_add_ons_init ) {
				event.preventDefault();

				// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
				setTimeout(
					function () {

						$.ajax(
							{
								url: responsiveInstallThemeAddonsVars.ajaxurl,
								type: 'POST',
								data: {
									'action': 'responsive-pro-activate-responsive-addons-plugin',
									'init': InstallResponsiveThemeAddons.responsive_add_ons_init,
									'_ajax_nonce': responsiveInstallThemeAddonsVars._ajax_nonce,
								},
							}
						)
							.done(
								function (result) {

									$( document ).trigger( 'responsive-pro-responsive-addons-activation-completed' );
								}
							);

					},
					1200
				);
			}
		},

		/**
		 * Reload the page
		 *
		 * @since 1.0.1
		 */
		_reloadThePage: function( event ) {
			setTimeout(
				function() {
					location.reload();
				},
				1000
			);
		},

		/**
		 * Activate Theme
		 *
		 * @since 1.0.1
		 */
		_activateTheme: function( event, response ) {
			event.preventDefault();

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout(
				function() {

						$.ajax(
							{
								url: responsiveInstallThemeAddonsVars.ajaxurl,
								type: 'POST',
								data: {
									'action' : 'responsive-pro-activate-responsive-theme',
									'_ajax_nonce': responsiveInstallThemeAddonsVars._ajax_nonce,
								},
							}
						)
						.done(
							function (result) {
								if ( result.success ) {
									$( document ).trigger( 'responsive-pro-responsive-theme-activation-completed' );
								}

							}
						);

				},
				3000
			);

		},

		/**
		 * Install and activate
		 *
		 * @since 1.0.1
		 */
		_install_and_activate_theme: function(event ) {
			event.preventDefault();

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( event );
			}

			wp.updates.installTheme(
				{
					slug: 'responsive'
				}
			);
		},

	};

	/**
	 * Initialize
	 */
	$(
		function(){
			InstallResponsiveThemeAddons.init();
		}
	);

})( jQuery );
