<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'bitnami_wordpress' );

/** Database username */
define( 'DB_USER', 'bn_wordpress' );

/** Database password */
define( 'DB_PASSWORD', '12345' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', '(ToSOAe[UM/G3q1n_]EBvt,gN7mRg(XE?e}Q82)LN2iA}:=L(j1/udRl1q0s_6;;' );
define( 'SECURE_AUTH_KEY', 'I?a)aY+wE6j*[<:r1tE,{>bx,jFp_>^|V?kMBce>i{OY3#i%!W7j?oE8;@$*alCB' );
define( 'LOGGED_IN_KEY', '7fpU1_wE]i-zrUle-7p:sSa[.PhUAaas{Qk~Wi/4}jy_[pm/Fi1m{.8jqk,rEDF=' );
define( 'NONCE_KEY', 'i5`xdjyZ22FjkiNHCHwjExjWLq`1&E>0JR*FIY0WJe9<OY])%k=zk73Eroo>y8tI' );
define( 'AUTH_SALT', '//aA3i`jmj%6rls9,U[EbB#^(x=3)X?T.zXW,`KUBwsf{sgk>G9`_{j^o$_gHICr' );
define( 'SECURE_AUTH_SALT', 'B{3?VLhgStvk%+9s7NhidI}MYFLv?(Z^9pO:w#>}N/Khd@s->5OXTtpk;segXMZv' );
define( 'LOGGED_IN_SALT', 'PojH6L$Mjp>a 31=|uC_r1|Bsg-67<{gUO(fMNF<ys`U_};}+Wvv_r%jV/4{8I^U' );
define( 'NONCE_SALT', 'TOy+$B.57klckzNp=[!h+[d#t)0v4WKLIpc]|+Eu3%C[X{(@hA=_x*^wN;.How0B' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */


// Enable the Query Monitor capabilities panel.
define( 'QM_ENABLE_CAPS_PANEL', true );

// Enable WordPress debugging mode.
define( 'WP_DEBUG', true );

// Specify the location of the debug log file.
define( 'WP_DEBUG_LOG', '/bitnami/wordpress/wp-content/debug.log' );

// Enable display of debug information on the website.
define( 'WP_DEBUG_DISPLAY', true );

// Disable the fatal error handler.
define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );

// Enable script debugging.
define( 'SCRIPT_DEBUG', true );


// Ensure display_errors is set to 'On' to respect the WP_DEBUG_DISPLAY setting.
ini_set( 'display_errors', '1' );

/* Add any custom values between this line and the "stop editing" line. */



define( 'FS_METHOD', 'direct' );
/**
 * The WP_SITEURL and WP_HOME options are configured to access from any hostname or IP address.
 * If you want to access only from an specific domain, you can modify them. For example:
 *  define('WP_HOME','http://example.com');
 *  define('WP_SITEURL','http://example.com');
 */
if ( defined( 'WP_CLI' ) ) {
	$_SERVER['HTTP_HOST'] = '127.0.0.1';
}

define( 'WP_HOME', 'https://scaling-funicular-p6779vv56v3665p-80.app.github.dev' );
define( 'WP_SITEURL', 'https://scaling-funicular-p6779vv56v3665p-80.app.github.dev' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/**
 * Disable pingback.ping xmlrpc method to prevent WordPress from participating in DDoS attacks
 * More info at: https://docs.bitnami.com/general/apps/wordpress/troubleshooting/xmlrpc-and-pingback/
 */
if ( ! defined( 'WP_CLI' ) ) {
	// remove x-pingback HTTP header
	add_filter(
        'wp_headers',
        function( $headers ) {
			unset( $headers['X-Pingback'] );
			return $headers;
		}
    );
	// disable pingbacks
	add_filter(
        'xmlrpc_methods',
        function( $methods ) {
			unset( $methods['pingback.ping'] );
			return $methods;
		}
    );
}



/**
 * Custom error handler to filter out specific types of errors.
 */
function custom_error_handler( $errno, $errstr, $errfile, $errline ) {
    // Define error types to ignore
    $ignore_errors = array( E_DEPRECATED, E_USER_DEPRECATED, E_NOTICE, E_USER_NOTICE );

    if ( in_array( $errno, $ignore_errors ) ) {
        return true; // Skip the error
    }

    // Otherwise, handle the error as per normal
    return false;
}

// Set the custom error handler
set_error_handler( 'custom_error_handler' );
