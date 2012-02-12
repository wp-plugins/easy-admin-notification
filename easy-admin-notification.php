<?php
/*
Plugin Name: Easy Admin Notification
Plugin URL: http://wpboxed.com
Description: Permits to admins to easily display notifications to users in the admin panel. Manage your notifications under appearance > Admin Notification.
Version: 1.3
Author: Rémi Corson
Author URI: http://wpboxed.com
Contributors: Rémi Corson
*/

/* ----------------------------------------
* plugin text domain for translations
----------------------------------------- */

load_plugin_textdomain( 'ean', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );


/* ----------------------------------------
* define the plugin base directory
----------------------------------------- */

global $ean_base_dir;
$ean_base_dir = dirname(__FILE__);

if(!defined('EAN_PLUGIN_DIR')) {
	define('EAN_PLUGIN_DIR', plugin_dir_url( __FILE__ ));
}


/* ----------------------------------------
* Includes
----------------------------------------- */
if( $_GET['page'] == 'ean-settings' ) {
	include($ean_base_dir . '/includes/styles.php');
	include($ean_base_dir . '/includes/options.php');
	include($ean_base_dir . '/includes/functions/ean_functions.php');
	include($ean_base_dir . '/includes/scripts.php');
}


/* ----------------------------------------
* load plugin data
----------------------------------------- */

$ean_options = get_option('ean_settings');


/* ----------------------------------------
* add subpage in appearance menu
----------------------------------------- */

function ean_settings_menu() {
	// add settings page
	add_submenu_page(
					'themes.php', 
					__('Easy Admin Notification Settings', 'ean'), 
					__('Admin Notification', 'ean'),
					'manage_options', 
					'ean-settings', 
					'ean_settings_page');
}
add_action('admin_menu', 'ean_settings_menu', 100);


/* ----------------------------------------
* register the plugin settings
----------------------------------------- */

function ean_register_settings() {

	// create whitelist of options
	register_setting( 'ean_settings_group', 'ean_settings' );
}
//call register settings function
add_action( 'admin_init', 'ean_register_settings', 100 );


/* ----------------------------------------
* create the submenu links in plugins page
----------------------------------------- */

function ean_plugin_action_links($links, $file) {
    static $this_plugin;
 
    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }
 
    // check to make sure we are on the correct plugin
    if ($file == $this_plugin) {
 
		// link to what ever you want
        $plugin_links[] = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/themes.php?page=ean-settings">'.__('Notification management','ean').'</a>';
 
        // add the links to the list of links already there
		foreach($plugin_links as $link) {
			array_unshift($links, $link);
		}
    }
 
    return $links;
}
add_filter('plugin_action_links', 'ean_plugin_action_links', 10, 2);


/* ----------------------------------------
* function to retrieve the get_option() value
----------------------------------------- */

/*
function get_the_option($option_name) {
	$ean_options = get_option('ean_settings');
	return $ean_options[$option_name];
}
*/


/* ----------------------------------------
* create the settings page layout
----------------------------------------- */

function ean_settings_page() {
	
	global $ean_options;
		
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Easy Admin Notification Settings', 'ean'); ?></h2>
		<?php
		if ( ! isset( $_REQUEST['settings-updated'] ) )
			$_REQUEST['settings-updated'] = false;
		?>
		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'Options saved', 'ean' ); ?></strong></p></div>
		<?php ean_user_ignore_delete(); ?>
		<?php endif; ?>
		<form method="post" action="options.php" class="ean_options_form">

			<?php settings_fields( addslashes('ean_settings_group') ); ?>
			
			<?php ean_show_custom_tabs(); ?>

			<?php ean_show_custom_fields(); ?>
			
			<!-- save the options -->
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'ean' ); ?>" />
			</p>
			
		</form>
	</div><!--end .wrap-->
	<?php
}

/* ------------------------------------------------------------------*/
/* Delete user ignore metadata */
/* ------------------------------------------------------------------*/					
function ean_user_ignore_delete() {
   	global $wpdb;
   	
   	// Deleta ean_notice_ignore user meta for ALL users
   	$wpdb->query(
		"
		DELETE FROM $wpdb->usermeta 
		WHERE meta_key = 'ean_notice_ignore'
		"
	);
}


/* ------------------------------------------------------------------*/
/* CONTRUCTION OF THE DIV NOTICE */
/* ------------------------------------------------------------------*/

function construct_notice() {

	global $current_user ;
	global $ean_options;
	
	get_currentuserinfo();

	ob_start(); 
	// Check that the user hasn't already clicked to ignore the message
	if ( !get_user_meta($current_user->ID, 'ean_notice_ignore') ) {
	    if( $ean_options['ean_error'] != 'on' ) { ?>
	    	<div class="updated"><p>
	    <?php } else { ?>
	    	<div class="error"><p>
	    <?php }
	    echo stripslashes( $ean_options['ean_text'] );
	    if( isset($ean_options['ean_hidebutton']) && $ean_options['ean_hidebutton'] == 'on' ) {
	    	echo ' | ';
	    	printf(__('<a href="%1$s">Hide</a>', 'ean'), get_admin_url().'?ean_notice_ignore=1');
	    } ?>
	    </p></div>
	    <?php
	    echo ob_get_clean();
	}
	
}


/* ------------------------------------------------------------------*/
/* ADMIN MESSAGE */
/* ------------------------------------------------------------------*/

if( isset($ean_options['ean_active']) && $ean_options['ean_active'] == 'on' && isset($ean_options['ean_text']) ) {

	add_action('admin_notices', 'ean_admin_notice');
	 
	function ean_admin_notice() {
	
	    global $current_user ;
	    global $ean_options;
	    global $current_screen;
	    
	   	$user_id = $current_user->ID;
			
		// Check user role and display to admins or all
		if( isset($ean_options['ean_adminsonly']) && $ean_options['ean_adminsonly'] == 'on') {
			if ( current_user_can('manage_options') ) {
				if( isset($ean_options['ean_pages']) && $ean_options['ean_pages'] == 'all') {
					construct_notice();
				} else {
					if( $current_screen->parent_base == $ean_options['ean_pages'] ) {
						construct_notice();
					}
				}
			}
		} else {
			if( isset($ean_options['ean_pages']) && $ean_options['ean_pages'] == 'all') {
				construct_notice();
			} else {
				if( $current_screen->parent_base == $ean_options['ean_pages'] ) {
					construct_notice();
				}
			}
		}

	}
	
}

/* ------------------------------------------------------------------*/
/* USER IGNORE MESSAGE */
/* ------------------------------------------------------------------*/

function ean_notice_ignore() {
    
    global $current_user;
   
    get_currentuserinfo();
    
        $user_id = $current_user->ID;
        // If user clicks to ignore the notice, add that to their user meta
        if ( isset($_GET['ean_notice_ignore']) && $_GET['ean_notice_ignore'] == 1 ) {
             add_user_meta($user_id, 'ean_notice_ignore', 'true', true);
    }
}
add_action('admin_init', 'ean_notice_ignore');


/* ------------------------------------------------------------------*/
/* NOTIFICATION IS ACTIVE BUT THERE'S NO MESSAGE! */
/* ------------------------------------------------------------------*/

function ean_empty_notice() {
	global $current_user ;
	get_currentuserinfo();
    
    if( isset($_REQUEST['page']) && $_REQUEST['page'] == 'ean-settings' ) {
    
	ob_start();
	?> 
	<div class="error"><p>
	    <?php _e("Ooop! You activated a notification but you haven't defined any message!", "ean"); ?>
	</p></div>
    <?php
    echo ob_get_clean();
    }
}

if( isset($ean_options['ean_active']) && $ean_options['ean_active'] == 'on' && $ean_options['ean_text'] == '') {
		add_action('admin_notices', 'ean_empty_notice');
}


/* ------------------------------------------------------------------*/
/* UNINSTALL PLUGIN */
/* ------------------------------------------------------------------*/

function ean_uninstall () 
{
    delete_option('ean_settings');
	ean_user_ignore_delete();
}

register_deactivation_hook( __FILE__, 'ean_uninstall' );