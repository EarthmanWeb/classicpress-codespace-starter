<?php
/**
 * RAEL Skin Init.
 *
 * @package RAEL
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Skins\TemplateBlocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Init
 */
class Skin_Init {

	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $skin_instance;

	/**
	 * Initiator
	 *
	 * @param string $style Skin.
	 */
	public static function get_instance( $style ) {

		require_once RAEL_DIR . 'includes/widgets-manager/widgets/skins/template-blocks/class-skin-style.php';
		switch ( $style ) {
			case 'rael_classic':
				require_once RAEL_DIR . 'includes/widgets-manager/widgets/skins/template-blocks/class-skin-classic.php';
				$skin_class = 'Responsive_Addons_For_Elementor\\WidgetsManager\\Widgets\\Skins\\TemplateBlocks\\Skin_Classic';
				break;
			case 'rael_cards':
				require_once RAEL_DIR . 'includes/widgets-manager/widgets/skins/template-blocks/class-skin-card.php';
				$skin_class = 'Responsive_Addons_For_Elementor\\WidgetsManager\\Widgets\\Skins\\TemplateBlocks\\Skin_Card';
				break;
		}

		if ( class_exists( $skin_class ) ) {
			self::$skin_instance[ $style ] = new $skin_class( $style );
		}

		return self::$skin_instance[ $style ];
	}
}
