<?php
/**
 * Skin Base File
 *
 * @since      1.0.0
 * @package    Responsive_Addons_For_Elementor
 */

namespace Responsive_Addons_For_Elementor\WidgetsManager\Widgets\Skins\Archive_Posts;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait RAEL_Posts_Archive_Skin_Base {
	/**
	 * Render Post on the frontend.
	 */
	public function render() {
		$this->parent->query_posts();

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			$this->render_loop_header();

			$should_escape = apply_filters( 'rael/theme_builder/posts_archive/escape_nothing_found_message', true ); // phpcs:ignore

			$message = $this->parent->get_settings_for_display( 'nothing_found_message' );
			if ( $should_escape ) {
				$message = esc_html( $message );
			}

			echo '<div class="elementor-posts-nothing-found">' . esc_html( $message ) . '</div>';

			$this->render_loop_footer();

			return;
		}

		parent::render();
	}
}
