/**
 * File responsible for Content Switcher widget frontend js
 *
 * @package responsive-addons-for-elementor
 */

var RAELWidgetContentSwitcherHandler = function ( $scope, $ ) {

	if ('undefined' == typeof $scope ) {
		return;
	}

	var id               = window.location.hash.substring( 1 );
	var pattern          = new RegExp( "^[\\w\\-]+$" );
	var sanitize_input   = pattern.test( id );
	var rael_ct_section_1 = $scope.find( ".rael-ct__section-1" );
	var rael_ct_section_2 = $scope.find( ".rael-ct__section-2" );
	var rael_ct_btn       = $scope.find( ".rael-ct__switcher-button" );
	var switch_type      = rael_ct_btn.attr( 'data-switch-type' );
	var rael_ct_label_1   = $scope.find( ".rael-ct__section-heading-1" );
	var rael_ct_label_2   = $scope.find( ".rael-ct__section-heading-2" );
	var current_class;

	switch ( switch_type ) {
		case 'round_1':
			current_class = '.rael-ct__switcher--round-1';
			break;
		case 'round_2':
			current_class = '.rael-ct__switcher--round-2';
			break;
		case 'rectangle':
			current_class = '.rael-ct__switch-rectangle';
			break;
		case 'label_box':
			current_class = '.rael-ct__switch-label-box';
			break;
		default:
			current_class = 'No Class Selected';
			break;
	}
	var rael_switch = $scope.find( current_class );

	if ('' !== id && sanitize_input ) {
		if (id === 'content-1' || id === 'content-2' ) {
			RAELContentSwitcher._openOnLink( $scope, rael_switch );
		}
	}

	if (rael_switch.is( ':checked' ) ) {
		rael_ct_section_1.hide();
		rael_ct_section_2.show();
	} else {
		rael_ct_section_1.show();
		rael_ct_section_2.hide();
	}

	rael_switch.on(
		'click',
		function (e) {
			rael_ct_section_1.toggle();
			rael_ct_section_2.toggle();
		}
	);

	/* Label 1 Click */
	rael_ct_label_1.on(
		'click',
		function (e) {
			// Uncheck.
			rael_switch.prop( "checked", false );
			rael_ct_section_1.show();
			rael_ct_section_2.hide();

		}
	);

	/* Label 2 Click */
	rael_ct_label_2.on(
		'click',
		function (e) {
			// Check.
			rael_switch.prop( "checked", true );
			rael_ct_section_1.hide();
			rael_ct_section_2.show();
		}
	);
};

var RAELContentSwitcher = {
	/**
	 * Open specific section on click of link
	 */

	_openOnLink: function ( $scope, rael_switch ) {

		var node_id     = $scope.data( 'id' );
		var rael_ct_btn  = $scope.find( ".rael-ct__switcher-button" );
		var switch_type = rael_ct_btn.attr( 'data-switch-type' );
		var node        = '.elementor-element-' + node_id;
		var node_toggle = '#rael-toggle-init' + node;

		$( 'html, body' ).animate(
			{
				scrollTop: $( '#rael-toggle-init' ).find( '.rael-ct-wrapper' ).offset().top
			},
			500
		);

		if (id === 'content-1' ) {

			$( node_toggle + ' .rael-ct__content-1' ).show();
			$( node_toggle + ' .rael-ct__content-2' ).hide();
			rael_switch.prop( "checked", false );
		} else {

			$( node_toggle + ' .rael-ct__content-2' ).show();
			$( node_toggle + ' .rael-ct__content-1' ).hide();
			rael_switch.prop( "checked", true );
		}
	},
}

jQuery( window ).on(
	'elementor/frontend/init',
	function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/rael-content-switcher.default', RAELWidgetContentSwitcherHandler );
	}
);/**
 * File responsible for Timeline widget frontend js
 *
 * @package responsive-addons-for-elementor
 */

var RAELWidgetTimelineHandler = function ($scope, $) {
	if ('undefined' == typeof $scope ) {
		return;
	}

	var id         = $scope.data( 'id' ),
		timeline   = $scope.find( '.rael-timeline-wrapper' ),
		dataScroll = timeline.data( 'scroll-tree' ),
		items      = timeline.find( '.rael-timeline__item' ),
		event      = `scroll.timelineScroll${id} resize.timelineScroll${id}`;

	function scroll_tree()
	{
		items.each(
			function () {
				let item_height          = $( this ).height();
				let offsetTop            = $( this ).offset().top;
				let window_center_coords = $( window ).scrollTop() + $( window ).height();
				let tree_inner           = $( this ).find( '.rael-timeline__tree-inner' );
				let scroll_height        = ($( window ).scrollTop() - tree_inner.offset().top) + ($( window ).outerHeight() / 2);

				if (offsetTop < window_center_coords) {
					$( this ).addClass( 'rael-timeline-scroll-tree' );
				} else {
					$( this ).removeClass( 'rael-timeline-scroll-tree' );
				}

				if (offsetTop < window_center_coords && items.hasClass( 'rael-timeline-scroll-tree' )) {
					if (item_height > scroll_height) {
						tree_inner.css( {"height": scroll_height * 1.5 + "px"} );
					} else {
						tree_inner.css( {"height": item_height * 1.2 + "px"} );
					}
				} else {
					tree_inner.css( {"height": "0"} );
				}
			}
		);
	}

	if ('yes' === dataScroll) {
		scroll_tree();
		$( window ).on( event, scroll_tree );
	} else {
		$( window ).off( event );
	}
};

jQuery( window ).on(
	'elementor/frontend/init',
	function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/rael-timeline.default', RAELWidgetTimelineHandler );
	}
);