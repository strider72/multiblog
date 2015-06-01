<?php
if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

// Settings will default to what is set in the autoconfig.  To change it for this
// blog, remove the '//' from the beginning of the line, and set it.  Settings
// in this file will override autoconfig for this one blog only.
// You can override *anything* set via $vmb_const[]

// You can have multiple installations in one database if you give each a unique 
// prefix.  If you don't set this, a prefix will be auto-generated.
// Only numbers, letters, and underscores please!
// Example: $table_prefix  = 'wp_';

// $table_prefix  = '';

// ** MySQL settings ** //
// $vmb_const['DB_NAME'] = 'putyourdbnamehere';    // The name of the database
// $vmb_const['DB_USER'] = 'usernamehere';     // Your MySQL username
// $vmb_const['DB_PASSWORD'] = 'yourpasswordhere'; // ...and password
// $vmb_const['DB_HOST'] = 'localhost';    // 99% chance you won't need to change this value
// $vmb_const['DB_CHARSET'] = 'utf8';
// $vmb_const['DB_COLLATE'] = '';

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
// $vmb_const['WPLANG'] = '';

// In WordPress 2.6+ you can change the location of wp-content and/or plugins
// $vmb_const['WP_CONTENT_DIR'] = '';
// $vmb_const['WP_CONTENT_URL'] = '';
// $vmb_const['WP_PLUGIN_DIR'] = '';
// $vmb_const['WP_PLUGIN_URL'] = '';

// In WordPress 2.8+ you can set certain plugins to automatically be activated 
// by adding them to the $vmb_auto_plugins[] array.
// Specify the path relative to the plugins folder.  See the readme for 
// an important further setup step.
// NOTE this is an override -- the specific blog admin can NOT deactivate these!
// $vmb_auto_plugins[] = 'hello.php';
// $vmb_auto_plugins[] = 'akismet/akismet.php';


/* That's all! Happy blogging. */

?>