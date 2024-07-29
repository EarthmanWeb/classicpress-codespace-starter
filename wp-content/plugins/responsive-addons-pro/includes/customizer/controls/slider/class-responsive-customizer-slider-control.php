<?php
/**
 * Customizer Control: responsive-slider.
 *
 * @package     Responsive WordPress theme
 * @subpackage  Controls
 * @see         https://github.com/aristath/kirki
 * @license     http://opensource.org/licenses/https://opensource.org/licenses/MIT
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Responsive_Customizer_Slider_Control' ) ) :
	/**
	 * Slider control
	 */
	class Responsive_Customizer_Slider_Control extends WP_Customize_Control {

		/**
		 * The control type.
		 *
		 * @access public
		 * @var string
		 */
		public $type = 'responsive-slider';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {
			wp_enqueue_script( 'responsive-slider', RESPONSIVE_ADDONS_PRO_URI . 'core/includes/customizer/assets/min/js/slider.min.js', array( 'jquery', 'customize-base', 'jquery-ui-slider' ), RESPONSIVE_ADDONS_PRO_VERSION, true );
			wp_enqueue_style( 'responsive-slider', RESPONSIVE_ADDONS_PRO_URI . 'core/includes/customizer/assets/min/css/slider.min.css', array(), RESPONSIVE_ADDONS_PRO_VERSION, true );
		}

		/**
		 * Renders the control wrapper and calls $this->render_content() for the internals.
		 *
		 * @see WP_Customize_Control::render()
		 */
		protected function render() {
			$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
			$class = 'customize-control has-switchers customize-control-' . $this->type;

			?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li>
			<?php
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['id'] = $this->id;

			$this->json['inputAttrs'] = '';
			foreach ( $this->input_attrs as $attr => $value ) {
				$this->json['inputAttrs'] .= $attr . '="' . esc_attr( $value ) . '" ';
			}

			$this->json['desktop'] = array();
			$this->json['tablet']  = array();
			$this->json['mobile']  = array();

			foreach ( $this->settings as $setting_key => $setting ) {
				$this->json[ $setting_key ] = array(
					'id'      => $setting->id,
					'default' => $setting->default,
					'link'    => $this->get_link( $setting_key ),
					'value'   => $this->value( $setting_key ),
				);
			}

		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 *
		 * @access protected
		 */
		protected function content_template() {
			?>
		<# if ( data.label ) { #>
			<span class="customize-control-title">
				<span>{{{ data.label }}}</span>
			</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<# if ( data.desktop ) { #>
			<div class="desktop control-wrap active">
				<div class="responsive-slider desktop-slider"></div>
				<div class="responsive-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input desktop-input" value="{{ data.desktop.value }}" {{{ data.desktop.link }}} />
				</div>
			</div>
		<# } #>

		<# if ( data.tablet ) { #>
			<!-- <div class="tablet control-wrap">
				<div class="responsive-slider tablet-slider"></div>
				<div class="responsive-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input tablet-input" value="{{ data.tablet.value }}" {{{ data.tablet.link }}} />
				</div>
			</div>
		<# } #> -->

		<# if ( data.mobile ) { #>
			<!-- <div class="mobile control-wrap">
				<div class="responsive-slider mobile-slider"></div>
				<div class="responsive-slider-input">
					<input {{{ data.inputAttrs }}} type="number" class="slider-input mobile-input" value="{{ data.mobile.value }}" {{{ data.mobile.link }}} />
				</div>
			</div>
		<# } #> -->

			<?php
		}
	}
endif;
