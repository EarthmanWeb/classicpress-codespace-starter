/**
 * Render Responsive Ready Sites Grid.
 *
 * @package RESPONSIVE ADDONS
 */

(function($){

	ResponsiveSitesRender = {

		_ref			: null,

		/**
		 * _api_params = {
		 * 		'search'                  : '',
		 * 		'per_page'                : '',
		 * 		'page'                    : '',
		 *   };
		 */
		_api_params		: {},

		active_site 	: '',

		active_site_data: {},

		init: function()
		{
			this._bind();
			this._setActiveSite();
		},

		/**
		 * Binds events for the Responsive Sites.
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( document ).on( 'cyberchimps-sites-api-request-error'   , ResponsiveSitesRender._addReadySiteSuggestionBlock );
			$( document ).on( 'responsive-api-post-loaded'           , ResponsiveSitesRender._reinitGrid );
			$( document ).on( 'responsive-api-post-loaded-on-scroll' , ResponsiveSitesRender._reinitGridScrolled );
			$( document ).on( 'responsive-get-active-demo-site-done' , ResponsiveSitesRender._loadFirstGrid );
			$( document ).on( 'scroll'                          , ResponsiveSitesRender._scroll );
		},

		_apiAddParam_site_url: function() {
			if ( responsiveSitesRender.sites && responsiveSitesRender.sites.site_url ) {
				ResponsiveSitesRender._api_params['site_url'] = responsiveSitesRender.sites.site_url;
			}
		},

		/**
		 * Show Sites
		 *
		 * @param  {Boolean} resetPagedCount Reset Paged Count.
		 * @param  {String}  trigger         Filtered Trigger.
		 */
		_showSites: function( resetPagedCount, trigger ) {

			if ( undefined === resetPagedCount ) {
				resetPagedCount = true
			}

			if ( undefined === trigger ) {
				trigger = 'responsive-api-post-loaded';
			}

			if ( resetPagedCount ) {
				ResponsiveSitesRender._resetPagedCount();
			}

			ResponsiveSitesRender._apiAddParam_per_page();
			ResponsiveSitesRender._apiAddParam_page();
			ResponsiveSitesRender._apiAddParam_site_url();

			// API Request.
			var api_post = {
				id: 'cyberchimps-sites',
				slug: 'cyberchimps-sites?' + decodeURIComponent( $.param( ResponsiveSitesRender._api_params ) ),
				trigger: trigger,
			};

			ResponsiveSitesAPI._api_request( api_post );
		},

		/**
		 * Load First Grid.
		 *
		 * This is triggered after all category loaded.
		 *
		 * @param  {object} event Event Object.
		 */
		_loadFirstGrid: function() {

			ResponsiveSitesRender._showSites();

		},

		/**
		 * Update Responsive sites list.
		 *
		 * @param  {object} event Object.
		 * @param  {object} data  API response data.
		 */
		_reinitGrid: function( event, data ) {

			var template = wp.template( 'responsive-sites-list' );

			$( 'body' ).removeClass( 'loading-content' );

			if ( responsiveSitesRender.active_site_data !== "" ) {
				jQuery( 'body' ).attr( 'data-responsive-active-site-data', JSON.stringify( responsiveSitesRender.active_site_data ) );
				data.active_site_data = JSON.parse( jQuery( 'body' ).attr( 'data-responsive-active-site-data' ) );
				jQuery( 'body' ).attr( 'data-responsive-active-site', ResponsiveSitesRender.active_site );
			} else {
				data.active_site_data = "";
				jQuery( 'body' ).attr( 'data-responsive-active-site',"" );
			}

			data.active_site = jQuery( 'body' ).attr( 'data-responsive-active-site' );

			// Reset active site data during grid initialization.
			jQuery( 'body' ).attr( 'data-responsive-active-site-data','' );

			jQuery( '#responsive-ready-sites-admin-page' ).show();
			jQuery( '#responsive-sites' ).show().html( template( data ) );

			var items_count = data.items.length;
			if ( items_count <= 0 ) {
				$( '#responsive-ready-sites-admin-page' ).find( '.spinner' ).removeClass( 'is-active' );
				$( '.responsive-sites-suggestions' ).remove();

			} else {
				$( 'body' ).removeClass( 'listed-all-sites' );
			}
		},

		// Returns if a value is an array.
		_isArray: function(value) {
			return value && typeof value === 'object' && value.constructor === Array;
		},

		// Set active site.
		_setActiveSite: function() {
			var active                        = responsiveSitesRender.active_site_data;
			ResponsiveSitesRender.active_site = responsiveSitesRender.active_site_data.slug;
		},

		/**
		 * On Scroll
		 */
		_scroll: function(event) {

			if ( ! $( 'body' ).hasClass( 'listed-all-sites' ) ) {

				var scrollDistance = jQuery( window ).scrollTop();

				var responsiveSitesBottom = Math.abs( jQuery( window ).height() - jQuery( '#responsive-ready-sites-admin-page' ).offset().top - jQuery( '#responsive-ready-sites-admin-page' ).height() );
				responsiveSitesBottom     = responsiveSitesBottom - 100;
				ajaxLoading               = jQuery( 'body' ).data( 'scrolling' );

				if (scrollDistance > responsiveSitesBottom && ajaxLoading == false) {
					ResponsiveSitesRender._updatedPagedCount();

					if ( ! $( '#responsive-sites .no-themes' ).length ) {
						$( '#responsive-ready-sites-admin-page' ).find( '.spinner' ).addClass( 'is-active' );
					}

					jQuery( 'body' ).data( 'scrolling', true );

					ResponsiveSitesRender._showSites( false, 'responsive-api-post-loaded-on-scroll' );
				}
			}
		},

		/**
		 * Append sites on scroll.
		 *
		 * @param  {object} event Object.
		 * @param  {object} data  API response data.
		 */
		_reinitGridScrolled: function( event, data ) {

			var template          = wp.template( 'responsive-sites-list' );
			data.active_site      = jQuery( 'body' ).attr( 'data-responsive-active-site' );
			data.active_site_data = jQuery( 'body' ).attr( 'data-responsive-active-site-data' );

			if ( 'undefined' !== typeof data.items && data.items.length > 0 ) {

				$( 'body' ).removeClass( 'loading-content' );

				setTimeout(
					function() {
						jQuery( '#responsive-sites' ).append( template( data ) );

					},
					800
				);
			} else {
				$( 'body' ).addClass( 'listed-all-sites' );
			}

		},

		/**
		 * Reset Page Count.
		 */
		_resetPagedCount: function() {

			jQuery( 'body' ).attr( 'data-responsive-demo-last-request', '1' );
			jQuery( 'body' ).attr( 'data-responsive-demo-paged', '1' );
			jQuery( 'body' ).attr( 'data-scrolling', false );
			jQuery( 'body' ).attr( 'data-responsive-active-site-data', '' );
			jQuery( 'body' ).attr( 'data-responsive-active-site', '' );

		},

		/**
		 * Add 'page' to api request.
		 *
		 * @private
		 */
		_apiAddParam_page: function() {
			var page_val                              = parseInt( jQuery( 'body' ).attr( 'data-responsive-demo-paged' ) ) || 1;
			ResponsiveSitesRender._api_params['page'] = page_val;
		},

		/**
		 * Update Page Count.
		 */
		_updatedPagedCount: function() {
			paged = parseInt( jQuery( 'body' ).attr( 'data-responsive-demo-paged' ) );
			jQuery( 'body' ).attr( 'data-responsive-demo-paged', paged + 1 );
			window.setTimeout(
				function () {
					jQuery( 'body' ).data( 'scrolling', false );
				},
				800
			);
		},
		/**
		 * Add per page Parameter.
		 */
		_apiAddParam_per_page: function() {
			var per_page_val = 15;
			if ( responsiveSitesRender.sites && responsiveSitesRender.sites["per_page"] ) {
				per_page_val = parseInt( responsiveSitesRender.sites["per_page"] );
			}
			ResponsiveSitesRender._api_params['per_page'] = per_page_val;
		},

		/**
		 * Add ready site suggestion Block
		 */
		_addReadySiteSuggestionBlock: function() {
			$( '#responsive-ready-sites-admin-page' ).find( '.spinner' ).removeClass( 'is-active' ).addClass( 'hide-me' );

			$( 'body' ).addClass( 'listed-all-sites' );
			var template = wp.template( 'responsive-sites-suggestions' );
			if ( ! $( '.responsive-sites-suggestions' ).length ) {
				$( '#responsive-sites' ).append( template );
			}
		},
	};

	/**
	 * Initialize ResponsiveSitesRender
	 */
	$(
		function(){
			ResponsiveSitesRender.init();
		}
	);

})( jQuery );
