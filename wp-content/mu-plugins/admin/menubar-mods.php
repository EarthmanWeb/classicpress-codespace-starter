<?php
/**
 * Plugin Name: EM Customize WP Admin Menu Bar
 * Description: Removes unwanted items from menu bars to clean up WP Admin.
 * Version: 1.0
 * Author: Terrance Orletsky / ChatGPT
 */

add_action(
	'wp_before_admin_bar_render',
	function() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'customize' );
		$wp_admin_bar->remove_menu( 'comments' );
		$wp_admin_bar->remove_menu( 'updates' );
		$wp_admin_bar->remove_menu( 'wp-logo' );
		$wp_admin_bar->remove_menu( 'wpseo-menu' );
		$wp_admin_bar->remove_menu( 'wp-mail-smtp-menu' );
		$wp_admin_bar->remove_menu( 'wpforms-menu' );

		if ( ! current_user_can( 'administrator' ) ) {
			// other stuff
		}
	},
	1000
);


/**
 * Removes 'Howdy' in admin bar
 */
add_filter(
    'admin_bar_menu',
    function( $wp_admin_bar ) {
		$my_account = $wp_admin_bar->get_node( 'my-account' );
		$newtext    = str_replace( 'Howdy,', '', $my_account->title );

		$wp_admin_bar->add_node(
            array(
				'id'    => 'my-account',
				'title' => $newtext,
            )
		);
	},
    25
);



/**
 * Removes the user photo from the admin bar
 */
function sps_modify_menu_bar_userinfo() {
    ?>
	<style>
		#wpadminbar #wp-admin-bar-my-account.with-avatar #wp-admin-bar-user-actions>li {
			margin-left: 0px !important;
		}
		#wp-admin-bar-my-account > a.ab-item img,
		#wp-admin-bar-user-info > a.ab-item > img,
		#wp-admin-bar-user-info {
			display: none !important;
		}
		#wpadminbar .quicklinks .menupop ul#wp-admin-bar-user-actions li .ab-item, 
		#wpadminbar .quicklinks .menupop.hover ul#wp-admin-bar-user-actions li .ab-item {
			min-width: 70px !important;
			width: 70px !important;
			overflow-x: hidden !important;
		}

		@media screen and (max-width: 1300px) {
			#wp-admin-bar-query-monitor, #wp-admin-bar-duplicate-post {
				display:none !important;
			}
		}
		@media screen and (max-width: 900px) {
			#wp-admin-bar-det_env_type, #wp-admin-bar-view_on {
				display:none !important;
			}
		}
	</style>
    <script>
        jQuery(document).ready(function($) {
            var welcomeLink = $('#wp-admin-bar-my-account > a.ab-item');

			var checkbox = document.getElementById('adduser-noconfirmation');
			if (checkbox) {
				checkbox.checked = true;
			}			
			checkbox = document.getElementById('noconfirmation');
			if (checkbox) {
				checkbox.checked = true;
			}
			
            // Extract the display name and set it as the title of the avatar link
            var displayName = $('#wp-admin-bar-my-account .display-name').text();
            $('#wp-admin-bar-my-account > a.ab-item').attr('title', displayName);

			// remove the img inside the link
			$('#wp-admin-bar-my-account > a.ab-item img').remove();
			$('#wp-admin-bar-user-info > a.ab-item > img').remove();
        });
    </script>
    <?php
}
add_action( 'admin_head', 'sps_modify_menu_bar_userinfo' );
add_action( 'wp_head', 'sps_modify_menu_bar_userinfo' );
