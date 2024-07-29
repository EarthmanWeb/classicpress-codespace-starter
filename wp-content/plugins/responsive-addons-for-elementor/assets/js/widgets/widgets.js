/**
 * Initializes the WordPress media element for the Elementor audio widget in edit mode.
 *
 * @package responsive-addons-for-elementor
 */

(function ($, window, document, undefined) {

	$( window ).on(
		'elementor/frontend/init',
		function () {

			if (elementorFrontend.isEditMode()) {
				elementorFrontend.hooks.addAction(
					'frontend/element_ready/rael_audio.default',
					function ($scope) {
						window.wp.mediaelement.initialize()
					}
				);
			}
		}
	);
})( jQuery, window, document );