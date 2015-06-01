<?php
//  Virtual Multiblog for WordPress
//  a.k.a. "Strider's Modified Mertner Method Multiblog"
//  Please see multiblog-readme.htm for more info.


// Set $vmb_core_only to true if you only want "core" functionality

// $vmb_core_only = true;


// You may (optionally) customize these here, or in individual config 
// files via $vmb_const[]

// define( 'WP_CONTENT_DIR', '' );
// define( 'WP_CONTENT_URL', '' );


// Path to multiblog directory.
// If you're changing the default wp-content location in 
// individual config files, you _MUST_ set VMB_DIR here.
// This should be somewhere within the web-accessible directories.

// define( 'VMB_DIR', '' );
// define( 'VMB_URL', '' );


// Set the location of the VMB blog config files if you don't want them 
// in the multiblog folder.  They can be located outside the web root folder
// for security.

// define( 'VMB_CONFIG_DIR', '' );


// Setting $vmb_diagnostics to true will hit your php log file with 
// several important variables every time a page is called.  This can 
// help with troubleshooting problems.

// $vmb_diagnostics = true;


// EXPERIMENTAL!  (Please email me with success/failure reports if you use this.)
// If you set this to true, the system will accept URLs redirected to it.
// To protect from hackers, this will ONLY work in conjunction with the 
// $vusers[] list.  If $vusers[] is empty, this does nothing.  Also, 
// recognition of example.com/<vuser> is turned off when you use this.

// define( 'VMB_ACCEPT_REDIRECTS', true );


// *******************************
// * DO NOT EDIT BELOW THIS LINE *
// *******************************

// Note: ABSPATH is defined in wp-load.php.  If your plugin is calling wp-config.php 
// directly, be aware that ABSPATH **may not be right** in WP 2.8 or later
if ( ! defined( 'ABSPATH' ) )
       define( 'ABSPATH', dirname( __FILE__ ) . '/' );

if ( ! defined( 'VMB_DIR' ) ) {
	defined( 'WP_CONTENT_DIR' ) ? 
	define( 'VMB_DIR', WP_CONTENT_DIR . '/multiblog' ) : 
	define( 'VMB_DIR', ABSPATH . 'wp-content/multiblog' );
}

require_once( VMB_DIR . '/resources/vmb-init.php' );

?>