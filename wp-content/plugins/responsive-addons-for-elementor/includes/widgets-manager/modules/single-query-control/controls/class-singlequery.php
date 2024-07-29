<?php
/**
 * RAEL Single Query.
 *
 * @package  Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Modules\SingleQueryControl\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * RAEL Single Query class
 */
class SingleQuery extends Control_Select2 {

	/**
	 * Get the widget type.
	 */
	public function get_type() {
		return 'rael-single-query';
	}

	/**
	 * 'query' can be used for passing query args in the structure and format used by WP_Query.
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(),
			array(
				'query' => '',
			)
		);
	}
}
