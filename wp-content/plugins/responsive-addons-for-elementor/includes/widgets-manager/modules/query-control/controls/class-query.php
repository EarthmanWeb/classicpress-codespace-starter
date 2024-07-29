<?php
/**
 * RAEL Query.
 *
 * @package  Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Modules\QueryControl\Controls;

use Elementor\Control_Select2;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Query
 */
class Query extends Control_Select2 {

	/**
	 * Get Type
	 */
	public function get_type() {
		return 'rael-query';
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
