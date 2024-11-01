<?php
// Plugin facebook SDK load

require_once ( WP_PLANNER_DIR . 'lib/scripts/facebook/facebook.php' );



/*

	Version: 	1.0.0

	Author: 	Web-Wizards

*/

class fbPlannerUtils

{

    // Hold an instance of the class

    private static $instance;

	

	// Hold an utils of the class

    private static $utils;

	

    private static $fb;

 

    // The singleton method getInstance

    public static function getInstance()

    {

        if (!isset(self::$instance)) {

            self::$instance = new fbPlannerUtils;

        }

        return self::$instance;

    }

	

	// The constructor, call on class instance

	public function __construct(){

	

		// tmp string

		$wplannerfb_settings = get_option( 'wplannerfb' );

		

		// create utils

		self::$utils = array(

			'token'		=> get_option('wplanner_token'),

			'appId'		=> $wplannerfb_settings['app_id'],

			'secret'	=> $wplannerfb_settings['app_secret'],

			'inputs_available' => $wplannerfb_settings['inputs_available']

		);

		

		// try to login on fb with static facebook key

		if(!$this->fb_login()){

			die('Invalid FB login!');

		}

	}

	

	public function fb_login() {

		// Create our Application instance (replace this with your appId and secret).

		self::$fb = new wwPP_Facebook(array(

			'appId'  => self::$utils['appId'],

			'secret' => self::$utils['secret'],

		));

		

		// set saved access token

		self::$fb->setAccessToken(self::$utils['token']);

		

		// Get User ID

		$user = self::$fb->getUser();

		if(trim($user) == ""){

			return false;

		}

		

		return true;

	}

	

	public function getFbUserData() {

		if($this->fb_login()){

			return self::$fb->api('/me');

		}else{

			return array();

		}

	}

	

	public function publishToWall($id, $whereToPost, $postPrivacy, $postData = NULL) {

		// retrive WP post metadata

		if( is_null($postData) ) {

			$postData = $this->getPostByID($id);

		}

		

		// where to publish post

		$whereToPost = unserialize($whereToPost);

		if(trim($whereToPost['profile']) == '' && trim($whereToPost['page_group']) == '')

			return false;

			

		if(count($postData) > 0) {

			try {

				$post_link = trim($postData['link']) == 'post_link' ? get_permalink($id) : $postData['link'];

				

				if($postPrivacy == 'CUSTOM') {

					$q_postPrivacy = array('value' => $postPrivacy, 'friends' => 'SELF');

				}else{

					$q_postPrivacy = array('value' => $postPrivacy);

				}

				

				if( trim($whereToPost['profile']) == 'on' ) {

					$statusUpdate = self::$fb->api(

						'/me/feed', 

						'post',

						array(

							'name' 			=> stripslashes($postData['name']),

							'message' 		=> self::$utils['inputs_available']['message'] == '1' ? stripslashes($postData['message']) : '',

							'privacy' 		=> $q_postPrivacy,

							'description' 	=> stripslashes($postData['description']),

							'picture'	 	=> self::$utils['inputs_available']['image'] == '1' ? $postData['picture'] : '',

							'caption'		=> self::$utils['inputs_available']['caption'] == '1' ? stripslashes($postData['caption']) : '',

							'link'			=> $post_link

						)

					);

				}

				$page_groups=$whereToPost['page_group'];

				print_r($postData);

	if(is_array($page_groups) && !empty($page_groups)) {

		$args = array(

			'name' 			=> stripslashes($postData['name']),

			'message' 		=> self::$utils['inputs_available']['message'] == '1' ? stripslashes($postData['message']) : '',

			'description' 	=> stripslashes($postData['description']),

			'picture'	 	=> self::$utils['inputs_available']['image'] == '1' ? stripslashes($postData['picture']) : '',

			'caption'		=> self::$utils['inputs_available']['caption'] == '1' ? stripslashes($postData['caption']) : '',

			'link'			=> $post_link

		);

					$batchPost=array();

					foreach($page_groups as $one_page_group) {

							$page_access_token = null;

							$whereToPost = explode('##', $one_page_group);

							$postTo_ident = $whereToPost[0];

							$postTo_id = $whereToPost[1];

							

							if($postTo_ident == 'page') {

								$page_access_token = $whereToPost[2];

								

								if( !empty($page_access_token) ) {

									$args['access_token'] = $page_access_token;

								}

							}

			$batchPost[] = array('method' => 'POST','relative_url' =>'/' . $postTo_id . '/feed','body' => http_build_query($args));

							unset($args['access_token']);

					}

				$statusUpdate = self::$fb->api('?batch='.urlencode(json_encode($batchPost)), 'POST');

				}

				//aspire sol

				//twitter start

				$imagea=str_replace(get_bloginfo('url').'/','',$postData['picture']);

				$realpathimage=ABSPATH .$imagea;

				echo get_option('twitter_customer_key');

				require 'codebird232.php';

				Codebird::setConsumerKey(get_option('twitter_customer_key'),get_option('twitter_customer_secret'));

$cb = Codebird::getInstance();

$cb->setToken(get_option('twitter_access_token'),get_option('twitter_access_token_secret')); 

				$params = array(

				  'status' => stripslashes($postData['name']),

				  'media[]' => $realpathimage 

				);

				$reply = $cb->statuses_updateWithMedia($params);

				//twitter ends

				return true;

			} catch (wwPP_FacebookApiException $e) {

				var_dump('<pre>',$e ,'</pre>'); die; 

				return false;

			}

		}

	}

	

	public function getPostByID($id){

		if((int)$id > 0){

			return array(

				'name' 			=> get_post_meta($id, '_wplannerfb_title', true),

				'link' 			=> get_post_meta($id, '_wplannerfb_permalink', true),

				'description' 	=> get_post_meta($id, '_wplannerfb_description', true),

				'caption' 		=> get_post_meta($id, '_wplannerfb_caption', true),

				'message' 		=> get_post_meta($id, '_wplannerfb_message', true),

				'picture'	 	=> get_post_meta($id, '_wplannerfb_image', true)

			);

		}

		return array();

	}

}