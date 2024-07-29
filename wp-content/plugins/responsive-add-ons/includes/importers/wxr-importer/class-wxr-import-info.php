<?php
/**
 * WordPress Importer
 *
 * @package WordPress Importer
 */

if ( ! class_exists( 'WXR_Import_Info' ) ) {

	/**
	 * WXR Import Info
	 */
	class WXR_Import_Info {

		/**
		 * Home.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $home
		 */
		public $home;

		/**
		 * Site Url.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $siteurl
		 */
		public $siteurl;

		/**
		 * Title.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $title
		 */
		public $title;

		/**
		 * Users.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $users
		 */
		public $users = array();

		/**
		 * Post Count.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $post_count
		 */
		public $post_count = 0;

		/**
		 * Media Count.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $media_count
		 */
		public $media_count = 0;

		/**
		 * Comment Count.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $comment_count
		 */
		public $comment_count = 0;

		/**
		 * Term Count.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $term_count
		 */
		public $term_count = 0;

		/**
		 * Generator.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $generator
		 */
		public $generator = '';

		/**
		 * Version.
		 *
		 * @since 1.0.0
		 * @var WXR_Import_Info $version
		 */
		public $version;
	}

}
