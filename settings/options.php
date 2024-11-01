<?php

if ( !function_exists( 'wplanner_fb_option_name' ) ) {

	function wplanner_fb_option_name() {

		$name = 'wplannerfb';
		
		$wplannerfb_settings = get_option( 'wplannerfb' );
		$wplannerfb_settings['id'] = $name;
		update_option( 'wplannerfb', $wplannerfb_settings );
	}
}

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the "id" fields, make sure to use all lowercase and no spaces.
 *  
 */
if ( !function_exists( 'wplanner_fb_options' ) ) {

	function wplanner_fb_options() {
		global $wpdb;
		
		/* Here we define the different drop downs for our option page */
		
		// Facebook language
		$select_fblanguage = array( "af_ZA" => "Afrikaans","sq_AL" => "Albanian","ar_AR" => "Arabic","hy_AM" => "Armenian","eu_ES" => "Basque","be_BY" => "Belarusian","bn_IN" => "Bengali","bs_BA" => "Bosanski","bg_BG" => "Bulgarian","ca_ES" => "Catalan","zh_CN" => "Chinese","cs_CZ" => "Czech","da_DK" => "Danish","fy_NL" => "Dutch","en_US" => "English","eo_EO" => "Esperanto","et_EE" => "Estonian","et_EE" => "Estonian","fi_FI" => "Finnish","fo_FO" => "Faroese","tl_PH" => "Filipino","fr_FR" => "French","gl_ES" => "Galician","ka_GE" => "Georgian","de_DE" => "German","zh_CN" => "Greek","he_IL" => "Hebrew","hi_IN" => "Hindi","hr_HR" => "Hrvatski","hu_HU" => "Hungarian","is_IS" => "Icelandic","id_ID" => "Indonesian","ga_IE" => "Irish","it_IT" => "Italian","ja_JP" => "Japanese","ko_KR" => "Korean","ku_TR" => "Kurdish","la_VA" => "Latin","lv_LV" => "Latvian","fb_LT" => "Leet Speak","lt_LT" => "Lithuanian","mk_MK" => "Macedonian","ms_MY" => "Malay","ml_IN" => "Malayalam","nl_NL" => "Nederlands","ne_NP" => "Nepali","nb_NO" => "Norwegian","ps_AF" => "Pashto","fa_IR" => "Persian","pl_PL" => "Polish","pt_PT" => "Portugese","pa_IN" => "Punjabi","ro_RO" => "Romanian","ru_RU" => "Russian","sk_SK" => "Slovak","sl_SI" => "Slovenian","es_LA" => "Spanish","sr_RS" => "Srpski","sw_KE" => "Swahili","sv_SE" => "Swedish","ta_IN" => "Tamil","te_IN" => "Telugu","th_TH" => "Thai","tr_TR" => "Turkish","uk_UA" => "Ukrainian","vi_VN" => "Vietnamese","cy_GB" => "Welsh" );
		
		// Facebook OpenGraph Post Types
		$select_fb_og_type = array(
			"Activities" => array("activity", "sport"),
			"Businesses" => array("bar", "company", "cafe", "hotel", "restaurant"),
			"Groups" => array("cause", "sports_league", "sports_team"),
			"Organizations" => array("band", "government", "non_profit", "school", "university"),
			"People" => array("actor", "athlete", "author", "director", "musician", "politician", "public_figure"),
			"Places" => array("city", "country", "landmark", "state_province"),
			"Products and Entertainment" => array("album", "book", "drink", "food", "game", "product", "song", "movie", "tv_show"),
			"Websites" => array("blog", "website", "article")
		);
		
		// Inputs available for posting to Facebook displayed on each post/page
		$inputs_available = array(
			"message" => __( "Message", WP_PLANNER_TEXTDOMAIN ), 
			"caption" => __( "Caption", WP_PLANNER_TEXTDOMAIN ), 
			"image" => __( "Image", WP_PLANNER_TEXTDOMAIN )
		);
							
		// Facebook privacy options
		$wplannerfb_post_privacy_options = array(
			"EVERYONE" => __( 'Everyone', WP_PLANNER_TEXTDOMAIN ), 
			"EVERYONE" => __( 'Everyone', WP_PLANNER_TEXTDOMAIN ), 
			"ALL_FRIENDS" => __( 'All Friends', WP_PLANNER_TEXTDOMAIN ), 
			"NETWORKS_FRIENDS" => __( 'Networks Friends', WP_PLANNER_TEXTDOMAIN ), 
			"FRIENDS_OF_FRIENDS" => __( 'Friends of Friends', WP_PLANNER_TEXTDOMAIN ),
			"CUSTOM" => __( 'Private (only me)', WP_PLANNER_TEXTDOMAIN )
		);
		
		// all alias of post types 
		$select_post_types = array();
		$exclude_post_types = array('attachment', 'revision', 'wplannertw', 'wptw2fbfeed_fb', 'wplannerfb', 'wplannerlin', 'wpsfpb');
		
		// Facebook available user Pages / Groups
		$fb_user_pages_groups = get_option('wplanner_user_pages');
		if(trim($fb_user_pages_groups) != "") {
				$fb_all_user_pages_groups = @json_decode($fb_user_pages_groups);
		}
			
		// create query string
		$querystr = "SELECT DISTINCT($wpdb->posts.post_type) FROM $wpdb->posts WHERE 1=1";
		$pageposts = $wpdb->get_results($querystr, ARRAY_A);
		if(count($pageposts) > 0 ) {
			foreach ($pageposts as $key => $value){
				if( !in_array($value['post_type'], $exclude_post_types) ) {
					$select_post_types[$value['post_type']] = ucfirst($value['post_type']);
				}
			}
		}
		
		// Defining the image directoy path for image radio buttons
		$imagepath =  WP_PLANNER_URL . 'settings/images/';
			
		$options = array();
		
		// Begin display options	
		$options[] = array( "name" => __( "General settings", WP_PLANNER_TEXTDOMAIN ),
							"type" => "heading" );
							
		$options[] = array( "name" => __( "Where do you want to activate the facebook planner?", WP_PLANNER_TEXTDOMAIN ),
							"id" => "where_metabox",
							"type" => "multicheck",
							"options" => $select_post_types );
		
		$options[] = array( "name" => __( "Publish on facebook optional fields", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "What inputs do you want to be available for posting to facebook? It will appear on page/post details", WP_PLANNER_TEXTDOMAIN ),
							"id" => "inputs_available",
							"type" => "multicheck",
							"options" => $inputs_available );
		
		$options[] = array( "name" => __( "Featured Image size to publish on facebook", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "WIDTH x HEIGHT (Without measuring units. Example: 450x320)", WP_PLANNER_TEXTDOMAIN ),
							"id" => "featured_image_size",
							"type" => "text" );
		
		$options[] = array( "name" => __( "", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "or select a preset that your theme already has set up (\"Crop\" function not used here)", WP_PLANNER_TEXTDOMAIN ),
							"id" => "featured_image_size_predefined",
							"type" => 'select',
							"options" => array(
								'' => __('-- use above settings for resize --', WP_PLANNER_TEXTDOMAIN), 
								'thumbnail' => __('Thumbnail', WP_PLANNER_TEXTDOMAIN), 
								'medium' => __('Medium', WP_PLANNER_TEXTDOMAIN), 
								'large' => __('Large', WP_PLANNER_TEXTDOMAIN), 
								'full' => __('Full', WP_PLANNER_TEXTDOMAIN)) 
							);
							
		$options[] = array( "name" => __( "Crop image?", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "If yes, the image will crop to fit the above desired size. If no, the image will just resize with the dimensions provided.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "featured_image_size_crop",
							"type" => 'select',
							"options" => array(
								'true' => __('Yes', WP_PLANNER_TEXTDOMAIN), 
								'false' => __('No', WP_PLANNER_TEXTDOMAIN))
							);
							
		$options[] = array( "name" => __( "Publish on facebook default privacy option", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "What privacy option would you like to be default when you're posting to facebook? This option can also be adjusted manually when setting the scheduler for each post/page.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "default_privacy_option",
							"class" => "mini",
							"type" => "select",
							"options" => $wplannerfb_post_privacy_options );
							
		$options[] = array( "name" => __( "Admin email", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Notify this email adress each time you post something on facebook.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "email",
							"type" => "text" );
		
		$options[] = array( "name" => __( "Admin email subject", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Subject for plugin email notification.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "email_subject",
							"type" => "text" );
							
		$options[] = array( "name" => __( "Admin email message", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Email content for plugin notification.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "email_message",
							"type" => "textarea" );
		
		$options[] = array( "name" => __( "Cron timezone", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Use valid timezone format from <a href='http://php.net/manual/en/timezones.php' target='_blank'>php.net</a>. E.g: America/Detroit", WP_PLANNER_TEXTDOMAIN ),
							"id" => "timezone",
							"type" => "text" );
		
							
		// Begin basic settings
		$options[] = array( "name" => __( "Facebook Settings", WP_PLANNER_TEXTDOMAIN ),
							"type" => "heading" );
							
		$options[] = array( "name" => __( "Important Information", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "You need to create a Facebook App. You can do that <a href='http://developers.facebook.com' target='_blank'>here.</a> and enter its details in to the fields below.", WP_PLANNER_TEXTDOMAIN ),
							"id" => 'info',
							"type" => "info");
		
		$options[] = array( "name" => __( "Authorization facebook app", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Facebook Application authorization for cron job.", WP_PLANNER_TEXTDOMAIN ),
							"id" => 'auth',
							"type" => "authorization_button");
		
		$options[] = array( "name" => __( "Facebook App ID", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Insert your Facebook App ID here.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "app_id",
							"std" => "",
							"type" => "text" );
							
		$options[] = array( "name" => __( "Facebook App Secret.", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Insert your Facebook App Secret here.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "app_secret",
							"std" => "",
							"type" => "text" );
							
		$options[] = array( "name" => __( "Facebook Language", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Select the language for Facebook. More Information about the languages can be found <a target='_blank' href='http://developers.facebook.com/docs/internationalization/'>here</a>.", WP_PLANNER_TEXTDOMAIN ),
							"id" => "language",
							"std" => "en_US",
							"type" => "select",
							"class" => "mini",
							"options" => $select_fblanguage );
		
							
		// Facebook available user pages / groups
		if( isset($fb_all_user_pages_groups) && count($fb_all_user_pages_groups) > 0 ) {
			// Facebook available user pages
			if(count($fb_all_user_pages_groups->pages) > 0) {
				$fb_all_user_pages = array();
				foreach($fb_all_user_pages_groups->pages as $key => $value) {
					$fb_all_user_pages[] = $value->name;
				}
				
				$options[] = array( "name" => __( "Facebook - Pages", WP_PLANNER_TEXTDOMAIN ),
									"type" => "heading" );
									
				$options[] = array( "name" => __( "Activate \"Filter Pages\"", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Select \"Yes\" if you want to limit the pages shown when publishing and then select from above only what you wish to be shown. <i><strong>This is usefull if you have a lot of pages and/or you have a master facebook account and you wish to limit specific users to see other pages.</strong></i>", WP_PLANNER_TEXTDOMAIN ),
							"id" => "page_filter",
							"type" => "select",
							"class" => "mini",
							"options" => array('No', 'Yes') );
									
				$options[] = array( "name" => __( "What pages do you want to be available when publishing?", WP_PLANNER_TEXTDOMAIN ),
									"desc" => __( "<strong>This option only works if the \"Filter Pages\" option from above is \"Yes\"</strong>", WP_PLANNER_TEXTDOMAIN ),
									"id" => "available_pages",
									"type" => "multicheck",
									"options" => $fb_all_user_pages );
			}
				
			// Facebook available user groups
			if(count($fb_all_user_pages_groups->groups) > 0) {
				foreach($fb_all_user_pages_groups->groups as $key => $value) {
					$fb_all_user_groups[] = $value->name;
				}
				
				$options[] = array( "name" => __( "Facebook - Groups", WP_PLANNER_TEXTDOMAIN ),
									"type" => "heading" );
				
				$options[] = array( "name" => __( "Activate \"Filter Groups\"", WP_PLANNER_TEXTDOMAIN ),
							"desc" => __( "Select \"Yes\" if you want to limit the groups shown when publishing and then select from above only what you wish to be shown. <i><strong>This is usefull if you have a lot of groups and/or you have a master facebook account and you wish to limit specific users to see other groups.</strong></i>", WP_PLANNER_TEXTDOMAIN ),
							"id" => "group_filter",
							"type" => "select",
							"class" => "mini",
							"options" => array('No', 'Yes') );
							
				$options[] = array( "name" => __( "What groups do you want to be available when publishing?", WP_PLANNER_TEXTDOMAIN ),
									"desc" => __( "<strong>This option only works if the \"Filter Groups\" option from above is \"Yes\"</strong>", WP_PLANNER_TEXTDOMAIN ),
									"id" => "available_groups",
									"type" => "multicheck",
									"options" => $fb_all_user_groups );
			}
		}
		
											
		// Scheduled tasks
		$options[] = array( "name" => __( "Scheduled Tasks", WP_PLANNER_TEXTDOMAIN ),
							"type" => "heading" );
							
							
		$options[] = array( "name" => __( "Scheduled Tasks", WP_PLANNER_TEXTDOMAIN ),
							"id" => "scheduled_tasks",
							"options" => array(
								'table' 	=> $wpdb->prefix . 'fb_post_planner_cron',
								'fields' 	=> array(
									'id'			=> array(
										'type' 	=> 'row',
										'label' => '#',
										'width'	=> '40'
									),
									'id_post'		=> array(
										'type' 	=> 'link',
										'label' => __( "Post", WP_PLANNER_TEXTDOMAIN ),
										'width'	=> '70',
										'before'=> '<a href="%s" title="View/Edit publishing details" target="_blank">',
										'text' 	=> 'Edit',
										'after' => '</a>'
									),
									'status'		=> array(
										'type' 		=> 'enum',
										'values' 	=> array(
											0 	=> __( "New", WP_PLANNER_TEXTDOMAIN ), 
											1	=> __( "Finished", WP_PLANNER_TEXTDOMAIN ),
											2	=> __( "Running", WP_PLANNER_TEXTDOMAIN ),
											3	=> __( "Error", WP_PLANNER_TEXTDOMAIN )
										),
										'label' 	=> __( "Status", WP_PLANNER_TEXTDOMAIN ),
										'width'		=> '60'
									),
									'attempts'	=> array(
										'type' 	=> 'row',
										'label' => __( "Executed<br /><i>(times)</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '75'
									),
									'response'		=> array(
										'type'		=> 'row',
										'label'		=> __( "Last Response", WP_PLANNER_TEXTDOMAIN )
									),
									'post_to'		=> array(
										'type'		=> 'serialize',
										'label'		=> __( "Post to", WP_PLANNER_TEXTDOMAIN )
									),
									'email_at_post'	=> array(
										'type' 		=> 'enum',
										'values' 	=> array(
											'on' 	=> 'ON', 
											'off'	=> 'OFF'
										),
										'label' => __( "Email <i>notification</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '100'
									),
									'repeat_status'	=> array(
										'type' 		=> 'enum',
										'values' 	=> array(
											'on' 	=> 'ON', 
											'off'	=> 'OFF'
										),
										'label' => __( "Repeating?", WP_PLANNER_TEXTDOMAIN ),
										'width' => '90'
									),
									'repeat_interval'	=> array(
										'type' 	=> 'row',
										'label' => __( "Repeat <i>(hours)</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '65'
									),
									'run_date'	=> array(
										'type' 	=> 'row',
										'label' => __( "Run at<br /><i>date/time</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '80'
									),
									
									'started_at'	=> array(
										'type' 	=> 'row',
										'label' => __( "Starting<br /><i>date/time</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '80'
									),
									'ended_at'	=> array(
										'type' 	=> 'row',
										'label' => __( "Ending<br /><i>date/time</i>", WP_PLANNER_TEXTDOMAIN ),
										'width' => '80'
									),
									'deleted'	=> array(
										'type' 	=> 'button',
										'action' => 'delete',
										'label' => __( "Remove", WP_PLANNER_TEXTDOMAIN ),
										'width' => '80'
									)
								)
							),
							"type" => "listdatabase" );
							
		return $options;
	}
}