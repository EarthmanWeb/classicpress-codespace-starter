<?php
/**
 * RAEL Classic Skin.
 *
 * @package RAEL
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Skins\TemplateBlocks;

use Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Skins\TemplateBlocks\Skin_Style;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Classic
 */
class Skin_Classic extends Skin_Style {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}

