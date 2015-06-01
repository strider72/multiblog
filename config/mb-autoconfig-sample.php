<?php
if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

// ** MySQL settings ** //
$vmb_const['DB_NAME'] = 'putyourdbnamehere';    // The name of the database
$vmb_const['DB_USER'] = 'usernamehere';     // Your MySQL username
$vmb_const['DB_PASSWORD'] = 'yourpasswordhere'; // ...and password
$vmb_const['DB_HOST'] = 'localhost';    // 99% chance you won't need to change this value
$vmb_const['DB_CHARSET'] = 'utf8';
$vmb_const['DB_COLLATE'] = '';

// Change each KEY to a different unique phrase.  You won't have to remember 
// the phrases later, so make them long and complicated.  You can visit
// http://api.wordpress.org/secret-key/1.1/ to get keys generated for you, 
// or just make something up.  Each key should have a different phrase.
$vmb_const['AUTH_KEY'] = 'put your unique phrase here';
$vmb_const['SECURE_AUTH_KEY'] = 'put your unique phrase here';
$vmb_const['LOGGED_IN_KEY'] = 'put your unique phrase here';
$vmb_const['NONCE_KEY'] = 'put your unique phrase here';

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
$vmb_const['WPLANG'] = '';

// Set $vmb_core_only to true if you only want "core" functionality
// $vmb_core_only = true;

// In WordPress 2.6+ you can change the location of wp-content and/or plugins
// $vmb_const['WP_CONTENT_DIR'] = '';
// $vmb_const['WP_CONTENT_URL'] = '';
// $vmb_const['WP_PLUGIN_DIR'] = '';
// $vmb_const['WP_PLUGIN_URL'] = '';

// In WordPress 2.8+ you can set certain plugins to automatically be activated 
// for all blogs by adding them to the $vmb_auto_plugins[] array.
// Specify the path relative to the plugins folder.  See the readme for 
// an important further setup step.
// NOTE this is an override -- the specific blog admin can NOT deactivate these!
// $vmb_auto_plugins[] = 'hello.php';
// $vmb_auto_plugins[] = 'akismet/akismet.php';


/* That's all, stop editing! Happy blogging. */

?>