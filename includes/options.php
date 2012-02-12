<?php

/* ----------------------------------------
* To retrieve a value use: $ean_options[$prefix.'var']
----------------------------------------- */

$prefix = 'ean_';

/* ----------------------------------------
* Create the TABS
----------------------------------------- */

$ean_custom_tabs = array(
		array(
			'label'=> __('General', 'ean'),
			'id'	=> $prefix.'general'
		),
		array(
			'label'=> __('Advanced', 'ean'),
			'id'	=> $prefix.'advanced'
		)
	);

/* ----------------------------------------
* Options Field Array
----------------------------------------- */

$ean_custom_meta_fields = array(

	/* -- TAB 1 -- */
	array(
		'id'	=> $prefix.'general', // Use data in $ean_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'=> __( 'Please configure the settings', 'ean' ),
		'id'	=> $prefix.'title',
		'type'	=> 'title'
	),

	array(
		'label'	=> __( 'Enable notification ?', 'ean' ),
		'desc'	=> __('Check this box to enable the notification message.', 'ean'),
		'id'	=> $prefix.'active',
		'type'	=> 'checkbox'
	),
	
	array(
		'label'	=> __( 'Enter the message to display', 'ean' ),
		'desc'	=> __('Enter the text you want to display in the notification. HTML code accepted.', 'ean').' <code>'.__('To create links use simple quotes.', 'ean'),
		'id'	=> $prefix.'text',
		'type'	=> 'text'
	),
	
	array(
		'label'	=> __( 'Error message ?', 'ean' ),
		'desc'	=> __('Check this box to display an error message. If uncheck, the message will be display as an information.', 'ean'),
		'id'	=> $prefix.'error',
		'type'	=> 'checkbox'
	),
	
	array(
		'label'	=> __( 'Display "hide button" ?', 'ean' ),
		'desc'	=> __('Check this box to display a "hide button" inside the notification box.<br />Each user will have the choice to hide or not the notification', 'ean'),
		'id'	=> $prefix.'hidebutton',
		'type'	=> 'checkbox'
	),
	
	array(
		'label'		=> __( 'Notification page(s)', 'ean' ),
		'desc'		=> __('Choose where you want to display the notification.', 'ean'),
		'id'		=> $prefix.'pages',
		'type'		=> 'select',
		'options' 	=> array (
			'one' => array (
				'label' => __( 'All pages', 'ean' ),
				'value'	=> 'all'
			),
			'two' => array (
				'label' => __( 'Dashboard Only', 'ean' ),
				'value'	=> 'index'
			)
		)
	),
	
	array(
		'type'	=> 'tab_end'
	),
	/* -- /TAB 1 -- */
	
	/* -- TAB 2 -- */
	array(
		'id'	=> $prefix.'advanced', // Use data in $ean_custom_tabs
		'type'	=> 'tab_start'
	),
	
	array(
		'label'	=> __( 'Only display to Admins ?', 'ean' ),
		'desc'	=> __('Check this box to display notice only to Admins', 'ean'),
		'id'	=> $prefix.'adminsonly',
		'type'	=> 'checkbox'
	),
	
	array(
		'type'	=> 'tab_end'
	)
	/* -- /TAB 2 -- */
);

?>