<?php
// IMPORTANT: This file does not go in the /plugins/ folder.  By default, it goes in /wp-content/mu-plugins/
// By setting this up as a WordPress MU plugin, it allows me to set hooks *before* the various plugin files are called by the base WP system.  


/*
Plugin Name: VMB Plugin Bootstrap
Version: 1.1.1

Description: This MU plugin is part of the Virtual Multiblog ("VMB") system.

Author: Stephen Rider
Author URI: http://striderweb.com/
Plugin URI: http://striderweb.com/nerdaphernalia/features/virtual-multiblog/
*/

if( defined( 'VMB_DIR' ) )
	include( VMB_DIR . '/resources/vmb-plugins.php' );

?>