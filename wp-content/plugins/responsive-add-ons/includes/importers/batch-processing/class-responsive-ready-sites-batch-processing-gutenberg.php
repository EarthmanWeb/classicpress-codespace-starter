<?php
/**
 * Batch Processing Gutenberg
 *
 * @package Responsive Addons
 * @since 2.2.1
 */

if ( ! class_exists( 'Responsive_Ready_Sites_Batch_Processing_Gutenberg' ) ) :

	/**
	 * Responsive Ready Sites Batch Processing Gutenberg
	 *
	 * @since 2.2.1
	 */
	class Responsive_Ready_Sites_Batch_Processing_Gutenberg {

		/**
		 * Instance
		 *
		 * @since 2.2.1
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 2.2.1
		 * @return object initialized object of class.
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 2.2.1
		 */
		public function __construct() {}

		/**
		 * Allowed tags.
		 *
		 * @param  array        $allowedposttags   Array of default allowable HTML tags.
		 * @param  string|array $context    The context for which to retrieve tags.
		 * @return array Array of allowed HTML tags.
		 */
		public function allowed_tags_and_attributes( $allowedposttags, $context ) {

			// Keep only for 'post' context.
			if ( 'post' === $context ) {

				// <svg> tag and attributes.
				$allowedposttags['svg'] = array(
					'xmlns'   => true,
					'viewbox' => true,
				);

				// <path> tag and attributes.
				$allowedposttags['path'] = array(
					'd' => true,
				);
			}

			return $allowedposttags;
		}

		/**
		 * Import
		 *
		 * @since 2.2.1
		 * @return void
		 */
		public function import() {

			add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_tags_and_attributes' ), 10, 2 );

			Responsive_Ready_Sites_Importer_Log::add( '---- Processing WordPress Posts / Pages - for "Gutenberg" ----' );

			$post_types = array( 'page' );

			$post_ids = Responsive_Ready_Sites_Batch_Processing::get_pages( $post_types );
			if ( empty( $post_ids ) && ! is_array( $post_ids ) ) {
				return;
			}

			foreach ( $post_ids as $post_id ) {
				$this->import_single_post( $post_id );
			}
		}

		/**
		 * Update post meta.
		 *
		 * @param  integer $post_id Post ID.
		 * @return void
		 */
		public function import_single_post( $post_id = 0 ) {

			$is_elementor_page = get_post_meta( $post_id, '_elementor_version', true );

			// If page contain Elementor meta then skip this page.
			if ( $is_elementor_page ) {
				return;
			}

			$ids_mapping = get_option( 'responsive_sites_wpforms_ids_mapping', array() );

			// Post content.
			$content = get_post_field( 'post_content', $post_id );

			if ( ! empty( $ids_mapping ) ) {
				// Replace ID's.
				foreach ( $ids_mapping as $old_id => $new_id ) {
					$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
				}
			}

			//Replace ID's for post and grid block
			$term_ids_mapping = get_option( 'responsive_sites_term_ids_mapping', array() );
			$term_ids_mapping = maybe_unserialize( $term_ids_mapping );
			if ( ! empty( $term_ids_mapping ) ) {
				// Replace ID's.
				foreach ( $term_ids_mapping as $old_id => $new_id ) {
					$content = str_replace( '{"categories":"'.$old_id, '{"categories":"'.$new_id, $content );
				}
			}
			$content = $this->get_content( $content );

			// Update content.
			wp_update_post(
				array(
					'ID'           => $post_id,
					'post_content' => $content,
				)
			);
		}

		/**
		 * Download and Replace images
		 *
		 * @since 2.2.8
		 *
		 * @param  string $content Mixed post content.
		 * @return array           Hotlink image array.
		 */
		public function get_content( $content = '' ) {

			$content = stripslashes( $content );

			// Extract all links.
			preg_match_all( '#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $content, $match );

			$all_links = array_unique( $match[0] );

			// Not have any link.
			if ( empty( $all_links ) ) {
				return $content;
			}

			$link_mapping = array();
			$image_links  = array();
			$other_links  = array();

			foreach ( $all_links as $key => $link ) {
				if ( preg_match( '/^((https?:\/\/)|(www\.))([a-z0-9-].?)+(:[0-9]+)?\/[\w\-]+\.(jpg|png|gif|jpeg)\/?$/i', $link ) ) {

					// Get all image links except *-150x, *-300x and *-1024x.
					if (
						false === strpos( $link, '-150x' ) &&
						false === strpos( $link, '-300x' ) &&
						false === strpos( $link, '-1024x' )
					) {
						$image_links[] = $link;
					}
				} else {

					// other links.
					$other_links[] = $link;
				}
			}

			if ( ! empty( $image_links ) ) {
				foreach ( $image_links as $key => $image_url ) {
					// Download remote image.
					$image            = array(
						'url' => $image_url,
						'id'  => 0,
					);
					$downloaded_image = Responsive_Ready_Sites_Image_Importer::get_instance()->import( $image );

					// Old and New image mapping links.
					$link_mapping[ $image_url ] = $downloaded_image['url'];
				}
			}

			$current_page_api = get_option( 'current_page_api' );
			if ( isset( $current_page_api ) ) {
					$site_url = get_site_url();
				foreach ( $other_links as $key => $link ) {
					$link_mapping[ $link ] = str_replace( $current_page_api, $site_url, $link );
				}
			}

			foreach ( $link_mapping as $old_url => $new_url ) {
				$content = str_replace( $old_url, $new_url, $content );

				// Replace the slashed URLs if any exist.
				$old_url = str_replace( '/', '/\\', $old_url );
				$new_url = str_replace( '/', '/\\', $new_url );
				$content = str_replace( $old_url, $new_url, $content );
			}

			return $content;
		}

	}

	/**
	 * Initiating by calling 'get_instance()' method
	 */
	Responsive_Ready_Sites_Batch_Processing_Gutenberg::get_instance();

endif;
