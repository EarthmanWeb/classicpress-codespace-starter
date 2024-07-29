<?php
/**
 * Plugin Name: EM Disable Comments and Pingbacks
 * Description: Disables comments and pingbacks for all post types sitewide, removes comments section from admin, and prevents comments from being submitted.
 * Version: 1.0
 * Author: Terrance Orletsky / ChatGPT
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Disables comments and pingbacks for all post types.
 *
 * @return void
 */
function em_disable_comments_and_pingbacks() {
    // Get all post types
    $post_types = get_post_types( array( 'public' => true ), 'names' );

    // Exclude attachments from disabling comments and pingbacks
    unset( $post_types['attachment'] );

    // Loop through each post type and disable comments and pingbacks
    foreach ( $post_types as $post_type ) {
        // Disable support for comments and trackbacks
        remove_post_type_support( $post_type, 'comments' );
        remove_post_type_support( $post_type, 'trackbacks' );
    }

}//end em_disable_comments_and_pingbacks()


add_action( 'init', 'em_disable_comments_and_pingbacks' );

/**
 * Removes comments section from admin.
 *
 * @return void
 */
function em_remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );

}//end em_remove_comments_admin_menu()

add_action( 'admin_menu', 'em_remove_comments_admin_menu' );

/**
 * Prevents comments from being submitted.
 *
 * @param [type] $comment_data The comment data.
 * @return String If the post type does not support comments, prevent comment from being submitted
 */
function em_prevent_comment_submission( $comment_data ) {
    // Check if the post type supports comments
    $post_type = get_post_type( $comment_data['comment_post_ID'] );
    if ( ! post_type_supports( $post_type, 'comments' ) ) {
        // Prevent comment from being submitted
        wp_die( 'Comments are closed.', '', array( 'response' => 403 ) );
    }

    return $comment_data;

}//end em_prevent_comment_submission()


add_filter( 'preprocess_comment', 'em_prevent_comment_submission' );

/**
 * Disables comments on future posts and hides existing comments.
 *
 * @return void
 */
function em_disable_future_comments_and_hide_existing() {
    // Disable comments on future posts
    update_option( 'default_comment_status', 'closed' );

    // Hide existing comments
    update_option( 'comment_registration', 1 );
    update_option( 'page_comments', 0 );
    update_option( 'comments_per_page', 0 );
    update_option( 'default_ping_status', 'closed' );
    update_option( 'default_pingback_flag', 0 );

}//end em_disable_future_comments_and_hide_existing()


add_action( 'admin_init', 'em_disable_future_comments_and_hide_existing' );
