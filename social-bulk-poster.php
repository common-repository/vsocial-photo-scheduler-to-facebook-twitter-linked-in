  <?php
/**
 * Plugin Name: vSocial Photo Scheduler to Facebook, Twitter, Linked-in
 * Description: Better than Hootsuite, this plugin allows you to Mass Schedule thousands of images and wp posts to Facebook, Twitter and LinkedIn Pages easily. Supports scheduling to multiple facebook pages.
 * Version: 1.0
 * Author: vBSocial Team
 * Author URI: http://vbsocial.com/
 * License: GPL2
 */
require "wp-schedular.php";
require "inc/ajax/generateposts.php";
require "inc/ajax/post.php";
require "inc/ajax/store_access.php";
require "inc/ajax/upload.php";
function sobp_enqueue_social_styles_scripts(){
	$plugin_url = plugins_url()."/vsocial-photo-scheduler-to-facebook-twitter-linked-in/inc/";
	wp_enqueue_style('main-stylesheet', $plugin_url.'css/bootstrap.min.css' );
	wp_enqueue_style( 'bootstrap-responsive', $plugin_url.'css/bootstrap-responsive.css' );
	wp_enqueue_style( 'animate', $plugin_url.'css/animate.css' );
	wp_enqueue_style( 'fileuploader', $plugin_url.'css/fileuploader.css' );
	wp_enqueue_style( 'inputfile', $plugin_url.'css/jquery.inputfile.css' );
	wp_enqueue_style( 'chosen', $plugin_url.'css/chosen.css' );
	wp_enqueue_style( 'jq-ui-css', $plugin_url.'css/jquery-ui.css' );
	
	wp_deregister_script('chosen');
	wp_register_script( 'chosen', $plugin_url.'js/chosen.js');

	wp_enqueue_script( 'jquery','','','',true);
	wp_enqueue_script( 'jquery-ui-core','','','',true);
	wp_enqueue_script( 'jquery-ui-tabs','','','',true);
	wp_enqueue_script( 'jquery-ui-accordion','','','',true);
	wp_enqueue_script( 'datatable', $plugin_url.'js/jquery.dataTables.js');
	wp_enqueue_script( 'bootstrap', $plugin_url.'js/bootstrap.js');
	wp_enqueue_script( 'fileuploader', $plugin_url.'js/fileuploader.js');
	wp_enqueue_script( 'chosen');
	wp_enqueue_script( 'inputfile', $plugin_url.'js/jquery.inputfile.js');
}
add_action("admin_head", "sobp_enqueue_social_styles_scripts" );
 
add_action( 'admin_menu', 'sobp_register_my_custom_menu_page' );
function sobp_register_my_custom_menu_page(){
    add_menu_page( 'vBSocial Scheduler', 'vBSocial Scheduler', 'manage_options', 'vsocial-photo-scheduler-to-facebook-twitter-linked-in', 'sobp_social_bulk_poster', plugins_url( 'vsocial-photo-scheduler-to-facebook-twitter-linked-in/images/sbp.png' ), 6 );
}
function sobp_social_bulk_poster(){
	require "inc/admin.php";
	}
	
	
function sobp_do_activate() {
$upload_dir = wp_upload_dir();
if(!is_dir($upload_dir['basedir']. '/sobp_upload/')) mkdir($upload_dir['basedir']. '/sobp_upload/',0777,true);
}

function isCurl(){
    if(function_exists('curl_version'))
      return true;
    else 
      return false;
    }
	
// Register plugin activation hooks
if ( !function_exists( 'wplanner_fb_activation_hook' ) ) {

	function wplanner_fb_activation_hook() {
		// setup cron
		wp_schedule_event(time(), 'hourly', 'wplanner_hourly_event');
		wplanner_fb_setup();
		$upload_dir = wp_upload_dir();
		if(!is_dir($upload_dir['basedir']. '/sobp_upload/')) mkdir($upload_dir['basedir']. '/sobp_upload/',0777,true);
		if(!is_dir($upload_dir['basedir']. '/sobp_db/')) mkdir($upload_dir['basedir']. '/sobp_db/',0777,true);
	}
}
register_activation_hook(__FILE__,'wplanner_fb_activation_hook' );

?>