<?php
//	Virtual Multiblog for WordPress
//	a.k.a. "Strider's Modified Mertner Method Multiblog"
//	Please see multiblog-readme.txt for more info.

if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

if ( ! defined( 'VMB_CONFIG_DIR' ) )
	define( 'VMB_CONFIG_DIR', VMB_DIR . '/config' );

require_once( 'vmb-core.php' );

// load config files
// if ( file_exists( VMB_CONFIG_DIR . '/' . 'mb-users.php' ) )
@	include_once( VMB_CONFIG_DIR . '/' . 'mb-users.php' );

// FIX ME: allow for old-style - auto OR config ??
// if ( file_exists( VMB_CONFIG_DIR . '/mb-autoconfig.php' ) )
@	include_once( VMB_CONFIG_DIR . '/mb-autoconfig.php' );
if ( $configfile = $vmb->get_bloginfo( 'config', true ) ) {
	require_once( $configfile );
	unset( $configfile );
}

// set constants from config variables...
foreach ( (array) $vmb_const as $key => $value ) {
	if ( ! defined( $key ) && $value !== null ) {
		define( $key, $value );
	}
}
unset( $vmbconst );

if( empty( $table_prefix ) )
	$table_prefix  = 'wp_' . get_virtual_user( true ) . '_';

if ( $vmb->get_sysinfo( 'diagnostics' ) == true )
	error_log( $vmb->diagnostics( false ) );

require_once( ABSPATH . 'wp-settings.php' );

// load core "plugin-like" code
if ( empty( $vmb_core_only ) )
	include_once( 'vmb-functions.php' );

add_action( 'in_admin_footer', array( $vmb, 'admin_footer' ), 1001 );

?>