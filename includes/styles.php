<?php

/* ----------------------------------------
* load CSS
----------------------------------------- */

function ean_admin_styles() {
	if( is_admin() ) {
		wp_enqueue_style('thickbox');
		wp_enqueue_style('ean-admin', EAN_PLUGIN_DIR.'/includes/css/admin-styles.css');
		wp_enqueue_style('jquery-ui-custom', EAN_PLUGIN_DIR.'/includes/css/jquery-ui-custom.css');
	}
}

if (isset($_GET['page']) && ( $_GET['page'] == 'ean-settings' ) ) {
	add_action('init', 'ean_admin_styles');
}

?>