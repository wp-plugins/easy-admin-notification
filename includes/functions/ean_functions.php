<?php

/* ------------------------------------------------------------------*/
/* ADD CUSTOM SCRIPTS FOR JQUERY UI */
/* ------------------------------------------------------------------*/

function ean_add_custom_scripts() {
	global $ean_custom_meta_fields, $ean_options;

	// Date Picker
	$output = '<script type="text/javascript">
				jQuery(function() {';

	foreach ($ean_custom_meta_fields as $field) { // loop through the fields looking for certain types
		if($field['type'] == 'date')
			$output .= 'jQuery(".datepicker").datepicker();';
			
		// Slider
		if ($field['type'] == 'slider') {
			$field_id = $field['id'];
			$value = $ean_options["$field_id"] != '' ? $ean_options["$field_id"] : '0';
			
			$output .= '
					jQuery( "#'.$field['id'].'-slider" ).slider({
						value: '.$value.',
						min: '.$field['min'].',
						max: '.$field['max'].',
						step: '.$field['step'].',
						slide: function( event, ui ) {
							jQuery( "#ean_val_slider_'.$field['id'].'" ).val( ui.value );
						}
					});';
		}
	}

	
	$output .= '});
		</script>';

	echo $output;
}

add_action('admin_head','ean_add_custom_scripts');

/* ------------------------------------------------------------------*/
/* CREATE THE FIELDS AND DISPLAY THEM */
/* ------------------------------------------------------------------*/

function ean_show_custom_tabs() {
	
	global $ean_custom_tabs;
	
	echo '<h2 class="nav-tab-wrapper">';
	foreach ($ean_custom_tabs as $tab) {
		echo '<a href="#'.$tab['id'].'" class="nav-tab">'.$tab['label'].'</a>';
	}
	echo '</h2>';
}

/* ------------------------------------------------------------------*/
/* CREATE THE FIELDS AND DISPLAY THEM */
/* ------------------------------------------------------------------*/

function ean_show_custom_fields() {

	global $ean_custom_meta_fields;
	$prefix = 'ean_';
	
	// Use nonce for verification
	echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	// Begin the field table and loop
	echo '<div id="tab_container">';

	
	
	foreach ($ean_custom_meta_fields as $field) {
		// get value of this field if it exists for this post
		$ean_options = get_option('ean_settings');
		
		// Begin a new tab
		if( $field['type'] == 'tab_start') {
			echo '<div class="tab_content" id="'.$field['id'].'">';
			echo '<table class="form-table">';
		}

		// begin a table row with
		echo '<tr>';

				if( $field['type'] != 'tab_start' && $field['type'] != 'tab_end') {
					if( $field['type'] == 'title') {
						echo '<th colspan="2"><h3 id="ean_settings['.$field['id'].']">'.$field['label'].'</h3></th>';
					} else {
						echo '<th><label for="ean_settings['.$field['id'].']">'.$field['label'].'</label></th>';
					}
				}
				
		if( $field['type'] != 'tab_start' && $field['type'] != 'tab_end') {
		echo	'<td>';
				switch($field['type']) {
					// text
					case 'text':
						echo '<input type="text" name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']" value="'.$ean_options[$field['id']].'" size="30" class="regular-text" />
							<span class="description">'.$field['desc'].'</span>';
					break;
					// textarea
					case 'textarea':
						echo '<textarea name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']" cols="60" rows="4">'.$ean_options[$field['id']].'</textarea>
							<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// checkbox
					case 'checkbox':
						echo '<input type="checkbox" name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']" ',$ean_options[$field['id']] ? ' checked="checked"' : '','/>
							<label for="ean_settings['.$field['id'].']"><span class="description">'.$field['desc'].'</span></label>';
					break;
					// select
					case 'select':
						echo '<select name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']">';
						foreach ($field['options'] as $option) {
							echo '<option', $ean_options[$field['id']] == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
						}
						echo '</select>&nbsp;<span class="description">'.$field['desc'].'</span>';
					break;
					// radio
					case 'radio':
						foreach ( $field['options'] as $option ) {
							echo '<input type="radio" name="ean_settings['.$field['id'].']" id="ean_settings['.$option['value'].']" value="'.$option['value'].'" ',$ean_options[$field['id']] == $option['value'] ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo '<span class="description">'.$field['desc'].'</span>';
					break;
					// checkbox_group
					case 'checkbox_group':
						foreach ($field['options'] as $option) {
							echo '<input type="checkbox" value="'.$option['value'].'" name="ean_settings['.$field['id'].'][]" id="ean_settings['.$option['value'].']"',$ean_options[$field['id']] && in_array($option['value'], $ean_options[$field['id']]) ? ' checked="checked"' : '',' />
									<label for="'.$option['value'].'">'.$option['label'].'</label><br />';
						}
						echo '<span class="description">'.$field['desc'].'</span>';
					break;
					// tax_select
					case 'tax_select':
						echo '<select name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']">
								<option value="">-- '.__('Select','ean').' --</option>'; // Select One
						$terms = get_terms($field['id'], 'get=all');
						$selected = wp_get_object_terms('', 'ean_settings['.$field['id'].']');
						foreach ($terms as $term) {
							if ($selected && $term->slug == $ean_options[$field['id']] )
								echo '<option value="'.$term->slug.'" selected="selected">'.$term->name.'</option>';
							else
								echo '<option value="'.$term->slug.'">'.$term->name.'</option>';
						}
						$taxonomy = get_taxonomy($ean_options[$field['id']]);
						echo '</select><br /><span class="description"><a href="'.get_bloginfo('home').'/wp-admin/edit-tags.php?taxonomy='.$field['id'].'">'.__('Manage', 'ean').' '.$taxonomy->label.'</a></span>';
					break; 
					// post_list
					case 'post_list':
						$items = get_posts( array (
							'post_type'	=> $field['post_type'],
							'posts_per_page' => -1
						));
						echo '<select name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']">
								<option value="">-- '.__('Select','ean').' --</option>'; // Select One
							foreach($items as $item) {
								echo '<option value="'.$item->ID.'"',$ean_options[$field['id']] == $item->ID ? ' selected="selected"' : '','>'.($item->post_type)
								.': '.$item->post_title.'</option>';
							} // end foreach
						echo '</select>&nbsp;<span class="description">'.$field['desc'].'</span>';
					break;     
					// date
					case 'date':
						echo '<input type="text" class="datepicker" name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']" value="'.$ean_options[$field['id']].'" size="30" />
								<span class="description">'.$field['desc'].'</span>';
					break;
					// image
					case 'image':
						//$image = get_template_directory_uri().'/images/image.png';
						echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
						if ($ean_options[$field['id']]) { $image = wp_get_attachment_image_src($ean_options[$field['id']], 'medium');	$image = $image[0]; }
						echo	'<input name="ean_settings['.$field['id'].']" id="ean_settings['.$field['id'].']" type="hidden" class="custom_upload_image" value="'.$ean_options[$field['id']].'" />
									<img src="'.$image.'" class="custom_preview_image" alt="" /><br />
										<input class="custom_upload_image_button button" type="button" value="'.__('Choose Image', 'ean').'" />
										<small> <a href="#" class="custom_clear_image_button">'.__('Remove Image', 'ean').'</a></small>
										<br clear="all" /><span class="description">'.$field['desc'].'';
					break;
					// slider
					case 'slider':
					$field_id = $field['id'];
					$value = $ean_options["$field_id"] != '' ? $ean_options["$field_id"] : '0';
						echo '<div id="'.$field['id'].'-slider"></div>
								<input type="text" name="ean_settings['.$field['id'].']" id="ean_val_slider_'.$field['id'].'" value="'.$value.'" size="5" />
								<br /><span class="description">'.$field['desc'].'</span>';
					break;
					// repeatable
					case 'repeatable':
						echo '
								<ul id="ean_settings['.$field['id'].']-repeatable" class="custom_repeatable">';
						$i = 0;

						if ( $ean_options[$field['id']] ) {
							foreach($ean_options[$field['id']] as $row) {
								echo '<li><span class="sort hndle">|||</span>
											<input type="text" name="ean_settings['.$field['id'].']['.$i.']" id="ean_settings['.$field['id'].']" value="'.$row.'" size="30" />
											<a class="repeatable-remove button" href="#">-</a></li>';
								$i++;
							}
						} else {
							echo '<li><span class="sort hndle">|||</span>
										<input type="text" name="ean_settings['.$field['id'].']['.$i.']" id="ean_settings['.$field['id'].']" value="" size="30" />
										<a class="repeatable-remove button" href="#">-</a></li>';
						}
						echo '</ul>';
						echo '<a class="repeatable-add button" href="#">'.__('Add','ean').' +</a>';
						echo '<br /><span class="description">'.$field['desc'].'</span>';
						
					break;

				} //end switch
		}
		echo '</td></tr>';
		
		
		// End a tab
		if( $field['type'] == 'tab_end') {
			echo '</table>';
			echo '</div>';
		}
		
	} // end foreach
	

	
	echo '</div>'; // End Div tab container
}
  

?>