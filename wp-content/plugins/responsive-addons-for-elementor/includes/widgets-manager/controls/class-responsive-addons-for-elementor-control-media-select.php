<?php
/**
 * RAEL media select control.
 *
 * A base control for creating media select control. Displays radio buttons styled as
 * groups of buttons with icons for each option.
 *
 * @package responsive-addons-for-elementor
 * @since 1.0.0
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Controls;

use Elementor\Plugin;
use Elementor\Base_Data_Control;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * RAEL media select control.
 *
 * A base control for creating media select control. Displays radio buttons styled as
 * groups of buttons with icons for each option.
 *
 * @since 1.0.0
 */
class Responsive_Addons_For_Elementor_Control_Media_Select extends Base_Data_Control {


	/**
	 * Get select control type.
	 *
	 * Retrieve the control type, in this case `select`.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'rael-media';
	}

	/**
	 * Get media control default values.
	 *
	 * Retrieve the default value of the media control. Used to return the default
	 * values while initializing the media control.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array Control default value.
	 */
	public function get_default_value() {
		return array(
			'url' => '',
			'id'  => '',
		);
	}

	/**
	 * Get media control default settings.
	 *
	 * Retrieve the default settings of the media control. Used to return the default
	 * settings while initializing the media control.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array(
			'label_block'  => true,
			'media_filter' => 'image',
			'dynamic'      => array(
				'returnType' => 'object',
			),
		);
	}

	/**
	 * Enqueue media control scripts and styles.
	 *
	 * Used to register and enqueue custom scripts and styles used by the media
	 * control.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_media();

		wp_enqueue_style( //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			'media',
			admin_url( '/css/media' . $suffix . '.css' )
		);

		wp_enqueue_style( 'rael-elementor-control' );
		wp_enqueue_script( 'rael-elementor-control-js' );
	}

	/**
	 * Render media control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function content_template() {
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<div class="rael-elementor-control-media elementor-control-tag-area elementor-control-preview-area" data-media-type="{{ data.media_filter }}">
					<div class="rael-elementor-control-media-upload-button">
						<i class="fa fa-plus-circle" aria-hidden="true"></i>
					</div>
					<div class="rael-elementor-control-media-attachment-area">
						<div class="rael-elementor-control-media-attachment"></div>
						<div class="rael-elementor-control-media-delete"><?php esc_html_e( 'Delete', 'responsive-addons-for-elementor' ); ?></div>
					</div>
				</div>
			</div>
			<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>
			<input type="hidden" data-setting="{{ data.name }}" />
		</div>
		<?php
	}
}
