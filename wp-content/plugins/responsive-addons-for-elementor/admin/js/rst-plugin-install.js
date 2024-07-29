/**
 * Install and Activates the RST Plugin.
 *
 * @package responsive-addons-for-elementor
 */

/* global rstPluginInstall */

jQuery( document ).ready(
	function ( $ ) {
		$.pluginInstall = {
			'init': function () {
				this.handleInstall();
				this.handleActivate();
			},

			'handleInstall': function () {
				var self = this;
				$( 'body' ).on(
					'click',
					'.responsive-addons-for-elementor-rst-button',
					function ( e ) {
									e.preventDefault();
									var button   = $( this );
									var slug     = button.attr( 'data-slug' );
									var url      = button.attr( 'href' );
									var redirect = $( button ).data( 'redirect' );
									button.text( rstPluginInstall.installing + '...' );
									button.addClass( 'updating-message' );
						wp.updates.installPlugin(
							{
								slug: slug,
								success: function () {
									$( '.responsive-addons-for-elementor-rst-button' ).text( rstPluginInstall.activating + '...' )
									$( '.responsive-addons-for-elementor-rst-button' ).addClass( 'updating-message' );
									self.activatePlugin( url, redirect );
								}
							}
						);
					}
				);
			},

			'activatePlugin': function ( url, redirect ) {
				if (typeof url === 'undefined' || ! url ) {
					return;
				}
				jQuery.ajax(
					{
						async: true,
						type: 'GET',
						url: url,
						success: function () {
							// Reload the page.
							if (typeof(redirect) !== 'undefined' && redirect !== '' ) {
								$( window ).off( 'beforeunload' );
								window.location.replace( redirect );
								window.location.href( redirect );
							} else {
								location.reload();
							}
						},
						error: function ( jqXHR, exception ) {
							var msg = '';
							if (jqXHR.status === 0 ) {
								msg = rstPluginInstall.verify_network;
							} else if (jqXHR.status === 404 ) {
								msg = rstPluginInstall.page_not_found;
							} else if (jqXHR.status === 500 ) {
								msg = rstPluginInstall.internal_server_error;
							} else if (exception === 'parsererror' ) {
								msg = rstPluginInstall.json_parse_failed;
							} else if (exception === 'timeout' ) {
								msg = rstPluginInstall.timeout_error;
							} else if (exception === 'abort' ) {
											msg = rstPluginInstall.ajax_req_aborted;
							} else {
									msg = rstPluginInstall.uncaught_error;
							}
							console.log( msg );
						},
					}
				);
			},

			'handleActivate': function () {
				var self = this;
				$( 'body' ).on(
					'click',
					'.activate-now',
					function ( e ) {
									e.preventDefault();
									var button   = $( this );
									var url      = button.attr( 'href' );
									var redirect = button.attr( 'data-redirect' );
									$( '.responsive-addons-for-elementor-rst-button' ).text( rstPluginInstall.activating + '...' )
									$( '.responsive-addons-for-elementor-rst-button' ).addClass( 'updating-message' );
									self.activatePlugin( url, redirect );
					}
				);
			},
		};
		$.pluginInstall.init();
	}
);
