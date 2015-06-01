<?php
//	Virtual Multiblog for WordPress
//	a.k.a. "Strider's Modified Mertner Method Multiblog"
//	Please see multiblog-readme.txt for more info.

if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check


class vmbp extends vmb {

	var $table_version = '0.1';
	var $option_version = '0.1';
	var $option_name = 'virtual_multiblog_data';
	var $table_name = 'wp_vmb_registry';
	var $option_bools = array();

	function vmbp() {
		$this->get_vmb_url();
		$this->get_wp_content_url();

		//if( function_exists('load_plugin_textdomain') )
		//	load_plugin_textdomain( 'vmb', VMBPATH . '/resources/languages/' );

		add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );

//		add_filter( 'mod_rewrite_rules', array(&$this, 'filter_mod_rewrite') );
	}

	function get_url_from_dir( $dir, $home = null ) {
		if ( ! $home ) $home = get_option('home');
		$parts = explode( '/', $home );
		$domain = implode( '/', array( $parts[0], $parts[1], $parts[2] ) );
		$url = $domain . str_replace( $_SERVER['DOCUMENT_ROOT'], '', $dir );
		return $url;
	}

	function get_vmb_url() {
		if ( defined( 'VMB_URL' ) ) {
			return VMB_URL;
		} else if ( defined( 'VMB_DIR' ) ) {
			define( 'VMB_URL', $this->get_url_from_dir( VMB_DIR ) );
			return VMB_URL;
		} else {
			return FALSE;
		}
	}

	function get_wp_content_url() {
		if ( defined( 'WP_CONTENT_URL' ) ) {
			return WP_CONTENT_URL;
		} else if ( defined( 'WP_CONTENT_DIR' ) ) {
			define( 'WP_CONTENT_URL', $this->get_url_from_dir( WP_CONTENT_DIR ) );
			return WP_CONTENT_URL;
		} else {
			return FALSE;
		}
	}

// abstracting l18n functions so I don't have to pass domain each time
// Also, in VMB we might be running before __() and _e() functions exist.
	function p__( $string ) {
		if( function_exists( '__' ) )
			return __( $string, 'vmb' );
		else
			return $string;
	}
	function p_e( $string ) {
		if( function_exists( '_e' ) )
			_e( $string, 'vmb' );
		else
			echo $string;
	}

	function set_defaults( $mode = 'merge', $curr_options = null ) {
	// $mode can also be set to "unset" or "reset"
		if ( 'unset' == $mode ) {
			delete_option( $this->option_name );
			return true;
		}
		
//		registration -- array( array( vmb_table_prefix, blog_key ) )
		$options = 
			array(
				'last_opts_ver' => $this->option_version,
				'groups' => array()
/*				'groups' => 
					array(
						'prefix' => 'key' // e.g. 'wp_' => 1;
					)
*/
			);
		if ( 'reset' == $mode ) {
			delete_option( $this->option_name );
			add_option( $this->option_name, $options );
		} else {
			if ( ! $curr_options ) $curr_options = get_option( $this->option_name );
			if ( $curr_options ) {
			// Merge existing prefs with new or missing defaults
				$options = array_merge( $options, $curr_options );
				$options['last_opts_ver'] = $this->option_version;
				update_option( $this->option_name, $options );
			} else {
				add_option( $this->option_name, $options );
			}
		}
		return $options;
	}

	function get_options( $refresh = false ) {
		// normally "caches" data as static.  $refresh tells it to check again
		static $options;
		if ( ! $options ) {
			$options = get_option( $this->option_name );
			if ( ! $options['last_opts_ver'] || version_compare( $this->option_version, $options['last_opts_ver'], '>' ) ) {
				$options = $this->set_defaults( 'merge', $options );
			}
		} else if ( $refresh ) {
			$options = get_option( $this->option_name );
		}
		return $options;
	}


//*********************************
//    Admin Page Functions
//*********************************

	// Call the Admin page
	function add_admin_page() {
		if ( current_user_can( 'manage_options' ) ) {
			add_submenu_page( 'index.php', $this->p__('Virtual Multiblog'), $this->p__('Multiblog'), 'manage_options', 'virtual-multiblog', array(&$this,'admin_page') );
			add_filter( 'ozh_adminmenu_icon', array( &$this, 'add_ozh_adminmenu_icon' ) );
		}
	}

	function add_ozh_adminmenu_icon( $hook ) {
		$icon = VMB_URL . '/resources/menu_icon.png';
		if ( $hook == 'virtual-multiblog' ) return $icon;
		return $hook;
	}

	// Display diagnostic info in an Admin page
	function admin_page() {
		$diagtxt = $this->diagnostics( true, true );
		$caption = $this->p__( 'Virtual Multiblog' );
		echo 
<<<EOT
	<div class="wrap">
		<h2>{$caption}</h2>
{$diagtxt}
	</div>
EOT;
/*	global $menu;
	global $submenu;
	echo "<pre>\n\$menu:\n";
	print_r($menu);
	echo "\n\n======================================================\n\$submenu:\n";
	print_r($submenu);
	echo '</pre>';
*/
	}


// ****************************************************
//   ALL CODE BELOW THIS POINT IS HIGHLY EXPERIMENTAL 
//                 (I.E. "BROKEN")
//            AND MAY EXPLODE ON CONTACT
// ****************************************************

// source: http://engine.taffel.se/2008/11/30/combining-content-from-multiple-blogs/
	function switch_db ( $pre = '' ) {
		global $wpdb, $wp_object_cache;
		static $tfl_cache, $tfl_prefix;
		if ( empty( $pre ) ) {
			if( empty( $tfl_prefix ) ) return;
			$pre = $tfl_prefix;
		}
		if ( ! isset( $tfl_cache ) )
			$tfl_cache = array();
		if ( ! isset( $tfl_prefix[$wpdb->prefix] ) )
			$tfl_prefix = $wpdb->prefix;
		if ( ! isset( $tfl_cache[$wpdb->prefix] ) )
			$tfl_cache[$wpdb->prefix] = $wp_object_cache;
		$wpdb->set_prefix( $pre );
		if ( ! isset( $tfl_cache[$pre] ) ) 
			$tfl_cache[$pre] = new WP_Object_Cache();
		$wp_object_cache = $tfl_cache[$pre];
	}
 
	function end_switch() {
		switch_db();
	}

/*
	//map the wpdb table names with the new prefix

	$blog_prefix = 'bob_';
	global $wpdb;
	$wpdb->prefix = $blog_prefix;
	$wpdb->posts = $wpdb->prefix . 'posts';
	$wpdb->postmeta = $wpdb->prefix . 'postmeta';

	//reload the query object with posts from new tables
	query_posts('showposts=5');

	//Permalinks and other post-related URLs are prepended with the value of the ‘home’ option
	//so we need to override this with the new proper value for the blog we’re pulling from
	$old_option = get_option('home');
	wp_cache_set('home', 'http://www.mysite.com/bob/', 'options');

	loop over posts…

	//Restore the original ‘home’ option so any remaining links render correctly
	wp_cache_set('home', $old_option, 'options');
	#
*/
	function get_pages( $echo = false ) {
return true; // return without doing anything
	// this get_pages is extremely ALPHA.  Doesn't really work yet.
		global $wpdb;

		$output = '';
	//wp_cache_flush();
		$wpdb->prefix = 'wp_striderweb_';
	//	echo "<p>\$wpdb->prefix = $wpdb->prefix</p>\n";
		$new_prefix = 'wp_nerdaphernalia_';
		$wpdb->posts = $wpdb->prefix . 'posts';
query_posts();
		$mbpages1 = get_pages();
		$wpdb->posts = $new_prefix . 'posts';
//	wp_cache_flush();
$old_option = get_option('home');
wp_cache_set('home', 'http://striderweb.local:8888/nerdaphernalia/', 'options');
		$mbpages2 = get_pages();
		$wpdb->prefix = $orig_prefix;
wp_cache_set('home', $old_option, 'options');

		$mbpages1 = array_merge( $mbpages1, $mbpages2 );
		$output .= '<ul>';
		foreach ($mbpages1 as $page) {
			foreach ($page as $key => $value) {
				if ($key == 'post_title') {
					$output .= '<li>' . $key . ' => ' . $value . '</li>';
				}
			}
		}
		$output .= '</ul>';

		if( $echo == true ) {
			echo $output;
			return true;
		} else {
			return $output;
		}
	/* 

	1. run get_pages()
	2. change $wpdb->prefix to 2nd blog
	3. run get_pages again   (NOPE!  Looks like I'll have to use raw SQL commands for the 2nd blog info.  get_pages() is cached somehow and returns same results twice....)
	4. append 2nd results to 1st results
	5. repeat 2-4 for remaining blogs, if any
	6. change $wpdb-> back to original value
	7. apply sorting to array per $args

	SEE:

	wp-settings L. 103-118
	post.php L. 1137
	post-template.php L. 326,308 for walk_page_tree() function
	plugin.php L. 40 for info on apply_filters
	TABLES "posts" and "postmeta"
	maybe wp-settings.php L. 105- for table info
	*/
	}


	function filter_mod_rewrite( $rules ) {
	// hard coded for now.  When I get the blog registry stuff working this will use that data
	// other than that it works fine. :)
		$rules = 
<<< EOS
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_URI} (/nerdaphernalia)?/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . %1/index.php [L]
</IfModule>
EOS;

//		error_log("mod_rewrite changed by Virtual Multiblog");  // DEBUG
		return $rules;
	}

	function print_vusers_list() {
	// Print the list of virtual users (used in index.php)
	// WARNING: print_virtualuser_list() is only vaguely functional
		global $vusers;
		$usercount = count($vusers);
		for ($i = 0; $i < $usercount; ++$i) {
			$user = $vusers[$i];
			if ($i == 0)
				$url = '/';
			else
				$url = '/' . $vusers[$i] . '/';
			echo( '<li><a href="' . $url . '">' . $user . '</a></li>' );
		}
	}

	//*********************************
	//    DB Table Functions
	//*********************************

	/*
	Need to create class: vblog
	v vuser
	v table_prefix
	v vmb_prefix (array)
	v name
	v home (URL, aka blog_home)
	v config_file (full path)
	v abspath
	v document_root
	f php_script_root = abspath - document_root

	f register
	f unregister
	f wp_func // wp function passthrough ???
		e.g. wp_func( 'nerdaphernalia, 'get_option', 'plugin_jspullquotes_settings' );

	*/

	/* We need


	New TABLE: wp_vmb_blogs
	-- table name uses variable "vmb prefix" so blogs can be grouped -- default "wp_"

		blog_key (autonumber)
		blog_name // from get_bloginfo('Name');
		blog_home // WP_HOME or get_bloginfo('URI');
		VUSER
		config_file // $vmb->get_bloginfo( 'config', true ); (full path)
		ABSPATH
		document_root // $_SERVER['DOCUMENT_ROOT']

	WP Option:
		registration -- array( array( vmb_table_prefix, blog_key ) )
	*/
	function setup_table() {
		$options = $this->get_options();

		if( ! $this->log_table_exists() || $options['last_table_ver'] != $this->table_version ) {
			$sql = 'CREATE TABLE ' . $this->table_name . ' (
				vblog_key INT AUTO_INCREMENT UNSIGNED NOT NULL,
				vblog_name TEXT,
				vblog_home TEXT NOT NULL,
				vuser TINYTEXT NOT NULL,
				config_file TEXT NOT NULL,
				abspath TEXT NOT NULL,
				document_root TEXT NOT NULL
				);';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

	function table_exists() {
		global $wpdb;
		return ( $wpdb->get_var( "SHOW TABLES LIKE '$this->table_name'" ) == $this->table_name );
	}

} // end class vmbp

	$vmb = new vmbp;

?>