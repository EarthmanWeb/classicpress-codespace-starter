/**
 * File responsibe for visual select control js
 *
 * @package responsive-addons-for-elementor
 */

(function ($, window, document, undefined) {

	$( window ).on(
		'elementor/frontend/init',
		function () {

			if (elementorFrontend.isEditMode() ) {
				elementorFrontend.hooks.addAction(
					'frontend/element_ready/rael_audio.default',
					function ( $scope ) {
						window.wp.mediaelement.initialize() }
				);
			}
		}
	);

	$( window ).on(
		'elementor:init',
		function () {

			var RaelControlBaseDataView = elementor.modules.controls.BaseData;

			/**
			 *  RAEL Visual Select Controller
			 */
			var RaelControlVisualSelectItemView = RaelControlBaseDataView.extend(
				{
					onReady: function () {
						this.ui.select.raelVisualSelect();
					},
					onBeforeDestroy: function () {
						this.ui.select.raelVisualSelect( 'destroy' );
					}
				}
			);
			elementor.addControlView( 'rael-visual-select', RaelControlVisualSelectItemView );

			/**
			 *  RAEL Media Select Controller
			 */
			var RaelMediaSelectControl = RaelControlBaseDataView.extend(
				{
					ui: function () {
						var ui = RaelControlBaseDataView.prototype.ui.apply( this, arguments );

						ui.controlMedia = '.rael-elementor-control-media';
						ui.mediaImage   = '.rael-elementor-control-media-attachment';
						ui.frameOpeners = '.rael-elementor-control-media-upload-button, .rael-elementor-control-media-attachment';
						ui.deleteButton = '.rael-elementor-control-media-delete';

						return ui;
					},

					events: function () {
						return _.extend(
							RaelControlBaseDataView.prototype.events.apply( this, arguments ),
							{
								'click @ui.frameOpeners': 'openFrame',
								'click @ui.deleteButton': 'deleteImage'
							}
						);
					},

					applySavedValue: function () {
						var control = this.getControlValue();

						this.ui.mediaImage.css( 'background-image', control.img ? 'url(' + control.img + ')' : '' );

						this.ui.controlMedia.toggleClass( 'elementor-media-empty', ! control.img );
					},

					openFrame: function () {
						if ( ! this.frame ) {
							this.initFrame();
						}

						this.frame.open();
					},

					deleteImage: function () {
						this.setValue(
							{
								url: '',
								img: '',
								id : ''
							}
						);

						this.applySavedValue();
					},

					/**
					 * Create a media modal select frame, and store it so the instance can be reused when needed.
					 */
					initFrame: function () {
						this.frame = wp.media(
							{
								button: {
									text: elementor.translate( 'insert_media' )
								},
								states: [
								new wp.media.controller.Library(
									{
										title: elementor.translate( 'insert_media' ),
										library: wp.media.query( { type: this.ui.controlMedia.data( 'media-type' ) } ),
										multiple: false,
										date: false
									}
								)
							]
							}
						);

						// When a file is selected, run a callback.
						this.frame.on( 'insert select', this.select.bind( this ) );
					},

					/**
					 * Callback handler for when an attachment is selected in the media modal.
					 * Gets the selected image information, and sets it within the control.
					 */
					select: function () {
						this.trigger( 'before:select' );

						// Get the attachment from the modal frame.
						var attachment = this.frame.state().get( 'selection' ).first().toJSON();

						if (attachment.url ) {
							this.setValue(
								{
									url: attachment.url,
									img: attachment.image.src,
									id : attachment.id
								}
							);

							this.applySavedValue();
						}

						this.trigger( 'after:select' );
					},

					onBeforeDestroy: function () {
						this.$el.remove();
					}
				}
			);

			elementor.addControlView( 'rael-media', RaelMediaSelectControl );
		}
	);

})( jQuery, window, document );