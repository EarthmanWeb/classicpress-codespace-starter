/**
 * File responsibe for editor js
 *
 * @package responsive-addons-for-elementor
 */

/**
 * Initialize all modules
 */

(function ($, window, document, undefined) {

	$( window ).on(
		'elementor:init',
		function () {

			var RaelControlSelect2 = elementor.modules.controls.Select2;

			// ControlSelect2View prototype.
			var RaelControlSelect2View = RaelControlSelect2.extend(
				{
					getSelect2Options: function () {
						return {
							dir: elementor.config.is_rtl ? 'rtl' : 'ltr'
						};
					},

					templateHelpers: function () {
						var helpers = RaelControlSelect2View.prototype.templateHelpers.apply( this, arguments ),
						fonts       = this.model.get( 'options' );

						helpers.getFontsByGroups = function ( groups ) {
							var filteredFonts = {};

							_.each(
								fonts,
								function ( fontType, fontName ) {
									if (_.isArray( groups ) && _.contains( groups, fontType ) || fontType === groups ) {
										filteredFonts[ fontName ] = fontName;
									}
								}
							);

							return filteredFonts;
						};

						console.log( helpers );

						return helpers;
					}
				}
			);

		}
	);

})( jQuery, window, document );
