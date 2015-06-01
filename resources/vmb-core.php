<?php
//	Virtual Multiblog for WordPress
//	a.k.a. "Strider's Modified Mertner Method Multiblog"
//	Please see multiblog-readme.txt for more info.

/*
	TO DO:	System for listing all virtual blogs (register in DB table?)
		TO DO:	finish mod_rewrite functions
		TO DO:	Universal login!!!
		TO DO:	Print page list that spans virtual installs
*/

if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

class vmb {

	function get_bloginfo( $show ) {
		$param = func_get_args();

		switch( strtolower($show) ) {
			case 'config' :
				// returns config file name
				// if $param[1] == true also returns server path
				// $param[2] = script name/path
				$output = $this->get_config_file( $param[1], $param[2] );
				break;
			case 'vuser' :
				// if using in a filename, set $param[1] to 'clean'
				if ( 'clean' != $param[1] && true !== $param[1] )
					$output = VUSER;
				else
					$output = $this->clean_vuser();
				break;
		}
		return $output;
	}

	function get_sysinfo( $show ) {
		$param = func_get_args();

		switch( strtolower($show) ) {
			case 'configpath':
					$output = VMB_CONFIG_DIR;
				break;
			case 'diagnostics':
				global $vmb_diagnostics;
				$output = $vmb_diagnostics;
				break;
			case 'title' :
				$output = $this->get_sysinfo('URI', true, 'Virtual Multiblog' );
				break;
			case 'uri' :
			case 'url' :
			// usage: $vmb->get_sysinfo('URI', bool html_link, str 'link_text', str 'title_attribute' )
				$address = 'http://striderweb.com/nerdaphernalia/features/virtual-multiblog/';
				$param[1] = isset( $param[1] ) ? true : false;
				if ( $param[1] == false ) {
					$output = $address;
				} else {
					$linktext = isset( $param[2] ) ? $param[2] : $address;
					$titletext = isset( $param[3] ) ? ' title="' . $param[3] . '"' : '';
					$output = '<a href="' . $address . '"' . $titletext . '>' . $linktext . '</a>';
				}
				break;
			case 'version' :
				$output = $this->sysinfo['version'];
				break;
			case 'date' : 
				$output = $this->sysinfo['date'];
				break;
		}
		return $output;
	}

//****************************************************//
//      Don't directly call the functions below       //
//           They may change in the future.           //
//  Use $vmb->get_bloginfo() and $vmb->get_sysinfo()  //
//****************************************************//

	// The Virtual Multiblog equivalent of a plugin header
	var $sysinfo = array(
		'version' => '2.7-dev',
		'date' => '2014-08-24'
	);

	// This function is the heart of the system
	function get_virtual_user( $rootpath = null ) {
	// NOTE: Don't confuse get_virtual_user() with $vmb->get_virtual_user()
	// get_virtual_user() is at the very bottom of this file

	// You can pass a different $_SERVER['SCRIPT_NAME'] string to $rootpath
	// If you're not doing that, use VUSER constant instead of calling this function

		if ( $rootpath == null ) {
			if ( defined( 'VUSER' ) ) {
				return VUSER;	// No need to repeat work!
			} else {
				$rootpath = $_SERVER['SCRIPT_NAME'];	// should never happen
//				error_log( '$vmb->get_virtual_user() -- $rootpath undefined' );
			}
		}

		if ( substr( $rootpath, -4 ) == '.php' )
			$rootpath = dirname( $rootpath );
		$rootpath = $this->str_deslash( $rootpath );
		$rootpath_dirs = explode ( "/", $rootpath);
		$rootpath = '';

		$app_dirs = array( 'wp-admin', 'wp-content', 'wp-includes' );
		foreach ( $rootpath_dirs as $dir ) {
			foreach ( $app_dirs as $app_dir ) {
				if ( $dir == $app_dir ) {
					break 2;
				}
			}
			$rootpath .= '/' . $dir;
		}
		$rootpath = $this->str_deslash( $rootpath );
		global $vusers;
		$server = $_SERVER['SERVER_NAME'];

		if ( $vusers[0] ) { // if $vusers[] has data
			global $mydomain;
			$usercount = count( $vusers );
			$tests = array();

			// Compare each $vuser[] to a series of grep tests.  First match wins!
 			// $curruser == http://***example.com/whatever***/ // 1
			$tests[] = '$server . \'/\' . $rootpath == $curruser';

			if ( defined( 'VMB_ACCEPT_REDIRECTS' ) && VMB_ACCEPT_REDIRECTS ) {
				$server = $_SERVER['HTTP_HOST'];
				if ( strpos( $server, ':' ) )
					$server = substr( $server, 0, strpos( $server, ':' ) );
			} else {
				// This test won't run if allowing redirects
				// $curruser is in http://example.com/***whatever*** // 2
//				$tests[] = 'stristr( $_SERVER[\'PHP_SELF\'], \'/\' . $curruser . \'/\' )';
				$tests[] = 'strpos( $_SERVER[\'PHP_SELF\'], \'/\' . $curruser . \'/\' ) === 0';
			}

			// $curruser is in http://***example.com***/ // 3
//			$tests[] = 'stristr( $shortserver, $curruser )';
			$tests[] = '$server == $curruser';
			// $curruser is in http://***whatever***.$mydomain/ // 4
//			$tests[] = 'stristr( $shortserver, $curruser . \'.\' . $mydomain )';
			$tests[] = '$server == $curruser . \'.\' . $mydomain';
			// first $test checks every vuser[], then the next $test runs....

			$server = $this->clean_server( $server );

			foreach( $tests as $test ) {
				foreach( $vusers as $curruser ) {
					$curruser = $this->clean_server( $curruser );
					if( eval( 'return ' . $test . ';' ) ) {
						$vuser = $curruser;
						break 2;
					}
				}
			}
			// If we can't figure it out, return the first $vuser on the list 
			if ( ! isset( $vuser ) ) $vuser = $this->clean_server( $vusers[0] );
		} else { // if $vuser[] _doesn't_ have data
			$vuser = $this->clean_server( $server . '/' . $rootpath );
		}

		if ( ! defined( 'VUSER' ) ) {
			define( 'VUSER', $vuser );
		}
		return $vuser;
	}

	function get_config_file( $fullpath = false, $rootpath = null, $include_missing = false ) {
		if ( ! defined( 'VUSER' ) ) {
			$this->get_virtual_user( $rootpath );
		}

		$configfile = 'mb-config-' . $this->clean_vuser() . '.php';
		if ( $include_missing || file_exists( VMB_CONFIG_DIR . '/' . $configfile ) ) {
			if ( $fullpath ) {
				return VMB_CONFIG_DIR . '/' . $configfile;
			} else {
				return $configfile;
			}
		}
		return false;
	}

/*	function load_config( $set_const = true ) {
		global $vmb_const;
		global $vusers;
		global $table_prefix;
		if ( file_exists( VMB_CONFIG_DIR . '/' . 'mb-users.php' ) )
			include_once( VMB_CONFIG_DIR . '/' . 'mb-users.php' );
		require_once( VMB_CONFIG_DIR . '/' . 'mb-autoconfig.php' );
		if ( $file = $this->get_bloginfo( 'config', true, $_SERVER['SCRIPT_NAME'] ) )
			require_once( $file );

		if ( $set_const ) {
			// set constants from config variables...
			foreach ( (array) $vmb_const as $key => $value ) {
				if ( ! defined( $key ) ) {
					define( $key, $value );
				}
			}
			unset( $vmb_const );
		}
	}
*/

	function clean_vuser( $string = VUSER ) {
	// replaces anything not alpha-numeric or understroke '_' with understroke, and converts all to lowercase
		$string = preg_replace('/[^a-z0-9_]/', '_', strtolower( $string ) );
		return $string;
	}

	function clean_server( $server = null ) {
		if( ! $server ) $server = $_SERVER['SERVER_NAME'];
		$server = strtolower( $server );
		$server = $this->str_deslash( $server );
		if( strpos( $server, 'www.' ) === 0 )
			$server = substr( $server, 4 );
		return $server;
	}

	function str_deslash( $string, $end = 'both' ) {
		switch( $end ) {
			case 'both' :
				$string = trim( $string, '/\\' );
				break;
			case 'start' :
				$string = ltrim( $string, '/\\' );
				break;
			case 'end' :
				$string = rtrim( $string, '/\\' );
				break;
		}
		return $string;
	}

	function config_NF( $autoconfig ) {
		$message = '<html>
		<body>
			<h1>Oh No!</h1>
			<p>I can\'t find a configuration file for this blog.  I can use either <code>mb-config-' . $this->get_bloginfo( 'vuser', 'clean' ) . '.php</code> or <code>' . $autoconfig . '</code></p>
			<p>Need more help? First, try the <code><a href="wp-content/multiblog/multiblog-readme.htm">multiblog-readme.htm</a></code> file.  Note that while <code>wp-config.php</code> belongs in the root blog directory, the blog-specific config file goes in the <code>wp-content/multiblog/</code> directory.</p>
			<p>Beyond that, you might try the <a href="http://wordpress.org/docs/faq/#wp-config">Wordpress help files</a>.</p>' . $this->diagnostics() . '
		</body>
	</html>';
		return $message;
	}

	function diagnostics( $html = true, $override = false ) {
		// $html == true wraps the result in <pre> tags
		// $override == true means ignore the status of $vmb_diagnostics

		if ( $override || $this->get_sysinfo( 'Diagnostics' ) ) {
			global $wp_version;
			global $wpdb;

			$clean_vuser = $this->get_bloginfo( 'vuser', true );

			if ( ! $wp_version )
				include_once( ABSPATH . 'wp-includes/version.php');

			if ( $wpdb )
				$mysql_version = $wpdb->get_var( 'SELECT VERSION() AS version' );
			else
				$mysql_version = '--';

			if ( defined( 'WP_SITEURL' ) )
				$wp_siteurl = WP_SITEURL;
			else
				$wp_siteurl = '--';

			if ( defined( 'WP_HOME' ) )
				$wp_home = WP_HOME;
			else
				$wp_home = '--';

			$return  = '
Virtual Multiblog Diagnostics
   Version................... ' . $this->get_sysinfo( 'Version' ) . '
   VUSER..................... ' . VUSER . '
   "Clean" VUSER............. ' . $clean_vuser . '
   VMB_DIR................... ' . VMB_DIR . '
   VMB_URL................... ' . VMB_URL . '
   Config File............... ' . $this->get_bloginfo( 'Config' ) . '
   WordPress version......... ' . $wp_version . '
   ABSPATH................... ' . ABSPATH . '
   WP_SITEURL.................' . $wp_siteurl . '
   WP_HOME....................' . $wp_home . '
   PHP version............... ' . PHP_VERSION . '
   MySQL version............. ' . $mysql_version . '
   $_SERVER[\'SERVER_NAME\']... ' . $_SERVER['SERVER_NAME'] . '
   $_SERVER[\'PHP_SELF\']...... ' . $_SERVER['PHP_SELF'] . '
   $_SERVER[\'HTTP_HOST\']..... ' . $_SERVER['HTTP_HOST'] . '
   $_SERVER[\'SCRIPT_NAME\']... ' . $_SERVER['SCRIPT_NAME'] . '
   $_SERVER[\'DOCUMENT_ROOT\']. ' . $_SERVER['DOCUMENT_ROOT'] . '
End Diagnostics
';
			if ( $html == true ) {
				$return = '<pre>' . $return . '</pre>';
			}
		} else {
			$return = '';
		}
		return $return;
	}

	// Add link to admin page footer
	function admin_footer() {
		echo ( '<p>' . $this->get_sysinfo('Title') . ' ' . $this->get_sysinfo('Version') . '</p>' );
	}

//*********************************
//  Auto-active plugin functions
//*********************************

	function auto_plugin_hooks() {
		global $vmb_auto_plugins;
		if( is_array( $vmb_auto_plugins ) ) {

			// add plugins to active_plugins array when called...
			add_filter( 'option_active_plugins', array( &$this, 'filter_get_active_plugins' ) );
			add_filter( 'transient_active_plugins', array( &$this, 'filter_get_active_plugins' ) ); // for cached data

			// ...but don't save it to the database
			add_filter( 'pre_update_option_active_plugins', array( &$this, 'filter_set_active_plugins' ) );

			// remove the default action links from plugin admin page for these plugins
			add_filter( "plugin_action_links", array( &$this, 'clear_plugin_action_links' ), 1, 2 );
		}
	}

	function filter_get_active_plugins( $data ) {
		// merge and sort arrays
		global $vmb_auto_plugins;
		if( $vmb_auto_plugins )
			$data = array_merge( (array) $data, (array) $vmb_auto_plugins );
		sort( $data );
		return $data;
	}

	function filter_set_active_plugins( $data ) {
		// remove VMB auto-plugins from array
		global $vmb_auto_plugins;
		if( $vmb_auto_plugins )
			return array_diff( (array) $data, (array) $vmb_auto_plugins );
		else
			return $data;
	}

	function clear_plugin_action_links( $links, $file ) {
		//clear out the default Action links (deactivate, edit, delete, etc.)
		global $vmb_auto_plugins;
		foreach( $vmb_auto_plugins as $plugin ) {
			if( $plugin == $file ) {
				return array();
			}
		}
		return $links;
	}

} // end of vmb class

$vmb = new vmb;

function get_virtual_user( $clean = false ) {
	if ( $clean ) {
		global $vmb;
		return $vmb->get_bloginfo( 'VUSER', 'clean');
	} else {
		return VUSER;
	}
}

?>