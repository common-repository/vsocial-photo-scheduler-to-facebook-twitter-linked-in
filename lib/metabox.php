<?php
$wplannerfb_settings = get_option('wplannerfb');
if( isset($wplannerfb_settings) && count($wplannerfb_settings) > 1 ) {

$wplannerfb_post_privacy_options = array("EVERYONE" => __( 'Everyone', WP_PLANNER_TEXTDOMAIN ), 
	 "ALL_FRIENDS" => __( 'All Friends', WP_PLANNER_TEXTDOMAIN ), 
	 "NETWORKS_FRIENDS" => __( 'Networks Friends', WP_PLANNER_TEXTDOMAIN ), 
	 "FRIENDS_OF_FRIENDS" => __( 'Friends of Friends', WP_PLANNER_TEXTDOMAIN ),
	 "CUSTOM" => __( 'Private (only me)', WP_PLANNER_TEXTDOMAIN )
);


function wplanner_fb_create() {
	global $wplannerfb_settings;
	$wplannerfb_where = $wplannerfb_settings['where_metabox'];
	
	if(count($wplannerfb_where) > 0){
		foreach($wplannerfb_where as $post_type => $status) {
			if($status == '1') {
				// Create the options meta box
				add_meta_box('wplannerfb-page-options', __( 'What do you want to publish on facebook ?', WP_PLANNER_TEXTDOMAIN ), 'wplannerfb_page_options', $post_type, 'normal', 'high');
				
				// Create the scheduler meta box
				add_meta_box('wplannerfb-page-scheduler', __( 'Post planner scheduler', WP_PLANNER_TEXTDOMAIN ), 'wplannerfb_page_scheduler', $post_type, 'side', 'high');
				
				// Create the post now meta box
				add_meta_box('wplannerfb-page-postnow', __( 'Publish on facebook now', WP_PLANNER_TEXTDOMAIN ), 'wplannerfb_page_postnow', $post_type, 'side', 'high');
			}
		}
	}
}

// Retrieve Wordpress Planner metadata values if they exist
function wplannerfb_post_meta($field) {
	global $post;
	$base = get_post_meta($post->ID, '_wplannerfb_'.$field, true);
	
	return htmlentities($base, ENT_QUOTES, 'UTF-8');
}

// Retrieve scheduling values if they exist
function wplannerfb_schedule_value($field) {
	global $wpdb, $post;
	return $wpdb->get_var( "SELECT `" . ( $field ) . "` FROM `" . ( $wpdb->prefix . 'fb_post_planner_cron' ) . "` WHERE 1=1 AND id_post=" . $post->ID );
}

function wplannerfb_page_options ( $post ) {
	global $wplannerfb_settings;
?>
	<!-- Creating the option fields -->
	<table width="100%" cellspacing="5" cellpadding="2" border="0">
	<tr>
		<td width="15%" valign="top"></td>
		<td width="85%" align="left"><a href="javascript:void(0);" id="auto-complete" rel="<?php echo home_url(); ?>" class="button-primary"><?php _e( 'Auto-Complete fields from above', WP_PLANNER_TEXTDOMAIN ); ?></a></td>
	</tr>
	<?php if($wplannerfb_settings['inputs_available']['message'] == '1') { ?>
	<tr>
		<td valign="top"><?php echo _e( 'Message:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td><textarea id="wplannerfb_message" name="wplannerfb_message" rows="4" style="width:100%;"><?php echo wplannerfb_post_meta('message'); ?></textarea></td>
	</tr>
	<?php } ?>
	<tr>
		<td><?php echo _e( 'Title:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td><input type="text" id="wplannerfb_title" name="wplannerfb_title" value="<?php echo wplannerfb_post_meta('title'); ?>" style="width:100%;"/></td>
	</tr>
	<tr>
		<td><?php echo _e( 'Permalink:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td>
			<input type="radio" name="wplannerfb_permalink" id="wplannerfb_post_link" value="post_link" <?php echo wplannerfb_post_meta('permalink') != '' && wplannerfb_post_meta('permalink') == 'post_link' ? 'checked="checked"' : 'checked="checked"'; ?> onclick="jQuery('#wplannerfb_permalink_value').hide();"/> &nbsp; <label for="wplannerfb_post_link"><?php _e( 'Use post link', WP_PLANNER_TEXTDOMAIN ); ?></label> &nbsp;&nbsp; 
			<input type="radio" name="wplannerfb_permalink" id="wplannerfb_custom_link" value="custom_link" <?php echo wplannerfb_post_meta('permalink') != '' && wplannerfb_post_meta('permalink') != 'post_link' ? 'checked="checked"' : ''; ?> onclick="jQuery('#wplannerfb_permalink_value').show();"/> &nbsp; <label for="wplannerfb_custom_link"><?php _e( 'Use custom link', WP_PLANNER_TEXTDOMAIN ); ?></label>
			<input type="text" id="wplannerfb_permalink_value" name="wplannerfb_permalink_value" value="<?php echo wplannerfb_post_meta('permalink') != 'post_link' ? wplannerfb_post_meta('permalink') : ''; ?>" style="display:<?php echo wplannerfb_post_meta('permalink') != 'post_link' ? 'block' : 'none'; ?>; float:right; width:75%;"/>
		</td>
	</tr>
	<?php if($wplannerfb_settings['inputs_available']['caption'] == '1') { ?>
	<tr>
		<td width="15%"><?php echo _e( 'Caption:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td width="85%"><input type="text" id="wplannerfb_caption" name="wplannerfb_caption" value="<?php echo wplannerfb_post_meta('caption'); ?>" style="width:100%;"/></td>
	</tr>
	<?php } ?>
	<tr>
		<td valign="top"><?php echo _e( 'Description:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td><textarea id="wplannerfb_description" name="wplannerfb_description" rows="4" style="width:100%;"><?php echo wplannerfb_post_meta('description'); ?></textarea></td>
	</tr>
	<?php if($wplannerfb_settings['inputs_available']['image'] == '1') { ?>
	<tr id="wplannerfb_upload">
		<td><?php echo _e( 'Image:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td><?php echo wplanner_fb_medialibrary_uploader('wplannerfb_image', wplannerfb_post_meta('image'), null, null, $post->ID); ?></td>
	</tr>
	<?php } ?>
	</table>
	
	<script type="text/javascript">
	// (POST) Facebook Planner
	var fb_planner_post = {
		init: function() {
			this.trigger();
		},
		
		autocomplete_fields: function() {
			var titleValue = jQuery('#titlewrap').find('input#title').val();
			var imageValue = jQuery('#wplannerfb_image').val();
			
			/*var site_url = jQuery('#auto-complete').attr('rel');
			var permalink = jQuery('#sample-permalink');
			var uniqStr = jQuery('#editable-post-name-full').text();
			
			var tmp_elm = jQuery('#sample-permalink').clone();
				tmp_elm.find('span').replaceWith(uniqStr);
			var linkValue = tmp_elm.text();
			
			if( linkValue.indexOf(site_url) == '-1' ) {
				var linkValue = site_url +'/'+ linkValue;
			}*/
			
			if(tinymce.activeEditor) {
				if(!tinymce.activeEditor.isHidden()) {
					tinymce.activeEditor.save();
				}
			}
			
			descValue = jQuery('#content').val();
            descValue = descValue.replace(/(<([^>]+)>)/ig,"");
            
			if( titleValue != jQuery('#wplannerfb_title').val() ) {
				jQuery('#wplannerfb_title').val(titleValue);
			}
			
			/*if( linkValue != jQuery('#wplannerfb_permalink').val() ) {
				jQuery('#wplannerfb_permalink').val(linkValue);
			}*/
			
			if( descValue != jQuery('#wplannerfb_description').val() ) {
				jQuery('#wplannerfb_description').val(descValue);
			}
			
			/*if( jQuery('#wplannerfb_upload').find('.upload').val() == '' && typeof jQuery('#set-post-thumbnail').find('img').attr('src') != 'undefined' ) {
				var featureImage = jQuery('#set-post-thumbnail').find('img').attr('src');
				
				jQuery('#wplannerfb_upload').find('.upload').val(featureImage);
				jQuery('#wplannerfb_upload').find('.screenshot').html('<br /><img src="'+ featureImage +'" width="265" alt=""/><br /><br /><a href="#" class="mlu_remove button">Remove Image</a>');
			}*/
			if( typeof jQuery('#wplannerfb_upload').find('.upload').val() != 'undefined' ) {
				jQuery.ajax({
					type		: "POST",
					url			: ajaxurl,
					data		: {
						'action'	: 'fb_getFeaturedImage',
						'postId'	: <?php echo $post->ID; ?>
					},
					dataType	: "json",
					success		: function(dataResponse) {
						var featuredImage = dataResponse;
						
						if( featuredImage.status == 'OK' ) {
							jQuery('#wplannerfb_upload').find('.upload').val(featuredImage.text);
							jQuery('#wplannerfb_upload').find('.screenshot').html('<br /><img src="'+ featuredImage.text +'" width="350" alt=""/><br /><br /><a href="#" class="mlu_remove button">Remove Image</a>');
						}
					},
					error		: function() {
						alert('Error retrieving featured image!');
					}
				});
			}
		},
		
		trigger: function() {
			this.autocomplete_fields();
		}	
	};
	
	jQuery('#auto-complete').click(function() {
		fb_planner_post.init();
	});
	</script>
<?php
}


function wplannerfb_page_scheduler ( $post ) {
	global $wplannerfb_post_privacy_options, $wplannerfb_settings;
	$post_to_check = unserialize(wplannerfb_schedule_value('post_to'));
?>		
	<input type="hidden" name="plannerSaveThePost" value="1" />
	<!-- Creating the scheduler fields -->
	<table width="100%" cellspacing="5" cellpadding="2" border="0">
	<tr>
		<td colspan="2">
			<?php echo _e( 'Publish on:', WP_PLANNER_TEXTDOMAIN ); ?>
			&nbsp;
			<input type="checkbox" id="wplannerfb_post_toprofile" name="wplannerfb_post_toprofile" <?php echo isset($post_to_check['profile']) && trim($post_to_check['profile']) == 'on' ? 'checked="checked"' : ''; ?> /> <label for="wplannerfb_post_toprofile"><?php echo _e( 'Profile', WP_PLANNER_TEXTDOMAIN ); ?></label>
			&nbsp;
			<input type="checkbox" id="wplannerfb_post_topage_group" name="wplannerfb_post_topage_group" <?php echo $post_to_check['page_group'] ? 'checked="checked"' : ''; ?> onclick="jQuery('#wplannerfb_now_post_to_page_group_div_sc').toggle();" /> <label for="wplannerfb_post_topage_group"><?php echo _e( 'Page / Group', WP_PLANNER_TEXTDOMAIN ); ?></label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php
			$pages = get_option('wplanner_user_pages');
			if(trim($pages) != ""){
				$allPages = @json_decode($pages);
			}
			?>
			<div id="wplannerfb_now_post_to_page_group_div_sc" style="<?php echo $post_to_check['page_group'] ? 'display:block;' : 'display:none;'; ?> width:250px;">
            <?php 
			$page_grp_arr=$post_to_check['page_group'];
    if( $wplannerfb_settings['page_filter'] == 1 ) {
        if( count($wplannerfb_settings['available_pages']) > 0 ) {
            echo "<h5>Pages</h5>";
            foreach ($wplannerfb_settings['available_pages'] as $key => $status) {
                if( $status == 1 ) {
                    echo '<input type="checkbox" name="wplannerfb_post_to_page_group[]" value="page##' . ( $allPages->pages[$key]->id ) . '##' . ( $allPages->pages[$key]->access_token ) . '" '.(in_array("page##".($allPages->pages[$key]->id).'##'.($allPages->pages[$key]->access_token),$post_to_check['page_group']) ? 'checked="checked"' : '').'/><label>' . ( $allPages->pages[$key]->name ).'</label><br />';
                }
            }
        }
    }else{
        if(count($allPages->pages) > 0) {
            echo "<h4>Pages</h4>";
            foreach ($allPages->pages as $key => $value) {
                echo '<input type="checkbox" name="wplannerfb_post_to_page_group[]" value="page##' . ( $value->id ) . '##' . ( $allPages->pages[$key]->access_token ) . '" '.(in_array("page##".($value->id).'##'.($allPages->pages[$key]->access_token),$post_to_check['page_group']) ? 'checked="checked"' : '').'/><label>' . ( $value->name ) . '</label><br />';
            }
        }
    }
    
    if( $wplannerfb_settings['group_filter'] == 1 ) {
        if(count($wplannerfb_settings['available_groups']) > 0) {
            echo "<h4>Groups</h4>";
            foreach ($wplannerfb_settings['available_groups'] as $key => $status) {
                if( $status == 1 ) {
                    echo '<input type="checkbox" name="wplannerfb_post_to_page_group[]"  value="group##' . ( $allPages->groups[$key]->id ) . '" '.(in_array("group##".($allPages->groups[$key]->id),$post_to_check['page_group']) ? 'checked="checked"' : '').'/><label>' . ( $allPages->groups[$key]->name ) . '</label><br />';
                }
            }
        }
    }else{
        if(count($allPages->groups) > 0) {
            echo "<h4>Groups</h4>";
            foreach ($allPages->groups as $key => $value) {
                echo '<input type="checkbox" name="wplannerfb_post_to_page_group[]" value="group##' . ( $value->id ) . '" '.(in_array("group##".($value->id ),$post_to_check['page_group']) ? 'checked="checked"' : '').'/><label>' . ( $value->name ) . '</label><br />';
            }
        }
    }
    ?>

            </div>
            
			<!--<select id="wplannerfb_post_to_page_group" name="wplannerfb_post_to_page_group" style="<?php echo $post_to_check['page_group'] ? 'display:block;' : 'display:none;'; ?> width:250px;">
				<?php
				if( $wplannerfb_settings['page_filter'] == 1 ) {
					if( count($wplannerfb_settings['available_pages']) > 0 ) {
						echo '<optgroup label="' . __( 'Pages', WP_PLANNER_TEXTDOMAIN ) . '">';
						foreach ($wplannerfb_settings['available_pages'] as $key => $status) {
							if( $status == 1 ) {
								echo '<option value="page##'.($allPages->pages[$key]->id).'##'.($allPages->pages[$key]->access_token).'" '.($post_to_check['page_group'] == "page##".($allPages->pages[$key]->id).'##'.($allPages->pages[$key]->access_token) ? 'selected="selected"' : '').'>' . ($allPages->pages[$key]->name) . '</option>';
							}
						}
						echo '</optgroup>';
					}
				}else{
					if(count($allPages->pages) > 0) {
						echo '<optgroup label="' . __( 'Pages', WP_PLANNER_TEXTDOMAIN ) . '">';
						foreach ($allPages->pages as $key => $value) {
							echo '<option value="page##'.( $value->id ).'##'.( $allPages->pages[$key]->access_token ).'" '.($post_to_check['page_group'] == "page##".($value->id) .'##'.($allPages->pages[$key]->access_token) ? 'selected="selected"' : '').'>' . ($value->name) . '</option>';
						}
						echo '</optgroup>';
					}
				}
				
				if( $wplannerfb_settings['group_filter'] == 1 ) {
					if(count($wplannerfb_settings['available_groups']) > 0) {
						echo '<optgroup label="' . __( 'Groups', WP_PLANNER_TEXTDOMAIN ) . '">';
						foreach ($wplannerfb_settings['available_groups'] as $key => $status) {
							if( $status == 1 ) {
								echo '<option value="group##'.($allPages->groups[$key]->id).'" '.($post_to_check['page_group'] == "group##".$allPages->groups[$key]->id ? 'selected="selected"' : '').'>' . ($allPages->groups[$key]->name) . '</option>';
							}
						}
						echo '</optgroup>';
					}
				}else{
					if(count($allPages->groups) > 0) {
						echo '<optgroup label="' . __( 'Groups', WP_PLANNER_TEXTDOMAIN ) . '">';
						foreach ($allPages->groups as $key => $value) {
							echo '<option value="group##'.($value->id).'" '.($post_to_check['page_group'] == "group##".$value->id ? 'selected="selected"' : '').'>' . ($value->name) . '</option>';
						}
						echo '</optgroup>';
					}
				}
				?>
			</select>-->
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo _e( 'Privacy:', WP_PLANNER_TEXTDOMAIN ); ?>
			<select id="wplannerfb_post_privacy" name="wplannerfb_post_privacy">
			<?php
			foreach($wplannerfb_post_privacy_options as $key => $value) {
				echo '<option value="'.($key).'" '.(wplannerfb_schedule_value('post_privacy') != '' ? (wplannerfb_schedule_value('post_privacy') == $key ? 'selected="selected"' : '') : ($wplannerfb_settings['default_privacy_option'] == $key ? 'selected="selected"' : '')).'>'.($value).'</option>';
			}
			?>
			</select>
		</td>
	</tr>
	<tr><td colspan="2"><hr/></td></tr>
	<tr>
		<td><?php echo _e( 'Publish date/hour:', WP_PLANNER_TEXTDOMAIN ); ?></td>
		<td>
			<?php
				$run_date = wplannerfb_schedule_value('run_date');
				if( $run_date != '' ) {
					$run_date = explode(' ', $run_date);
					$run_date_hour = explode(':', $run_date[1]);
					$run_date = date('m/d/Y', strtotime($run_date[0])) .' @ '. $run_date_hour[0];
				}
			?>
			<input type="text" id="wplannerfb_date_hour" name="wplannerfb_date_hour" value="<?php echo $run_date; ?>" size="13" autocomplete="off"/>
			<script type="text/javascript">
			// Display DateTimePicker
			jQuery('#wplannerfb_date_hour').datetimepicker({
				timeFormat: 'H',
				separator: ' @ ',
				showMinute: false,
				ampm: false,
				timeOnlyTitle: '<?php echo _e( 'Choose Time', WP_PLANNER_TEXTDOMAIN ); ?>',
				timeText: '<?php echo _e( 'At', WP_PLANNER_TEXTDOMAIN ); ?>',
				hourText: '<?php echo _e( 'Hour', WP_PLANNER_TEXTDOMAIN ); ?>',
				currentText: '<?php echo _e( 'Now', WP_PLANNER_TEXTDOMAIN ); ?>',
				closeText: '<?php echo _e( 'Done', WP_PLANNER_TEXTDOMAIN ); ?>',
				addSliderAccess: true,
				sliderAccessArgs: { touchonly: false }
			});
			
			// Check for mandatory empty fields AND Auto-Complete fields with data from post/page (title, permalink, content) if empty
			jQuery('#wplannerfb_date_hour').live('click', function() {
				if( jQuery('#wplannerfb_title').val() == '' || 
					/*jQuery('#wplannerfb_permalink').val() == '' || */
					jQuery('#wplannerfb_description').val() == '')
				{	
					fb_planner_post.init();
					alert('<?php _e( "Your mandatory fields were empty. Auto-Complete was done using your current post/page data. Please check before submiting.", WP_PLANNER_TEXTDOMAIN ); ?>');
				}
			});
			
			// Auto-Check repeat interval input
			$repeating_interval = jQuery('#wplannerfb_repeating_interval');
			$repeating_interval.keyup(function(){
				$t = jQuery(this),
				val = $t.val();
				
				if(val != parseInt(val) || parseInt(val) < 1){
					$t.val(parseInt(val));
				}
			})
			</script>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="checkbox" id="wplannerfb_repeating" name="wplannerfb_repeating"  onclick="jQuery('#wplannerfb_repeating_wrapper').toggle();" <?php echo wplannerfb_schedule_value('repeat_status') == 'on' ? 'checked="checked"' : ''; ?> /> <label for="wplannerfb_repeating"><?php echo _e( 'Repeating', WP_PLANNER_TEXTDOMAIN ); ?></label>
			<br />
			<div id="wplannerfb_repeating_wrapper" style="<?php echo wplannerfb_schedule_value('repeat_status') == 'on' ? 'display:block;' : 'display:none;'; ?>">
			<input type="text" id="wplannerfb_repeating_interval" name="wplannerfb_repeating_interval" value="<?php echo wplannerfb_schedule_value('repeat_interval'); ?>" size="2"> <?php echo _e( 'hour(s) or', WP_PLANNER_TEXTDOMAIN ); ?> 
			<select id="wplannerfb_repeating_interval_sel" name="wplannerfb_repeating_interval_sel" onchange="jQuery('#wplannerfb_repeating_interval').val(jQuery(this).val());">
				<option value="" disabled="disabled">-- <?php echo _e( 'select interval', WP_PLANNER_TEXTDOMAIN ); ?> --</option>
				<option value="24">Every day</option>
				<option value="168">Every week</option>
				<option value="730">Every month</option>
				<option value="8766">Every year</option>
			</select>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" id="wplannerfb_email_at_post" name="wplannerfb_email_at_post" <?php echo wplannerfb_schedule_value('email_at_post') == 'on' ? 'checked="checked"' : ''; ?> /> <label for="wplannerfb_email_at_post"><?php echo _e( 'Email me when it\'s published on facebook', WP_PLANNER_TEXTDOMAIN ); ?></label></td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" id="wplannerfb_publish_at_save" name="wplannerfb_publish_at_save" value="wplannerfb_publish_at_save" /> <label for="wplannerfb_publish_at_save"><?php echo _e( 'Send to Facebook after publish / update', WP_PLANNER_TEXTDOMAIN ); ?></label></td>
	</tr>
	</table>
<?php
}

function wplannerfb_page_postnow ( $post ) {
	global $wplannerfb_settings, $wplannerfb_post_privacy_options;
?>		
	<script type="text/javascript">
		jQuery(document).ready(function() {
		
			var postNowBtn = jQuery('#post_planner_postNowFBbtn');
			
			postNowBtn.click(function() {
				// Auto-Complete fields with data from above (title, permalink, content) if empty
				if( jQuery('#wplannerfb_title').val() == '' || 
					/*jQuery('#wplannerfb_permalink').val() == '' || */
					jQuery('#wplannerfb_description').val() == ''
				) {	
					var c = confirm("<?php _e( "Your mandatory fields are empty. Do you want to auto-complete and then publish to Facebook?", WP_PLANNER_TEXTDOMAIN ); ?>");
					
					if(c == true) {
						fb_planner_post.init();
					}else{
						alert('<?php _e( "Publish canceled.", WP_PLANNER_TEXTDOMAIN ); ?>');
						return false;
					}
				}
				
				
				var postTo = '',
					postMe = jQuery('#wplannerfb_now_post_to_me'),
					postPageGroup = jQuery('#wplannerfb_now_post_to_page'),
					postTOFbNow = jQuery('#postTOFbNow');
					
				postTOFbNow.show();
				postNowBtn.hide();
				
				var postToProfile = '';
				var postToPageGroup = '';
				if( postMe.attr('checked') == 'checked' ) {
					postToProfile = 'on';
				}
				if( postPageGroup.attr('checked') == 'checked' ) {
					postToPageGroup = new Array();
					jQuery(".wplannerfb_now_post_to_page_h").each(function(){ 
					if(jQuery(this).is(':checked')) postToPageGroup.push(jQuery(this).val());
					
				});
				}
				
				
				
				var data = {
					action: 'publish_fb_now',
					postId: <?php echo $post->ID;?>,
					postTo: {'profile' : postToProfile, 'page_group' : postToPageGroup},
					privacy: jQuery('#wplannerfb_now_post_privacy').val(),
					wplannerfb_message: jQuery('#wplannerfb_message').val(),
					wplannerfb_title: jQuery('#wplannerfb_title').val(),
					wplannerfb_permalink: jQuery("input[name=wplannerfb_permalink]:checked").val(),
					wplannerfb_permalink_value: jQuery('#wplannerfb_permalink_value').val(),
					wplannerfb_caption: jQuery('#wplannerfb_caption').val(),
					wplannerfb_description: jQuery('#wplannerfb_description').val(),
					wplannerfb_image: jQuery('#wplannerfb_image').val()
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					console.log(response);
					if(jQuery.trim(response) == 'OK'){
						postTOFbNow.hide();
						alert('<?php echo _e( 'The post was published on facebook OK!', WP_PLANNER_TEXTDOMAIN ); ?>');
						postNowBtn.show();
					}else{
						alert('<?php echo _e( 'Error on publishing. Please try again later!', WP_PLANNER_TEXTDOMAIN ); ?>');
						postNowBtn.show();
					}
				});
				return false;
			});
		});
	</script>
	<!-- Creating the scheduler fields -->
	<table width="100%" cellspacing="5" cellpadding="2" border="0">
	<tr>
		<td colspan="2">
			<?php echo _e( 'Publish on:', WP_PLANNER_TEXTDOMAIN ); ?>
			&nbsp;
			<input type="checkbox" id="wplannerfb_now_post_to_me" name="wplannerfb_now_post_toprofile" checked="checked" /> 
            <label for="wplannerfb_now_post_to_me"><?php echo _e( 'Profile', WP_PLANNER_TEXTDOMAIN ); ?></label>
			&nbsp;
			<input type="checkbox" id="wplannerfb_now_post_to_page" name="wplannerfb_now_post_topage_group" onclick="jQuery('#wplannerfb_now_post_to_page_group_div').toggle();" /> <label for="wplannerfb_now_post_to_page"><?php echo _e( 'Page / Group', WP_PLANNER_TEXTDOMAIN ); ?></label>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php
			$pages = get_option('wplanner_user_pages');
			if(trim($pages) != ""){
				$allPages = @json_decode($pages);
			}
			?>
            <style>
            #wplannerfb_now_post_to_page_group_div{}
			#wplannerfb_now_post_to_page_group_div h4{ margin:5px 0px; }
			#wplannerfb_now_post_to_page_group_div label{ margin-left:4px; font-size:12px; }
			#wplannerfb_now_post_to_page_group_div input{ margin-top:5px; }
            </style>
<div id="wplannerfb_now_post_to_page_group_div" style="width:250px; display:none">
                
<?php
    if( $wplannerfb_settings['page_filter'] == 1 ) {
        if( count($wplannerfb_settings['available_pages']) > 0 ) {
            echo "<h5>Pages</h5>";
            foreach ($wplannerfb_settings['available_pages'] as $key => $status) {
                if( $status == 1 ) {
                    echo '<input type="checkbox" class="wplannerfb_now_post_to_page_h" value="page##' . ( $allPages->pages[$key]->id ) . '##' . ( $allPages->pages[$key]->access_token ) . '" /><label>' . ( $allPages->pages[$key]->name ).'</label><br />';
                }
            }
        }
    }else{
        if(count($allPages->pages) > 0) {
            echo "<h4>Pages</h4>";
            foreach ($allPages->pages as $key => $value) {
                echo '<input type="checkbox" class="wplannerfb_now_post_to_page_h" value="page##' . ( $value->id ) . '##' . ( $allPages->pages[$key]->access_token ) . '" /><label>' . ( $value->name ) . '</label><br />';
            }
        }
    }
    
    if( $wplannerfb_settings['group_filter'] == 1 ) {
        if(count($wplannerfb_settings['available_groups']) > 0) {
            echo "<h4>Groups</h4>";
            foreach ($wplannerfb_settings['available_groups'] as $key => $status) {
                if( $status == 1 ) {
                    echo '<input type="checkbox" class="wplannerfb_now_post_to_page_h"  value="group##' . ( $allPages->groups[$key]->id ) . '" /><label>' . ( $allPages->groups[$key]->name ) . '</label><br />';
                }
            }
        }
    }else{
        if(count($allPages->groups) > 0) {
            echo "<h4>Groups</h4>";
            foreach ($allPages->groups as $key => $value) {
                echo '<input type="checkbox" class="wplannerfb_now_post_to_page_h" value="group##' . ( $value->id ) . '" /><label>' . ( $value->name ) . '</label><br />';
            }
        }
    }
    ?>
            </div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo _e( 'Privacy:', WP_PLANNER_TEXTDOMAIN ); ?>
			<select id="wplannerfb_now_post_privacy" name="wplannerfb_now_post_privacy">
			<?php
			foreach($wplannerfb_post_privacy_options as $key => $value) {
				echo '<option value="'.($key).'" '.($wplannerfb_settings['default_privacy_option'] == $key || wplannerfb_schedule_value('post_privacy') == $key ? 'selected="selected"' : '' ).'>'.($value).'</option>';
			}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2"><input type="checkbox" id="wplannerfb_now_publish_at_save" name="wplannerfb_now_publish_at_save" value="wplannerfb_now_publish_at_save" /> <label for="wplannerfb_now_publish_at_save"><?php echo _e( 'Send to Facebook after publish / update', WP_PLANNER_TEXTDOMAIN ); ?></label></td>
	</tr>
	<tr>
		<td colspan="2">
			<a href="#" id="post_planner_postNowFBbtn" class="button-primary"><?php echo _e( 'Publish now on facebook', WP_PLANNER_TEXTDOMAIN ); ?> </a>
			<span id="postTOFbNow" style="display: none; border: 1px solid #dadada; text-align: center; margin: 10px 0px 0px 0px; width: 160px; padding: 3px; background-color: #dfdfdf;"><?php echo _e( 'Publishing on facebook ...', WP_PLANNER_TEXTDOMAIN ); ?></span>
		</td>
	</tr>
	</table>
<?php
}


// Hook to save Wordpress Facebook planner data
add_action( 'save_post', 'wplannerfb_save_meta' );
function wplannerfb_save_meta( $post_id ) {
	global $wpdb;
	
	// do not save if this is an auto save routine
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return $post_id;
	
	//verify post is not a revision
	if ( !wp_is_post_revision( $post_id ) ) {
		/************************/
		/* TEXT DATA
		/************************/
		
		if ( isset( $_POST['wplannerfb_message'] ) ) {
			update_post_meta( $post_id, '_wplannerfb_message', strip_tags( $_POST['wplannerfb_message'] ) );
		}
		
		if ( isset( $_POST['wplannerfb_title'] ) ) {
			update_post_meta( $post_id, '_wplannerfb_title', strip_tags( $_POST['wplannerfb_title'] ) );
		}
		
		if ( isset( $_POST['wplannerfb_permalink'] ) ) {
			if( trim($_POST['wplannerfb_permalink']) == 'custom_link' ) {
				$wplannerfb_permalink = $_POST['wplannerfb_permalink_value'];
			}else{
				//$wplannerfb_permalink = get_permalink($post_id);
				$wplannerfb_permalink = 'post_link';
			}
			
			update_post_meta( $post_id, '_wplannerfb_permalink', $wplannerfb_permalink );
		}
		
		if ( isset( $_POST['wplannerfb_caption'] ) ) {
			update_post_meta( $post_id, '_wplannerfb_caption', strip_tags( $_POST['wplannerfb_caption'] ) );
		}
		
		if ( isset( $_POST['wplannerfb_description'] ) ) {
			update_post_meta( $post_id, '_wplannerfb_description', strip_tags( $_POST['wplannerfb_description'] ) );
		}
		
		if ( isset( $_POST['wplannerfb']['wplannerfb_image'] ) ) {
			update_post_meta( $post_id, '_wplannerfb_image', strip_tags( $_POST['wplannerfb']['wplannerfb_image'] ) );
		}
		
		
		
		/************************/
		/* SCHEDULER DATA
		/************************/

		// AUTO-SUBMIT on PUBLISH (Scheduler & Publish now)
		if ( (isset($_POST['wplannerfb_publish_at_save']) && trim($_POST['wplannerfb_publish_at_save']) == 'wplannerfb_publish_at_save') && (isset($_POST['wplannerfb_post_privacy']) && trim($_POST['wplannerfb_post_privacy']) != '') ) {
			$page_group = isset($_POST['wplannerfb_post_topage_group']) ? $_POST['wplannerfb_post_to_page_group'] : '';
			$wherePost 	= serialize(array('profile' => (isset($_POST['wplannerfb_post_toprofile']) ? 'on' : 'off'), 'page_group' => $page_group));
			$privacy 	= $_POST['wplannerfb_post_privacy'];
		}
		else if ( (isset($_POST['wplannerfb_now_publish_at_save']) && trim($_POST['wplannerfb_now_publish_at_save']) == 'wplannerfb_now_publish_at_save') && (isset($_POST['wplannerfb_now_post_privacy']) && trim($_POST['wplannerfb_now_post_privacy']) != '') ) {
			$page_group = isset($_POST['wplannerfb_now_post_topage_group']) ? $_POST['wplannerfb_now_post_to_page_group'] : '';
			$wherePost 	= serialize(array('profile' => isset($_POST['wplannerfb_now_post_toprofile']) ? 'on' : 'off', 'page_group' => $page_group));
			$privacy 	= $_POST['wplannerfb_now_post_privacy'];
		}
		
		if ( ((isset($_POST['wplannerfb_publish_at_save']) && trim($_POST['wplannerfb_publish_at_save']) == 'wplannerfb_publish_at_save') && (isset($_POST['wplannerfb_post_privacy']) && trim($_POST['wplannerfb_post_privacy']) != '')) || 
			 ((isset($_POST['wplannerfb_now_publish_at_save']) && trim($_POST['wplannerfb_now_publish_at_save']) == 'wplannerfb_now_publish_at_save') && (isset($_POST['wplannerfb_now_post_privacy']) && trim($_POST['wplannerfb_now_post_privacy']) != '')) ) 
		{
			// Plugin facebook utils load
			require_once ( WP_PLANNER_DIR . 'lib/scripts/fb-utils/fb-utils.class.php' );
			
			// start instance of fb post planner
			$fbUtils = fbPlannerUtils::getInstance();
			$fbUtils->publishToWall($post_id, $wherePost, $privacy);
		}
		// END AUTO-SUBMIT
		
		
		if ( (isset($_POST['wplannerfb_post_toprofile']) || isset($_POST['wplannerfb_post_topage_group'])) && trim($_POST['wplannerfb_date_hour']) != '' && isset($_POST['wplannerfb_post_privacy'])) {
			$date_hour = $_POST['wplannerfb_date_hour'];
			$date_hour = explode(' @ ', $_POST['wplannerfb_date_hour']);
			
			// date format
			$date = $date_hour[0];
			$start_date = date('Y-m-d', strtotime($date));
			
			// hour format
			$hf = explode(' ', $date_hour[1]);
			$start_hour = $hf[0];
			
			// Final DATETIME MySQL format (first time running)
			$run_date = $start_date.' '.$start_hour.':00:00';
			
			// check if post_id exists
			$checkIfPostIdExist = $wpdb->get_var( "SELECT `id` FROM `" . ( $wpdb->prefix . 'fb_post_planner_cron' ) . "` WHERE 1=1 AND id_post=" . $post_id );
			
			if( (int)$checkIfPostIdExist == 0 ) {
				$wpdb->insert(
					$wpdb->prefix . 'fb_post_planner_cron',
					array(
						'id_post' => $post_id,
						'post_to' => serialize(array(
							'profile' => isset($_POST['wplannerfb_post_toprofile']) ? 'on' : 'off',
							'page_group' => isset($_POST['wplannerfb_post_topage_group']) ? $_POST['wplannerfb_post_to_page_group'] : ''
						)),
						'post_privacy' =>  $_POST['wplannerfb_post_privacy'],
						'email_at_post' => !$_POST['wplannerfb_email_at_post'] ? 'off' : 'on',
						'run_date' => $run_date,
						'repeat_status' => !$_POST['wplannerfb_repeating'] ? 'off' : 'on',
						'repeat_interval' => $_POST['wplannerfb_repeating_interval']
					)
				);
			}else{
				$wpdb->update(
					$wpdb->prefix . 'fb_post_planner_cron',
					array(
						'post_to' => serialize(array(
							'profile' => isset($_POST['wplannerfb_post_toprofile']) ? 'on' : 'off',
							'page_group' => isset($_POST['wplannerfb_post_topage_group']) ? $_POST['wplannerfb_post_to_page_group'] : ''
						)),
						'post_privacy' =>  $_POST['wplannerfb_post_privacy'],
						'email_at_post' => !$_POST['wplannerfb_email_at_post'] ? 'off' : 'on',
						'run_date' => $run_date,
						'repeat_status' => !$_POST['wplannerfb_repeating'] ? 'off' : 'on',
						'repeat_interval' => $_POST['wplannerfb_repeating_interval']
					),
					array( 'id' => $checkIfPostIdExist )
				);
				
				if( $run_date > date('Y-m-d H:00:00') ) {
					$wpdb->update(
						$wpdb->prefix . 'fb_post_planner_cron',
						array(
							'status' => 0
						),
						array( 'id' => $checkIfPostIdExist )
					);
				}
			}
		}
	}
}
}