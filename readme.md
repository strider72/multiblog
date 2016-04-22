# Virtual Multiblog

### for WordPress

#### a.k.a. "Strider's Modified Mertner Method Multiblog"

Home Page: http://striderweb.com/nerdaphernalia/features/virtual-multiblog/  
DONATE: https://paypal.me/SteveRider

Virtual Multiblog allows you to run more than one blog off a single install of
WordPress. Each blog is managed as a completely separate install -- separate
admin sections, separate users, etc. They will happily co-exist with different
themes and plugins activated. The biggest advantage is that you will only have
to keep one set of program files up-to-date, and this method should be
expandable to as many different blogs as you like.

## DISCLAIMER

USE AT YOUR OWN RISK. If it blows up, or deletes your hard drive, or in any
other way does something you don't want it to do, too bad. Not my
responsibility. If you don't agree with those terms, Don't Use It.

THAT BEING SAID... I (Stephen) use it myself. I believe it works well, and if
you do have problems, I would be interested in knowing about them so I can
make improvements to the system.

## CONTENTS

  * Installation
    * Easy Setup
    * Advanced Setup
    * Giving a Blog Its Own Config File (Optional)
      * Determining VUSER
    * Automatically Activate Plugins
    * Custom File Locations (Optional)
    * Easy Subdomains (Optional)
    * Creating Symbolic Links
  * New Functions
  * Using Custom Permalinks
  * The Future
  * Contribute
  * History/ Acknowledgements

## Installation

First I'll give a basic overview of how this works. WordPress will be
installed normally in one directory, and additional blogs will be created by
pointing other domains, subdomains, or "directories" at that one install.
"Directories" in this case are actually Symbolic Links (or "symlinks"), which
are a type of secondary representation of a directory -- somewhat similar to a
Mac alias or a Windows shortcut. There is a single change to the WordPress
code to make this all work: a modified configuration file that loads a
different configuration depending on what directory or domain it thinks it's
in. Everything else is stock standard WordPress, which makes this method
_extremely_ compatible with existing plugins, themes, and add-ons of all
kinds.

### Easy Setup

CRITERIA -- You can use easy setup if all blogs' data will be held in a single
database.

For these directions, we'll use an example setup. We are going to set up four
blogs, at http://www.catblog.com/, http://www.dogblog.com/,
http://mutts.dogblog.com/, and http://www.catblog.com/persians/

(NOTE: As of version 2.4 the plugin is no longer needed.)

  1. Install the WordPress files as usual.
  2. Place the "multiblog" folder in the `wp-content/` directory.
  3. Move the `wp-config.php` file from "multiblog" to the directory in which you installed WordPress.
  4. In the `wp-content/multiblog/config/` folder, rename `mb-autoconfig-sample.php` to `mb-autoconfig.php`. Open `mb-autoconfig.php` in a text editor. Input your database login information.
  5. Set up whatever symbolic links, subdomains, or domains you wish so they all point to the directory with WordPress installed. Note: redirects and "parking" will not work for domains -- the domains must be set up directly on the server. (In Apache that probably means "[Virtual Hosts](http://httpd.apache.org/docs/1.3/vhosts/)".)
  6. In this example:
    * WordPress is installed in the root directory. Catblog.com, dogblog.com, and mutts.dogblog.com are pointed to that root directory. We then create a symlink called "persians" in the root directory that points back to the root directory.
    * Yes, this means that dogblog.com/persians and mutts.dogblog.com/persians are also active blogs. To prevent this, you would have to use the Advanced Setup described below. This is a pretty catch-all example, but in Real Life™ **I do not recommend mixing directory blogs with multiple domains using Easy Setup**. If there is only one domain pointed to these directories, then this is not an issue.
  7. With your web browser, go to each blog and set things up as normal through WordPress.

You're done! Told you it was easy!

### Advanced Setup

Advanced setup allows for a bit more security, and allows for a default
"fallback" blog. In the future, advanced setup will hopefully allow for
advanced template functions such as calling a list of links to all blogs in
the multiblog setup.

For this example, we're going to create http://www.catblog.com/,
http://www.dogblog.com/, http://mutts.dogblog.com/,
http://www.dogblog.com/fido/, and http://www.catblog.com/celebrities/morris/

  1. Follow Steps 1-4 from the Easy Install.
  2. In `wp-content/multiblog/config/`, rename `mb-users-sample.php` to `mb-users.php`. Open `mb-users.php` in a text editor. Populate `$vusers[]` according to the instructions there. For this example we have:  

    $vusers[] = 'catblog.com';  
    $vusers[] = 'dogblog.com';  
    $vusers[] = 'mutts.dogblog.com';  
    $vusers[] = 'catblog.com/celebrities/morris';  
    $vusers[] = 'dogblog.com/fido';  

    Save and close the file

    CAUTION: You could simply set that last one to `$vusers[] = 'fido';` (ditto `'celebrities/morris'`), but that would open us up to the same issue described in Easy Setup: catblog.com/fido would also be an active blog! If mixing domains and directories, set specific $vusers! Again, this is only an issue if using both multiple domains _and_ directories.

  3. For this example:
    * Set up the two domains, and the "mutts.dogblog" subdomain, to point to the same root directory.
    * In the site's root directory, create a symbolic link to the root directory, and call it "fido". (More information on symbolic links can be found below).
    * In the site's root directory, create a folder called "celebrities". Inside that folder, create a symbolic link to the root directory, and call it "morris".
  4. With your web browser, go to each blog and set things up as normal through WordPress.

### OPTIONAL: Giving a blog its own config file

Any blog using this setup can have its own completely separate config file.
You can do this if, for example, you want to store a blog's data in a separate
database. _You can do this for just one blog, all of them, or none of them._

Go into the `wp-content/multiblog/config/` directory and make a copy of `mb-
config-sample.php`. Rename it to `mb-config-VUSER.php` (See below for how to
determine the VUSER.) Open that file in a text editor and set the database
information and, optionally, the `$table_prefix`. If you use the same database
for multiple blogs, you _must_ set a different `$table_prefix` for each. If
you don't set `$table_prefix`, it will be auto-configured.

**IMPORTANT**: You should note that, as of VMB 2.5, both autoconfig and the blog-specific config are called, in that order. Anything set in the blog specific file will override a similar settings from the autoconfig. Because of this, you can set default settings in autoconfig and then _just_ set the changes in the blog specific files. This is very handy if, for example, the only configuration difference between blogs is the table prefix or language.

In the new config files, instead of defining CONSTANTs directly, we now assign
them via `$vmb_const[]`. This is what allows the override I just described --
PHP doesn't let you re-define a constant, but the VMB system runs through all
the config files and then takes anything in `$vmb_const[]` and assigns it as a
constant according to its last value. Yes, that means that you can set ANY
constant this way in your config files.

#### Determining VUSER

If you use the "Easy Setup" method, then your VUSER is the domain or
subdomain, plus directory if any, except that all non-alphanumeric characters
are replaced with an understroke ('_'), and any 'www.' is removed. (Non-
alphanumeric = anything not a letter or a number.)

If you set up the `$vusers[]` array in `mb-users.php`, then the current VUSER
for a blog is whatever that value is, cleaned up as described in the previous
paragraph.

For example: With "`mutts.dogblog.com`", the VUSER is "`mutts_dogblog_com`".
For "`www.catblog.com/morris`", the VUSER is "`catblog_com_morris`" and the
config file is `mb-config-catblog_com_morris.php`. (Note the difference
between dashes and understrokes!)

### OPTIONAL: Automatically Activate Plugins

_Requires WordPress 2.8 or higher._

You can set certain WordPress plugins to be activated automatically in all (or
some) blogs. The plugins should be placed in the regular WP plugins directory.
In a config file, create a list of the plugin files you want auto-activated,
adding them to the `$vmb_auto_plugins` array. The plugin files should be
listed relative to the `plugins folder`. For example:

    $vmb_auto_plugins[] = 'hello.php';
    $vmb_auto_plugins[] = 'akismet/akismet.php';

**Important:** There is one additional piece of setup required for this to work. In the `multiblog` folder there is a folder named `mu-plugins`. Move the `mu-plugins` folder into the `wp-content` folder. You should end up with `/wp-content/mu-plugins/vmb-plugins-bootstrap.php`

Note that this is a complete override of regular WordPress plugin activation.
That is, these plugins cannot be turned off via the normal WordPress
interface. To turn them off, remove them from the `$vmb_auto_plugins[]` array.
They should function normally in all other ways.

### OPTIONAL: Custom File Locations

You can change the location of multiblog folder as well as the your config
folder. Open up the `wp-config.php` file and follow the instructions therein.

Changing the location of the config folder has a couple advantages:

  * You can increase security by moving your database login information out of the web-accessible directories
  * It makes updates easier because your configuration files won't be overwritten when you replace an older `multiblog` folder.

If you are setting a custom wp-content folder via the standard WordPress
constant `WP_CONFIG_DIR`, **you must set `VMB_DIR`**.

### OPTIONAL: Easy Subdomains (Advanced Setup only)

Let's say you have a whole bunch of blogs you want to put on subdomains of
dogblog.com. You can save yourself some typing by setting `$mydomain =
'dogblog.com';` in the `mb-users.php` file. If that is set, you can add
subdomain blogs by name alone, e.g. `$vusers[] = 'mutts';` would work for both
`http://mutts.dogblog.com/` and `http://www.dogblog.com/mutts/`. Mind you,
both of those would be the same blog with the same WordPress tables (and note
the caution in Step 3 of Advanced Setup). You can't set two different domains
to `$mydomain`.

### Creating Symbolic Links

Sorry -- this section isn't complete yet. The simplest solution for the non-
techie is to ask your hosting provider to do it for you. :)

Slightly longer answer -- there is no simple set of instructions, as setting
up Symbolic Links can be different depending on your Operating System, disk
filesystem, PHP version or configuration, server configuration, and so forth.
Nonetheless, I will try to get some pointers up at some point.

Let me put it another way. **_I_** don't make my own symlinks -- I call my web
host, Mike. However, on my test site (running on my OS X laptop via
[MAMP](http://www.mamp.info/)) I have a utility called
[Cocktail](http://www.maintain.se/cocktail/) that makes them for me.

If you are on a Unix-like system (including Linux or OS X) you can probably
open a Terminal window and use the `ln` command.

## New Functions

The following are stable and can be called in templates or plugins:

constant `VUSER`
    * The current virtual user. This is handy for creating Themes, for example, as you can call different template files for each VUSER.

function `get_virtual_user( $clean )`
    * Returns the current virtual user. Set `$clean` to TRUE to get a "filename friendly" version -- with dots, slashes, etc. converted to underscores.

function `$vmb->get_bloginfo( $show )`  
The following are recognized for `$show`:
    * `'config'`: the config file being used. Setting second parameter to `true` will return the entire server path as well
    * `'vuser'`: deprecated. Use `get_virtual_user()` instead.

function `$vmb->get_sysinfo( $show )`  
The following are recognized for `$show`:
    * `'configpath'`: the server path to the configuration files.
    * `'diagnostics'` (other parameters: `$html`, `$override`): returns diagnostic info to help with troubleshooting. By default, wraps result in `<pre>` tags; set `$html = false` to return the raw string. IMPORTANT: By default this function returns _nothing_ unless `$vmb_diagnostics` is set to `true` in `wp-config.php`. This means you can leave it in a template and only have it show when needed. If you want to show it always, set `$override` to `true`.
    * `'version'`: the version of the Virtual Multiblog system.

## Using Custom Permalinks

If you are just using domain or subdomain-based addressing, the default
WordPress rewrites will work just fine. If you are using directory-based
blogs, and you want to use "pretty" permalinks, you will need to make some
changes to your `.htaccess` file:

Open up your `.htaccess` in a text editor. Delete everything between "# BEGIN
WordPress" and "# END WordPress". Above the "# BEGIN WordPress", insert the
following (putting in your blog names of course)...

    <IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} (/cat|/dog)?/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . %1/index.php [L]
    </IfModule>

... and then **set permissions on the `.htaccess` file so that WordPress can
_not_ overwrite this**. For additional blogs, simply add the symbolic links
inside the parentheses on the third line like so:
`(/cat|/dog|/bird|/lizard)?/`

## The Future

I have a number of plans for down the road. First off is figuring out a way
for the system to "register" all the blogs running off a particular setup.
(That is, enable your setup to keep track of all your sites.) Once that
happens, I can do a lot of neat things, such as WordPress functions that list
all blogs, or allowing a single login that spans multiple sites/blogs.

I am also constantly seeking ways to further smooth the install and
configuration process. For example: once I get the registration system set up,
I may also be able to automate the mod_rewrite stuff, so that WordPress's
"pretty permalinks" will work without manual modifications to the .htaccess
file.

I also plan on figuring out a way to automate update notifications, and soon
the Plugins page may just have a row for VMB. (Even though it's not
technically a plugin, I think it would be nice to have the info, version, and
links on the Plugins page.)

All this will take time, of course. That why it's "the future" ;). But I do
use the system myself, and I would like to see all of these ideas realized.

## Contribute

_Was this download worth something to you?_

I've put a lot of time and effort into making Virtual Multiblog work well. I
use it myself, and I'm gratified that so many others have found it useful. If
you would like to support my efforts, (and encourage me to code faster!)
please consider making a donation. The suggested donation for use of this
system is $20-25, but every bit helps, and even a dollar or two is
appreciated.

## History/ Acknowledgements

###    v 2.7-dev-2014-08-24
    * Fixed plugin description - removed "If you see this something's wrong" message since WP now shows the MU plugins in admin
    * Renamed $vmb->get_virtual_user() to $vmb->get_vuser() to avoid confusion with (not in a class) get_virtual_user()
    * Get rid of eval() for tests within $vmb->get_vuser()
    * Increase efficiency of $vmb->get_vuser() -- reduces calls to $vmb->clean_server()
    * NEW $valiases array -- alpha at this point -- test but don't use in production!
    * Minor code cleanup
###    v 2.6.2 (28 September 2009)
    * Restores ABSPATH define in wp-config.php. Some plugins (improperly) call wp-config.php directly & we should try not to break those.
    * Fixes "PHP bad offset" bug. props Hackadelic

###    v 2.6.1 (23 July 2009)
    * Moved plugin_action_links hook to vmb-plugins.php
    * Wrapped all plugin hook stuff in is_array($vmb_auto_plugins) check
    * Include_once() mb-autoconfig and mb-user instead of require_once()
    * Removed some file_exists checks in favor of @include()
    * Minor usage comment changes
    * Removed ABSPATH define. Should never be needed and could cause problems if present.
    * fixed broken link/ID to auto-plugins section
    * Specified WP 2.8+ for auto plugins
    * clarified use notes
    * Added NONCE_KEY define
    * Renamed "ozh_menu_icon.png" to "menu_icon.png"
    * Added prelim "database switch" code (not yet implemented though)
    * vmb-plugins.php is now just a bootstrap, calling a function in vmb-core.php
    * Minor improvement to vmb->get_sysinfo
    * vmb-plugins-bootstrap.php now checks if it's running under VMB before doing anything

###    v 2.6 (28 May 2009)
    * Added auto-plugin functionality via $vmb_auto_plugins[] array
    * New file vmb-plugin-bootstrap.php
    * New file resources/vmb-plugins.php
    * Removed wp-config-vmb.php -- that code is now at the bottom of wp-config.php
    * Moved admin footer out of vmb-functions.php
    * Setting $vmb_core_only now prevents entire vmb_functions.php from loading
    * Consolidated vmbp() and vmbp_init()
    * Improved get_vmb_url() and get_wp_content_url()
    * Relocated mod_rewrite stuff to "this stuff is broken" area
    * Improved str_deslash (removes backslashes)
    * The usual minor cleanup

###    v2.5 (17 Nov 2008)
    * Significant improvements to the code that determines the VUSER.
    * Fully WordPress 2.6 compatible (can handle moving wp-content directory)
    	* tested on WordPress 2.7 beta 1
    * Can specify defaults in autoconfig and override in later config file
    * New VMB_DIR constant
    * New VMB_URL constant
    * ***Experimental*** New VMB_ACCEPT_REDIRECTS constant
    * Stripped wp-config.php -- functions moved to wp-config-vmb.php or multiblog/resources/ for easier upgrades
    * Fixed readme to reflect lack of plugin
    * Added WP_SITEURL and WP_HOME to diagnostics
    * Added "Ohz' Admin Drop Down Menu" icon
    * Improved handling of slashes on paths and vusers
    * Improved handling of missing $rootpath in $vmb->get_virtual_user();
    * "Plugin" functions can be turned off by setting $vmb_core_only in wp-config.php

###    v2.4 (9 July 2008)
    * Fixed two recent bugs in get_virtual_user() that could prevent VUSER from being determined correctly (Thanks, David Mohr)
    * Eliminated plugin.  That functionality now built in

###    v2.3 (12 June 2008)
    * Updated admin for WP 2.5:
    	- added direct link from plugins page
    	- added footer
    * Code cleanup and abstraction -- e.g. most functions called via "$this->"
    * Added $vmb->get_plugin_data()

###    v2.2.3 (22 April 2008)
    * BUGFIX: If another plugin called wp-config.php directly, VUSERS came up wrong 
        (thank you "Pozmu.net" for the catch _and_ the fix.)
    * Fixed typos (duh) in sample configuration files
    * Added SECRET_KEY define to sample configuration files, per changes to 
        the standard wp-config.php in WordPress 2.5

###    v2.2.2 (7 April 2008)
    * BUGFIX: Security hole introduced in 2.2.  VUSER detection should key 
        off of $_SERVER['SERVER_NAME'] instead of $_SERVER['HTTP_HOST'];

###    v2.2.1 (31 March 2008)
    * All default config files now end with "-sample.php" so that upgrades 
        don't overwrite existing configurations.

###    v2.2 (29 March 2008) 
    * Most information calls abstracted through get_*info functions
    * Class declared as global $vmb.  Internal calls abstracted through $this->
    * BUGFIX: get_virtual_user() -- line to get dirname( $rootpath ) was missing

###    v2.1.2 (never released):
    * Moved most functions into vmb class
    * Added MySQL and PHP versions to vmb::diagnostics output
    * Other minor changes to vmb::diagnostics

###    v2.1.1 (16 December 2007):
    * SECURITY FIX: Multiblog Options panel was accessible to all user 
        levels.  Now requires 'manage_options' capability

###    v2.1 (15 Dec 2007):
    * Blogs can be in subdirectories, e.g. 
    	http://example.com/blogs/cat/ and http://example.com/blogs/dog/
    * Changed all mb_ naming to vmb_ (functions and variables) to avoid 
    	confusion with PHP multi-byte functions
    * vmb_diagnostics allows override
    * New function: vmb_get_homepage()
    * New function: vmb_get_bloginfo()
    * code abstraction and cleanup
    * new support plugin
    * Plugin: Added basic Options page -- currently shows diagnostic info and homepage link
    * Tightened replace in vmb_clean_vuser() for better security
    * If somebody calls vmb_get_virtual_user(), it just returns VUSER 
    	instead of re-calculating (unless an $altroot is passed)
    * Re-added get_virtual_user() function for backwards compatibility 
    	with original Mertner version

###    v 2.0 (21 Nov 2007):
		Hugely improved ease of setup and improved (i.e. fixed) usability 
		with domains and subdomains.  Ability to auto-configure all, some, 
		or none of the blogs, adding great flexibility between 
		standardized or custom configurations on a blog-to-blog basis. 
		Also added user-accessible -- and stable -- functions, and the VUSER 
		constant.

###    v 1.1:
    *	New Owner: Stephen Rider
    *	Extensive reorganization, code cleanup, and full documentation.
    	Among other improvements, the whole package became self-contained 
    	within the wp-content directory.

###    v 1.0:
	*	The basic symlink methodology was created by [Allan Mertner](http://www.mertner.com/allan/index.php?p=15).

Many of the improvements to version 2 were based upon or inspired by
commenters to my blog. To them, and to everyone who has sent comments, I am
grateful.

Good luck. Have fun.

Stephen Rider

http://striderweb.com/

