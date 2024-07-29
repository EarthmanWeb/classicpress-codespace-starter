<?php
// only for wp-cli
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	// add a hook that runs before the command
	WP_CLI::add_hook( 'before_invoke:core check-update', 'cp_correct_check_update' );
}

function cp_correct_check_update() {
	// if we have ClassicPress
	if ( function_exists( 'classicpress_version' ) ) {
		$gcu =  get_core_updates();
		if ( 'latest' == $gcu[0]->{'response'} ){
			WP_CLI::success( "ClassicPress is up-to-date." );
		} else {
			WP_CLI::warning( "ClassicPress needs you: response=" . $gcu[0]->{'response'} . "." );
		};
		// then exit to prevent the core check-update command to 
		// continue his work
		exit;
	};
};
