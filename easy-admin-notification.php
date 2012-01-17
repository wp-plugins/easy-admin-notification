<?php
/*
Plugin Name: Easy Admin Notification
Plugin URL: http://wpboxed.com
Description: Permits to admins to easily display notifications to users in the admin panel. Manage your notifications under appearance > Admin Notification.
Version: 1.0
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
* load CSS
----------------------------------------- */

function ean_admin_styles() {
	wp_enqueue_style('ean-admin', EAN_PLUGIN_DIR.'css/admin-styles.css');
}

if (isset($_GET['page']) && ( $_GET['page'] == 'ean-settings' ) ) {
	add_action('init', 'ean_admin_styles');
}

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
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[active]"><?php _e( 'Enable notification ?', 'ean' ); ?></label>
					</th>
					<td>
						<input type="checkbox" value="1" name="ean_settings[active]" id="ean_settings[active]" <?php if(isset($ean_options['active'])) checked('1', $ean_options['active']); ?>/>
						<span class="description"><?php _e('Check this box to enable the notification message.', 'ean'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[text]"><?php _e( 'Enter the message to display', 'ean' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="ean_settings[text]" style="width: 300px;" name="ean_settings[text]" value="<?php if(isset($ean_options['text'])) { echo $ean_options['text']; } ?>" type="text" />
						<span class="description"><?php _e('Enter the text you want to display in the notification. HTML code accepted.', 'ean'); ?> <code><?php _e('To create links use simple quotes.', 'ean'); ?></code></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[active]"><?php _e( 'Error message ?', 'ean' ); ?></label>
					</th>
					<td>
						<input type="checkbox" value="1" name="ean_settings[error]" id="ean_settings[error]" <?php if(isset($ean_options['error'])) checked('1', $ean_options['error']); ?>/>
						<span class="description"><?php _e('Check this box to display an error message. If uncheck, the message will be display as an information.', 'ean'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[hidebutton]"><?php _e( 'Display "hide button" ?', 'ean' ); ?></label>
					</th>
					<td>
						<input type="checkbox" value="1" name="ean_settings[hidebutton]" id="ean_settings[hidebutton]" <?php if(isset($ean_options['hidebutton'])) checked('1', $ean_options['hidebutton']); ?>/>
						<span class="description"><?php _e('Check this box to display a "hide button" inside the notification box.<br />Each user will have the choice to hide or not the notification', 'ean'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[pages]"><?php _e( 'Notification page(s)', 'ean' ); ?></label>
					</th>
					<td>
						<select id="ean_settings[pages]" name="ean_settings[pages]">
							<option value="all" <?php selected('all', $ean_options['pages']); ?>><?php _e( 'All pages', 'ean' ); ?></option>
							<option value="index" <?php selected('index', $ean_options['pages']); ?>><?php _e( 'Dashboard Only', 'ean' ); ?></option>
						</select>
						<span class="description"><?php _e('Choose where you want to display the notification.', 'ean'); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ean_settings[adminsonly]"><?php _e( 'Only display to Admins ?', 'ean' ); ?></label>
					</th>
					<td>
						<input type="checkbox" value="1" name="ean_settings[adminsonly]" id="ean_settings[adminsonly]" <?php if(isset($ean_options['adminsonly'])) checked('1', $ean_options['adminsonly']); ?>/>
						<span class="description"><?php _e('Check this box to display notice only to Admins', 'ean'); ?></span>
					</td>
				</tr>
			</table>
			
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

	ob_start(); 
	// Check that the user hasn't already clicked to ignore the message
	if ( ! get_user_meta($user_id, 'ean_notice_ignore') ) {
	    if($ean_options['error'] != 1 ) { ?>
	    	<div class="updated"><p>
	    <?php } else { ?>
	    	<div class="error"><p>
	    <?php }
	    echo stripslashes( $ean_options['text'] );
	    if($ean_options['hidebutton'] == 1 ) {
	    	echo ' | ';
	    	printf(__('<a href="%1$s">Hide</a>', 'ean'), get_admin_url().'?ean_notice_ignore=0');
	    } ?>
	    </p></div>
	    <?php
	    echo ob_get_clean();
	}
	
}

/* ------------------------------------------------------------------*/
/* ADMIN MESSAGE */
/* ------------------------------------------------------------------*/

if( $ean_options['active'] == 1 && $ean_options['text'] ) {

	add_action('admin_notices', 'ean_admin_notice');
	 
	function ean_admin_notice() {
	
	    global $current_user ;
	    global $ean_options;
	    global $current_screen;
	    
	   	$user_id = $current_user->ID;
			
		// Check user role and display to admins or all
		if( $ean_options['adminsonly'] == 1) {
			if ( current_user_can('manage_options') ) {
				if(  $ean_options['pages'] == 'all') {
					construct_notice();
				} else {
					if( $current_screen->parent_base == $ean_options['pages'] ) {
						construct_notice();
					}
				}
			}
		} else {
			if(  $ean_options['pages'] == 'all') {
				construct_notice();
			} else {
				if( $current_screen->parent_base == $ean_options['pages'] ) {
					construct_notice();
				}
			}
		}

	}
	 
	add_action('admin_init', 'ean_notice_ignore');
	 
	function ean_notice_ignore() {
	    global $current_user;
	        $user_id = $current_user->ID;
	        // If user clicks to ignore the notice, add that to their user meta
	        if ( isset($_GET['ean_notice_ignore']) && '0' == $_GET['ean_notice_ignore'] ) {
	             add_user_meta($user_id, 'ean_notice_ignore', 'true', true);
	    }
	}
}

/* ------------------------------------------------------------------*/
/* NOTIFICATION IS ACTIVE BUT THERE'S NO MESSAGE! */
/* ------------------------------------------------------------------*/

function ean_empty_notice() {
	global $current_user ;
    
    if( $_REQUEST[page] == 'ean-settings' ) {
    
	ob_start();
	?> 
	<div class="error"><p>
	    <?php _e("Ooop! You activated a notification but you haven't defined any message!", "ean"); ?>
	</p></div>
    <?php
    echo ob_get_clean();
    }
}

if( $ean_options['active'] == 1 && $ean_options['text'] == '') {
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