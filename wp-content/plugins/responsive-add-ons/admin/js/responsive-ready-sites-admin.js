/**
 * Responsive Ready Sites importer events
 *
 * @package Responsive Ready Sites
 */

/**
 * AJAX Request Queue
 *
 * - add()
 * - remove()
 * - run()
 * - stop()
 */
var ResponsiveSitesAjaxQueue = (function() {

	var requests = [];

	return {

		/**
		 * Add AJAX request
		 */
		add:  function(opt) {
			requests.push( opt );
		},

		/**
		 * Remove AJAX request
		 */
		remove:  function(opt) {
			if ( jQuery.inArray( opt, requests ) > -1 ) {
				requests.splice( $.inArray( opt, requests ), 1 );
			}
		},

		/**
		 * Run / Process AJAX request
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
		 */
		stop:  function() {

			requests = [];
			clearTimeout( this.tid );
		}
	};

}());

/**
* Reset Post Chunks
*/
(function( $ ) {

	const resetPostChunks = (chunk) => {
		ResponsiveSitesAdmin.import_progress_status_text = "Resetting posts...";
		ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
		$.ajax(
			{
				url  : responsiveSitesAdmin.ajaxurl,
				type : 'POST',
				data : {
					action : 'responsive-ready-sites-delete-posts',
					ids: chunk,
					_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
				},
			}
		)
			.fail(
				function( jqXHR ){
					ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
				}
			)
			.done(
				function ( message ) {
					ResponsiveSitesAdmin.import_progress_status_text = "Resetting posts done...";
					ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
					$( document ).trigger( 'responsive-ready-sites-reset-data-done' );

				}
			);
	};
	/**
	 * WXR Import
	 *
	 * - updateDelta()
	 * - updateProgress()
	 * - render()
	 */
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

	/**
	 * Responsive Sites Admin
	 *
	 * - init()
	 * - _show_default_page_builder_sites()
	 * - _bind()
	 * - _resetPagedCount()
	 * - _doNothing()
	 * - _toggle_tooltip()
	 * - _areEqual()
	 * - _closeFullOverlay()
	 * - _importSiteOptionsScreen()
	 * - _importSiteProgressScreen()
	 * - _importDemo()
	 * - _is_responsive_theme_active()
	 * - _log_error()
	 * - _checkResponsiveAddonsProInstalled()
	 * - _preview()
	 * - _renderDemoPreview()
	 * - _process_import()
	 * - _importSite()
	 * - _installRequiredPlugins()
	 * - _removePluginFromQueue()
	 * - _bulkPluginInstallActivate()
	 * - _installAllPlugins()
	 * - _pluginInstalling()
	 * - _pluginInstallSuccess()
	 * - _activateAllPlugins()
	 * - _ready_for_import_site()
	 * - _ready_for_import_template()
	 * - _resetData()
	 * - _is_reset_data()
	 * - _backup_before_reset_options()
	 * - _backupOptions()
	 * - _reset_customizer_data()
	 * - _reset_site_options()
	 * - _reset_widgets_data()
	 * - _reset_terms()
	 * - _reset_wp_forms()
	 * - _reset_posts()
	 * - _importWPForms()
	 * - _importXML()
	 * - _importCustomizerSettings()
	 * - _importWidgets()
	 * - _importCustomizerSettings()
	 * - _importSiteOptions()
	 * - _importSiteEnd()
	 * - _importPagePreviewScreen()
	 * - _change_site_preview_screenshot()
	 * - _set_preview_screenshot_by_page()
	 * - _importSinglePageOptions()
	 * - _importSinglePage()
	 * - _get_id()
	 * - _import_wpform()
	 * - _importPage()
	 * - ucwords()
	 * - _sync_templates_library_with_ajax()
	 */
	ResponsiveSitesAdmin = {

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

		filter_array: [],
		autocompleteTags: [],

		templateData: {},

		site_customizer_data: '',

		required_plugins: '',

		xml_path         : '',
		wpforms_path	: '',
		import_start_time  : '',
		import_end_time    : '',
		import_page_start_time  : '',
		import_page_end_time    : '',
		import_total_time : '',

		current_page_id : '',
		processing_single_template: false,
		pro_plugins_flag: false,
		hasFlexboxContainer: false,

		mouseLocation : false,
		importFlag    : false,
		mouseLocation_SiteType : false,
		mouseLocation_PgBuilderIcon : false,
		conditionCheckVariable : false,
		import_progress_status_text : '',
		import_progress_percent: 0,

		init: function()
		{
			this._show_default_page_builder_sites();
			this._resetPagedCount();
			this._bind();
			this._addAutocomplete();
			this._autocomplete();
		},

		_show_default_page_builder_sites: async function() {

			if (Object.keys(responsiveSitesAdmin.default_page_builder_sites).length) {
				var template = wp.template('responsive-sites-list');
				var data = null;
		
				try {
					const updated_sites = await ResponsiveSitesAdmin.__updateFavoriteSites();
					data = updated_sites.data;
					data = ResponsiveSitesAdmin._filter_sites_by_page_builder(data);
					ResponsiveSitesAdmin.add_sites(data);
				} catch (error) {
					console.log(error);
				}
			} else {

				var temp = [];
				for (var i = 0; i < 8; i++) {
					temp['id-' + i] = {
						'title' : 'Lorem Ipsum',
						'class' : 'placeholder-site',
						'slug' 	: 'placeholder-site'
					};
				}

				ResponsiveSitesAdmin.add_sites(temp);
				$('#respnonsive-sites').addClass('temp');

				ResponsiveSitesAdmin._sync_templates_library_with_ajax( true );
			}
		},


		/**
		 * Binds events for the Responsive Ready Sites.
		 */
		_bind: function()
		{

			$( '.responsive-sites__category-filter-anchor, .responsive-sites__category-filter-items' ).hover(function(){
				ResponsiveSitesAdmin.mouseLocation = true;
			}, function(){
				ResponsiveSitesAdmin.mouseLocation = false;
			});

			$( '.responsive-sites__type-filter-anchor, .responsive-sites__type-filter-items' ).hover
			(function(){
				ResponsiveSitesAdmin.mouseLocation_SiteType = true;
			}, function(){
				ResponsiveSitesAdmin.mouseLocation_SiteType = false;
			});

			$( '.selected-page-builder, .page-builders' ).hover
			(function(){
				ResponsiveSitesAdmin.mouseLocation_PgBuilderIcon = true;
			}, function(){
				ResponsiveSitesAdmin.mouseLocation_PgBuilderIcon = false;
			});

			$( "body" ).mouseup(function(){
				if( ! ResponsiveSitesAdmin.mouseLocation ) ResponsiveSitesAdmin._closeFilter();
			});

			$( "body" ).mouseup(function(){
				if( ! ResponsiveSitesAdmin.mouseLocation_SiteType ) ResponsiveSitesAdmin._closeSiteTypeFilter();
			});

			$( "body" ).mouseup(function(){
				if( ! ResponsiveSitesAdmin.mouseLocation_PgBuilderIcon ) ResponsiveSitesAdmin._closeBuilderTypeFilter();
			});

			// Site Import events.
			$( document ).on( 'click'                     , '.import-demo-data, .responsive-ready-site-import-free, .responsive-addons-ready-site-import', ResponsiveSitesAdmin._allProcessRun );
			$( document ).on( 'click'                     , '.theme-browser .inactive.ra-site-single .theme-screenshot, .theme-browser .inactive.ra-site-single .more-details, .theme-browser .inactive.ra-site-single .install-theme-preview', ResponsiveSitesAdmin._preview );
			$( document ).on( 'click'                     , '.theme-browser .active.ra-site-single .theme-screenshot, .theme-browser .active.ra-site-single .more-details, .theme-browser .active.ra-site-single .install-theme-preview', ResponsiveSitesAdmin._doNothing );
			$( document ).on( 'click'                     , '.responsive-addons-go-back-btn', ResponsiveSitesAdmin._closeFullOverlay );
			$( document ).on( 'click', '.responsive-demo-import-options-free, .responsive-addons-demo-import-options', ResponsiveSitesAdmin._importSiteOptionsScreen );
			$( document ).on( 'click', '.responsive-ready-site-import-with-sub, .responsive-ready-site-import-without-sub', ResponsiveSitesAdmin._importSiteProgressScreen );
			$( document ).on( 'responsive-get-active-theme' , ResponsiveSitesAdmin._is_responsive_theme_active );
			$( document ).on( 'responsive-theme-install-activate' , ResponsiveSitesAdmin._getResponsiveTheme );
			$( document ).on( 'responsive-ready-sites-install-start'       , ResponsiveSitesAdmin._process_import );
			$( document ).on( 'responsive-ready-sites-import-set-site-data-done'   		, ResponsiveSitesAdmin._installRequiredPlugins );
			$( document ).on( 'responsive-ready-sites-install-and-activate-required-plugins-done', ResponsiveSitesAdmin._resetData );
			$( document ).on( 'responsive-ready-sites-reset-data'							, ResponsiveSitesAdmin._backup_before_reset_options );
			$( document ).on( 'responsive-ready-sites-backup-settings-before-reset-done'	, ResponsiveSitesAdmin._reset_customizer_data );
			$( document ).on( 'responsive-ready-sites-reset-customizer-data-done'			, ResponsiveSitesAdmin._reset_site_options );
			$( document ).on( 'responsive-ready-sites-reset-site-options-done'				, ResponsiveSitesAdmin._reset_widgets_data );
			$( document ).on( 'responsive-ready-sites-reset-widgets-data-done'				, ResponsiveSitesAdmin._reset_terms );
			$( document ).on( 'responsive-ready-sites-delete-terms-done'					, ResponsiveSitesAdmin._reset_wp_forms );
			$( document ).on( 'responsive-ready-sites-delete-wp-forms-done'				, ResponsiveSitesAdmin._reset_posts );
			$( document ).on( 'responsive-ready-sites-reset-data-done' , ResponsiveSitesAdmin._importWPForms );
			$( document ).on( 'responsive-ready-sites-import-wpforms-done' , ResponsiveSitesAdmin._importXML );
			$( document ).on( 'responsive-ready-sites-import-xml-done' , ResponsiveSitesAdmin._importCustomizerSettings );
			$( document ).on( 'responsive-ready-sites-import-customizer-settings-done' , ResponsiveSitesAdmin._importWidgets );
			$( document ).on( 'responsive-ready-sites-import-widgets-done' , ResponsiveSitesAdmin._importSiteOptions );
			$( document ).on( 'responsive-ready-sites-import-options-done' , ResponsiveSitesAdmin._importSiteEnd );
			
			// Single Page Import events.
			$( document ).on( 'click', '.responsive-ready-site-import-with-sub.import-page, .responsive-ready-site-import-without-sub.import-page', ResponsiveSitesAdmin._importPageProgressScreen );
			$( document ).on( 'click'                     , '.single-page-import-button-free, .single-page-import-button', ResponsiveSitesAdmin._importSinglePageOptions );
			$( document ).on( 'click'                     , '.responsive-ready-page-import-free, .responsive-ready-page-import', ResponsiveSitesAdmin._importSinglePage );
			$( document ).on( 'click', '.responsive-page-import-options-free, .responsive-addons-page-import-options', ResponsiveSitesAdmin._importPagePreviewScreen );
			$( document ).on( 'click'                     , '#single-pages .site-single', ResponsiveSitesAdmin._change_site_preview_screenshot );
			$( document ).on( 'responsive-ready-page-install-and-activate-required-plugins-done' , ResponsiveSitesAdmin._importPage );
			$( document ).on( 'responsive-ready-sites-import-page-free-start'   		, ResponsiveSitesAdmin._installRequiredPlugins );

			// Wordpress Plugin install events.
			$( document ).on( 'wp-plugin-installing'      , ResponsiveSitesAdmin._pluginInstalling );
			$( document ).on( 'wp-plugin-install-success' , ResponsiveSitesAdmin._pluginInstallSuccess );

			//Improved layout
			$( document ).on( 'click', '.responsive-sites__category-filter-anchor', ResponsiveSitesAdmin._toggleCategoryFilter );
			$( document ).on( 'click', '.responsive-sites__type-filter-anchor', ResponsiveSitesAdmin._toggleSiteTypeFilter );
			$( document ).on('click', '.page-builder-icon', ResponsiveSitesAdmin._toggle_page_builder_list );
			$( document ).on( 'click', '.responsive-sites__filter-wrap-checkbox label, .rst-menu-parent-category, .rst-menu-child-category', ResponsiveSitesAdmin._filterClick );
			$( document ).on('keyup input'                     , '#wp-filter-search-input', ResponsiveSitesAdmin._search );
			$( document ).on( 'click'                    , '.nav-tab-wrapper .page-builders li', ResponsiveSitesAdmin._change_page_builder );
			$( document ).on('click'                     , '.ui-autocomplete .ui-menu-item', ResponsiveSitesAdmin._show_search_term );
			$( document ).on('click', '.responsive-ready-sites-sync-templates-button', ResponsiveSitesAdmin._sync_library);

			$( document ).on('click', '#install_responsive_checkbox', ResponsiveSitesAdmin._displayNoticeBarUnchecked);
			$( document ).on('click', '#rst-admin-overlay', ResponsiveSitesAdmin._displayAdminOverlayPopup);
			$( document ).on('click', '.responsive-ready-sites-sync-templates-library-message .notice-dismiss', ResponsiveSitesAdmin._removeTemplateRefreshMessage);
			$( document ).on('click', '#rst-favorite-btn', ResponsiveSitesAdmin._addremoveFavoriteTemplate);
			$( document ).on('click', '#rst-my-favorite-btn', ResponsiveSitesAdmin._displayFavoriteTemplates);

			$( document ).on('click', '.responsive-demo-import-options-no-auth, .responsive-page-import-options-no-auth', ResponsiveSitesAdmin._displayAppConnectModal);
			$( document ).on('click', '.responsive-demo-import-options-no-auth-unlock-access, .responsive-page-import-options-no-auth-unlock-access', ResponsiveSitesAdmin._displayUnlockTemplatesModal);
			$( document ).on('click', '#responsive-addons-app-modal-close', ResponsiveSitesAdmin._closeAppConnectModal);
			$( document ).on('click', '#responsive-addons-app-unlock-template-modal-close', ResponsiveSitesAdmin._closeUnlockTemplatesModal);

			// Add user subscription to Moosend
			$( document ).on( 'click' , '.responsive-ready-site-import-with-sub' , ResponsiveSitesAdmin._addUserToSubscriptionList );
				
			$(window).on('beforeunload', function() {
				if(ResponsiveSitesAdmin.import_progress_percent > 0 && ResponsiveSitesAdmin.import_progress_percent < 100) {
						return "Are you sure you want to cancel the site import process?";
				}
			});
			$(document).on( 'click' , '.auth-success-msg .notice-dismiss', function() {
				$( '.responsive-templates-app-auth-sucess-msg' ).remove();
			});
			$(document).on( 'click' , '.plan-upgraded-success-msg .notice-dismiss', function() {
				$( '.responsive-templates-app-plan-upgraded-msg' ).remove();
			});
		},

		/**
		 * Run Theme and Other Processes in Parallel.
		 */
		_allProcessRun: function() {
			ResponsiveSitesAdmin.import_start_time = performance.now();
			ResponsiveSitesAdmin.import_progress_status_text = "Pre-Checking and Starting Up Import Process";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			ResponsiveSitesAdmin.import_progress_percent += 2;
			if(responsiveSitesAdmin.isResponsiveProActive) {
				ResponsiveSitesAdmin._process_import();
				ResponsiveSitesAdmin._isInstallResponsiveThemeChecked();
			} else {
				let importPromise = new Promise((resolve, reject) => {
					ResponsiveSitesAdmin._checkImportCapabilities(function(result) {
						console.log( result );
						resolve(result);
					});
				});
				
				importPromise.then((importCaps) => {
					if (importCaps) {
						ResponsiveSitesAdmin._process_import();
						ResponsiveSitesAdmin._isInstallResponsiveThemeChecked();
					}
				});
			}
		},

		/**
		 * Reset Page Count.
		 */
		_resetPagedCount: function() {

			$( 'body' ).addClass( 'loading-content' );
			$( 'body' ).attr( 'data-responsive-demo-last-request', '1' );
			$( 'body' ).attr( 'data-responsive-demo-paged', '1' );
			$( 'body' ).attr( 'data-scrolling', false );

		},

		/**
		 * Do Nothing.
		 */
		_doNothing: function( event ) {
			event.preventDefault();
		},

		/**
		 * toggle tooltip
		 */
		_toggle_tooltip: function( event ) {
			event.preventDefault();
			var tip_id = $( this ).data( 'tip-id' ) || '';
			if ( tip_id && $( '#' + tip_id ).length ) {
				$( '#' + tip_id ).toggle();
				$('.' + tip_id + ' .dashicons').toggleClass('active');
			}
		},

		/**
		 * Check if arrays are equal
		 */
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
		 * Close Full Overlay
		 */
		_closeFullOverlay: function (event) {
			event.preventDefault();

			if( $('body').hasClass('responsive-ready-sites-import-page-preview-page-screen') ) {
				$('body').removeClass('responsive-ready-sites-import-page-preview-page-screen');
				$('#responsive-ready-site-pages-preview').empty().hide();
				$( '#responsive-ready-site-preview' ).show();
				$('body').addClass('responsive-ready-site-preview-screen');
			} else if ( $('body').hasClass('responsive-ready-site-preview-screen') ) {
				$('body').removeClass('responsive-ready-site-preview-screen')
				$( '#responsive-ready-site-preview' ).hide();
				$( '#responsive-sites' ).show();
			} else if ( $('body').hasClass('responsive-ready-site-import-options-screen') ) {
				$('body').removeClass('responsive-ready-site-import-options-screen')
				$( '#responsive-ready-sites-import-options' ).hide();
				$( '#responsive-ready-site-preview' ).show();
				$('body').addClass('responsive-ready-site-preview-screen');
			} else if ( $('body').hasClass('responsive-ready-site-import-page-options-screen') ) {
				$('body').removeClass('responsive-ready-site-import-page-options-screen')
				$( '#responsive-ready-sites-import-options' ).hide();
				$( '#responsive-ready-site-pages-preview' ).show();
				$('body').addClass('responsive-ready-sites-import-page-preview-page-screen');
			}
		},

		/**
		 * Import Site progress Screen
		 */
		_importSiteOptionsScreen: function(event) {
			event.preventDefault();

			$( '#responsive-ready-site-preview' ).hide();
			$( '#responsive-ready-sites-import-options' ).show();

			var self = $( this ).parents( '.responsive-ready-site-preview' );

			var demoId                  = self.data( 'demo-id' ) || '',
				apiURL                  = self.data( 'demo-api' ) || '',
				demoType                = self.data( 'demo-type' ) || '',
				active_site             = self.data( 'active-site' ) || '',
				check_plugins_installed = self.data( 'check_plugins_installed' ) || '',
				demoURL                 = self.data( 'demo-url' ) || '',
				screenshot              = self.data( 'screenshot' ) || '',
				demo_name               = self.data( 'demo-name' ) || '',
				pages                   = self.data( 'pages' ) || '',
				demo_slug               = self.data( 'demo-slug' ) || '',
				requiredPlugins         = self.data( 'required-plugins' ) || '',
				responsiveSiteOptions   = self.find( '.responsive-site-options' ).val() || '',
				hasAppAuth              = responsiveSitesAdmin.hasAppAuth;

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
				active_site: active_site,
				demo_api: apiURL,
				screenshot: screenshot,
				name: demo_name,
				slug: demo_slug,
				required_plugins: JSON.stringify( requiredPlugins ),
				responsive_site_options: responsiveSiteOptions,
				pages: JSON.stringify( pages ),
				pro_plugins_flag: ResponsiveSitesAdmin.pro_plugins_flag,
				require_flexbox_container:ResponsiveSitesAdmin.hasFlexboxContainer,
				has_app_auth: hasAppAuth,
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
				active_site             = self.data( 'active-site' ) || '',
				check_plugins_installed = self.data( 'check_plugins_installed' ) || '',
				demoURL                 = self.data( 'demo-url' ) || '',
				screenshot              = self.data( 'screenshot' ) || '',
				demo_name               = self.data( 'demo-name' ) || '',
				pages                   = self.data( 'pages' ) || '',
				demo_slug               = self.data( 'demo-slug' ) || '',
				requiredPlugins         = self.data( 'required-plugins' ) || '',
				responsiveSiteOptions   = self.find( '.responsive-site-options' ).val() || '';

				var rbeaPlugin = {
					"name": "Responsive Block Editor Addons",
					"slug": "responsive-block-editor-addons",
					"init": "responsive-block-editor-addons/responsive-block-editor-addons.php"
				};
				if(requiredPlugins){
					var isDuplicate = requiredPlugins.some(function(plugin) {
						return plugin.slug === rbeaPlugin.slug;
					});
					// If it's not a duplicate, add it to the array
					if (!isDuplicate) {
						requiredPlugins.push(rbeaPlugin);
					}
				}

			var template = wp.template( 'responsive-ready-sites-import-progress-page' );

			templateData = [{
				id: demoId,
				demo_type: demoType,
				check_plugins_installed: check_plugins_installed,
				demo_url: demoURL,
				active_site: active_site,
				demo_api: apiURL,
				screenshot: screenshot,
				name: demo_name,
				slug: demo_slug,
				required_plugins: JSON.stringify( requiredPlugins ),
				responsive_site_options: responsiveSiteOptions,
				pages: JSON.stringify( pages ),
				pro_plugins_flag: ResponsiveSitesAdmin.pro_plugins_flag,
			}];
			$( '#responsive-ready-sites-import-progress' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );

			if ( $.isArray( requiredPlugins ) ) {
				// or.
				var $pluginsFilter = $( '#plugin-filter' ),
					data           = {
						action           : 'responsive-ready-sites-required-plugins',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
						required_plugins : requiredPlugins
				};

				// Add disabled class from import button.
				$( '.responsive-demo-import' )
					.addClass( 'disabled not-click-able' )
					.removeAttr( 'data-import' );

				$( '.required-plugins' ).addClass( 'loading' ).html( '<span class="spinner is-active"></span>' );

				// Required Required.
				$.ajax(
					{
						url  : responsiveSitesAdmin.ajaxurl,
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
								$( '.responsive-ready-site-import-free' ).addClass( 'disabled not-click-able' );
								$( '.responsive-ready-site-import-free' ).prop( 'disabled',true );
								$( '.responsive-ready-sites-install-plugins-title' ).append( '<span class="warning"> - Please make sure you have following plugins Installed</span>' );
								$( '#responsive-ready-sites-tooltip-plugins-settings' ).css( 'display', 'block' );
							}
							/**
							 * Enable Demo Import Button
							 *
							 * @type number
							 */
							responsiveSitesAdmin.requiredPlugins = required_plugins;
						}
					);

			}
		},

		/**
		 *
		 * Check if install responsive theme checkbox is checked
		 */
		 _isInstallResponsiveThemeChecked: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			ResponsiveSitesAdmin.import_progress_status_text = "Checking Responsive Theme Install Status";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			if ( $( '.responsive-ready-sites-install-responsive' ).find('.checkbox').is(':checked') ) {
				// return true;
				$( document ).trigger( 'responsive-theme-install-activate' );
			}
			// return false;
		},

		/**
		 * Fires when a nav item is clicked.
		 */
		_importDemo: function(event) {
			event.preventDefault();

			$( '.sites-import-process-errors .current-importing-status-error-title' ).html( '' );

			$( '.sites-import-process-errors' ).hide();
			$( '.responsive-ready-site-import-free' ).addClass( 'updating-message installing' )
				.text( "Importing.." );
			$( '.responsive-ready-site-import-free' ).addClass( 'disabled not-click-able' );

			var output = '<div class="current-importing-status-title"></div><div class="current-importing-status-description"></div>';
			$( '.current-importing-status' ).html( output );

			$( document ).trigger( 'responsive-get-active-theme' );

		},

		/**
		 * Installs and Activate Responsive Theme
		 */
		_getResponsiveTheme: function(event) {

			event.preventDefault();
			ResponsiveSitesAdmin.import_progress_status_text = "Installing Responsive Theme....";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			ResponsiveSitesAdmin.import_progress_percent += 2;

			$.ajax(
				{
					url: responsiveSitesAdmin.ajaxurl,
					type: 'POST',
					data: {
						'action': 'get-responsive',
						'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
			.done(
				function (result) {
					ResponsiveSitesAdmin.import_progress_percent += 2;
					ResponsiveSitesAdmin.import_progress_status_text = "Installed Responsive Theme....";
					ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
					// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
					setTimeout( function() {

						$.ajax({
							url: ResponsiveInstallThemeVars.ajaxurl,
							type: 'POST',
							data: {
								'action' : 'responsive-ready-sites-activate-theme',
								'_ajax_nonce' : ResponsiveInstallThemeVars._ajax_nonce,
							},
						})
							.done(function (result) {
								if( result.success ) {
									ResponsiveSitesAdmin.import_progress_percent += 2;
									ResponsiveSitesAdmin.import_progress_status_text = "Activated Responsive Theme....";
									ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
									$('#responsive-theme-activation a').text( ResponsiveInstallThemeVars.activated );
								}
							});

					}, 1200 );
				}
			);

		},

		/**
		 * Check if Responsive theme is active
		 */
		_is_responsive_theme_active: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			$.ajax(
				{
					url: responsiveSitesAdmin.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-is-theme-active',
						'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.done(
					function (result) {
						if (result.success) {
							$( document ).trigger( 'responsive-ready-sites-install-start' );
						} else {
							$( document ).trigger( 'responsive-theme-install-activate' );
						}
					}
				);
		},

		/**
		 * Log error
		 */
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

			$( '.responsive-ready-site-import-free' ).removeClass( 'updating-message installing' )
				.text( "Import Site" );
			$( '.responsive-ready-site-import-free' ).removeClass( 'disabled not-click-able' );
			$( '.responsive-ready-sites-tooltip-icon' ).removeClass( 'processed-import' );
			$( '.responsive-ready-sites-tooltip-icon' ).removeClass( 'processing-import' );
			$( '.responsive-ready-sites-import-process-wrap' ).hide();
		},

		/**
		 * Show notice when responsive theme checkbox is unchecked.
		 */
		_displayNoticeBarUnchecked: function() {
			let svg = '<svg xmlns="http://www.w3.org/2000/svg" style="vertical-align:-4px" width="16" height="16" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16"><g fill="currentColor"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="m8.93 6.588l-2.29.287l-.082.38l.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319c.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246c-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0z"/></g></svg>';
			if ( $( '.responsive-ready-sites-install-responsive' ).find('.checkbox').is(':checked') ) {
				$( '.current-importing-status-error-title' ).html( '' );
				$( '.sites-import-process-errors' ).css( 'display', 'none' );
			} else {
				$( '.sites-import-process-errors' ).css( 'display', 'block' );
				$( '.current-importing-status-error-title' ).html( '<div style="display: flex; text-align: left; align-items: baseline; margin-left: 6px;"><div>' + svg + '</div><div style="margin-left:10px"><p>Importing the original website design requires activating the Responsive theme. <span>Choosing another theme works, but you\'ll need to manually adjust all the customizer settings to match the original website.</span></p></div></div>' );
			}
		},

		/**
		 * Check if Responsive pro is installed
		 */
		_checkResponsiveAddonsProInstalled: function() {
			var is_pro_installed;
			$.ajax(
				{
					url: responsiveSitesAdmin.ajaxurl,
					async: false,
					type : 'POST',
					dataType: 'json',
					data: {
						'action': 'check-responsive-add-ons-pro-installed',
						'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
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

			ResponsiveSitesAdmin._renderDemoPreview( self );
		},

		/**
		 * Render Demo Preview
		 */
		_renderDemoPreview: function(anchor) {

			var demoId                         = anchor.data( 'demo-id' ) || '',
				demoURL                        = anchor.data( 'demo-url' ) || '',
				screenshot                     = anchor.data( 'screenshot' ) || '',
				demo_name                      = anchor.data( 'demo-name' ) || '',
				active_site                    = anchor.data( 'active-site' ) || '',
				demo_slug                      = anchor.data( 'demo-slug' ) || '',
				wpforms_path                   = anchor.data( 'wpforms-path' ) || '',
				requiredPlugins                = anchor.data( 'required-plugins' ) || '',
				requiredProPlugins             = anchor.data( 'required-pro-plugins' ) || '',
				allow_pages                    = anchor.data( 'allow-pages' ) || false,
				pages                    	   = anchor.data( 'pages' ) || '',
				check_plugins_installed        = anchor.data( 'check_plugins_installed' ) || '',
				responsiveSiteOptions          = anchor.find( '.responsive-site-options' ).val() || '',
				demo_type                      = anchor.data( 'demo-type' ) || '',
				requireFlexboxContainer        = anchor.data( 'require-flex-box-container' ) || false,
				isResponsiveAddonsProInstalled = ResponsiveSitesAdmin._checkResponsiveAddonsProInstalled(),
				hasAppAuth                     = responsiveSitesAdmin.hasAppAuth;

			if(requireFlexboxContainer){
				ResponsiveSitesAdmin.hasFlexboxContainer = true;
			}else{
				ResponsiveSitesAdmin.hasFlexboxContainer = false;
			}

			var template = wp.template( 'responsive-ready-site-preview' );

			templateData = [{
				id: demoId,
				demo_url: demoURL + '/?utm_source=free-to-pro&utm_medium=responsive-ready-site-importer&utm_campaign=responsive-pro&utm_content=preview',
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
				allow_pages: allow_pages,
				pages: JSON.stringify( pages ),
				require_flexbox_container: requireFlexboxContainer,
				has_app_auth: hasAppAuth,
			}];

			$('body').addClass('responsive-ready-site-preview-screen');
			$( '#responsive-ready-site-preview' ).append( template( templateData[0] ) );
			$( '.theme-install-overlay' ).css( 'display', 'block' );

		},

		/**
		 * Check Import permissions for the current installation.
		 */
		_checkImportCapabilities: function(importCapsHandler) {
			ResponsiveSitesAdmin.import_progress_percent += 5;
			ResponsiveSitesAdmin.import_progress_status_text = "Checking Import permissions...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			let self     = $( '.responsive-ready-sites-advanced-options-wrap' );
			let demoType = self.data( 'demo-type' ) || '';
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-get-import-capabilities',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
						demo_type: demoType,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( "There was an error while processing import. Please try again.", true );					}
				)
				.done(
					function ( response ) {
						if( undefined !== response.data.connection_status && response.data.connection_status === 'active' ) {
							if (typeof importCapsHandler === 'function') {
								importCapsHandler(true);
							}
						} else {
							if( response.success && response.data.activate_results.success ) {
								if (typeof importCapsHandler === 'function') {
									importCapsHandler(true);
								}
							} else if ( response.data.error && undefined !== response.data.message ) {
								ResponsiveSitesAdmin._log_error(response.data.message, true );
							} else if( !response.data.success ) {
								ResponsiveSitesAdmin._log_error( "No Connections available. Upgrade the plan to import the template.", true );
							}
						}
					}
				);	
		},
		/**
		 * Import Process Starts
		 */
		_process_import: function() {
			ResponsiveSitesAdmin.import_progress_status_text = "Gathering pervious imported data...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			var site_id = $( '.responsive-ready-sites-advanced-options-wrap' ).find( '.demo_site_id' ).val();

			var apiURL = responsiveSitesAdmin.ApiURL + 'cyberchimps-sites/' + site_id;

			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-set-reset-data',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.done(
					function ( response ) {
						if ( response.success ) {
							ResponsiveSitesAdmin.import_progress_percent += 2;
							ResponsiveSitesAdmin.site_imported_data = response.data;
						}
					}
				);

			if ( apiURL ) {
				ResponsiveSitesAdmin._importSite( apiURL );
			}

		},

		/**
		 * Start Import Process by API URL.
		 *
		 * @param  {string} apiURL Site API URL.
		 */
		_importSite: function( apiURL ) {
			ResponsiveSitesAdmin.import_progress_percent += 5;
			ResponsiveSitesAdmin.import_progress_status_text = "Processing Import...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			// Request Site Import.
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						'action'  : 'responsive-ready-sites-import-set-site-data-free',
						'api_url' : apiURL,
						'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( demo_data ) {
						// Check is site imported recently and set flag.

						// 1. Fail - Request Site Import.
						if ( false === demo_data.success ) {
							ResponsiveSitesAdmin._log_error( demo_data.data, true );
						} else {
							ResponsiveSitesAdmin.xml_path                       = encodeURI( demo_data.data['xml_path'] ) || '';
							ResponsiveSitesAdmin.wpforms_path                   = encodeURI( demo_data.data['wpforms_path'] ) || '';
							ResponsiveSitesAdmin.active_site_slug               = demo_data.data['slug'] || '';
							ResponsiveSitesAdmin.active_site_title              = demo_data.data['title'];
							ResponsiveSitesAdmin.active_site_featured_image_url = demo_data.data['featured_image_url'];
							ResponsiveSitesAdmin.site_customizer_data           = JSON.stringify( demo_data.data['site_customizer_data'] ) || '';
							ResponsiveSitesAdmin.required_plugins               = JSON.stringify( demo_data.data['required_plugins'] ) || '';
							ResponsiveSitesAdmin.required_pro_plugins           = JSON.stringify( demo_data.data['required_pro_plugins'] || '' );
							ResponsiveSitesAdmin.widgets_data                   = JSON.stringify( demo_data.data['site_widgets_data'] ) || '';
							ResponsiveSitesAdmin.site_options_data              = JSON.stringify( demo_data.data['site_options_data'] ) || '';
							ResponsiveSitesAdmin.pages                          = JSON.stringify( demo_data.data['pages'] ) || '';

							$( document ).trigger( 'responsive-ready-sites-import-set-site-data-done' );
						}
					}
				);
		},

		/**
		 * Install required plugins
		 */
		_installRequiredPlugins: function( event ){
			ResponsiveSitesAdmin.import_progress_status_text = "Gathering Required Plugins...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			var requiredPlugins = JSON.parse( ResponsiveSitesAdmin.required_plugins );
			if( ResponsiveSitesAdmin.required_pro_plugins ) {
				var requiredProPlugins = JSON.parse( ResponsiveSitesAdmin.required_pro_plugins );
			}

			if ( $.isArray( requiredPlugins ) ) {

				// Required Required.
				$.ajax(
					{
						url  : responsiveSitesAdmin.ajaxurl,
						type : 'POST',
						data : {
							action           : 'responsive-ready-sites-required-plugins',
							_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
							required_plugins : requiredPlugins,
							required_pro_plugins : requiredProPlugins,
						},
					}
				)
					.done(
						function ( response ) {
							var required_plugins = response.data['required_plugins'] || '';
							ResponsiveSitesAdmin.import_progress_percent +=2;
							responsiveSitesAdmin.required_plugins = required_plugins;
							ResponsiveSitesAdmin._bulkPluginInstallActivate();
						}
					);

			} else {
				if ( ResponsiveSitesAdmin.processing_single_template ) {
					$( document ).trigger( 'responsive-ready-page-install-and-activate-required-plugins-done' );
				} else {
					$( document ).trigger( 'responsive-ready-sites-install-and-activate-required-plugins-done' );
				}
			}
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
		 * Bulk Plugin Active & Install
		 */
		_bulkPluginInstallActivate: function()
		{
			if ( 0 === responsiveSitesAdmin.required_plugins.length ) {
				return;
			}

			var not_installed 	 = responsiveSitesAdmin.required_plugins.notinstalled || '';
			var activate_plugins = responsiveSitesAdmin.required_plugins.inactive || '';
			var pro_plugins		 = responsiveSitesAdmin.required_plugins.proplugins || '';

			// Install wordpress.org plugins.
			if ( not_installed.length > 0 ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Installing Required Plugins...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				ResponsiveSitesAdmin._installAllPlugins( not_installed );
			}

			// Activate wordpress.org plugins.
			if ( activate_plugins.length > 0 ) {
				ResponsiveSitesAdmin._activateAllPlugins( activate_plugins );
			}

			// Install Pro Plugins.
			if ( pro_plugins.length > 0 ) {
				ResponsiveSitesAdmin._installProPlugins( pro_plugins );
			}

			if ( activate_plugins.length <= 0 && not_installed.length <= 0 ) {
				if ( ResponsiveSitesAdmin.processing_single_template ) {
					ResponsiveSitesAdmin._ready_for_import_template();
				} else {
					ResponsiveSitesAdmin._ready_for_import_site();
				}
			}

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
		 * Installing Plugin
		 */
		_pluginInstalling: function(event, args) {
			event.preventDefault();
		},

		/**
		 * Install plugin success
		 */
		_pluginInstallSuccess: function( event, response ) {

			if ( typeof responsiveSitesAdmin.required_plugins.notinstalled !== 'undefined' && responsiveSitesAdmin.required_plugins.notinstalled ) {
				event.preventDefault();

				// Reset not installed plugins list.
				var pluginsList                                    = responsiveSitesAdmin.required_plugins.notinstalled;
				responsiveSitesAdmin.required_plugins.notinstalled = ResponsiveSitesAdmin._removePluginFromQueue( response.slug, pluginsList );

				var $plugin_name = $( '.plugin-card-' + response.slug ).data( 'name' );
				ResponsiveSitesAdmin.import_progress_percent += 2;
				ResponsiveSitesAdmin.import_progress_status_text = "Installed "+ $plugin_name + " Plugin...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
				setTimeout(
					function () {

						var $init = $( '.plugin-card-' + response.slug ).data( 'init' );

						$.ajax(
							{
								url: responsiveSitesAdmin.ajaxurl,
								type: 'POST',
								data: {
									'action': 'responsive-ready-sites-required-plugin-activate',
									'init': $init,
									'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
								},
							}
						)
							.done(
								function (result) {

									if (result.success) {
										ResponsiveSitesAdmin.import_progress_percent += 1;
										ResponsiveSitesAdmin.import_progress_status_text = "Activated "+ $plugin_name + " Plugin";
										ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
										var pluginsList = responsiveSitesAdmin.required_plugins.inactive;

										// Reset not installed plugins list.
										responsiveSitesAdmin.required_plugins.inactive = ResponsiveSitesAdmin._removePluginFromQueue( response.slug, pluginsList );

										if ( ResponsiveSitesAdmin.processing_single_template ) {
											ResponsiveSitesAdmin._ready_for_import_template();
										} else {
											ResponsiveSitesAdmin._ready_for_import_site();
										}
									}
								}
							);

					},
					1200
				);
			}
		},

		/**
		 * Activate All Plugins.
		 */
		_activateAllPlugins: function( activate_plugins ) {
			ResponsiveSitesAdmin.import_progress_status_text = "Activating Required Plugin...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);

			$.each(
				activate_plugins,
				function(index, single_plugin) {
					ResponsiveSitesAjaxQueue.add(
						{
							url: responsiveSitesAdmin.ajaxurl,
							type: 'POST',
							data: {
								'action'            : 'responsive-ready-sites-required-plugin-activate',
								'init'              : single_plugin.init,
								'_ajax_nonce'      : responsiveSitesAdmin._ajax_nonce,
							},
							success: function( result ){

								if ( result.success ) {
									ResponsiveSitesAdmin.import_progress_percent += 2;
									ResponsiveSitesAdmin.import_progress_status_text = "Activated "+single_plugin.name;
									ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
									var pluginsList = responsiveSitesAdmin.required_plugins.inactive;

									// Reset not installed plugins list.
									responsiveSitesAdmin.required_plugins.inactive = ResponsiveSitesAdmin._removePluginFromQueue( single_plugin.slug, pluginsList );

									if ( ResponsiveSitesAdmin.processing_single_template ) {
										ResponsiveSitesAdmin._ready_for_import_template();
									} else {
										ResponsiveSitesAdmin._ready_for_import_site();
									}

								}
							}
						}
					);
				}
			);
			ResponsiveSitesAjaxQueue.run();
		},

		/**
		 * Install Pro Plugins.
		 */
		_installProPlugins: function( pro_plugins ) {
			ResponsiveSitesAdmin.import_progress_status_text = "Installing Required Plugins...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);

			$.ajax(
				{
					url: responsiveSitesAdmin.ajaxurl,
					type: 'POST',
					data: {
						'action': 'responsive-ready-sites-install-required-pro-plugins',
						'pro_plugin': pro_plugins,
						'_ajax_nonce': responsiveSitesAdmin._ajax_nonce,
					}
				}
			)
				.done(
					function (result) {
						if ( false === result.success ) {
							ResponsiveSitesAdmin._log_error( 'Failed to activate required plugins.', true );
						} else {
							// Reset not installed plugins list.
							$.each(
								pro_plugins,
								function (index, single_plugin) {
									ResponsiveSitesAdmin.import_progress_percent += 2;
									ResponsiveSitesAdmin.import_progress_status_text = "Activated "+single_plugin.name;
									ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
									var pluginsList                                     = responsiveSitesAdmin.required_plugins.proplugins;
									responsiveSitesAdmin.required_plugins.proplugins = ResponsiveSitesAdmin._removePluginFromQueue( single_plugin.slug, pluginsList );
								}
							);
							if ( ResponsiveSitesAdmin.processing_single_template ) {
								ResponsiveSitesAdmin._ready_for_import_template();
							} else {
								ResponsiveSitesAdmin._ready_for_import_site();
							}
						}
					}
				);
		},

		/**
		 * Ready for site import
		 */
		_ready_for_import_site: function () {
			var notinstalled = responsiveSitesAdmin.required_plugins.notinstalled || 0;
			var inactive     = responsiveSitesAdmin.required_plugins.inactive || 0;

			if ( ResponsiveSitesAdmin._areEqual( notinstalled.length, inactive.length ) ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Ready for site import...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				$( document ).trigger( 'responsive-ready-sites-install-and-activate-required-plugins-done' );
			}
		},

		/**
		 * Ready for template import
		 *
		 * @private
		 */
		_ready_for_import_template: function () {
			var notinstalled = responsiveSitesAdmin.required_plugins.notinstalled || 0;
			var inactive     = responsiveSitesAdmin.required_plugins.inactive || 0;

			if ( ResponsiveSitesAdmin._areEqual( notinstalled.length, inactive.length ) ) {
				ResponsiveSitesAdmin.import_progress_percent += 10;
				ResponsiveSitesAdmin.import_progress_status_text = "Ready for template import...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				$( document ).trigger( 'responsive-ready-page-install-and-activate-required-plugins-done' );
			}
		},

		/**
		 * Trigger reset data event
		 */
		_resetData: function( event ) {
			event.preventDefault();

			if( ResponsiveSitesAdmin._is_reset_data() ) {
				ResponsiveSitesAdmin.import_progress_percent += 2;
				ResponsiveSitesAdmin.import_progress_status_text = "Resetting Site";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				$(document).trigger('responsive-ready-sites-reset-data');
			} else {
				ResponsiveSitesAdmin.import_progress_percent = ResponsiveSitesAdmin.import_progress_percent < 25 ? 25 : ResponsiveSitesAdmin.import_progress_percent;
				$( document ).trigger( 'responsive-ready-sites-reset-data-done' );
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

		/**
		 * Backup before reset settings
		 */
		_backup_before_reset_options: function() {
			ResponsiveSitesAdmin.import_progress_percent += 1;
			ResponsiveSitesAdmin.import_progress_status_text = "Taking settings backup...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			ResponsiveSitesAdmin._backupOptions( 'responsive-ready-sites-backup-settings-before-reset-done' );
			ResponsiveSitesAdmin.backup_taken = true;
		},

		/**
		 * Backup settings
		 */
		_backupOptions: function( trigger_name ) {
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-backup-settings',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,

					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						ResponsiveSitesAdmin.import_progress_percent += 3;
						// Custom trigger.
						$( document ).trigger( trigger_name );
					}
				);
		},

		/**
		 * Reset customizer data
		 */
		_reset_customizer_data: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			ResponsiveSitesAdmin.import_progress_status_text = "Resetting customizer...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-customizer-data',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-reset-customizer-data-done' );
					}
				);
		},

		/**
		 * Reset site options
		 */
		_reset_site_options: function() {
			// Site Options.
			ResponsiveSitesAdmin.import_progress_percent += 2;
			ResponsiveSitesAdmin.import_progress_status_text = "Resetting site options...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-site-options',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-reset-site-options-done' );
					}
				);
		},

		/**
		 * Reset widgets data
		 */
		_reset_widgets_data: function() {
			// Widgets.
			ResponsiveSitesAdmin.import_progress_percent += 2;
			ResponsiveSitesAdmin.import_progress_status_text = "Resetting widgets...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					data : {
						action : 'responsive-ready-sites-reset-widgets-data',
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( data ) {
						$( document ).trigger( 'responsive-ready-sites-reset-widgets-data-done' );
					}
				);
		},

		/**
		 * Reset terms
		 */
		_reset_terms: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesAdmin.site_imported_data['reset_terms'].length ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Resetting terms...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				ResponsiveSitesAdmin.reset_remaining_terms = ResponsiveSitesAdmin.site_imported_data['reset_terms'].length;

				$.each(
					ResponsiveSitesAdmin.site_imported_data['reset_terms'],
					function(index, term_id) {
						ResponsiveSitesAjaxQueue.add(
							{
								url: responsiveSitesAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-terms',
									term_id : term_id,
									_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
								},
								success: function( result ){
									if ( ResponsiveSitesAdmin.reset_processed_terms < ResponsiveSitesAdmin.site_imported_data['reset_terms'].length ) {
										ResponsiveSitesAdmin.reset_processed_terms += 1;
									}

									ResponsiveSitesAdmin.reset_remaining_terms -= 1;
									if ( 0 == ResponsiveSitesAdmin.reset_remaining_terms ) {
										$( document ).trigger( 'responsive-ready-sites-delete-terms-done' );
									}
								}
							}
						);
					}
				);
				ResponsiveSitesAjaxQueue.run();

			} else {
				$( document ).trigger( 'responsive-ready-sites-delete-terms-done' );
			}
		},

		/**
		 * Reset wp forms
		 */
		_reset_wp_forms: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesAdmin.site_imported_data['reset_wp_forms'].length ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Resetting forms...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				ResponsiveSitesAdmin.reset_remaining_wp_forms = ResponsiveSitesAdmin.site_imported_data['reset_wp_forms'].length;

				$.each(
					ResponsiveSitesAdmin.site_imported_data['reset_wp_forms'],
					function(index, post_id) {
						ResponsiveSitesAjaxQueue.add(
							{
								url: responsiveSitesAdmin.ajaxurl,
								type: 'POST',
								data: {
									action  : 'responsive-ready-sites-delete-wp-forms',
									post_id : post_id,
									_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
								},
								success: function( result ){

									if ( ResponsiveSitesAdmin.reset_processed_wp_forms < ResponsiveSitesAdmin.site_imported_data['reset_wp_forms'].length ) {
										ResponsiveSitesAdmin.reset_processed_wp_forms += 1;
									}

									ResponsiveSitesAdmin.reset_remaining_wp_forms -= 1;
									if ( 0 == ResponsiveSitesAdmin.reset_remaining_wp_forms ) {
										$( document ).trigger( 'responsive-ready-sites-delete-wp-forms-done' );
									}
								}
							}
						);
					}
				);
				ResponsiveSitesAjaxQueue.run();

			} else {
				$( document ).trigger( 'responsive-ready-sites-delete-wp-forms-done' );
			}

		},

		/**
		 * Reset Posts
		 */
		_reset_posts: function() {
			ResponsiveSitesAdmin.import_progress_percent += 2;
			if ( ResponsiveSitesAdmin.site_imported_data['reset_posts'].length ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Gathering posts for deletion...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				//chunkArray contains all the post IDs
				const chunkArray = ResponsiveSitesAdmin.site_imported_data['reset_posts'];
				resetPostChunks(chunkArray);
			} else {
				$( document ).trigger( 'responsive-ready-sites-reset-data-done' );
			}
		},

		/**
		 * Import WpForms
		 */
		_importWPForms: function() {

			ResponsiveSitesAdmin.import_progress_percent = 50;
			ResponsiveSitesAdmin.import_progress_status_text = "Importing forms...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action	: 'responsive-ready-sites-import-wpforms',
						wpforms_path : ResponsiveSitesAdmin.wpforms_path,
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( forms){
						if (false === forms.success) {
							// log.
						} else {
							$( document ).trigger( 'responsive-ready-sites-import-wpforms-done' );
						}
					}
				)
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
			if( ResponsiveSitesAdmin._is_import_site_data() ) {
				//Import only when flag is set to false.
				if ( ! ResponsiveSitesAdmin.importFlag ) {
				ResponsiveSitesAdmin.import_progress_status_text = "Importing Site Content...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				$.ajax(
					{
						url: responsiveSitesAdmin.ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'responsive-ready-sites-import-xml',
							xml_path: ResponsiveSitesAdmin.xml_path,
							_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
						},
					}
				)
					.fail(
						function( jqXHR ){
							ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
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

								$( '.current-importing-status-description' ).html( '' ).show();


								var evtSource       = new EventSource( wxrImport.data.url );
								evtSource.onmessage = function (message) {
									var data = JSON.parse( message.data );
									switch (data.action) {
										case 'updateDelta':
											_importXMLVar++;
											_importXMLVar % 6 === 0 ? ResponsiveSitesAdmin.import_progress_percent += 1 : ResponsiveSitesAdmin.import_progress_percent;
											// ResponsiveSitesAdmin.import_progress_percent += xmlImportProgressVar;
											$('.ready-sites-import-progress-info-percent').text(ResponsiveSitesAdmin.import_progress_percent+"%");
											wxrImport.updateDelta( data.type, data.delta );
											break;
											
											case 'complete':
											// _importXMLVar++;
											evtSource.close();

											$( document ).trigger( 'responsive-ready-sites-import-xml-done' );

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
			ResponsiveSitesAdmin.importFlag = true;
			}
			else {
				$( document ).trigger( 'responsive-ready-sites-import-xml-done' );
			}
		},

		/**
		 * Import Customizer Setting
		 */
		_importCustomizerSettings: function() {
			if( ResponsiveSitesAdmin._is_import_customizer_settings() ) {
				ResponsiveSitesAdmin.import_progress_percent = ResponsiveSitesAdmin.import_progress_percent < 75 ? 75 : ResponsiveSitesAdmin.import_progress_percent;
				ResponsiveSitesAdmin.import_progress_status_text = "Importing Customizer Settings...";
				ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
				$.ajax(
					{
						url: responsiveSitesAdmin.ajaxurl,
						type: 'POST',
						dataType: 'json',
						data: {
							action: 'responsive-ready-sites-import-customizer-settings',
							site_customizer_data: ResponsiveSitesAdmin.site_customizer_data,
							_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
						},
					}
				)
					.fail(
						function( jqXHR ){
							ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
						}
					)
					.done(
						function (forms) {
							if (false === forms.success) {
								// log.
							} else {
								$( document ).trigger( 'responsive-ready-sites-import-customizer-settings-done' );
							}
						}
						)
					}
			else{
			   $( document ).trigger( 'responsive-ready-sites-import-customizer-settings-done' );
			}
		},

		/**
		 * Import Widgets.
		 */
		_importWidgets: function( event ) {
			ResponsiveSitesAdmin.import_progress_percent += 5;
			ResponsiveSitesAdmin.import_progress_status_text = "Importing Widgets...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action       : 'responsive-ready-sites-import-widgets',
						widgets_data : ResponsiveSitesAdmin.widgets_data,
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( widgets_data ) {

						if ( false === widgets_data.success ) {
							ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );

						} else {

							$( document ).trigger( 'responsive-ready-sites-import-widgets-done' );
						}
					}
				);
		},

		/**
		 * Import Site Options.
		 */
		_importSiteOptions: function( event ) {
			ResponsiveSitesAdmin.import_progress_percent = 90;
			ResponsiveSitesAdmin.import_progress_status_text = "Importing Site Options...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action       : 'responsive-ready-sites-import-options',
						options_data : ResponsiveSitesAdmin.site_options_data,
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
					}
				)
				.done(
					function ( options_data ) {

						// Fail - Import Site Options.
						if ( false === options_data.success ) {
							ResponsiveSitesAdmin._log_error( 'There was an error while processing import. Please try again.', true );
						} else {

							// 3. Pass - Import Site Options.
							$( document ).trigger( 'responsive-ready-sites-import-options-done' );
						}
					}
				);
		},

		/**
		 * Import Site Complete.
		 */
		_importSiteEnd: function( event ) {
			ResponsiveSitesAdmin.import_progress_status_text = "Final finishings...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action : 'responsive-ready-sites-import-end',
						slug: ResponsiveSitesAdmin.active_site_slug,
						title: ResponsiveSitesAdmin.active_site_title,
						featured_image_url: ResponsiveSitesAdmin.active_site_featured_image_url,
						_ajax_nonce      : responsiveSitesAdmin._ajax_nonce,
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
							ResponsiveSitesAdmin.import_end_time = performance.now();
							ResponsiveSitesAdmin.import_progress_percent = 100;
							ResponsiveSitesAdmin.import_progress_status_text = "Import Done";

							// Calculate the total time taken in seconds
							ResponsiveSitesAdmin.import_total_time = Math.floor((ResponsiveSitesAdmin.import_end_time  - ResponsiveSitesAdmin.import_start_time ) / 1000); // Convert milliseconds to seconds

							ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
							// $( "responsive-sites-imported-site-link" ).attr( "href", responsiveSitesAdmin.siteURL );
							ResponsiveSitesAdmin._importCompletionCongratsScreen(responsiveSitesAdmin.siteURL);
						}, 8000);
					}
				}
			);
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

			$('body').removeClass('responsive-ready-site-preview-screen');
			$('body').addClass('responsive-ready-sites-import-page-preview-page-screen');

			var template = wp.template( 'responsive-ready-sites-import-page-preview-page' );

			templateData = [{
				demo_type: demoType,
				demo_api: apiURL,
				name: demo_name,
				wpforms_path: wpforms_path,
				required_plugins: JSON.stringify( requiredPlugins ),
				required_pro_plugins: JSON.stringify( requiredProPlugins ) || '',
				responsive_site_options: responsiveSiteOptions,
				pages:  pages,
				screenshot: screenshot,
			}];

			$( '#responsive-ready-site-pages-preview' ).append( template( templateData[0] ) )
			$( '.theme-install-overlay' ).css( 'display', 'block' );
		},

		/**
		 * Import Single Page Progress Screen
		 */
		_importPageProgressScreen: function(event) {
			event.preventDefault();
			var self = $( '.responsive-ready-sites-advanced-options-wrap.single-page-import-options-page' );

			$( '#responsive-ready-sites-import-options' ).hide();

			$( '#responsive-ready-sites-page-import-progress' ).show();

			var apiURL                = self.data( 'demo-api' ) || '',
				page_id               = self.data( 'page-id' ) || '',
				demoType              = self.data( 'demo-type' ) || '',
				demo_name             = self.data( 'demo-name' ) || '',
				wpforms_path          = self.data( 'wpforms-path' ) || '',
				screenshot            = self.data( 'screenshot' ) || '',
				requiredPlugins       = self.data( 'required-plugins' ) || '',
				pages                 = self.data( 'pages' ) || '',
				responsiveSiteOptions = self.find( '.responsive-site-options' ).val() || '';

			var template = wp.template( 'responsive-ready-sites-import-single-page-progress-page' );

			templateData = [{
				demo_type: demoType,
				demo_api: apiURL,
				name: demo_name,
				wpforms_path: wpforms_path,
				required_plugins: JSON.stringify( requiredPlugins ),
				responsive_site_options: responsiveSiteOptions,
				pages:  pages,
				screenshot: screenshot,
				page_id: page_id,
			}];

			$( '#responsive-ready-sites-page-import-progress' ).append( template( templateData[0] ) )
			$( '.theme-install-overlay' ).css( 'display', 'block' );
		},

		/**
		 * Preview Templates for the Site
		 */
		_change_site_preview_screenshot: function( event ) {
			event.preventDefault();

			var item = $( this );

			ResponsiveSitesAdmin._set_preview_screenshot_by_page( item );
		},

		/**
		 * Set Preview Image for the Page
		 */
		_set_preview_screenshot_by_page: function( element ) {
			var large_img_url = $( element ).find( '.theme-screenshot' ).attr( 'data-featured-src' ) || '';
			var url           = $( element ).find( '.theme-screenshot' ).attr( 'data-src' ) || '';
			var page_name     = $( element ).find( '.theme-name' ).text() || '';
			var demo_type     = $( element ).find( '.theme-screenshot' ).attr( 'data-demo-type' ) || '';

			$( element ).siblings().removeClass( 'current_page' );
			$( element ).addClass( 'current_page' );

			$( '.single-page-import-button-' + demo_type ).removeClass( 'disabled' );
			$( '.single-page-import-button' ).removeClass( 'disabled' );
			if ( page_name ) {
				var title = responsiveSitesAdmin.importSingleTemplateButtonTitle.replace( '%s', page_name.trim() );
				$( '.single-page-import-button-' + demo_type ).text( title );
				$( '.single-page-import-button' ).text( title );
			}

			if ( url ) {
				$( '.single-site-preview' ).animate(
					{
						scrollTop: 0
					},
					0
				);
				$( '.single-site-preview img' ).addClass( 'loading' ).attr( 'src', url );
				var imgLarge    = new Image();
				imgLarge.src    = large_img_url;
				imgLarge.onload = function () {
					$( '.single-site-preview img' ).removeClass( 'loading' );
					$( '.single-site-preview img' ).attr( 'src', imgLarge.src );
				};
			}
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
				demo_type 	 = self.data( 'demo-type' ) || '',
				isResponsiveAddonsProInstalled = ResponsiveSitesAdmin._checkResponsiveAddonsProInstalled();
				
				var page_id = ResponsiveSitesAdmin._get_id( $( '#single-pages' ).find( '.current_page' ).attr( 'data-page-id' ) ) || '';
				
				var required_plugins = JSON.parse( $( '#single-pages' ).find( '.current_page' ).attr( 'data-required-plugins' ) ) || '';

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
			
			var includes_wp_forms = JSON.parse( $( '#single-pages' ).find( '.current_page' ).attr( 'data-includes-wp-forms' ) ) || false;

			$( '#responsive-ready-site-pages-preview' ).hide();

			$( '#responsive-ready-sites-import-options' ).show();

			var template = wp.template( 'responsive-ready-sites-import-single-page-options-page' );

			templateData = [{
				page_id: page_id,
				page_title:  pageTitle,
				demo_api: demo_api,
				required_plugins: required_plugins,
				require_flexbox_container: ResponsiveSitesAdmin.hasFlexboxContainer,
				wpforms_path: wpforms_path,
				includes_wp_forms: includes_wp_forms,
				demo_type: demo_type,
				pro_plugins_flag: ResponsiveSitesAdmin.pro_plugins_flag,
				is_responsive_addons_pro_installed: isResponsiveAddonsProInstalled,
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
		},

		/**
		 * Import single page.
		 */
		_importSinglePage: function(event) {
			event.preventDefault();
			if( responsiveSitesAdmin.isResponsiveProActive ){
				ResponsiveSitesAdmin._importSinglePageFunction();
			} else {
				let importPromise = new Promise((resolve, reject) => {
					ResponsiveSitesAdmin._checkImportCapabilities(function(result) {
						resolve(result);
					});
				});
				
				importPromise.then((importCaps) => {
					if (!importCaps) {
						return;
					}
					ResponsiveSitesAdmin._importSinglePageFunction();
				});
			}
		},

		_importSinglePageFunction: function() {
			ResponsiveSitesAdmin.import_page_start_time = performance.now();
			ResponsiveSitesAdmin.import_progress_percent += 25;
			ResponsiveSitesAdmin.import_progress_status_text = "Processing Import...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);

			var self = $( '.responsive-ready-sites-advanced-options-wrap.single-page-import-options-page' );

			var required_plugins  = self.data( 'required-plugins' ) || '',
				includes_wp_forms = self.data( 'includes-wp-forms' ) || false,
				wpforms_path      = self.data( 'wpforms-path' ) || '';

			ResponsiveSitesAdmin.current_page_id  = self.data( 'page-id' ) || '';
			ResponsiveSitesAdmin.current_page_api = self.data( 'demo-api' ) || '';
			ResponsiveSitesAdmin.required_plugins = JSON.stringify( required_plugins );

			if ( includes_wp_forms ) {
				ResponsiveSitesAdmin.wpforms_path = wpforms_path;
			} else {
				ResponsiveSitesAdmin.wpforms_path = '';
			}

			ResponsiveSitesAdmin.import_start_time = new Date();

			$( '.sites-import-process-errors .current-importing-status-error-title' ).html( '' );

			$( '.sites-import-process-errors' ).hide();
			$( '.responsive-ready-page-import-free' ).addClass( 'updating-message installing' )
				.text( "Importing.." );
			$( '.responsive-ready-page-import-free' ).addClass( 'disabled not-click-able' );

			ResponsiveSitesAdmin.processing_single_template = true;

			$( document ).trigger( 'responsive-ready-sites-import-page-free-start' );
		},

		/**
		 * Get Page id from attribute
		 */
		_get_id: function( site_id ) {
			return site_id.replace( 'id-', '' );
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
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action      : 'responsive-ready-sites-import-wpforms',
						wpforms_path : wpforms_path,
						_ajax_nonce : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( jqXHR );
						ResponsiveSitesAdmin._log_error( jqXHR.status + jqXHR.statusText, 'Import WP Forms Failed!', jqXHR );
					}
				)
				.done(
					function ( response ) {

						// 1. Fail - Import WPForms Options.
						if ( false === response.success ) {
							ResponsiveSitesAdmin._log_error( response.data, 'Import WP Forms Failed!' );
						} else {
							if ( callback && typeof callback == "function") {
								callback( response );
							}
						}
					}
				);
		},

		/**
		 * Import page.
		 */
		_importPage: function() {

			ResponsiveSitesAdmin.import_progress_percent = ResponsiveSitesAdmin.import_progress_percent < 75 ? 74 : ResponsiveSitesAdmin.import_progress_percent;
			ResponsiveSitesAdmin.import_progress_status_text = "Importing Page...";
			ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);

			ResponsiveSitesAdmin._import_wpform(
				ResponsiveSitesAdmin.wpforms_path,
				function( form_response ) {

					page_api_url = ResponsiveSitesAdmin.current_page_api + '/wp-json/wp/v2/pages/' + ResponsiveSitesAdmin.current_page_id;

					fetch( page_api_url ).then(
						response => {
							return response.json();
						}
					).then(
						data => {
							// Import Single Page.
							$.ajax(
								{
									url: responsiveSitesAdmin.ajaxurl,
									type: 'POST',
									dataType: 'json',
									data: {
										'action': 'responsive-sites-create-page',
										'_ajax_nonce': responsiveSitesAdmin._ajax_nonce,
										'data': data,
										'current_page_api': ResponsiveSitesAdmin.current_page_api,
									},
									success: function (response) {
										if (response.success) {
											$( '.single-site-wrap' ).hide();
											ResponsiveSitesAdmin.import_page_end_time = performance.now();

											ResponsiveSitesAdmin.import_progress_percent = 100;
											ResponsiveSitesAdmin.import_progress_status_text = "Page Imported.";
											// Calculate the total time taken in seconds
											ResponsiveSitesAdmin.import_total_time = Math.floor((ResponsiveSitesAdmin.import_page_end_time  - ResponsiveSitesAdmin.import_page_start_time ) / 1000); // Convert milliseconds to seconds
											
											ResponsiveSitesAdmin._updateImportProcessStatusText(ResponsiveSitesAdmin.import_progress_status_text);
											$( "responsive-sites-imported-site-link" ).attr( "href", response.data['link'] );
											ResponsiveSitesAdmin._importCompletionCongratsScreen(response.data['link']);
										} else {
											ResponsiveSitesAdmin._log_error( 'Page Rest API Request Failed!', true );
										}
									}
								}
							);
						}
					).catch(
						err => {
							ResponsiveSitesAdmin._log_error( 'Page Rest API Request Failed!', true );
						}
					);
				}
			);
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

		_sync_templates_library_with_ajax: function( is_append ) {

			$.ajax({
				url: responsiveSitesAdmin.ajaxurl,
				type: 'POST',
				data: {
					action: 'responsive-sites-get-sites-request-count',
				},
			})
				.fail(function (jqXHR) {
					console.log('The api request to fetch the sites request count fails');
				})
				.done(function (response) {

					var total = response.data;

					for( let i = 1; i <= total; i++ ) {

						ResponsiveSitesAjaxQueue.add({
							url: responsiveSitesAdmin.ajaxurl,
							type: 'POST',
							data: {
								action  : 'responsive-ready-sites-import-sites',
								page_no : i,
							},
							success: function( result ){
								if( is_append ) {
									if( ! ResponsiveSitesAdmin.isEmpty( result.data ) ) {

										var template          = wp.template( 'responsive-sites-list' );

										var data = ResponsiveSitesAdmin._filter_sites_by_page_builder(result.data);

										// First fill the placeholders and then append remaining sites.
										if ($('.placeholder-site').length) {
											for (site_id in result.data) {
												if ($('.placeholder-site').length) {
													$('.placeholder-site').first().remove();
												}
											}
											if ($('#responsive-sites .site-single:not(.placeholder-site)').length) {
												$('#responsive-sites .site-single:not(.placeholder-site)').last().after(template(data));
											} else {
												$('#responsive-sites').prepend(template(data));
											}
										} else {
											$('#responsive-sites').append(template(data));
										}

										responsiveSitesAdmin.default_page_builder_sites = $.extend({}, responsiveSitesAdmin.default_page_builder_sites, result.data);
									}

								}

								if (i === total && responsiveSitesAdmin.strings.syncCompleteMessage) {
									$('#wpbody-content').find('.responsive-sites-sync-templates-library-message').remove();
									var noticeContent = wp.updates.adminNotice({
										className: 'notice responsive-ready-sites-notice notice-success is-dismissible responsive-ready-sites-sync-templates-library-message',
										message: responsiveSitesAdmin.strings.syncCompleteMessage + ' <button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveSitesAdmin.dismiss + '</span></button>',
									});
									$('#responsive-sites-header').before(noticeContent);

									$('.responsive-ready-sites-sync-templates-button').removeClass('updating-message');
								}
							}
						});
					}
					// Run the AJAX queue.
					ResponsiveSitesAjaxQueue.run();
				});
			ResponsiveSitesAdmin._sync_templates_library_complete();
			},

		_sync_templates_library_complete: function () {
			$.ajax({
				url: responsiveSitesAdmin.ajaxurl,
				type: 'POST',
				data: {
					action: 'responsive-ready-sites-update-templates-library-complete',
				},
			}).done(function (response) {
				console.log("Ready Sites data Updated");
			});
		},

		_sync_library: function (event) {
			event.preventDefault();
			var button = $(this);

			if (button.hasClass('updating-message')) {
				return;
			}

			button.addClass('updating-message');

			$('.responsive-ready-sites-sync-templates-library-message').remove();

			var noticeContent = wp.updates.adminNotice({
				className: 'responsive-ready-sites-sync-templates-library-message responsive-ready-sites-notice notice notice-info',
				message: responsiveSitesAdmin.syncTemplatesLibraryStart + '<button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveSitesAdmin.dismiss + '</span></button>',
			});

			$('#responsive-sites-header').before(noticeContent);

			$(document).trigger('wp-updates-notice-added');

			$.ajax({
				url: responsiveSitesAdmin.ajaxurl,
				type: 'POST',
				data: {
					action: 'responsive-ready-sites-update-templates-library',
				},
			})
				.done(function (response) {
					if (response.success) {
						if ('updated' === response.data) {

							$('#wpbody-content').find('.responsive-ready-sites-sync-templates-library-message').remove();
							var noticeContent = wp.updates.adminNotice({
								className: 'notice responsive-ready-sites-notice notice-success is-dismissible responsive-ready-sites-sync-templates-library-message',
								message: responsiveSitesAdmin.strings.syncCompleteMessage + ' <button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveSitesAdmin.dismiss + '</span></button>',
							});
							$('#responsive-sites-header').before(noticeContent);
							button.removeClass('updating-message');
						} else {
							ResponsiveSitesAdmin._sync_templates_library_with_ajax();
						}
					} else {
						$('#wpbody-content').find('.responsive-ready-sites-sync-templates-library-message').remove();
						var noticeContent = wp.updates.adminNotice({
							className: 'notice responsive-ready-sites-notice notice-error is-dismissible responsive-ready-sites-sync-templates-library-message',
							message: response.data + ' <button type="button" class="notice-dismiss"><span class="screen-reader-text">' + responsiveSitesAdmin.dismiss + '</span></button>',
						});
						$('#responsive-sites-header').before(noticeContent);
						button.removeClass('updating-message');
					}
				});
		},

		_toggleSiteTypeFilter: function( e ) {

			var items = $( '.responsive-sites__type-filter-items' );

			if ( items.hasClass( 'visible' ) ) {
				items.removeClass( 'visible' );
				items.hide();
			} else {
				items.addClass( 'visible' );
				items.show();
			}
		},

		_toggleCategoryFilter: function( e ) {

			var items = $( '.responsive-sites__category-filter-items' );

			if ( items.hasClass( 'visible' ) ) {
				items.removeClass( 'visible' );
				items.hide();
			} else {
				items.addClass( 'visible' );
				items.show();
			}
		},

		_toggle_page_builder_list: function( event ) {
			event.preventDefault();
			$(this).toggleClass( 'active' );
			$('body').toggleClass( 'showing-page-builders' );
		},


		_closeFilter: function( e ) {

			var items = $( '.responsive-sites__category-filter-items' );
			items.removeClass( 'visible' );
			items.hide();
		},

		_closeSiteTypeFilter: function( e ) {

			var items = $( '.responsive-sites__type-filter-items' );
			items.removeClass( 'visible' );
			items.hide();
		},

		_closeBuilderTypeFilter: function( e ) {

			var builder_icon = $( '.page-builder-icon' );
			builder_icon.removeClass( 'active' );
		},

		

		_filterClick: async function( e ) {

			ResponsiveSitesAdmin.filter_array = [];
			if ( $( this ).hasClass( 'rst-menu-parent-category' )) {
				$( '.responsive-sites__category-filter-anchor' ).attr( 'data-slug', $( this ).data( 'slug' ) );
				$( '#responsive-sites__category-filter' ).find( '.rst-menu-child-category' ).removeClass( 'child-category-active' );
				$( '#responsive-sites__category-filter' ).find( '.rst-menu-parent-category' ).removeClass( 'category-active' );
				$( this ).addClass( 'category-active' );
				$( '.responsive-sites__category-filter-anchor' ).text( $( this ).find('.rst-menu-parent-category-title').text() );
				$( '.responsive-sites__category-filter-anchor' ).trigger( 'click' );
				$( '#wp-filter-search-input' ).val( '' );
			}
			else if ( $( this ).hasClass( 'rst-menu-child-category' ) ) {
				$( '.responsive-sites__category-filter-anchor' ).attr( 'data-slug', $( this ).data( 'slug' ) );
				$( '#responsive-sites__category-filter' ).find( '.rst-menu-child-category' ).removeClass( 'child-category-active' );
				$( '#responsive-sites__category-filter' ).find( '.rst-menu-parent-category' ).removeClass( 'category-active' );
				$( this ).addClass( 'child-category-active' );
				$( '.responsive-sites__category-filter-anchor' ).text( $( this ).find('span').text() );
				$( '.responsive-sites__category-filter-anchor' ).trigger( 'click' );
				$( '#wp-filter-search-input' ).val( '' );
			}

			var $filter_name = $( '.responsive-sites__category-filter-anchor' ).attr( 'data-slug' );

			if ( '' != $filter_name ) {
				ResponsiveSitesAdmin.filter_array.push( $filter_name );
			}
			$('input[name="responsive-sites-radio"]').change(function() {
				// Get the value of the selected radio button
				var selectedValue = $('input[name="responsive-sites-radio"]:checked').val();
		
				// Map the selected value to the corresponding text
				var text = {
					'': 'All',
					'free': 'Free',
					'premium': 'Premium'
				}[selectedValue];
		
				// Update the text of the element
				$('.responsive-sites__type-filter-anchor').text(text);
			});

			if( $( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).length ) {
				$( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]' ).removeClass('active');
				$( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).addClass('active');
			}
			var $filter_type = $( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).val();

			if ( '' != $filter_type ) {
				ResponsiveSitesAdmin.filter_array.push( $filter_type );
			}

			ResponsiveSitesAdmin._closeSiteTypeFilter();

			ResponsiveSitesAdmin._get_templates();
		},

		_get_templates: function() {
			
			$( '#wp-filter-search-input' ).val( '' );
			
			var search_term   = '';

			$('body').removeClass('responsive-sites-no-search-result');

			var items = [];

			items = ResponsiveSitesAdmin._get_templates_helper( search_term ).
			then(function(items) {
				if( ! ResponsiveSitesAdmin.isEmpty( items ) ) {
					ResponsiveSitesAdmin.add_sites( items );
				} else {
					$('#responsive-sites').html( wp.template('responsive-sites-suggestions') );
				}
			})
			.catch(function(error) {
				console.error(error);
			});
		},

		_get_templates_helper: async function( search_term ) {
			$("#rst-my-favorite-btn").removeClass("active");
			var items = [],
				tags_strings = [];
			search_term = search_term.toLowerCase();

			var $page_builder = $('.page-builders .active').attr('data-page-builder') || 'elementor';

			if ( search_term == '' && ResponsiveSitesAdmin.filter_array.length == 0 && $page_builder == 'all' ) {
				return responsiveSitesAdmin.default_page_builder_sites;
			}

			var $filter_type = $( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).val();
			var $filter_name = $( '.responsive-sites__category-filter-anchor' ).attr( 'data-slug' );

			var data_sites = null;
			try{
				const updated_sites = await ResponsiveSitesAdmin.__updateFavoriteSites();
				data_sites = updated_sites.data;

				for( site_id in data_sites ) {
	
					var current_site = data_sites[site_id];
					var text_match = true;
					var free_match = true;
					var category_match = true;
					var page_builder_match = false;
					var match_id = '';
	
					if ( '' != search_term ) {
						text_match = false;
					}
	
					if ( '' != $filter_name ) {
						category_match = false;
					}
	
					if ( '' != $filter_type ) {
						free_match = false;
					}
	
					if( '' != $page_builder) {
						page_builder_match = false;
					}
	
					// Check in site title.
					if( current_site['title'] ) {
						var site_title = ResponsiveSitesAdmin._unescape_lower( current_site['title']['rendered'] );
	
						if( site_title.toLowerCase().includes( search_term ) ) {
							text_match = true;
							match_id = site_id;
						}
					}
	
					// Check in site tags.
					if ( null !== current_site['sites_tags'] && Object.keys(current_site['sites_tags']).length) {
						for( tag_id in current_site['sites_tags'] ) {
							var tag_title = current_site['sites_tags'][tag_id];
							tag_title = ResponsiveSitesAdmin._unescape_lower(tag_title.replace('-', ' '));
							if (tag_title.toLowerCase().includes(search_term)) {
								text_match = true;
								match_id = site_id;
							}
						}
					}
	
					for( filter_id in ResponsiveSitesAdmin.filter_array ) {
						var slug = ResponsiveSitesAdmin.filter_array[filter_id];
						if( slug == 'free' && 'free' == current_site['demo_type'] ) {
							free_match = true;
							match_id = site_id;
						}
						if( slug == 'premium' && 'free' != current_site['demo_type'] ) {
							free_match = true;
							match_id = site_id;
						}
						if ( slug != 'free' && slug != 'premium' && undefined != slug ) {
							for( cat_id in current_site['sites_category'] ) {
								if( slug.toLowerCase() == current_site['sites_category'][cat_id] ) {
									category_match = true;
									match_id = site_id;
								}
							}
						}
					}
	
					if ( $page_builder == 'all' || current_site['page_builder'] == $page_builder ) {
						page_builder_match = true;
					}
	
					if ( '' != match_id ) {
						if ( text_match && category_match && free_match && page_builder_match ) {
							items[site_id] = current_site;
							items[site_id]['type'] = 'site';
							items[site_id]['site_id'] = site_id;
							items[site_id]['pages_count'] = ( undefined != current_site['pages'] ) ? Object.keys( current_site['pages'] ).length : 0;
							tags_strings.push( ResponsiveSitesAdmin._unescape_lower( current_site['title']['rendered'] ));
	
							if ( null !== current_site['sites_tags'] && Object.keys(current_site['sites_tags']).length) {
								for (tag_id in current_site['sites_tags']) {
									var tag_title = current_site['sites_tags'][tag_id];
									tag_title = ResponsiveSitesAdmin._unescape_lower(tag_title.replace('-', ' '));
									if (tag_title.toLowerCase().includes(search_term)) {
										tags_strings.push(ResponsiveSitesAdmin._unescape_lower(tag_title));
									}
								}
							}
						}
					}
				}
	
				if ( tags_strings.length > 0 ) {
					ResponsiveSitesAdmin.autocompleteTags = tags_strings;
					ResponsiveSitesAdmin._autocomplete();
				}
				return items;
			} catch (error) {
				console.log(error);
			}
		},

		_search: function(event) {

			var search_input  = $( this ),
				search_term   = $.trim( search_input.val() ) || '';

			if( 13 === event.keyCode ) {
				$('.responsive-sites-autocomplete-result .ui-autocomplete').hide();
				$('.search-form').removeClass('searching');
				$('#responsive-sites-admin').removeClass('searching');
			}

			$('body').removeClass('responsive-sites-no-search-result');

			var searchTemplateFlag = false,
				items = [];

			if( search_term.length ) {
				search_input.addClass('has-input');
				$('#responsive-sites-admin').addClass('searching');
				searchTemplateFlag = true;
			} else {
				search_input.removeClass('has-input');
				$('#responsive-sites-admin').removeClass('searching');
				ResponsiveSitesAdmin._filterClick();
				return;
			}

			items = ResponsiveSitesAdmin._get_sites_and_pages_by_search_term( search_term ).
			then(function(items) {
				if( ! ResponsiveSitesAdmin.isEmpty( items ) ) {
					if ( searchTemplateFlag ) {
						ResponsiveSitesAdmin.add_sites_after_search( items );
					} else {
						ResponsiveSitesAdmin.add_sites( items );
					}
				} else {
					if( search_term.length ) {
						$('body').addClass('responsive-sites-no-search-result');
					}
					$('#responsive-sites').html( wp.template('responsive-sites-suggestions') );
				}
			})
			.catch(function(error) {
				console.error(error);
			});
		},

		_get_sites_and_pages_by_search_term: async function( search_term ) {
			$("#rst-my-favorite-btn").removeClass("active");
			var items = [],
				tags_strings = [];
			search_term = search_term.toLowerCase();

			var $page_builder = $('.page-builders .active').attr('data-page-builder') || 'elementor';

			if ( search_term == '' && ResponsiveSitesAdmin.filter_array.length == 0 && $page_builder == 'all' ) {
				return responsiveSitesAdmin.default_page_builder_sites;
			}

			var $filter_type = $( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).val();

			var data_sites = null;
			try{
				const updated_sites = await ResponsiveSitesAdmin.__updateFavoriteSites();
				data_sites = updated_sites.data;

				for( site_id in data_sites ) {
	
					var current_site = data_sites[site_id];
					var text_match = true;
					var free_match = true;
					var page_builder_match = false;
					var match_id = '';
	
					if ( '' != search_term ) {
						text_match = false;
					}
	
					if ( '' != $filter_type ) {
						free_match = false;
					}
	
					if( '' != $page_builder) {
						page_builder_match = false;
					}
	
					// Check in site title.
					if( current_site['title'] ) {
						var site_title = ResponsiveSitesAdmin._unescape_lower( current_site['title']['rendered'] );
	
						if( site_title.toLowerCase().includes( search_term ) ) {
							text_match = true;
							match_id = site_id;
						}
					}
	
					// Check in site tags.
					if ( null !== current_site['sites_tags'] && Object.keys(current_site['sites_tags']).length) {
						for( tag_id in current_site['sites_tags'] ) {
							var tag_title = current_site['sites_tags'][tag_id];
							tag_title = ResponsiveSitesAdmin._unescape_lower(tag_title.replace('-', ' '));
							if (tag_title.toLowerCase().includes(search_term)) {
								text_match = true;
								match_id = site_id;
							}
						}
					}

					for( filter_id in ResponsiveSitesAdmin.filter_array ) {
						var slug = ResponsiveSitesAdmin.filter_array[filter_id];
						if( slug == 'free' && 'free' == current_site['demo_type'] ) {
							free_match = true;
							match_id = site_id;
						}
						if( slug == 'premium' && 'free' != current_site['demo_type'] ) {
							free_match = true;
							match_id = site_id;
						}
						if ( slug != 'free' && slug != 'premium' && undefined != slug ) {
							for( cat_id in current_site['sites_category'] ) {
								if( slug.toLowerCase() == current_site['sites_category'][cat_id] ) {
									free_match = true;
									match_id = site_id;
								}
							}
						}
					}
	
					if ( $page_builder == 'all' || current_site['page_builder'] == $page_builder ) {
						page_builder_match = true;
					}
	
					if ( '' != match_id ) {
						if ( text_match && free_match && page_builder_match ) {
							items[site_id] = current_site;
							items[site_id]['type'] = 'site';
							items[site_id]['site_id'] = site_id;
							items[site_id]['pages_count'] = ( undefined != current_site['pages'] ) ? Object.keys( current_site['pages'] ).length : 0;
							tags_strings.push( ResponsiveSitesAdmin._unescape_lower( current_site['title']['rendered'] ));
	
							if ( null !== current_site['sites_tags'] && Object.keys(current_site['sites_tags']).length) {
								for (tag_id in current_site['sites_tags']) {
									var tag_title = current_site['sites_tags'][tag_id];
									tag_title = ResponsiveSitesAdmin._unescape_lower(tag_title.replace('-', ' '));
									if (tag_title.toLowerCase().includes(search_term)) {
										tags_strings.push(ResponsiveSitesAdmin._unescape_lower(tag_title));
									}
								}
							}
						}
					}
				}
	
				if ( tags_strings.length > 0 ) {
					ResponsiveSitesAdmin.autocompleteTags = tags_strings;
					ResponsiveSitesAdmin._autocomplete();
				}
				return items;
			} catch (error) {
				console.log(error);
			}
		},

		_filter_sites_by_page_builder: function( data ) {

			var items = [];

			var $page_builder = $('.page-builders .active').attr('data-page-builder') || 'elementor';

			for( site_id in data ) {

				var current_site = data[site_id];

				var page_builder_match = false;

				if ( current_site['page_builder'] === $page_builder ) {
					page_builder_match = true;
				}
				if ( page_builder_match ) {
					items[site_id] = current_site;
					items[site_id]['type'] = 'site';
					items[site_id]['site_id'] = site_id;
					items[site_id]['pages_count'] = ( undefined != current_site['pages'] ) ? Object.keys( current_site['pages'] ).length : 0;
				}
			}

			return items;
		},

		_unescape_lower: function( input_string ) {
			var input_string = ResponsiveSitesAdmin._unescape( input_string );
			return input_string.toLowerCase();
		},

		_unescape: function( input_string ) {
			var title = _.unescape( input_string );

			// @todo check why below character not escape with function _.unescape();
			title = title.replace('&#8211;', '-' );
			title = title.replace('&#8217;', "'" );

			return title;
		},

		isEmpty: function(obj) {
			for(var key in obj) {
				if(obj.hasOwnProperty(key))
					return false;
			}
			return true;
		},

		add_sites_after_search: function( data ) {
			var template          = wp.template( 'responsive-sites-list' );

			$('#responsive-sites').html( template( data ) );
		},


		add_sites: function( data ) {
			var template          = wp.template( 'responsive-sites-list' );

			$('#responsive-sites').html( template( data ) );
		},

		_change_page_builder: function() {

			ResponsiveSitesAdmin.filter_array = [];

			var $filter_name = $( '.responsive-sites__category-filter-anchor' ).attr( 'data-slug' );

			if ( '' != $filter_name ) {
				ResponsiveSitesAdmin.filter_array.push( $filter_name );
			}

			if( $( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).length ) {
				$( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]' ).removeClass('active');
				$( '.responsive-sites__filter-wrap-checkbox input[name=responsive-sites-radio]:checked' ).addClass('active');
			}
			var $filter_type = $( '.responsive-sites__filter-wrap-checkbox .checkbox.active' ).val();

			if ( '' != $filter_type ) {
				ResponsiveSitesAdmin.filter_array.push( $filter_type );
			}

			ResponsiveSitesAdmin._closeFilter();

			var page_builder_slug = $(this).attr('data-page-builder') || '';
			var page_builder_title = $(this).find('.title').text() || '';
			var page_builder_icon = $(this).find('img').attr('src') || '';

			if( page_builder_title ) {
				$('.selected-page-builder').find('.page-builder-title span').text( page_builder_title );
				$('.selected-page-builder').find('.page-builder-title img').attr('src', page_builder_icon);
			}

			if( $('.page-builders [data-page-builder="'+page_builder_slug+'"]').length ) {
				$('.page-builders [data-page-builder="'+page_builder_slug+'"]').siblings().removeClass('active');
				$('.page-builders [data-page-builder="'+page_builder_slug+'"]').addClass('active');
			}

			ResponsiveSitesAdmin._get_templates();
		},

		_addAutocomplete: async function() {

			var data_sites = null;
				try{
					const updated_sites = await ResponsiveSitesAdmin.__updateFavoriteSites();
					data_sites = updated_sites.data;

					var sites = data_sites || [];
					var strings = [];
		
					// Add site title's in autocomplete.
					for( site_id in sites ) {
		
						var title = ResponsiveSitesAdmin._unescape( sites[ site_id ]['title'] );
		
						title = title.toLowerCase().replace('&#8211;', '-' );
		
						strings.push( title );
					}
		
					ResponsiveSitesAdmin.autocompleteTags = strings;

				} catch (error) {
					console.log(error);
				}

		},

		_autocomplete: function() {

			var strings = ResponsiveSitesAdmin.autocompleteTags;
			strings = _.uniq( strings );
			strings = _.sortBy( strings );

			$( "#wp-filter-search-input" ).autocomplete({
				appendTo: ".responsive-sites-autocomplete-result",
				classes: {
					"ui-autocomplete": "responsive-sites-auto-suggest"
				},
				source: function(request, response) {
					var results = $.ui.autocomplete.filter(strings, request.term);

					// Show only 10 results.
					response(results.slice(0, 15));
				},
				open: function( event, ui ) {
					$('.search-form').addClass( 'searching' );
				},
				close: function( event, ui ) {
					$('.search-form').removeClass( 'searching' );
				}
			});

			$( "#wp-filter-search-input" ).focus();
		},

		_show_search_term: function() {
			var search_term = $(this).text() || '';
			$('#wp-filter-search-input').val( search_term );
			$('#wp-filter-search-input').trigger( 'keyup' );
		},

		_displayAdminOverlayPopup: function() {
			$(".page-builder-icon").removeClass( 'active' );
			$(".responsive-sites-overlay-reveal").slideToggle();
			$(".responsive-sites-overlay-reveal").animate({ right: 0 }, 200);
		
			$("#close-admin-overlay").on('click', function() {
			  $(".responsive-sites-overlay-reveal").animate({ right: "-400px" }, 200, function() {
				$(".responsive-sites-overlay-reveal").hide();
			  });
			});
		},

		_removeTemplateRefreshMessage: function() {
			$(".responsive-ready-sites-sync-templates-library-message").remove();
		},

		_addremoveFavoriteTemplate: function() {

			const site_id = $( this ).parents( '.ra-site-single' ).data( 'demo-id' ) || '';
			let favoriteStatus = $( this ).parents( '.ra-site-single' ).data( 'favorite-status' ) || false;

			favoriteStatus = !favoriteStatus;
			$( this ).parents( '.ra-site-single' ).data( 'favorite-status', favoriteStatus );
			$(this).parents('.ra-site-single').attr('data-favorite-status', favoriteStatus);

			if(favoriteStatus) {
				$(this).addClass('active');
			}
			else{
				$(this).removeClass('active');
			}

			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action       : 'responsive-sites-favorite',
						site_id 	 : site_id,
						is_favorite  : favoriteStatus,
						_ajax_nonce  : responsiveSitesAdmin._ajax_nonce,
					},
				}
			)
				.fail(
					function( jqXHR ){
						ResponsiveSitesAdmin._log_error( 'There was an error adding/removing favorite template. Please try again.', true );
					}
				)
				.done(
					function ( new_favorites ) {
					}
				);		
			
		},

		_displayFavoriteTemplates: function () {

			$( '#wp-filter-search-input' ).val( '' );

			$(this).toggleClass('active');

			if($(this).hasClass('active')){
				$.ajax(
					{
						url  : responsiveSitesAdmin.ajaxurl,
						type : 'POST',
						dataType: 'json',
						data : {
							action       : 'responsive-favorite-site-details',
							_ajax_nonce  : responsiveSitesAdmin._ajax_nonce,
						},
					}
				)
					.fail(
						function( jqXHR ){
							ResponsiveSitesAdmin._log_error( 'There was an error while retrieving favorite templates. Please try again.', true );
						}
					)
					.done(
						function ( favorite_sites ) {

							const active_page_builder = $('.page-builders .active').attr('data-page-builder');
							if(active_page_builder !== 'all'){
								const filterData = ResponsiveSitesAdmin._filter_sites_by_page_builder(favorite_sites.data);
								ResponsiveSitesAdmin.add_sites(filterData);
							}
							else{
								ResponsiveSitesAdmin.add_sites(favorite_sites.data);
							}							
						}
					);
			}
			else{
				const active_page_builder = $('.page-builders .active').attr('data-page-builder');
				var data_sites = null;
				ResponsiveSitesAdmin.__updateFavoriteSites()
				.then(function(updated_sites) {
					data_sites = updated_sites.data;

					const data = data_sites;
					
					ResponsiveSitesAdmin._get_templates();
				})
				.catch(function(error) {
					console.log(error);
				});

			}

		},

		__updateFavoriteSites: function() {

			return new Promise(function(resolve, reject) {
				$.ajax({
				  url: responsiveSitesAdmin.ajaxurl,
				  type: 'POST',
				  dataType: 'json',
				  data: {
					action: 'responsive-update_all_sites_fav_status',
					_ajax_nonce: responsiveSitesAdmin._ajax_nonce,
				  }
				})
				.done(function(updated_sites) {
				  resolve(updated_sites); // Resolve the Promise with the data
				})
				.fail(function(jqXHR) {
				  reject('Error'); // Reject the Promise with an error message
				  ResponsiveSitesAdmin._log_error('There was an error while updating favorite templates. Please try again.', true);
				});
			  });

		},

		_addUserToSubscriptionList: function() {

			let userEmail = $('#ready-sites-subscriber-email').val();
			let templateName = $('#ready-sites-importing-template-name').val();
			let value = $("#ready-sites-subscription-check").prop("checked");

			$.ajax(
				{
					url  : responsiveSitesAdmin.ajaxurl,
					type : 'POST',
					dataType: 'json',
					data : {
						action      : 'responsive-ready-sites-add-subscriber-to-moosend',
						_ajax_nonce : responsiveSitesAdmin._ajax_nonce,
						user_email : userEmail,
						template_name : templateName,
						ready_sites_subscripiton_checkbox : value,
					},
				}
			)
				.done(
					function ( response ) {
					}
				);
			  
		},

		_updateImportProcessStatusText: function (status_text) {
			let importPercent = ResponsiveSitesAdmin.import_progress_percent;
			if(importPercent < 25){
				$('.ready-sites-import-progress-bar').addClass('import-stage-1');
			} 
			else if(importPercent > 35 && importPercent < 50){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-1');
				$('.ready-sites-import-progress-bar').addClass('import-stage-2');
			} 
			else if(importPercent > 60 && importPercent < 76){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-2');
				$('.ready-sites-import-progress-bar').addClass('import-stage-3');
			}
			else if(importPercent > 76){
				$('.ready-sites-import-progress-bar').removeClass('import-stage-3');
				$('.ready-sites-import-progress-bar').addClass('import-stage-4 import-done');
			} 
			$('.ready-sites-import-progress-info-text').text(status_text);
			$('.ready-sites-import-progress-info-percent').text(ResponsiveSitesAdmin.import_progress_percent+"%");
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

			$( '.responsive-ready-sites-import-time-taken' ).text(ResponsiveSitesAdmin.import_total_time);
			
			$('#responsive-sites-imported-site-link').attr( "href", importedSiteURL );
			
			let tweetMsg = $('.responsive-sites-tweet-text').text();
			$('#responsive-sites-twitter-tweet-link').attr( "href", "https://twitter.com/intent/tweet?text="+tweetMsg );

			ResponsiveSitesAdmin._importCompletionCongratsConfetti();

		},

		_displayAppConnectModal: function () {
			$(".responsive-addons-app-connect-modal").css("display", "flex");
		},

		_closeAppConnectModal: function () {
			$('.responsive-addons-app-connect-modal').css('display', 'none');
		},

		_displayUnlockTemplatesModal: function() {
			$(".responsive-addons-app-unlock-access-modal").css("display", "flex");
		},

		_closeUnlockTemplatesModal: function () {
			$('.responsive-addons-app-unlock-access-modal').css('display', 'none');
		},

	};

	/**
	 * Initialize ResponsiveSitesAdmin
	 */
	$(
		function(){
			ResponsiveSitesAdmin.init();
		}
	);

})( jQuery );
