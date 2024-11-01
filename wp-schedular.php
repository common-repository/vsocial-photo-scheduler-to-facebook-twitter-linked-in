<?php 
define( 'WP_PLANNER_VERSION', '1.3' );
define( 'WP_PLANNER_URL', WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
define( 'WP_PLANNER_DIR', WP_PLUGIN_DIR . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) ));
define( 'WP_PLANNER_SETTINGS', WP_PLANNER_DIR . 'settings/' );
define( 'WP_PLANNER_TEXTDOMAIN', 'wplannerfb' );
		
// Loading the textdomain to get the plugin ready for translation
load_plugin_textdomain( WP_PLANNER_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

// Plugin settings
require_once ( WP_PLANNER_SETTINGS . 'settings.php' );

// Meta boxes
require_once ( WP_PLANNER_DIR . 'lib/metabox.php' );

// Plugin facebook SDK load
require_once ( WP_PLANNER_DIR . 'lib/scripts/facebook/facebook.php' );

// Get plugin settings
$wplannerfb_settings = get_option( 'wplannerfb' );

// Create our Application instance (replace this with your appId and secret).
if( (isset($wplannerfb_settings['app_id']) && trim($wplannerfb_settings['app_id']) != '') && ( isset($wplannerfb_settings['app_secret']) && trim($wplannerfb_settings['app_secret']) != '') ) {
	$facebook = new wwPP_Facebook(array(
		'appId'  => $wplannerfb_settings['app_id'],
		'secret' => $wplannerfb_settings['app_secret']
	));
}

/* Add Options menu item to Admin Bar. */
if(!function_exists( 'wplanner_fb_adminbar' )) {
	function wplanner_fb_adminbar() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu( array(
			'id' => 'wplanner_fb_options',
			'title' => __( 'Wordpress Posts Schedular Options', WP_PLANNER_TEXTDOMAIN ),
			'href' => admin_url( 'admin.php?page=wplannerfb' )
		));
	}
}

// Rolecheck (Super Admin / Administrator / Editor
if (!function_exists( 'wplanner_fb_rolescheck' )) {
	function wplanner_fb_rolescheck () {
		global $wplannerfb_settings;
		
		if ( current_user_can('activate_plugins') ) {
			/* Add Options menu item to Admin Bar. */
			add_action( 'admin_menu', 'wplanner_fb_add_page' );
			add_action( 'wp_before_admin_bar_render', 'wplanner_fb_adminbar' );
		}
		
		// Hook to create the plugin post options meta box
		if( current_user_can('manage_categories') ) {
			if( isset($wplannerfb_settings) && count($wplannerfb_settings) > 1 ) {
				add_action( 'add_meta_boxes', 'wplanner_fb_create' );
			}
		}
	}
}
add_action( 'init', 'wplanner_fb_rolescheck' );	

// Adds actions to hook in the required css and javascript
add_action( "admin_print_styles",'wplanner_fb_load_styles' );
add_action( "admin_print_scripts", 'wplanner_fb_load_scripts' );

add_action( 'admin_init', 'wplanner_fb_init' );
add_action( 'admin_init', 'wplanner_fb_mlu_init' );

// Register plugin database
if ( !function_exists( 'wplanner_fb_setup' ) ) {
	function wplanner_fb_setup() {
        global $wpdb;

        // cron table
        $create_table = "
		CREATE TABLE IF NOT EXISTS `" . ($wpdb->prefix . "fb_post_planner_cron") . "` (
			`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_post` BIGINT(20) NOT NULL,
			`post_to` TEXT NULL,
			`post_to-page_group` VARCHAR(255) NULL DEFAULT NULL,
			`post_privacy` VARCHAR(255) NULL DEFAULT NULL,
			`email_at_post` ENUM('off','on') NOT NULL DEFAULT 'off',
			`status` SMALLINT(1) NOT NULL DEFAULT '0',
			`response` TEXT NULL,
			`started_at` TIMESTAMP NULL DEFAULT NULL,
			`ended_at` TIMESTAMP NULL DEFAULT NULL,
			`run_date` DATETIME NULL DEFAULT NULL,
			`repeat_status` ENUM('off','on') NOT NULL DEFAULT 'off' COMMENT 'one-time | repeating',
			`repeat_interval` INT(11) NULL DEFAULT NULL COMMENT 'minutes',
			`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`attempts` SMALLINT(6) NOT NULL,
			`deleted` TINYINT(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			UNIQUE INDEX `id_post` (`id_post`),
			INDEX `status` (`status`),
			INDEX `deleted` (`deleted`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8";

        if ($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "fb_post_planner_cron" . "'") != $wpdb->prefix . "fb_post_planner_cron") {
            require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
            dbDelta($create_table);
        }
    }
}


// Register plugin activation hooks
if ( !function_exists( 'wplanner_do_this_hourly' ) ) {
	function wplanner_do_this_hourly() {
		// Plugin cron class loading
		require_once ( WP_PLANNER_DIR . 'lib/scripts/cron/cron.class.php' );
	}
}
add_action('wplanner_hourly_event', 'wplanner_do_this_hourly');

if ( !function_exists( 'wplanner_fb_delete_options' ) ) {

	function wplanner_fb_delete_options() {
		global $wpdb;
	
		wp_clear_scheduled_hook('wplanner_hourly_event');
	
		$wpdb->query("drop table if exists `" . $wpdb->prefix . "fb_post_planner_cron" . "`");

		$plugin_options = $wpdb->get_results('SELECT * FROM `' . $wpdb->prefix . 'options` where 1=1 and option_name REGEXP "wplannerfb_([a-z])"');
		
		if ($plugin_options) {
			foreach ($plugin_options as $option) {
				delete_option($option->option_name);
			}
		}
		delete_option( 'wplannerfb' );
	}
}
register_deactivation_hook(__FILE__,'wplanner_fb_delete_options' );




register_uninstall_hook( __FILE__, 'wplanner_fb_delete_options' );


if ( !function_exists( 'wplanner_fb_postFB_callback' ) ) {

	function wplanner_fb_postFB_callback () {
		$id 		= (int)$_POST['postId'];
		$wherePost 	= serialize($_POST['postTo']);

		$privacy 	= $_POST['privacy'];
		
		$postData = array(
			'name' 			=> $_POST['wplannerfb_title'],
			'link' 			=> ( trim($_POST['wplannerfb_permalink']) == 'custom_link' ? trim($_POST['wplannerfb_permalink_value']) : get_permalink($id) ),
			'description' 	=> $_POST['wplannerfb_description'],
			'caption' 		=> $_POST['wplannerfb_caption'],
			'message' 		=> $_POST['wplannerfb_message'],
			'picture'	 	=> $_POST['wplannerfb_image']
		);
		
		// Plugin facebook utils load
		require_once ( WP_PLANNER_DIR . 'lib/scripts/fb-utils/fb-utils.class.php' );

		// start instance of fb post planner
		$fbUtils = fbPlannerUtils::getInstance();
		$publishToFBResponse = $fbUtils->publishToWall($id, $wherePost, $privacy, $postData);
		
		if($publishToFBResponse === true){
			echo 'OK';
		}else{
			echo 'ERROR';
		}
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_publish_fb_now', 'wplanner_fb_postFB_callback');

/* WP Ajax requests */
if ( !function_exists( 'wplanner_fb_getFeaturedImage' ) ) {
	function wplanner_fb_getFeaturedImage () {
		global $wplannerfb_settings;
		
		$postId = (int)$_POST['postId'];
		$result = array(
			'text' => 'Post has no featured image.',
			'status' => 'ERR'
		);
		
		// check if the post has a Post Thumbnail assigned to it.
		if ( has_post_thumbnail($postId) ) {
			//$__featuredImage = get_the_post_thumbnail($postId, 'large'); // img html format
			$imgSize = $wplannerfb_settings['featured_image_size'];
			$imgSize_predefined = $wplannerfb_settings['featured_image_size_predefined'];
			
			if( trim($imgSize) != '' && $imgSize_predefined == '' ) {
				$imgSize = explode('x', strtolower($imgSize));
				$imgSize = array('width' => trim($imgSize[0]), 'height' => trim($imgSize[1]));
				$imgCrop = $wplannerfb_settings['featured_image_size_crop'] == 'true' ? true : false;
				$suffix = $imgSize['width'] .'x'. $imgSize['height'] . '_wplannerfb';
				$dest_path = wp_upload_dir();
				$jpeg_quality = 90;
				
				$__featuredImage = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), 'full' );
				$__img_path = str_replace( str_replace('/plugins', '', WP_PLUGIN_URL), str_replace('/plugins', '', WP_PLUGIN_DIR), $__featuredImage[0] );
				$__resizedFeaturedImage = image_resize( $__img_path, $imgSize['width'], $imgSize['height'], $imgCrop, $suffix, $dest_path['path'], $jpeg_quality );
				$__resizedFeaturedImage = str_replace( str_replace('/plugins', '', WP_PLUGIN_DIR), str_replace('/plugins', '', WP_PLUGIN_URL), $__resizedFeaturedImage );
			}else{
				$__resizedFeaturedImage = wp_get_attachment_image_src( get_post_thumbnail_id( $postId ), $imgSize_predefined );
				$__resizedFeaturedImage = $__resizedFeaturedImage[0];
			}
			
			$result = array(
				'text'	=> $__resizedFeaturedImage,
				'status' => (!empty($__resizedFeaturedImage) ? 'OK' : 'ERR')
			);
		}
		
		if(count($result) > 0){
			echo json_encode($result);
		}else{
			// Error messages in JSON format!
		}
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_fb_getFeaturedImage', 'wplanner_fb_getFeaturedImage');

if ( !function_exists( 'wplanner_deleterow_callback' ) ) {
	function wplanner_deleterow_callback () {
		global $wpdb;
		$rowid 		= (int)$_POST['rowid'];
		
		// delete schedule
		$deleteSchedule = $wpdb->query("DELETE FROM ".($wpdb->prefix . 'fb_post_planner_cron')." WHERE id='".($rowid)."' ");
		
		if($deleteSchedule) {
			echo 'OK';
		}else{
			_e( 'Error deleting this task!', WP_PLANNER_TEXTDOMAIN );
		}
		
		die(); // this is required to return a proper result
	}
}
add_action('wp_ajax_fbppdeletetask', 'wplanner_deleterow_callback');