/**
 * File responsible for admin js
 *
 * @package responsive-addons-for-elementor
 */

var $ = jQuery.noConflict();
$( document ).ready(
	function () {
		let hash = window.location.hash;
		if (hash === "") {
			window.location.hash = "#widgets";
		}
		applyCurrentClass()
		if (window.location.hash === '#widgets' || window.location.hash === '#settings') {

			$( ".responsive-addons-for-elementor-tabs-inner-content" ).addClass( 'responsive-addons-for-elementor-widgets-section-spacing' )
		}
		if (hash === '#widgets' ) {
			$( '.responsive-addons-for-elementor-tab-content' ).hide()
			$( ".responsive-addons-for-elementor-tabs-inner-content" ).addClass( 'responsive-addons-for-elementor-widgets-section-spacing' )
			$( '.responsive-addons-for-elementor-tab' ).removeClass( 'responsive-addons-for-elementor-active-tab' )
			$( '.responsive-addons-for-elementor-widgets-tab' ).addClass( 'responsive-addons-for-elementor-active-tab' )
			$( '#rael_widgets' ).show()
		}
		if (hash === '#templates' ) {
			goToRST()
			$( ".responsive-addons-for-elementor-tabs-inner-content" ).css( "background-image", "url('" + localize.raelurl + "admin/images/rst-template-preview.jpg')" );
		}
		$( '.responsive-addons-for-elementor-tab-content' ).hide()
		$( '.responsive-addons-for-elementor-tab' ).removeClass( 'responsive-addons-for-elementor-active-tab' )
		$( '.responsive-addons-for-elementor-' + hash.substring( 1 ) + '-tab' ).addClass( 'responsive-addons-for-elementor-active-tab' )
		$( '#rael_' + hash.substring( 1 ) ).show()

		$( '.responsive-addons-for-elementor-tab' ).click(
			function () {
				$( '.responsive-addons-for-elementor-tab-content' ).hide()
				$( '.responsive-addons-for-elementor-tab' ).removeClass( 'responsive-addons-for-elementor-active-tab' )
				let tab = $( this ).data( 'tab' );
				$( '#rael_' + tab ).show();
				window.location.hash = tab;
				$( this ).addClass( 'responsive-addons-for-elementor-active-tab' );
			}
		);

		$( window ).on(
			'hashchange',
			function () {
				let currentHash = window.location.hash;
				applyCurrentClass()
				if (currentHash === '#widgets' || currentHash === '#settings' ) {
					$( '.responsive-addons-for-elementor-tab-content' ).hide()
					$( ".responsive-addons-for-elementor-tabs-inner-content" ).addClass( 'responsive-addons-for-elementor-widgets-section-spacing' )
					$( '.responsive-addons-for-elementor-tab' ).removeClass( 'responsive-addons-for-elementor-active-tab' )
					$( '.responsive-addons-for-elementor-widgets-tab' ).addClass( 'responsive-addons-for-elementor-active-tab' )
					$( '#rael_widgets' ).show()
				} else {
					$( ".responsive-addons-for-elementor-tabs-inner-content" ).removeClass( 'responsive-addons-for-elementor-widgets-section-spacing' )
				}
				if (currentHash === '#settings' ) {
						$( '.responsive-addons-for-elementor-tab-content' ).hide()
						$( '.responsive-addons-for-elementor-tab' ).removeClass( 'responsive-addons-for-elementor-active-tab' )
						$( '.responsive-addons-for-elementor-settings-tab' ).addClass( 'responsive-addons-for-elementor-active-tab' )
						$( '#rael_settings' ).show()
				}
				if (currentHash === '#templates') {
					goToRST()
					$( ".responsive-addons-for-elementor-tabs-inner-content" ).css( "background-image", "url('" + localize.raelurl + "admin/images/rst-template-preview.jpg')" );
				} else {
					$( ".responsive-addons-for-elementor-tabs-inner-content" ).css( "background-image", "none" );
				}
			}
		);

		$( '.rael-widgets-input-checkbox' ).on(
			'change',
			function (event) {
				let index        = $( this ).data( 'index' )
				let value        = $( this ).prop( "checked" )
				let widgetStatus = true
				$( '.rael-widget-category-card' ).each(
					function () {
						let widgetSwitchValue = $( this ).find( '.rael-widgets-input-checkbox' ).prop( "checked" );
						if ( ! widgetSwitchValue ) {
							widgetStatus = false
							return false
						}
					}
				);
				$( '#rael-widgets-toggle-widgets' ).prop( "checked", widgetStatus )
				toggleWidgets( undefined, index, value )
			}
		)

		$( '#rael-widgets-toggle-widgets' ).on(
			'change',
			function (event) {
				let istoggleAll = $( this ).prop( "checked" )
				if ($( this ).prop( "checked" ) ) {
					$( '.rael-widgets-input-checkbox' ).prop( "checked", true )
				} else {
					$( '.rael-widgets-input-checkbox' ).prop( "checked", false )
				}
					toggleWidgets( istoggleAll, undefined, undefined )
			}
		)

		$( '.rael-widget-tab' ).on(
			'click',
			function (event) {
				$( '.rael-widget-tab' ).removeClass( 'rael-active-widget-category' )
				$( this ).addClass( 'rael-active-widget-category' )
				let tabID = $( this ).attr( 'id' )
				toggleWidgetbyCategory( tabID )
				$( '#rael-input-search-widgets' ).val( '' )
			}
		)

		$( '#rael-input-search-widgets' ).on(
			'keyup',
			function (event) {
				let query = $( this ).val();
				let tabId = $( '.rael-active-widget-category' ).attr( 'id' )
				if (query === '' ) {
					toggleWidgetbyCategory( tabId )
				} else {
					$( '.rael-widget-category-card' ).each(
						function () {
							let widgetTitle = $( this ).find( '.rael-widgets-card-title p' ).text().toLowerCase();
							if (widgetTitle.includes( query.toLowerCase() )) {
								$( this ).show();
							} else {
								$( this ).hide();
							}
						}
					);
				}
			}
		)

		function displayToast( msg, status )
		{
			let background = status === 'error' ? '#FF5151' : '#00CF21';
			Toastify(
				{
					text: msg,
					duration: 3000,
					gravity: "top",
					position: "center",
					stopOnFocus: true,
					offset: {
						x: 0,
						y: 30
					},
					style: {
							background,
					},
				}
			).showToast();
		}

		function goToRST()
		{
			if (localize.isRSTActivated ) {
				window.location.href = localize.siteurl + '/wp-admin/admin.php?page=responsive_add_ons'
				return
			}
		}

		function applyCurrentClass()
		{
			if (window.location.hash === '' || window.location.hash === '#settings' ) {
				$( '#toplevel_page_rael_getting_started ul li:eq(1)' ).removeClass( 'current' )
				$( '#toplevel_page_rael_getting_started ul li:eq(3)' ).addClass( 'current' )
			} else {
				$( '#toplevel_page_rael_getting_started ul li:eq(1)' ).addClass( 'current' )
				$( '#toplevel_page_rael_getting_started ul li:eq(3)' ).removeClass( 'current' )
			}
		}

		function toggleWidgets( istoggleAll, index, value )
		{

			let data = {
				action: 'rael_widgets_toggle',
				_nonce: localize.nonce,
			}

			if (istoggleAll === undefined ) {
				data.index = index, data.value = value
			} else {
				data.toggle_value = istoggleAll
			}

			$.ajax(
				{
					type: 'POST',
					url: localize.ajaxurl,
					data,
					success: function success( data )
					{
						if (data.success ) {
							displayToast( 'Settings Saved', 'success' )
						} else {
							displayToast( 'Error', 'error' )
						}
					}
				}
			);
		}

		function toggleWidgetbyCategory( tabId )
		{
			if (tabId === 'all' ) {
				$( '.rael-widget-category-card' ).show()
			} else {
				$( '.rael-widget-category-card' ).hide()
				$( '.rael-widget-category-' + tabId ).show()
			}
		}
	}
);
