<?php
/**
 * Custom megamenu walker nav edit.
 *
 * @package Responsive Addons Pro
 */

/**
 * Create HTML list of nav menu input items.
 *
 * @since     2.3.0
 */
class Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu_Edit {

	/**
	 * Start the element output.
	 *
	 * @param  mixed   $output [description].
	 * @param  mixed   $item   [description].
	 * @param  integer $depth  [description].
	 * @param  array   $args   [description].
	 * @param  integer $id   [description].
	 *
	 * @return void          [description].
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_output = '';

		parent::start_el( $item_output, $item, $depth, $args, $id );

		$position = '<fieldset class="field-move';

		$extra = $this->get_fields( $item, $depth, $args, $id );

		$output .= str_replace( $position, $extra . $position, $item_output );
	}

	/**
	 * Add custom hook to add new field.
	 *
	 * @param  mixed   $item  [description].
	 * @param  integer $depth  [description].
	 * @param  array   $args  [description].
	 * @param  integer $id   [description].
	 * @return [type]        [description]
	 */
	protected function get_fields( $item, $depth, $args = array(), $id = 0 ) {
		ob_start();

		$item_id = intval( $item->ID );

		return ob_get_clean();
	}

} // Walker_Nav_Menu_Edit_Custom
