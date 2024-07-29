/**
 * Responsive Ready Sites API
 *
 * @package RESPONSIVE ADDONS
 */

(function($){

	ResponsiveSitesAPI = {

		_api_url      : responsiveSitesApi.ApiURL,
		_stored_data  : {
			'cyberchimps-sites' : [],
		},

		/**
		 * API Request
		 */
		_api_request: function( args, callback ) {

			var params = {
				method: 'GET',
				cache: 'default',
			};

			if ( responsiveSitesRender.headers ) {
				params['headers'] = responsiveSitesRender.headers;
			}

			fetch( ResponsiveSitesAPI._api_url + args.slug, params ).then(
				response => {
					if ( response.status === 200 ) {
						return response.json().then(
							items => ({
								items 		: items,
								})
							)
					} else {
						$( document ).trigger( 'cyberchimps-sites-api-request-error' );
						return response.json();
                }
				}
			)
				.then(
					data => {
                    if ( 'object' === typeof data ) {

							data['args'] = args;
							if ( data.args.id && 'undefined' !== typeof data.items ) {
								ResponsiveSitesAPI._stored_data[ args.id ] = $.merge( ResponsiveSitesAPI._stored_data[ data.args.id ], data.items );
							}

							if ( 'undefined' !== typeof args.trigger && '' !== args.trigger ) {
								$( document ).trigger( args.trigger, [data] );
							}

							if ( callback && typeof callback == "function") {
								callback( data );
							}
						}
					}
				);
		},

	};

})( jQuery );
