<?php
/**
 * Plugin Name: Earthman Media Utility Functions
 * Description: PHP and WP functions for quick use
 * Version: 1.0
 * Author: Terrance Orletsky - Earthman Media
 * Author URI: https://earthmanmedia.com
 * License: MIT
 */

/**
 * Parse and log data objects/arrays/strings.
 *
 * @param string  $prefix  Displays before log data.
 * @param any     $data  Data to parse and output.
 * @param boolean $echo  Print to screen in html?.
 * @param boolean $die  Exit PHP process after?.
 * @return void
 */


/**
 * Enhanced logging function.
 *
 * Outputs data to the error log, or directly on the page, with optional integration with Query Monitor.
 *
 * @param string $prefix Prefix for the log message.
 * @param mixed  $data Data to log.
 * @param bool   $echo Whether to echo the data. Default is false.
 * @param bool   $die Whether to terminate execution after logging. Default is false.
 */
function em_log( $prefix, $data = '', $echo = false, $die = false ) {
    if ( is_object( $data ) || is_array( $data ) ) {
        $data = print_r( $data, true );
    }

    if ( $echo ) {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            $data = "\n\n~~~~~~~~~~~~~~~~~~~~~~~~~\n{$prefix}:\n{$data}\n~~~~~~~~~~~~~~~~~~~~~~~~~\n\n";
        } else {
            $data = "<div style='margin:30px 50px 0px 180px;'>{$prefix}:<pre style='text-align: left;'>{$data}</pre></div>";
        }
        echo $data;
    } else {
        $data = "\n{$prefix}:\n{$data}\n";

        // if Query Monitor is active, use it for logging, too
		do_action( 'qm/debug', $data );
		error_log( $data );

    }

    if ( $die ) {
        die( "\n\n~~~ fin ~~~\n\n" );
    }
}

/**
 * Stop heartbeat for local / lando performance boost
 *
 * @return void
 */
function em_stop_ajax_admin_heartbeat() {
	if ( strstr( site_url(), 'seattleschools.org' ) < 1 && strstr( site_url(), 'pantheonsite.io' ) < 1 ) {
		wp_deregister_script( 'heartbeat' );
	}
}
// add_action( 'init', 'em_stop_ajax_admin_heartbeat', 1 );


function em_generateCallTrace() {
	$e     = new Exception();
	$trace = explode( "\n", $e->getTraceAsString() );
	// reverse array to make steps line up chronologically
	$trace = array_reverse( $trace );
	array_shift( $trace ); // remove {main}
	array_pop( $trace ); // remove call to this method
	$length = count( $trace );
	$result = array();

	for ( $i = 0; $i < $length; $i++ ) {
		$result[] = ( $i + 1 ) . ')' . substr( $trace[ $i ], strpos( $trace[ $i ], ' ' ) ); // replace '#someNum' with '$i)', set the right ordering
	}

	return "\t" . implode( "\n\t", $result );
}



function em_array_unshift_assoc( &$arr, $key, $val ) {
	$arr         = array_reverse( $arr, true );
	$arr[ $key ] = $val;
	$arr         = array_reverse( $arr, true );
	return $arr;
}


function em_recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = em_recursive_parse_args( $value, $new_args[ $key ] );
		} else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}



function em_get_rewrites( $rules ) {
	em_log( 'Rewrite rules', $rules, 1 );
	return $rules;
}
// add_filter( 'rewrite_rules_array', 'em_get_rewrites' );



function em_get_all_multisites( &$wp_query ) {
	// extend limit of number of displayed sites beyond 100
	$wp_query->query_vars['number'] = 0;
}
add_filter( 'pre_get_sites', 'em_get_all_multisites' );


function em_format_file_size( $bytes ) {
	if ( $bytes >= 1073741824 ) {
		$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ( $bytes >= 1048576 ) {
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} elseif ( $bytes > 1 ) {
		$bytes = $bytes . ' bytes';
	} elseif ( $bytes == 1 ) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}



function generate_wpdb_prepare_placeholders_from_array( $array ) {
    $placeholders = array_map(
        function ( $item ) {
			// sanitize the item text using wp functions
			wp_kses_post( $item );
            return '\'' . $item . '\'';
        },
        $array
    );
    return join( ',', $placeholders );
}




// Hook into the REST API request for saving post to check and modify the meta fields accordingly.
add_filter( 'rest_pre_dispatch', 'em_correct_gutenberg_meta_issue', 10, 3 );

/**
 * Intercepts REST API requests to modify the meta field in the request payload.
 *
 * @param mixed           $result  Response to replace the requested version with. Can be anything a normal endpoint can return, or null to not hijack the request.
 * @param WP_REST_Server  $server  Server instance.
 * @param WP_REST_Request $request Request used to generate the response.
 * @return mixed Modified result, or null to not hijack the request.
 */
function em_correct_gutenberg_meta_issue( $result, $server, $request ) {
	$params = $request->get_json_params();

	if ( isset( $params['meta'] ) && is_array( $params['meta'] ) ) {
		// Log the intercepted meta for debugging purposes.

		if ( array_key_exists( 'footnotes', $params['meta'] ) ) {
			// Remove "footnotes" from the meta to prevent the error.
			unset( $params['meta']['footnotes'] );

			// Update the request parameters after modification.
			$request->set_param( 'meta', $params['meta'] );

		}
	}

    // Return null to let the request proceed as normal.
    return null;
}
