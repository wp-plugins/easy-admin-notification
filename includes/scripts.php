<?php

/* ----------------------------------------
* load scripts
----------------------------------------- */
function ean_admin_scripts() {
wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-datepicker');
	

	wp_enqueue_script('ean-admin-scripts', EAN_PLUGIN_DIR . 'includes/js/admin-scripts.js');
	wp_enqueue_script('media-uploader', EAN_PLUGIN_DIR . 'includes/js/media-uploader.js');
}

if (isset($_GET['page']) && ( $_GET['page'] == 'ean-settings') ){
	add_action('admin_print_scripts', 'ean_admin_scripts');
}


?>