<?php 
	$configs=array(	'facebook_app_id'=>get_option('facebook_app_id'),

					'facebook_app_secret'=>get_option('facebook_app_secret'),

					

					'twitter_customer_key'=>get_option('twitter_customer_key'),

					'twitter_customer_secret'=>get_option('twitter_customer_secret'),

					'twitter_access_token'=>get_option('twitter_access_token'),

					'twitter_access_token_secret'=>get_option('twitter_access_token_secret'),

					

					'linkedin_api_key'=>get_option('linkedin_api_key'),

					'linkedin_secret_key'=>get_option('linkedin_secret_key'),

					'root_path'=>ABSPATH

				);

	$canvasUrl = admin_url("admin.php?page=vbsocial-scheduler");

	$folder = "upload"; //NAME OF YOUR FOLDER WITH IMAGES 

?>