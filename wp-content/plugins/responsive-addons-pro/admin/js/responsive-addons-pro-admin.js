/**
 * Responsive Ready Sites importer events
 *
 * @since 1.0.0
 * @package Responsive Ready Sites
 */

/**
 * AJAX Request Queue
 *
 * - add()
 * - remove()
 * - run()
 * - stop()
 *
 * @since 1.0.0
 */
 var ResponsiveSitesProAjaxQueue = (function() {

	var requests = [];

	return {

		/**
		 * Add AJAX request
		 *
		 * @since 1.0.0
		 */
		add:  function(opt) {
			requests.push( opt );
		},

		/**
		 * Remove AJAX request
		 *
		 * @since 1.0.0
		 */
		remove:  function(opt) {
			if ( jQuery.inArray( opt, requests ) > -1 ) {
				requests.splice( $.inArray( opt, requests ), 1 );
			}
		},

		/**
		 * Run / Process AJAX request
		 *
		 * @since 1.0.0
		 */
		run: function() {
			var self = this,
				oriSuc;

			if ( requests.length ) {
				oriSuc = requests[0].complete;

				requests[0].complete = function() {
					if ( typeof(oriSuc) === 'function' ) {
						oriSuc();
					}
					requests.shift();
					self.run.apply( self, [] );
				};

				jQuery.ajax( requests[0] );

			} else {

				self.tid = setTimeout(
					function() {
						self.run.apply( self, [] );
					},
					1000
				);
			}
		},

		/**
		 * Stop AJAX request
		 *
		 * @since 1.0.0
		 */
		stop:  function() {

			requests = [];
			clearTimeout( this.tid );
		}
	};

}());

(function( $ ) {

	const resetPostChunks = (chunk) => {
		ResponsiveSitesProAdmin.import_progress_status_text = "Resetting posts...";
		ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
		$.ajax(
			{
				url  : responsiveSitesProAdmin.ajaxurl,
				type : 'POST',
				data : {
					action : 'responsive-ready-sites-delete-posts',
					ids: chunk,
					_ajax_nonce      : responsiveSitesProAdmin._ajax_nonce,
				},
			}
		)
			.fail(
				function( jqXHR ){
					responsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
				}
			)
			.done(
				function ( message ) {
							ResponsiveSitesProAdmin.import_progress_status_text = "Resetting posts done...";
							ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
							$( document ).trigger( 'responsive-ready-sites-pro-delete-posts-done' );
							$( document ).trigger( 'responsive-ready-sites-pro-reset-data-done' );

				}
			);
	};

	var wxrImport = {
		complete: {
			posts: 0,
			media: 0,
			users: 0,
			comments: 0,
			terms: 0,
		},

		updateDelta: function (type, delta) {
			this.complete[ type ] += delta;

			var self = this;
			requestAnimationFrame(
				function () {
					self.render();
				}
			);
		},
		updateProgress: function ( type, complete, total ) {
			var text = complete + '/' + total;

			if ( 'undefined' !== type && 'undefined' !== text ) {
				total = parseInt( total, 10 );
				if ( 0 === total || isNaN( total ) ) {
					total = 1;
				}
				var percent      = parseInt( complete, 10 ) / total;
				var progress     = Math.round( percent * 100 ) + '%';
				var progress_bar = percent * 100;

				if ( progress_bar <= 100 ) {
					var process_bars        = document.getElementsByClassName( 'responsive-ready-sites-import-process' );
					var process_bars_length = process_bars.length;
					for ( var i = 0; i < process_bars_length; i++ ) {
						process_bars[i].value = progress_bar;
					}
				}
			}
		},
		render: function () {
			var types    = Object.keys( this.complete );
			var complete = 0;
			var total    = 0;

			for (var i = types.length - 1; i >= 0; i--) {
				var type = types[i];
				this.updateProgress( type, this.complete[ type ], this.data.count[ type ] );

				complete += this.complete[ type ];
				total    += this.data.count[ type ];
			}

			this.updateProgress( 'total', complete, total );
		}
	};

	ResponsiveSitesProAdmin = {

		reset_remaining_posts: 0,
		reset_remaining_wp_forms: 0,
		reset_remaining_terms: 0,
		reset_processed_posts: 0,
		reset_processed_wp_forms: 0,
		reset_processed_terms: 0,
		site_imported_data: null,

		current_site: [],
		current_screen: '',
		active_site_slug: '',
		active_site_title: '',
		active_site_featured_image_url: '',
		widgets_data: '',
		site_options_data: '',

		templateData: {},

		site_customizer_data: '',

		required_plugins: '',
		required_pro_plugins: '',

		xml_path         : '',
		wpforms_path	: '',
		import_start_time  : '',
		import_end_time    : '',
		import_page_start_time  : '',
		import_page_end_time    : '',
		import_total_time    : '',

		pro_plugins_flag : false,

		current_page_id : '',
		processing_single_template: false,
		importFlagPro   : false,
		import_progress_status_text : '',
		import_progress_percent: 0,

		init: function()
		{
			this._bind();
		},

		/**
		 * Binds events for the Responsive Ready Sites.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( document ).on( 'click'                     , '.import-demo-data, .responsive-ready-site-import-pro', ResponsiveSitesProAdmin._importDemo );
			$( document ).on( 'click'                     , '.theme-browser .inactive.ra-site-single .theme-screenshot, .theme-browser .inactive.ra-site-single .more-details, .theme-browser .inactive.ra-site-single .install-theme-preview', ResponsiveSitesProAdmin._preview );
			$( document ).on( 'click'                     , '.theme-browser .active.ra-site-single .theme-screenshot, .theme-browser .active.ra-site-single .more-details, .theme-browser .active.ra-site-single .install-theme-preview', ResponsiveSitesProAdmin._doNothing );
			$( document ).on( 'click', '.responsive-demo-import-options-pro', ResponsiveSitesProAdmin._importSiteOptionsScreen );
			$( document ).on( 'click', '.responsive-ready-site-import-with-sub, .responsive-ready-site-import-without-sub', ResponsiveSitesProAdmin._importSiteProgressScreen );

			$( document ).on( 'responsive-get-active-theme-pro' , ResponsiveSitesProAdmin._is_responsive_theme_active );
			$( document ).on( 'responsive-ready-sites-pro-install-start'       , ResponsiveSitesProAdmin._process_import );

			$( document ).on( 'responsive-ready-sites-pro-import-set-site-data-done'   		, ResponsiveSitesProAdmin._installRequiredPlugins );
			$( document ).on( 'responsive-ready-sites-pro-install-and-activate-required-plugins-done', ResponsiveSitesProAdmin._resetData );
			$( document ).on( 'responsive-ready-sites-pro-reset-data'							, ResponsiveSitesProAdmin._backup_before_rest_options );
			$( document ).on( 'responsive-ready-sites-pro-backup-settings-before-reset-done'	, ResponsiveSitesProAdmin._reset_customizer_data );
			$( document ).on( 'responsive-ready-sites-pro-reset-customizer-data-done'			, ResponsiveSitesProAdmin._reset_site_options );
			$( document ).on( 'responsive-ready-sites-pro-reset-site-options-done'				, ResponsiveSitesProAdmin._reset_widgets_data );
			$( document ).on( 'responsive-ready-sites-pro-reset-widgets-data-done'				, ResponsiveSitesProAdmin._reset_terms );
			$( document ).on( 'responsive-ready-sites-pro-delete-terms-done'					, ResponsiveSitesProAdmin._reset_wp_forms );
			$( document ).on( 'responsive-ready-sites-pro-delete-wp-forms-done'				, ResponsiveSitesProAdmin._reset_posts );

			$( document ).on( 'responsive-ready-sites-pro-reset-data-done' , ResponsiveSitesProAdmin._importWPForms );
			$( document ).on( 'responsive-ready-sites-pro-import-wpforms-done' , ResponsiveSitesProAdmin._importXML );
			$( document ).on( 'responsive-ready-sites-pro-import-xml-done' , ResponsiveSitesProAdmin._importCustomizerSettings );
			$( document ).on( 'responsive-ready-sites-pro-import-customizer-settings-done' , ResponsiveSitesProAdmin._importWidgets );
			$( document ).on( 'responsive-ready-sites-pro-import-widgets-done' , ResponsiveSitesProAdmin._importSiteOptions );
			$( document ).on( 'responsive-ready-sites-pro-import-options-done' , ResponsiveSitesProAdmin._importEnd );
			$( document ).on( 'wp-plugin-installing'      , ResponsiveSitesProAdmin._pluginInstalling );
			$( document ).on( 'wp-plugin-install-success' , ResponsiveSitesProAdmin._installSuccess );

			// Single Page Import events.
			$( document ).on( 'click'                     , '.single-page-import-button-pro', ResponsiveSitesProAdmin._importSinglePageOptions );
			$( document ).on( 'click'                     , '.responsive-ready-page-import-pro', ResponsiveSitesProAdmin._importSinglePage );
			$( document ).on( 'click', '.responsive-page-import-options-pro', ResponsiveSitesProAdmin._importPagePreviewScreen );
			$( document ).on( 'responsive-ready-page-pro-install-and-activate-required-plugins-done' , ResponsiveSitesProAdmin._importPage );
			$( document ).on( 'responsive-ready-sites-import-page-pro-start'   		, ResponsiveSitesProAdmin._installRequiredPlugins );

			$(window).on('beforeunload', function() {
				if(ResponsiveSitesProAdmin.import_progress_percent > 0 && ResponsiveSitesProAdmin.import_progress_percent < 100) {
						return "Are you sure you want to cancel the site import process?";
				}
			});
		},

		/**
		 * Do Nothing.
		 */
		_doNothing: function( event ) {
			event.preventDefault();
		},

		/**
		 * Import Complete.
		 */
		_importEnd: function( event ) {
			ResponsiveSitesProAdmin.import_progress_status_text = "Final finishings...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action : 'responsive-ready-sites-import-end',
						slug: ResponsiveSitesProAdmin.active_site_slug,
						title: ResponsiveSitesProAdmin.active_site_title,
						featured_image_url: ResponsiveSitesProAdmin.active_site_featured_image_url,
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					}
				}
			)
				.done(
					function ( data ) {

						// Fail - Import In-Complete.
						if ( false === data.success ) {
							// log.
						} else {
							setTimeout( function () {
								ResponsiveSitesProAdmin.import_end_time = performance.now();
								ResponsiveSitesProAdmin.import_progress_percent = 100;
								ResponsiveSitesProAdmin.import_progress_status_text = "Import Done";
								// Calculate the total time taken in seconds
								ResponsiveSitesProAdmin.import_total_time = Math.floor((ResponsiveSitesProAdmin.import_end_time  - ResponsiveSitesProAdmin.import_start_time ) / 1000); // Convert milliseconds to seconds

								ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
								ResponsiveSitesProAdmin._importCompletionCongratsScreen(responsiveSitesProAdmin.siteURL);

							}, 10000 );
						}
					}
				);
		},

		/**
		 * Import Site options Screen
		 */
		_importSiteOptionsScreen: function(event) {
			event.preventDefault();

			$( '#responsive-ready-site-preview' ).hide();
			$( '#responsive-ready-sites-import-options' ).show();

			var self = $( this ).parents( '.responsive-ready-site-preview' );

			var demoId                  = self.data( 'demo-id' ) || '',
				apiURL                  = self.data( 'demo-api' ) || '',
				demoType                = self.data( 'demo-type' ) || '',
				check_plugins_installed = self.data( 'check_plugins_installed' ) || '',
				demoURL                 = self.data( 'demo-url' ) || '',
				active_site             = self.data( 'active-site' ) || '',
				screenshot              = self.data( 'screenshot' ) || '',
				demo_name               = self.data( 'demo-name' ) || '',
				demo_slug               = self.data( 'demo-slug' ) || '',
				requiredPlugins         = self.data( 'required-plugins' ) || '',
				requiredProPlugins      = self.data( 'required-pro-plugins' ) || '',
				responsiveSiteOptions   = self.find( '.responsive-site-options' ).val() || '';

				var rbeaPlugin = {
					"name": "Responsive Block Editor Addons",
					"slug": "responsive-block-editor-addons",
					"init": "responsive-block-editor-addons/responsive-block-editor-addons.php"
				};
				var isDuplicate = requiredPlugins.some(function(plugin) {
					return plugin.slug === rbeaPlugin.slug;
				});
				// If it's not a duplicate, add it to the array
				if (!isDuplicate) {
					requiredPlugins.push(rbeaPlugin);
				}

			var template = wp.template( 'responsive-ready-sites-import-options-page' );

			templateData = [{
				id: demoId,
				demo_type: demoType,
				check_plugins_installed: check_plugins_installed,
				demo_url: demoURL,
				demo_api: apiURL,
				screenshot: screenshot,
				name: demo_name,
				active_site: active_site,
				slug: demo_slug,
				required_plugins: JSON.stringify( requiredPlugins ),
				required_pro_plugins: JSON.stringify( requiredProPlugins ),
				responsive_site_options: responsiveSiteOptions,
				pro_plugins_flag: ResponsiveSitesProAdmin.pro_plugins_flag,
			}];

			$('body').removeClass('responsive-ready-site-preview-screen');
			$('body').addClass('responsive-ready-site-import-options-screen');
			$( '#responsive-ready-sites-import-options' ).append( template( templateData[0] ) );
		},

		/**
		 * Import Site progress Screen
		 */
		_importSiteProgressScreen: function(event) {
			event.preventDefault();

			var site_id = $( this ).data( 'demo-id' ) || '';

			var self = $( this ).parents( '.responsive-ready-sites-advanced-options-wrap' );

			$( '#responsive-ready-sites-import-progress' ).show();

			var demoId                  = self.data( 'demo-id' ) || '',
				apiURL                  = self.data( 'demo-api' ) || '',
				demoType                = self.data( 'demo-type' ) || '',
				check_plugins_installed = self.data( 'check_plugins_installed' ) || '',
				demoURL                 = self.data( 'demo-url' ) || '',
				active_site             = self.data( 'active-site' ) || '',
				screenshot              = self.data( 'screenshot' ) || '',
				demo_name               = self.data( 'demo-name' ) || '',
				demo_slug               = self.data( 'demo-slug' ) || '',
				requiredPlugins         = self.data( 'required-plugins' ) || '',
				requiredProPlugins      = self.data( 'required-pro-plugins' ) || '',
				responsiveSiteOptions   = self.find( '.responsive-site-options' ).val() || '';

				var rbeaPlugin = {
					"name": "Responsive Block Editor Addons",
					"slug": "responsive-block-editor-addons",
					"init": "responsive-block-editor-addons/responsive-block-editor-addons.php"
				};
				var isDuplicate = requiredPlugins.some(function(plugin) {
					return plugin.slug === rbeaPlugin.slug;
				});
				// If it's not a duplicate, add it to the array
				if (!isDuplicate) {
					requiredPlugins.push(rbeaPlugin);
				}

			var template = wp.template( 'responsive-ready-sites-import-progress-page' );

			if ( typeof requiredProPlugins !== 'undefined'  && $.isArray(requiredProPlugins) ) {

				$( requiredProPlugins ).each(
					function( index, plugin ) {
						if( plugin.slug != 'responsive-elementor-addons') {
							ResponsiveSitesProAdmin.pro_plugins_flag = true;
						}
					}
				);
			}

			templateData = [{
				id: demoId,
				demo_type: demoType,
				check_plugins_installed: check_plugins_installed,
				demo_url: demoURL,
				demo_api: apiURL,
				screenshot: screenshot,
				name: demo_name,
				active_site: active_site,
				slug: demo_slug,
				required_plugins: JSON.stringify( requiredPlugins ),
				required_pro_plugins: JSON.stringify( requiredProPlugins ),
				responsive_site_options: responsiveSiteOptions,
				pro_plugins_flag: ResponsiveSitesProAdmin.pro_plugins_flag,
			}];
			$( '#responsive-ready-sites-import-progress' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );

			if ( $.isArray( requiredPlugins ) || $.isArray( requiredProPlugins ) ) {
				var $pluginsFilter = $( '#plugin-filter' ),
					data           = {
						action           : 'responsive-ready-sites-required-plugins-pro',
						_ajax_nonce      : responsiveSitesProAdmin._ajax_nonce,
						required_plugins : requiredPlugins,
						required_pro_plugins : requiredProPlugins,
				};

				// Add disabled class from import button.
				$( '.responsive-demo-import' )
					.addClass( 'disabled not-click-able' )
					.removeAttr( 'data-import' );

				$( '.required-plugins' ).addClass( 'loading' ).html( '<span class="spinner is-active"></span>' );

				// Required Required.
				$.ajax(
					{
						url  : responsiveSitesProAdmin.ajaxurl,
						type : 'POST',
						data : data,
					}
				)
					.fail(
						function( jqXHR ){

							// Remove loader.
							$( '.required-plugins' ).removeClass( 'loading' ).html( '' );

						}
					)
					.done(
						function ( response ) {
							required_plugins = response.data['required_plugins'];

							// Remove loader.
							$( '.required-plugins' ).removeClass( 'loading' ).html( '' );
							$( '.required-plugins-list' ).html( '' );

							/**
							 * Count remaining plugins.
							 *
							 * @type number
							 */
							var remaining_plugins = 0;

							/**
							 * Pro Plugins
							 *
							 * List of required Pro plugins.
							 */
							if ( typeof required_plugins.proplugins !== 'undefined' ) {

								$( required_plugins.proplugins ).each(
									function( index, plugin ) {
										if( plugin.slug === 'responsive-elementor-addons') {
											$('.required-plugins-list').append('<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>');
										} else {
											$('.required-third-party-plugins-list').append('<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + ' - <span class="responsive-premium-plugin-tag">3rd Party Plugin</span></li>');
										}
									}
								);
							}

							/**
							 * Not Installed
							 *
							 * List of not installed required plugins.
							 */
							if ( typeof required_plugins.notinstalled !== 'undefined' ) {

								// Add not have installed plugins count.
								remaining_plugins += parseInt( required_plugins.notinstalled.length );

								$( required_plugins.notinstalled ).each(
									function( index, plugin ) {
										$( '.required-plugins-list' ).append( '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>' );
									}
								);
							}

							/**
							 * Inactive
							 *
							 * List of not inactive required plugins.
							 */
							if ( typeof required_plugins.inactive !== 'undefined' ) {
								// Add inactive plugins count.
								remaining_plugins += parseInt( required_plugins.inactive.length );

								$( required_plugins.inactive ).each(
									function( index, plugin ) {
										$( '.required-plugins-list' ).append( '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>' );
									}
								);
							}

							/**
							 * Active
							 *
							 * List of not active required plugins.
							 */
							if ( typeof required_plugins.active !== 'undefined' ) {

								$( required_plugins.active ).each(
									function( index, plugin ) {
										$( '.required-plugins-list' ).append( '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>' );
									}
								);
							}

							if ( check_plugins_installed && typeof required_plugins.notinstalled !== 'undefined' && required_plugins.notinstalled.length > 0 ) {
								$( '.responsive-ready-site-import-pro' ).addClass( 'disabled not-click-able' );
								$( '.responsive-ready-site-import-pro' ).prop( 'disabled',true );
								$( '.responsive-ready-sites-install-plugins-title' ).append( '<span class="warning"> - Please make sure you have following plugins Installed</span>' );
								$( '#responsive-ready-sites-tooltip-plugins-settings' ).css( 'display', 'block' );
							}

							/**
							 * Enable Demo Import Button
							 *
							 * @type number
							 */
							responsiveSitesProAdmin.requiredPlugins = required_plugins;
						}
					);

			}
		},

		_toggle_tooltip: function( event ) {
			event.preventDefault();
			var tip_id = $( this ).data( 'tip-id' ) || '';
			if ( tip_id && $( '#' + tip_id ).length ) {
				$( '#' + tip_id ).toggle();
			}
		},

		/**
		 * Import WpForms
		 */
		_importWPForms: function() {

			ResponsiveSitesProAdmin.import_progress_percent = 50;
			ResponsiveSitesProAdmin.import_progress_status_text = "Importing forms...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action	: 'responsive-ready-sites-import-wpforms',
						wpforms_path : ResponsiveSitesProAdmin.wpforms_path,
						_ajax_nonce : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( forms){
						if (false === forms.success) {
							// log.
						} else {
							$( document ).trigger( 'responsive-ready-sites-pro-import-wpforms-done' );
						}
					}
				)
		},

		/**
		 * Import Customizer Setting
		 */
		_importCustomizerSettings: function() {
			if( ResponsiveSitesProAdmin._is_import_customizer_settings() ) {

				ResponsiveSitesProAdmin.import_progress_percent = ResponsiveSitesProAdmin.import_progress_percent < 75 ? 75 : ResponsiveSitesProAdmin.import_progress_percent;
				ResponsiveSitesProAdmin.import_progress_status_text = "Importing Customizer Settings...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				$.ajax(
					{
						url: responsiveSitesProAdmin.ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'responsive-ready-sites-import-customizer-settings',
							site_customizer_data: ResponsiveSitesProAdmin.site_customizer_data,
							_ajax_nonce: responsiveSitesProAdmin._ajax_nonce
						},
					}
				)
					.fail(
						function( jqXHR ){
							ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
						}
					)
					.done(
						function (forms) {
							if (false === forms.success) {
								// log.
							} else {
								$( document ).trigger( 'responsive-ready-sites-pro-import-customizer-settings-done' );
							}
						}
						)
			} else{
				$( document ).trigger( 'responsive-ready-sites-pro-import-customizer-settings-done' );
			}
		},

		/**
		 * Import Site Options.
		 */
		_importSiteOptions: function( event ) {
			ResponsiveSitesProAdmin.import_progress_percent = 90;
			ResponsiveSitesProAdmin.import_progress_status_text = "Importing Site Options...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action       : 'responsive-ready-sites-import-options',
						options_data : ResponsiveSitesProAdmin.site_options_data,
						_ajax_nonce  : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( options_data ) {

						// Fail - Import Site Options.
						if ( false === options_data.success ) {
							ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
						} else {

							// 3. Pass - Import Site Options.
							$( document ).trigger( 'responsive-ready-sites-pro-import-options-done' );
						}
					}
				);
		},

		/**
		 * Import Widgets.
		 */
		_importWidgets: function( event ) {
			ResponsiveSitesProAdmin.import_progress_percent += 5;
			ResponsiveSitesProAdmin.import_progress_status_text = "Importing Widgets...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action       : 'responsive-ready-sites-import-widgets',
						widgets_data : ResponsiveSitesProAdmin.widgets_data,
						_ajax_nonce  : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( widgets_data ) {

						if ( false === widgets_data.success ) {
							ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );

						} else {

							$( document ).trigger( 'responsive-ready-sites-pro-import-widgets-done' );
						}
					}
				);
		},

		/**
		 * Bulk Plugin Active & Install
		 */
		_bulkPluginInstallActivate: function()
		{
			if ( 0 === responsiveSitesProAdmin.required_plugins.length ) {
				return;
			}

			var not_installed 	 = responsiveSitesProAdmin.required_plugins.notinstalled || '';
			var activate_plugins = responsiveSitesProAdmin.required_plugins.inactive || '';
			var pro_plugins		 = responsiveSitesProAdmin.required_plugins.proplugins || '';

			// Install wordpress.org plugins.
			if ( not_installed.length > 0 ) {
				ResponsiveSitesProAdmin.import_progress_status_text = "Installing Required Plugins...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

				ResponsiveSitesProAdmin._installAllPlugins( not_installed );
			}

			// Activate wordpress.org plugins.
			if ( activate_plugins.length > 0 ) {
				ResponsiveSitesProAdmin._activateAllPlugins( activate_plugins );
			}

			// Install Pro Plugins.
			if ( pro_plugins.length > 0 ) {
				ResponsiveSitesProAdmin._installProPlugins( pro_plugins );
			}

			if ( activate_plugins.length <= 0 && not_installed.length <= 0 ) {
				if ( ResponsiveSitesProAdmin.processing_single_template ) {
					ResponsiveSitesProAdmin._ready_for_import_template();
				} else {
					ResponsiveSitesProAdmin._ready_for_import_site();
				}
			}

		},

		_ready_for_import_site: function () {
			var notinstalled = responsiveSitesProAdmin.required_plugins.notinstalled || 0;
			var inactive     = responsiveSitesProAdmin.required_plugins.inactive || 0;
			var proplugins   = responsiveSitesProAdmin.required_plugins.proplugins || 0;

			if ( ResponsiveSitesProAdmin._areEqual( notinstalled.length, inactive.length, proplugins.length ) ) {
				ResponsiveSitesProAdmin.import_progress_status_text = "Ready for site import...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				$( document ).trigger( 'responsive-ready-sites-pro-install-and-activate-required-plugins-done' );
			}
		},

		/**
		 * Ready for template import
		 *
		 * @private
		 */
		_ready_for_import_template: function () {
			var notinstalled = responsiveSitesProAdmin.required_plugins.notinstalled || 0;
			var inactive     = responsiveSitesProAdmin.required_plugins.inactive || 0;
			var proplugins   = responsiveSitesProAdmin.required_plugins.proplugins || 0;

			if ( ResponsiveSitesProAdmin._areEqual( notinstalled.length, inactive.length, proplugins.length ) ) {
				ResponsiveSitesProAdmin.import_progress_percent += 10;
				ResponsiveSitesProAdmin.import_progress_status_text = "Ready for template import...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				$( document ).trigger( 'responsive-ready-page-pro-install-and-activate-required-plugins-done' );
			}
		},

		_areEqual:function () {
			var len = arguments.length;
			for (var i = 1; i < len; i++) {
				if (arguments[i] === null || arguments[i] !== arguments[i - 1]) {
					return false;
				}
			}
			return true;
		},

		/**
		 * Individual Site Preview
		 *
		 * On click on image, more link & preview button.
		 */
		_preview: function( event ) {

			event.preventDefault();

			var site_id = $( this ).parents( '.ra-site-single' ).data( 'demo-id' ) || '';

			var self = $( this ).parents( '.theme' );
			self.addClass( 'theme-preview-on' );

			$( '#responsive-sites' ).hide();

			$( '#responsive-ready-site-preview' ).show();

			self.addClass( 'theme-preview-on' );

			$( 'html' ).addClass( 'responsive-site-preview-on' );

			ResponsiveSitesProAdmin._renderDemoPreview( self );
		},

		/**
		 * Render Demo Preview
		 */
		_renderDemoPreview: function(anchor) {

			var demoId                             = anchor.data( 'demo-id' ) || '',
				apiURL                             = anchor.data( 'demo-api' ) || '',
				demoURL                            = anchor.data( 'demo-url' ) || '',
				screenshot                         = anchor.data( 'screenshot' ) || '',
				demo_name                          = anchor.data( 'demo-name' ) || '',
				active_site                        = anchor.data( 'active-site' ) || '',
				demo_slug                          = anchor.data( 'demo-slug' ) || '',
				wpforms_path                   	   = anchor.data( 'wpforms-path' ) || '',
				requiredPlugins                    = anchor.data( 'required-plugins' ) || '',
				requiredProPlugins                 = anchor.data( 'required-pro-plugins' ) || '',
				allow_pages                        = anchor.data( 'allow-pages' ) || false,
				pages                              = anchor.data( 'pages' ) || '',
				responsiveSiteOptions              = anchor.find( '.responsive-site-options' ).val() || '',
				demo_type                          = anchor.data( 'demo-type' ) || '',
				check_plugins_installed            = anchor.data( 'check_plugins_installed' ) || '',
				isResponsiveAddonsProInstalled     = ResponsiveSitesProAdmin._checkResponsiveAddonsProInstalled(),
				isResponsiceAddonsProLicenseActive = ResponsiveSitesProAdmin._checkRespomsiveAddonsProLicenseActive();

			var template = wp.template( 'responsive-ready-site-preview' );

			templateData = [{
				id: demoId,
				demo_url: demoURL,
				demo_api: demoURL,
				screenshot: screenshot,
				name: demo_name,
				active_site: active_site,
				wpforms_path: wpforms_path,
				slug: demo_slug,
				required_plugins: JSON.stringify( requiredPlugins ),
				required_pro_plugins: JSON.stringify( requiredProPlugins ),
				responsive_site_options: responsiveSiteOptions,
				demo_type: demo_type,
				check_plugins_installed: check_plugins_installed,
				is_responsive_addons_pro_installed: isResponsiveAddonsProInstalled,
				is_responsive_addons_pro_license_active: isResponsiceAddonsProLicenseActive,
				allow_pages: allow_pages,
				pages: JSON.stringify( pages ),
			}];
			$('body').addClass('responsive-ready-site-preview-screen');
			$( '#responsive-ready-site-preview' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );

		},

		/**
		 * Check if Responsive Addons Pro is installed or not
		 */
		_checkResponsiveAddonsProInstalled: function() {
			var is_pro_installed;
			$.ajax(
				{
					url: responsiveSitesProAdmin.ajaxurl,
					async: false,
					type : 'POST',
					dataType: 'json',
					data: {
						'action': 'check-responsive-add-ons-pro-installed',
						'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
					}
				}
			)
				.done(
					function ( response ) {
						is_pro_installed = response;
					}
				);

			if (is_pro_installed.success) {
				return true;
			} else {
				return false;
			}
		},

		/**
		 * Check if Responsive Addons Pro is installed or not
		 */
		_checkRespomsiveAddonsProLicenseActive: function() {
			var is_pro_license_active;
			$.ajax(
				{
					url: responsiveSitesProAdmin.ajaxurl,
					async: false,
					type : 'POST',
					dataType: 'json',
					data: {
						'action': 'check-responsive-add-ons-pro-license-active',
						'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
					}
				}
			)
				.done(
					function ( response ) {
						is_pro_license_active = response;
					}
				);

			if (is_pro_license_active.success) {
				return true;
			} else {
				return false;
			}
		},

		/**
		 * Activate All Plugins.
		 */
		_activateAllPlugins: function( activate_plugins ) {
			ResponsiveSitesProAdmin.import_progress_status_text = "Activating Required Plugin...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.each(
				activate_plugins,
				function(index, single_plugin) {

					ResponsiveSitesProAjaxQueue.add(
						{
							url: responsiveSitesProAdmin.ajaxurl,
							type: 'POST',
							data: {
								'action'            : 'responsive-ready-sites-required-plugin-activate',
								'init'              : single_plugin.init,
								'_ajax_nonce'		: responsiveSitesProAdmin._ajax_nonce,
							},
							success: function( result ){

								if ( result.success ) {
									ResponsiveSitesProAdmin.import_progress_percent += 2;
									ResponsiveSitesProAdmin.import_progress_status_text = "Activated "+single_plugin.name;
									ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

									var pluginsList = responsiveSitesProAdmin.required_plugins.inactive;

									// Reset not installed plugins list.
									responsiveSitesProAdmin.required_plugins.inactive = ResponsiveSitesProAdmin._removePluginFromQueue( single_plugin.slug, pluginsList );

									if ( ResponsiveSitesProAdmin.processing_single_template ) {
										ResponsiveSitesProAdmin._ready_for_import_template();
									} else {
										ResponsiveSitesProAdmin._ready_for_import_site();
									}

								}
							}
						}
					);
				}
			);
			ResponsiveSitesProAjaxQueue.run();
		},

		/**
		 * Install Pro Plugins.
		 */
		_installProPlugins: function( pro_plugins ) {
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			ResponsiveSitesProAdmin.import_progress_status_text = "Installing Pro Plugins...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			$.ajax(
				{
					url: responsiveSitesProAdmin.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-ready-sites-install-required-pro-plugins',
						'license_key': responsiveSitesProAdmin.license_key,
						'request_url': responsiveSitesProAdmin.CcURL,
						'pro_plugin': pro_plugins,
						'product_id': responsiveSitesProAdmin.product_id,
						'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
					}
				}
			)
				.done(
					function (result) {
						if ( false === result.success ) {
							ResponsiveSitesProAdmin._log_error( 'Please activate the Responsive Pro License to use PRO template.', true );
						} else {
							// Reset not installed plugins list.
							$.each(
								pro_plugins,
								function (index, single_plugin) {
									ResponsiveSitesProAdmin.import_progress_percent += 2;
									ResponsiveSitesProAdmin.import_progress_status_text = "Activated "+single_plugin.name;
									ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
									var pluginsList                                     = responsiveSitesProAdmin.required_plugins.proplugins;
									responsiveSitesProAdmin.required_plugins.proplugins = ResponsiveSitesProAdmin._removePluginFromQueue( single_plugin.slug, pluginsList );
								}
							);
							if ( ResponsiveSitesProAdmin.processing_single_template ) {
								ResponsiveSitesProAdmin._ready_for_import_template();
							} else {
								ResponsiveSitesProAdmin._ready_for_import_site();
							}
						}
					}
				);
		},

		/**
		 * Remove plugin from the queue.
		 */
		_removePluginFromQueue: function( removeItem, pluginsList ) {
			return jQuery.grep(
				pluginsList,
				function( value ) {
					return value.slug != removeItem;
				}
			);
		},

		/**
		 * Install All Plugins.
		 */
		_installAllPlugins: function( not_installed ) {

			$.each(
				not_installed,
				function(index, single_plugin) {

					// Add each plugin activate request in Ajax queue.
					// @see wp-admin/js/updates.js.
					wp.updates.queue.push(
						{
							action: 'install-plugin', // Required action.
							data:   {
								slug: single_plugin.slug
							}
						}
					);
				}
			);

			// Required to set queue.
			wp.updates.queueChecker();
		},

		/**
	    *
	    * Check if import site content checkbox is checked
	    */
		_is_import_site_data: function() {
			if ( $( '.responsive-ready-sites-import-xml' ).find('.checkbox').is(':checked') ) {
				return true;
			}
			return false;
		},

		/**
	    *
	    * Check if import site content checkbox is checked
	    */
		_is_import_customizer_settings: function() {
			if ( $( '.responsive-ready-sites-import-customizer' ).find('.checkbox').is(':checked') ) {
				return true;
			}
			return false;
		},

		/**
		 * Import XML Data.
		 */
		_importXML: function() {
			if( ResponsiveSitesProAdmin._is_import_site_data() ) {
				if ( !ResponsiveSitesProAdmin.importFlagPro ) {
					ResponsiveSitesProAdmin.import_progress_status_text = "Importing Site Content...";
					ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				$.ajax(
					{
						url: responsiveSitesProAdmin.ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'responsive-ready-sites-import-xml',
							xml_path: ResponsiveSitesProAdmin.xml_path,
							_ajax_nonce : responsiveSitesProAdmin._ajax_nonce,
						},
						beforeSend: function () {
							$( '.responsive-ready-sites-import-process-wrap' ).show();
						},
					}
				)
					.fail(
						function( jqXHR ){
							ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
						}
					)
					.done(
						function (xml_data) {
							let _importXMLVar = 0;
							// 2. Fail - Import XML Data.
							if (false === xml_data.success) {
								// log.
							} else {

								// 2. Pass - Import XML Data.

								// Import XML though Event Source.
								wxrImport.data = xml_data.data;
								wxrImport.render();

								var evtSource       = new EventSource( wxrImport.data.url );
								evtSource.onmessage = function (message) {
									var data = JSON.parse( message.data );
									switch (data.action) {
										case 'updateDelta':
											_importXMLVar++;
											_importXMLVar % 9 === 0 ? ResponsiveSitesProAdmin.import_progress_percent += 1 : ResponsiveSitesProAdmin.import_progress_percent;

											$('.ready-sites-import-progress-info-percent').text(ResponsiveSitesProAdmin.import_progress_percent+"%");
											wxrImport.updateDelta( data.type, data.delta );
											break;

										case 'complete':
											evtSource.close();
											$( document ).trigger( 'responsive-ready-sites-pro-import-xml-done' );

											break;
									}
								};
								evtSource.addEventListener(
									'log',
									function (message) {
										var data    = JSON.parse( message.data );
										var message = data.message || '';
										if (message && 'info' === data.level) {
											message = message.replace(
												/"/g,
												function (letter) {
													return '';
												}
											);
											// log message on screen.
										}
									}
								);
							}
						}
					);
				}
			ResponsiveSitesProAdmin.importFlagPro = true;
			} else{
				$( document ).trigger( 'responsive-ready-sites-pro-import-xml-done' );
			}

		},

		/**
		 * Fires when a nav item is clicked.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _importDemo
		 */
		_importDemo: function(event) {
			event.preventDefault();
			ResponsiveSitesProAdmin.import_start_time = performance.now();
			ResponsiveSitesProAdmin.import_progress_status_text = "Pre-Checking and Starting Up Import Process";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			var date = new Date();

			// ResponsiveSitesProAdmin.import_start_time = new Date();

			$( '.sites-import-process-errors .current-importing-status-error-title' ).html( '' );

			$( '.sites-import-process-errors' ).hide();
			$( '.responsive-ready-site-import-pro' ).addClass( 'updating-message installing' )
				.text( "Importing.." );
			$( '.responsive-ready-site-import-pro' ).addClass( 'disabled not-click-able' );

			var output = '<div class="current-importing-status-title"></div><div class="current-importing-status-description"></div>';
			$( '.current-importing-status' ).html( output );

			$( document ).trigger( 'responsive-get-active-theme-pro' );

		},

		_log_error: function( data, append ) {

			$( '.sites-import-process-errors' ).css( 'display', 'block' );
			$( '.ready-sites-import-progress-info' ).css( 'display', 'none' );
			$( '.ready-sites-import-progress-bar-wrap' ).css( 'display', 'none' );
			var markup = '<p>' + data + '</p>';
			if (typeof data == 'object' ) {
				var markup = '<p>' + JSON.stringify( data ) + '</p>';
			}

			if ( append ) {
				$( '.current-importing-status-error-title' ).append( markup );
			} else {
				$( '.current-importing-status-error-title' ).html( markup );
			}

			$( '.responsive-ready-site-import-pro' ).removeClass( 'updating-message installing' )
				.text( "Import Site" );
			$( '.responsive-ready-site-import-pro' ).removeClass( 'disabled not-click-able' );
			$( '.responsive-ready-sites-tooltip-icon' ).removeClass( 'processed-import' );
			$( '.responsive-ready-sites-tooltip-icon' ).removeClass( 'processing-import' );
			$( '.responsive-ready-sites-import-process-wrap' ).hide();
		},

		/**
		 * Import Process Starts
		 *
		 * @since 1.0.0
		 * @method _process_import
		 */
		_process_import: function() {

			ResponsiveSitesProAdmin.import_progress_status_text = "Gathering pervious imported data...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			var site_id = $( '.responsive-ready-sites-advanced-options-wrap' ).find( '.demo_site_id' ).val();

			var apiURL = responsiveSitesProAdmin.ApiURL + 'cyberchimps-sites/' + site_id;

			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-set-reset-data',
						_ajax_nonce : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.done(
					function ( response ) {
						if ( response.success ) {
							ResponsiveSitesProAdmin.import_progress_percent += 2;
							ResponsiveSitesProAdmin.site_imported_data = response.data;
						}
					}
				);

			if ( apiURL ) {
				ResponsiveSitesProAdmin._importSite( apiURL );
			}

		},

		/**
		 * Start Import Process by API URL.
		 *
		 * @param  {string} apiURL Site API URL.
		 */
		_importSite: function( apiURL ) {
			ResponsiveSitesProAdmin.import_progress_percent += 5;
			ResponsiveSitesProAdmin.import_progress_status_text = "Processing Import...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			// Request Site Import.
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						'action'  : 'responsive-ready-sites-import-set-site-data-pro',
						'api_url' : apiURL,
						'_ajax_nonce' : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( demo_data ) {

						// Check is site imported recently and set flag.

						// 1. Fail - Request Site Import.
						if ( false === demo_data.success ) {
							ResponsiveSitesProAdmin._log_error( demo_data.data, true );
						} else {

							ResponsiveSitesProAdmin.xml_path                       = encodeURI( demo_data.data['xml_path'] ) || '';
							ResponsiveSitesProAdmin.wpforms_path                   = encodeURI( demo_data.data['wpforms_path'] ) || '';
							ResponsiveSitesProAdmin.active_site_slug               = demo_data.data['slug'] || '';
							ResponsiveSitesProAdmin.active_site_title              = demo_data.data['title'];
							ResponsiveSitesProAdmin.active_site_featured_image_url = demo_data.data['featured_image_url'];
							ResponsiveSitesProAdmin.site_customizer_data           = JSON.stringify( demo_data.data['site_customizer_data'] ) || '';
							ResponsiveSitesProAdmin.required_plugins               = JSON.stringify( demo_data.data['required_plugins'] ) || '';
							ResponsiveSitesProAdmin.required_pro_plugins           = JSON.stringify( demo_data.data['required_pro_plugins'] || '' );
							ResponsiveSitesProAdmin.widgets_data                   = JSON.stringify( demo_data.data['site_widgets_data'] ) || '';
							ResponsiveSitesProAdmin.site_options_data              = JSON.stringify( demo_data.data['site_options_data'] ) || '';

							$( document ).trigger( 'responsive-ready-sites-pro-import-set-site-data-done' );
						}
					}
				);
		},

		_installRequiredPlugins: function( event ){

			ResponsiveSitesProAdmin.import_progress_status_text = "Gathering Required Plugins...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			var requiredPlugins    = JSON.parse( ResponsiveSitesProAdmin.required_plugins );
			var requiredProPlugins = JSON.parse( ResponsiveSitesProAdmin.required_pro_plugins );

			if ( $.isArray( requiredPlugins ) ) {

				// Required Required.
				$.ajax(
					{
						url  : responsiveSitesProAdmin.ajaxurl,
						type : 'POST',
						data : {
							action           : 'responsive-ready-sites-required-plugins-pro',
							_ajax_nonce      : responsiveSitesProAdmin._ajax_nonce,
							required_plugins : requiredPlugins,
							required_pro_plugins : requiredProPlugins,
						},
					}
				)
					.done(
						function ( response ) {
							var required_plugins = response.data['required_plugins'] || '';
							responsiveSitesProAdmin.import_progress_percent +=2;
							responsiveSitesProAdmin.required_plugins = required_plugins;
							ResponsiveSitesProAdmin._bulkPluginInstallActivate();
						}
					);

			} else {
				if ( ResponsiveSitesProAdmin.processing_single_template ) {
					$( document ).trigger( 'responsive-ready-page-pro-install-and-activate-required-plugins-done' );
				} else {
					$( document ).trigger( 'responsive-ready-sites-pro-install-and-activate-required-plugins-done' );
				}
			}
		},

		_resetData: function( event ) {
			event.preventDefault();

			if( ResponsiveSitesProAdmin._is_reset_data() ) {
				ResponsiveSitesProAdmin.import_progress_percent += 2;
				ResponsiveSitesProAdmin.import_progress_status_text = "Resetting Site";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				$( document ).trigger( 'responsive-ready-sites-pro-reset-data' );
			} else {
				ResponsiveSitesProAdmin.import_progress_percent = ResponsiveSitesProAdmin.import_progress_percent < 25 ? 25 : ResponsiveSitesProAdmin.import_progress_percent;
				$( document ).trigger( 'responsive-ready-sites-pro-reset-data-done' );
			}
		},

		/**
		 *
		 * Check if delete previous data checkbox is checked
		 */
		_is_reset_data: function() {
			if ( $( '.responsive-ready-sites-reset-data' ).find('.checkbox').is(':checked') ) {
				return true;
			}
			return false;
		},

		ucwords: function( str ) {
			if ( ! str ) {
				return '';
			}

			str = str.toLowerCase().replace(
				/\b[a-z]/g,
				function(letter) {
					return letter.toUpperCase();
				}
			);

			str = str.replace(
				/-/g,
				function(letter) {
					return ' ';
				}
			);

			return str;
		},

		/**
		 * Install Success
		 */
		_installSuccess: function( event, response ) {

			if ( typeof responsiveSitesProAdmin.required_plugins.notinstalled !== 'undefined' && responsiveSitesProAdmin.required_plugins.notinstalled ) {
				event.preventDefault();

				// Reset not installed plugins list.
				var pluginsList                                       = responsiveSitesProAdmin.required_plugins.notinstalled;
				responsiveSitesProAdmin.required_plugins.notinstalled = ResponsiveSitesProAdmin._removePluginFromQueue( response.slug, pluginsList );

				var $plugin_name = $( '.plugin-card-' + response.slug ).data( 'name' );
				ResponsiveSitesProAdmin.import_progress_percent += 2;
				ResponsiveSitesProAdmin.import_progress_status_text = "Installed "+ $plugin_name + " Plugin...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
				setTimeout(
					function () {

						var $init = $( '.plugin-card-' + response.slug ).data( 'init' );

						$.ajax(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									'action': 'responsive-ready-sites-required-plugin-activate',
									'init': $init,
									'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
								},
							}
						)
							.done(
								function (result) {

									if (result.success) {
										ResponsiveSitesProAdmin.import_progress_percent += 1;
										ResponsiveSitesProAdmin.import_progress_status_text = "Activated "+ $plugin_name + " Plugin";
										ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
										var pluginsList = responsiveSitesProAdmin.required_plugins.inactive;

										// Reset not installed plugins list.
										responsiveSitesProAdmin.required_plugins.inactive = ResponsiveSitesProAdmin._removePluginFromQueue( response.slug, pluginsList );

										$( '.responsive-ready-sites-import-plugins .responsive-ready-sites-tooltip-icon' ).addClass( 'processed-import' );
										if ( ResponsiveSitesProAdmin.processing_single_template ) {
											ResponsiveSitesProAdmin._ready_for_import_template();
										} else {
											ResponsiveSitesProAdmin._ready_for_import_site();
										}
									}
								}
							);

					},
					1200
				);
			}

		},

		_backup_before_rest_options: function() {
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			ResponsiveSitesProAdmin.import_progress_status_text = "Taking settings backup...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			ResponsiveSitesProAdmin._backupOptions( 'responsive-ready-sites-pro-backup-settings-before-reset-done' );
			ResponsiveSitesProAdmin.backup_taken = true;
		},

		_recheck_backup_options: function() {
			ResponsiveSitesProAdmin._backupOptions( 'responsive-ready-sites-backup-settings-done' );
			ResponsiveSitesProAdmin.backup_taken = true;
		},

		_backupOptions: function( trigger_name ) {
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-backup-settings',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {

						// Custom trigger.
						$( document ).trigger( trigger_name );
					}
				);
		},

		/**
		 * Installing Plugin
		 */
		_pluginInstalling: function(event, args) {
			event.preventDefault();
		},

		_reset_customizer_data: function() {
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			ResponsiveSitesProAdmin.import_progress_status_text = "Resetting customizer...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-customizer-data',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-pro-reset-customizer-data-done' );
					}
				);
		},

		_reset_site_options: function() {
			// Site Options.
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			ResponsiveSitesProAdmin.import_progress_status_text = "Resetting site options...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-site-options',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-pro-reset-site-options-done' );
					}
				);
		},

		_reset_widgets_data: function() {
			// Widgets.
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			ResponsiveSitesProAdmin.import_progress_status_text = "Resetting widgets...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-widgets-data',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-pro-reset-widgets-data-done' );
					}
				);
		},

		/**
		 * Reset Posts
		 */
			_reset_posts: function() {
				ResponsiveSitesProAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_posts'].length ) {
				ResponsiveSitesProAdmin.import_progress_status_text = "Gathering posts for deletion...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				//chunkArray contains all the post IDs
				const chunkArray = ResponsiveSitesProAdmin.site_imported_data['reset_posts'];
				resetPostChunks(chunkArray);
			} else {
				$( document ).trigger( 'responsive-ready-sites-pro-delete-posts-done' );
				$( document ).trigger( 'responsive-ready-sites-pro-reset-data-done' );
			}
		},

		_reset_wp_forms: function() {
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length ) {
				ResponsiveSitesProAdmin.import_progress_status_text = "Resetting forms...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				ResponsiveSitesProAdmin.reset_remaining_wp_forms = ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length;

				$.each(
					ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'],
					function(index, post_id) {
						ResponsiveSitesProAjaxQueue.add(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-wp-forms',
									post_id : post_id,
									_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
								},
								success: function( result ){

									if ( ResponsiveSitesProAdmin.reset_processed_wp_forms < ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length ) {
										ResponsiveSitesProAdmin.reset_processed_wp_forms += 1;
									}

									ResponsiveSitesProAdmin.reset_remaining_wp_forms -= 1;
									if ( 0 == ResponsiveSitesProAdmin.reset_remaining_wp_forms ) {
										$( document ).trigger( 'responsive-ready-sites-pro-delete-wp-forms-done' );
									}
								}
							}
						);
					}
				);
				ResponsiveSitesProAjaxQueue.run();

			} else {
				$( document ).trigger( 'responsive-ready-sites-pro-delete-wp-forms-done' );
			}

		},

		_reset_terms: function() {
			ResponsiveSitesProAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length ) {
				ResponsiveSitesProAdmin.import_progress_status_text = "Resetting terms...";
				ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
				ResponsiveSitesProAdmin.reset_remaining_terms = ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length;

				$.each(
					ResponsiveSitesProAdmin.site_imported_data['reset_terms'],
					function(index, term_id) {
						ResponsiveSitesProAjaxQueue.add(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-terms',
									term_id : term_id,
									_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
								},
								success: function( result ){
									if ( ResponsiveSitesProAdmin.reset_processed_terms < ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length ) {
										ResponsiveSitesProAdmin.reset_processed_terms += 1;
									}

									ResponsiveSitesProAdmin.reset_remaining_terms -= 1;
									if ( 0 == ResponsiveSitesProAdmin.reset_remaining_terms ) {
										$( document ).trigger( 'responsive-ready-sites-pro-delete-terms-done' );
									}
								}
							}
						);
					}
				);
				ResponsiveSitesProAjaxQueue.run();

			} else {
				$( document ).trigger( 'responsive-ready-sites-pro-delete-terms-done' );
			}
		},

		_reset_everything: function () {

			// reset customizer data.
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-customizer-data',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						// display message on fail.
					}
				)
				.done(
					function ( data ) {
						// reverted customizer data.
					}
				);

			// reset options data.
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-site-options',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						// display message on fail.
					}
				)
				.done(
					function ( data ) {
						// options are reverted.
					}
				);

			// Widgets.
			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-widgets-data',
						_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						// display message on fail.
					}
				)
				.done(
					function ( data ) {
						// widgets data is reverted.
					}
				);

			// delete posts.
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_posts'].length ) {

				ResponsiveSitesProAdmin.reset_remaining_posts = ResponsiveSitesProAdmin.site_imported_data['reset_posts'].length;

				$.each(
					ResponsiveSitesProAdmin.site_imported_data['reset_posts'],
					function(index, post_id) {

						ResponsiveSitesProAjaxQueue.add(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-posts',
									post_id : post_id,
									_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
								},
								success: function( result ){

									if ( ResponsiveSitesProAdmin.reset_processed_posts < ResponsiveSitesProAdmin.site_imported_data['reset_posts'].length ) {
										ResponsiveSitesProAdmin.reset_processed_posts += 1;
									}

									ResponsiveSitesProAdmin.reset_remaining_posts -= 1;
								}
							}
						);
					}
				);
				ResponsiveSitesProAjaxQueue.run();

			}

			// delete wp-forms.
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length ) {
				ResponsiveSitesProAdmin.reset_remaining_wp_forms = ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length;

				$.each(
					ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'],
					function(index, post_id) {
						ResponsiveSitesProAjaxQueue.add(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-wp-forms',
									post_id : post_id,
									_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
								},
								success: function( result ){

									if ( ResponsiveSitesProAdmin.reset_processed_wp_forms < ResponsiveSitesProAdmin.site_imported_data['reset_wp_forms'].length ) {
										ResponsiveSitesProAdmin.reset_processed_wp_forms += 1;
									}

									ResponsiveSitesProAdmin.reset_remaining_wp_forms -= 1;
								}
							}
						);
					}
				);
				ResponsiveSitesProAjaxQueue.run();

			}

			// delete terms.
			if ( ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length ) {
				ResponsiveSitesProAdmin.reset_remaining_terms = ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length;

				$.each(
					ResponsiveSitesProAdmin.site_imported_data['reset_terms'],
					function(index, term_id) {
						ResponsiveSitesProAjaxQueue.add(
							{
								url: responsiveSitesProAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-terms',
									term_id : term_id,
									_ajax_nonce: responsiveSitesProAdmin._ajax_nonce,
								},
								success: function( result ){
									if ( ResponsiveSitesProAdmin.reset_processed_terms < ResponsiveSitesProAdmin.site_imported_data['reset_terms'].length ) {
										ResponsiveSitesProAdmin.reset_processed_terms += 1;
									}

									ResponsiveSitesProAdmin.reset_remaining_terms -= 1;
								}
							}
						);
					}
				);
				ResponsiveSitesProAjaxQueue.run();

			}
		},

		// check if Responsive theme or child theme of Responsive is active.
		_is_responsive_theme_active: function() {
			ResponsiveSitesProAdmin.import_progress_percent += 5;
			ResponsiveSitesProAdmin.import_progress_status_text = "Checking Responsive Theme Install Status";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			$.ajax(
				{
					url: responsiveSitesProAdmin.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-is-theme-active',
						'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.done(
					function (result) {
						if (result.success) {
							$( document ).trigger( 'responsive-ready-sites-pro-install-start' );
						} else {
							ResponsiveSitesProAdmin._log_error( 'Please make sure you have Responsive Theme active.', true );
						}
					}
				);
		},

		/**
		 * Import Single Page options Screen
		 */
		_importSinglePageOptions: function(event) {
			event.preventDefault();

			var self = $( this ).parents( '.responsive-ready-sites-advanced-options-wrap' );

			var demo_api     = self.data( 'demo-api' ) || '',
				wpforms_path = self.data( 'wpforms-path' ) || '',
				pages        = $('.responsive-ready-site-preview').data( 'pages' ) || '',
				demo_type 	 = self.data( 'demo-type' ) || '';


			var page_id = ResponsiveSitesProAdmin._get_id( $( '#single-pages' ).find( '.current_page' ).attr( 'data-page-id' ) ) || '';

			var required_plugins = JSON.parse( $( '#single-pages' ).find( '.current_page' ).attr( 'data-required-plugins' ) ) || '';

			var required_pro_plugins = JSON.parse( $( '#single-pages' ).find( '.current_page' ).attr( 'data-required-pro-plugins' ) ) || '';

			var includes_wp_forms = JSON.parse( $( '#single-pages' ).find( '.current_page' ).attr( 'data-includes-wp-forms' ) ) || false;

			let pageMap = {};
			pages.forEach(function(page) {
				pageMap[page.page_id] = page.page_title;
			});

			// Function to get page_title based on page_id
			function getPageTitleById(pageId) {
				return pageMap[pageId];
			}

			let pageTitle = getPageTitleById(page_id);

			var rbeaPlugin = {
				"name": "Responsive Block Editor Addons",
				"slug": "responsive-block-editor-addons",
				"init": "responsive-block-editor-addons/responsive-block-editor-addons.php"
			};
			var isDuplicate = required_plugins.some(function(plugin) {
				return plugin.slug === rbeaPlugin.slug;
			});
			// If it's not a duplicate, add it to the array
			if (!isDuplicate) {
				required_plugins.push(rbeaPlugin);
			}

			if ( typeof required_pro_plugins !== 'undefined' ) {

				$( required_pro_plugins ).each(
					function( index, plugin ) {
						if( plugin.slug != 'responsive-elementor-addons') {
							ResponsiveSitesProAdmin.pro_plugins_flag = true;
						}
					}
				);
			}

			$( '#responsive-ready-site-pages-preview' ).hide();

			$( '#responsive-ready-sites-import-options' ).show();

			var template = wp.template( 'responsive-ready-sites-import-single-page-options-page' );

			templateData = [{
				page_id: page_id,
				page_title:  pageTitle,
				demo_api: demo_api,
				required_plugins: required_plugins,
				required_pro_plugins: required_pro_plugins,
				wpforms_path: wpforms_path,
				includes_wp_forms: includes_wp_forms,
				demo_type: demo_type,
				pro_plugins_flag: ResponsiveSitesProAdmin.pro_plugins_flag,
			}];

			$('body').removeClass('responsive-ready-site-preview-screen');
			$('body').removeClass('responsive-ready-sites-import-page-preview-page-screen');
			$('body').addClass('responsive-ready-site-import-page-options-screen');
			$( '#responsive-ready-sites-import-options' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );

			$( '.required-plugins' ).removeClass( 'loading' ).html( '' );
			$( '.required-plugins-list' ).html( '' );

			$( required_plugins ).each(
				function( index, plugin ) {
					$( '.required-plugins-list' ).append( '<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>' );
				}
			);

			$( required_pro_plugins ).each(
				function( index, plugin ) {
					if( plugin.slug === 'responsive-elementor-addons') {
						$('.required-plugins-list').append('<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + '</li>');
					} else {
						$('.required-third-party-plugins-list').append('<li class="plugin-card plugin-card-' + plugin.slug + '" data-slug="' + plugin.slug + '" data-init="' + plugin.init + '" data-name="' + plugin.name + '">' + plugin.name + ' - <span class="responsive-premium-plugin-tag">Premium</span></li>');
					}
				}
			);
		},

		/**
		 * Import single page.
		 */
		_importSinglePage: function(event) {
			event.preventDefault();
			ResponsiveSitesProAdmin.import_page_start_time = performance.now();
			ResponsiveSitesProAdmin.import_progress_percent += 25;
			ResponsiveSitesProAdmin.import_progress_status_text = "Processing Import...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			var date = new Date();

			var self = $( '.responsive-ready-sites-advanced-options-wrap.single-page-import-options-page' );

			var required_plugins     = self.data( 'required-plugins' ) || '',
				required_pro_plugins = self.data( 'required-pro-plugins' ) || '',
				includes_wp_forms    = self.data( 'includes-wp-forms' ) || false,
				wpforms_path         = self.data( 'wpforms-path' ) || '';

			ResponsiveSitesProAdmin.current_page_id      = self.data( 'page-id' ) || '';
			ResponsiveSitesProAdmin.current_page_api     = self.data( 'demo-api' ) || '';
			ResponsiveSitesProAdmin.required_plugins     = JSON.stringify( required_plugins );
			ResponsiveSitesProAdmin.required_pro_plugins = JSON.stringify( required_pro_plugins );

			if ( includes_wp_forms ) {
				ResponsiveSitesProAdmin.wpforms_path = wpforms_path;
			} else {
				ResponsiveSitesProAdmin.wpforms_path = '';
			}

			$( '.sites-import-process-errors .current-importing-status-error-title' ).html( '' );

			$( '.sites-import-process-errors' ).hide();
			$( '.responsive-ready-page-import-pro' ).addClass( 'updating-message installing' )
				.text( "Importing.." );
			$( '.responsive-ready-page-import-pro' ).addClass( 'disabled not-click-able' );

			ResponsiveSitesProAdmin.processing_single_template = true;

			$( document ).trigger( 'responsive-ready-sites-import-page-pro-start' );
		},

		/**
		 * Import Single Page Preview Screen
		 */
		_importPagePreviewScreen: function(event) {
			event.preventDefault();

			var self = $( this ).parents( '.responsive-ready-site-preview' );

			$( '#responsive-ready-site-preview' ).hide();

			$( '#responsive-ready-site-pages-preview' ).show();

			var apiURL                = self.data( 'demo-api' ) || '',
				demoType              = self.data( 'demo-type' ) || '',
				demo_name             = self.data( 'demo-name' ) || '',
				wpforms_path          = self.data( 'wpforms-path' ) || '',
				screenshot            = self.data( 'screenshot' ) || '',
				requiredPlugins       = self.data( 'required-plugins' ) || '',
				requiredProPlugins    = self.data( 'required-pro-plugins' ) || '',
				pages                 = self.data( 'pages' ) || '',
				responsiveSiteOptions = self.find( '.responsive-site-options' ).val() || '';

			var template = wp.template( 'responsive-ready-sites-import-page-preview-page' );

			templateData = [{
				demo_type: demoType,
				demo_api: apiURL,
				name: demo_name,
				wpforms_path: wpforms_path,
				required_plugins: JSON.stringify( requiredPlugins ) || '',
				required_pro_plugins: JSON.stringify( requiredProPlugins ) || '',
				responsive_site_options: responsiveSiteOptions,
				pages:  pages,
				screenshot: screenshot,
			}];
			$('body').removeClass('responsive-ready-site-preview-screen');
			$('body').addClass('responsive-ready-sites-import-page-preview-page-screen');
			$( '#responsive-ready-site-pages-preview' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );
		},

		/**
		 * Import page.
		 */
		_importPage: function() {

			ResponsiveSitesProAdmin.import_progress_percent = ResponsiveSitesProAdmin.import_progress_percent < 75 ? 74 : ResponsiveSitesProAdmin.import_progress_percent;
			ResponsiveSitesProAdmin.import_progress_status_text = "Importing Page...";
			ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);

			ResponsiveSitesProAdmin._import_wpform(
				ResponsiveSitesProAdmin.wpforms_path,
				function( form_response ) {

					page_api_url = ResponsiveSitesProAdmin.current_page_api + '/wp-json/wp/v2/pages/' + ResponsiveSitesProAdmin.current_page_id;

					fetch( page_api_url ).then(
						response => {
							return response.json();
						}
					).then(
						data => {
							// Import Single Page.
							$.ajax(
								{
									url: responsiveSitesProAdmin.ajaxurl,
									type: 'POST',
									dataType: 'json',
									data: {
										'action': 'responsive-sites-create-page',
										'_ajax_nonce': responsiveSitesProAdmin._ajax_nonce,
										'data': data,
										'current_page_api': ResponsiveSitesProAdmin.current_page_api,
									},
									success: function (response) {
										if (response.success) {
											$( '.single-site-wrap' ).hide();
											ResponsiveSitesProAdmin.import_page_end_time = performance.now();
											ResponsiveSitesProAdmin.import_progress_percent = 100;
											ResponsiveSitesProAdmin.import_progress_status_text = "Page Imported.";

											ResponsiveSitesProAdmin.import_total_time = Math.floor((ResponsiveSitesProAdmin.import_page_end_time  - ResponsiveSitesProAdmin.import_page_start_time ) / 1000); // Convert milliseconds to seconds

											ResponsiveSitesProAdmin._updateImportProcessStatusText(ResponsiveSitesProAdmin.import_progress_status_text);
											ResponsiveSitesProAdmin._importCompletionCongratsScreen(response.data['link']);

										} else {
											ResponsiveSitesProAdmin._log_error( 'Page Rest API Request Failed!', true );
										}
									}
								}
							);
						}
					).catch(
						err => {
							ResponsiveSitesProAdmin._log_error( 'Page Rest API Request Failed!', true );
						}
					);
				}
			);
		},

		/**
		 * Import WP Forms
		 */
		_import_wpform: function( wpforms_path, callback ) {

			if ( '' == wpforms_path ) {
				if ( callback && typeof callback == "function") {
					callback( '' );
				}
				return;
			}

			$.ajax(
				{
					url  : responsiveSitesProAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action      : 'responsive-ready-sites-import-wpforms',
						wpforms_path : wpforms_path,
						_ajax_nonce : responsiveSitesProAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesProAdmin._log_error( jqXHR );
						ResponsiveSitesProAdmin._log_error( jqXHR.status + jqXHR.statusText, 'Import WP Forms Failed!', jqXHR );
					}
				)
				.done(
					function ( response ) {

						// 1. Fail - Import WPForms Options.
						if ( false === response.success ) {
							ResponsiveSitesProAdmin._log_error( response.data, 'Import WP Forms Failed!' );
						} else {
							if ( callback && typeof callback == "function") {
								callback( response );
							}
						}
					}
				);
		},

		/*
		* Update Import Process Status Text
		*/
		_updateImportProcessStatusText: function (status_text) {

			let importPercent = ResponsiveSitesProAdmin.import_progress_percent;
			if(importPercent < 25){
				$('.ready-sites-import-progress-bar').addClass('import-stage-1');
			}
			else if(importPercent >= 25 && importPercent < 50){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-1');
				$('.ready-sites-import-progress-bar').addClass('import-stage-2');
			}
			else if(importPercent >= 50 && importPercent < 75){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-2');
				$('.ready-sites-import-progress-bar').addClass('import-stage-3');
			}
			else if(importPercent >= 75){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-3');
				$('.ready-sites-import-progress-bar').addClass('import-stage-4 import-done');
			}
			$('.ready-sites-import-progress-info-text').text(status_text);
			$('.ready-sites-import-progress-info-percent').text(ResponsiveSitesProAdmin.import_progress_percent+"%");
		},

		_importCompletionCongratsConfetti: function() {

			var container = document.getElementById('wpwrap');
			var myCanvas = document.createElement('canvas');
			myCanvas.id = 'responsive-sites-canvas'; // Set the ID for the canvas element
			container.appendChild(myCanvas);

			var myConfetti = confetti.create(
				myCanvas,
				{ resize: true }
			);
			setTimeout(function() {
				myConfetti({
					particleCount: 250,
					origin: { x: 1, y: 1.4 },
					gravity: 0.4,
					spread: 80,
					ticks: 300,
					angle: 120,
					startVelocity: 100,
					colors: [
						'#0e6ef1',
						'#f5b800',
						'#ff344c',
						'#98e027',
						'#9900f1',
					],
				});
			}, 100);
			setTimeout(function() {
				myConfetti({
					particleCount: 250,
					origin: { x: 0, y: 1.4 },
					gravity: 0.4,
					spread: 80,
					ticks: 300,
					angle: 60,
					startVelocity: 100,
					colors: [
						'#0e6ef1',
						'#f5b800',
						'#ff344c',
						'#98e027',
						'#9900f1',
					],
				});
			}, 100);
		},

		_importCompletionCongratsScreen: function(importedSiteURL) {

			$('#responsive-ready-sites-page-import-progress').hide();
			$('#responsive-ready-sites-import-progress').hide();

			$('#responsive-ready-sites-import-done-congrats').show();

			let template = wp.template( 'responsive-ready-sites-import-done-congrats-page' );

			$( '#responsive-ready-sites-import-done-congrats' ).append( template( templateData[0] ) );

			$( '.responsive-ready-sites-import-time-taken' ).text(ResponsiveSitesProAdmin.import_total_time);

			$('#responsive-sites-imported-site-link').attr( "href", importedSiteURL );

			let tweetMsg = $('.responsive-sites-tweet-text').text();
			$('#responsive-sites-twitter-tweet-link').attr( "href", "https://twitter.com/intent/tweet?text="+tweetMsg );

			ResponsiveSitesProAdmin._importCompletionCongratsConfetti();

		},

		/**
		 * Get Page id from attribute
		 */
		_get_id: function( site_id ) {
			return site_id.replace( 'id-', '' );
		},
	};

	/**
	 * Initialize ResponsiveSitesProAdmin
	 */
	$(
		function(){
			ResponsiveSitesProAdmin.init();
		}
	);

})( jQuery );
