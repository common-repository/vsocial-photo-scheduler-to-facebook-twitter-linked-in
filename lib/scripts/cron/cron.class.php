<?php 
$absolute_path = __FILE__;

$path_to_file = explode( 'wp-content', $absolute_path );

$path_to_wp = $path_to_file[0];



// Access WordPress

/*require_once( $path_to_wp . '/wp-load.php' );*/



// Plugin facebook SDK load

require_once ( WP_PLANNER_DIR . 'lib/scripts/fb-utils/fb-utils.class.php' );



/*

	Version: 	1.0.0

	Author: 	Web-Wizards

*/

class wPlannerCron{
	

    // Hold an instance of the class

    private static $instance;

	

	// Hold an utils of the class

    private static $utils;

	

	// Hold an db

    private static $db;

	

	// Hold an fbUtils

    private static $fbUtils;

	

	private static $now = null;

 

    // The singleton method getInstance

    public static function getInstance()

    {

        if (!isset(self::$instance)) {

            self::$instance = new wPlannerCron;

        }

        return self::$instance;

    }

	

	// The constructor, call on class instance

	public function __construct(){

		global $wpdb;

		

		// store wpdb instance

		self::$db = $wpdb;

		

		// start instance of fb post planner

		self::$fbUtils = fbPlannerUtils::getInstance();

		

		// tmp array

		$wplannerfb_settings = get_option( 'wplannerfb' );

		

		// create utils

		self::$utils = array(

			'email_prompt'  	=> $wplannerfb_settings['email'],

			'email_subject'  	=> $wplannerfb_settings['email_subject'],

			'email_message'  	=> $wplannerfb_settings['email_message'],

			'email_message_lock'=> 0,

			'cron'				=> array(

				'table'			=> self::$db->prefix . 'fb_post_planner_cron',

				'time_zone'		=> $wplannerfb_settings['timezone'],

				'first_time '	=> time()

			)

		);

		

		// set new timezone

		if(trim(self::$utils['cron']['time_zone']) != ""){

			$this->setTImezone();

		}

		

		// update now

		self::$now = strtotime(date("Y-m-d H:i:s"));

	}



	private function checkPostTo($db_postTo) {

		$post_to = '';

		$db_postTo = unserialize($db_postTo);

		

		$pg = get_option('wplanner_user_pages');

		if( trim($pg) != '' ){

			$pg = @json_decode($pg);

		}

		

		if( trim($db_postTo['profile']) == 'on' ) {

			$post_to = '<li>Profile</li>';

		}

		

		if( trim($db_postTo['page_group']) != '' ) {

			$page_group = explode('##', $db_postTo['page_group']);

			

			if($page_group[0] == 'page') {

				foreach($pg->pages as $k => $v) {

					if($v->id == $page_group[1]) {

						$post_to_title = $v->name;

					}

				}

			}else if($page_group[0] == 'group') {

				foreach($pg->groups as $k => $v) {

					if($v->id == $page_group[1]) {

						$post_to_title = $v->name;

					}

				}

			}

			

			$post_to .= '<li>' . (ucfirst($page_group[0])).": " . $post_to_title . '</li>';

		}

		

		return '<ul>' . $post_to . '</ul>';

	}

	

	private function publish_to_wall() {

        wp_mail( self::$utils['email_prompt'], self::$utils['email_prompt'], self::$utils['email_message']);

    }

	

	private function setTImezone() {

		date_default_timezone_set(self::$utils['cron']['time_zone']);

	}

	

	private function getAllNewTasks() {

		// CRON: set default status (no tasks running)

		$task_status = false;

		

		// status 1 = completed tasks

		$tasks = self::$db->get_results( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where 1=1 and status!='1' and deleted='0'", ARRAY_A );

		

		// exit if no tasks to be run

		if( isset($tasks) && count($tasks) > 0 ) {

			// loop tasks

			foreach ($tasks as $key => $task) {

				// run only if task is not running

				if(isset($task['status']) && $task['status'] != '2') {

					// get data from DB and convert to unix time

		            $expiration_date = strtotime($task['run_date']); 

					

		            if (self::$now >= $expiration_date) {

						// set start date

						self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set started_at=NOW(), status=2 WHERE id_post=" . $task['id_post']);

						

						// send post to wall, update DB

		$publishToFBResponse = self::$fbUtils->publishToWall($task['id_post'], $task['post_to'], $task['post_privacy']);

						

						if(isset($publishToFBResponse) && $publishToFBResponse === true) {

							$updateStatus = ($task['repeat_status'] == 'off' ? 1 : 0);

							

							self::$db->query("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status={$updateStatus}, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='".__('Published with success on Facebook', WP_PLANNER_TEXTDOMAIN)."' WHERE id_post=" . $task['id_post']);

							

							if(isset($task['email_at_post']) && $task['email_at_post'] == 'on') {

								$updatedTask = self::$db->get_row( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where id_post=".($task['id_post'])." and deleted='0'", ARRAY_A );

								$postData = self::$fbUtils->getPostByID($task['id_post']);

								

								self::$utils['email_message'] .= '<br /><br />

									-----------------------------------------------------------------------------------------------------------------------------<br /><br />

									<div style="font-size:18px;"><strong>'.__('Post ID', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['id_post'].' (<a href="'.$postData['link'].'" target="_blank">'.__('View', WP_PLANNER_TEXTDOMAIN).'</a> | <a href="'.get_edit_post_link($task['id_post']).'" target="_blank">'.__('Edit', WP_PLANNER_TEXTDOMAIN).'</a>)</div><br />

									<span style="text-decoration:underline;">'.__('Published details', WP_PLANNER_TEXTDOMAIN).'</span><br />

									<strong>'.__('Title', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['name'].'<br />

									<strong>'.__('Description', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['description'].'<br />

									'.(isset($postData['caption']) && trim($postData['caption']) != '' ? '<strong>'.__('Caption', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['caption'].'<br />' : '').'

									'.(isset($postData['message']) && trim($postData['message']) != '' ? '<strong>'.__('Message', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['message'].'<br />' : '').'

									<strong>'.__('Picture', WP_PLANNER_TEXTDOMAIN).':</strong> '.(isset($postData['picture']) && trim($postData['picture']) != '' ? __('YES', WP_PLANNER_TEXTDOMAIN).' (<a href="'.($postData['picture']).'" target="_blank">'.__('view picture', WP_PLANNER_TEXTDOMAIN).'</a>)' : __('NO', WP_PLANNER_TEXTDOMAIN)).'<br />

									<br />

									<span style="text-decoration:underline;">'.__('Publishing settings', WP_PLANNER_TEXTDOMAIN).'</span><br />

									<strong>'.__('Privacy', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['post_privacy'].'<br />

									<strong>'.__('Published to', WP_PLANNER_TEXTDOMAIN).':</strong> '.(self::checkPostTo($task['post_to'])).'<br />

									<strong>'.__('Started share at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['started_at'].'<br />

									<strong>'.__('Ended share at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['ended_at'].'<br />

								';

								if(isset($task['repeat_interval']) && $task['repeat_interval'] > 0) {

									self::$utils['email_message'] .= '

										<strong>'.__('Next run at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['run_date'].'<br />

										<strong>'.__('Repeat interval', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['repeat_interval'].' '.__('hour(s)', WP_PLANNER_TEXTDOMAIN).'<br />

									';

								}

								self::$utils['email_message'] .= '

									<strong>'.__('Executed', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['attempts'].' '.__('time(s)', WP_PLANNER_TEXTDOMAIN).'<br />

									<strong>'.__('Last response', WP_PLANNER_TEXTDOMAIN).':</strong> '.(isset($updatedTask['response']) && trim($updatedTask['response']) != __('Published with success on Facebook', WP_PLANNER_TEXTDOMAIN) ? '<span style="color: red; font-weight: bold;">'.$updatedTask['response'].'</span>' : $updatedTask['response'])

								;

							}

						}else{

							

							self::$db->query(self::$db->prepare("UPDATE " . ( self::$utils['cron']['table'] ) . " set attempts=attempts+1, run_date=DATE_ADD(DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00'), INTERVAL repeat_interval HOUR), status=3, ended_at='".(date('Y-m-d H:i:s', self::$now))."', response='" . ($publishToFBResponse) . "' WHERE id_post = %d", $task['id_post']));

							

							if( isset(self::$utils['email_message_lock']) && self::$utils['email_message_lock'] === 0 ) {

								self::$utils['email_message_lock'] = 1;

								self::$utils['email_message'] = '<h3 style="color:red; font-weight:bold;">'.__('Error on publish !', WP_PLANNER_TEXTDOMAIN).'</h3>';

							}

							

							if(isset($task['email_at_post']) && $task['email_at_post'] == 'on') {

								$updatedTask = self::$db->get_row( "SELECT * FROM " . ( self::$utils['cron']['table'] ) . " where id_post=".($task['id_post'])." and deleted='0'", ARRAY_A );

								$postData = self::$fbUtils->getPostByID($task['id_post']);

								

								self::$utils['email_message'] .= '<br /><br />

									-----------------------------------------------------------------------------------------------------------------------------<br /><br />

									<div style="font-size:18px;"><strong>'.__('Post ID', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['id_post'].' (<a href="'.$postData['link'].'" target="_blank">'.__('View', WP_PLANNER_TEXTDOMAIN).'</a> | <a href="'.get_edit_post_link($task['id_post']).'" target="_blank">'.__('Edit', WP_PLANNER_TEXTDOMAIN).'</a>)</div><br />

									<span style="text-decoration:underline;">'.__('Published details', WP_PLANNER_TEXTDOMAIN).'</span><br />

									<strong>'.__('Title', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['name'].'<br />

									<strong>'.__('Description', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['description'].'<br />

									'.(trim($postData['caption']) != '' ? '<strong>'.__('Caption', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['caption'].'<br />' : '').'

									'.(trim($postData['message']) != '' ? '<strong>'.__('Message', WP_PLANNER_TEXTDOMAIN).':</strong> '.$postData['message'].'<br />' : '').'

									<strong>'.__('Picture', WP_PLANNER_TEXTDOMAIN).':</strong> '.(trim($postData['picture']) != '' ? __('YES', WP_PLANNER_TEXTDOMAIN).' (<a href="'.($postData['picture']).'" target="_blank">'.__('view picture', WP_PLANNER_TEXTDOMAIN).'</a>)' : __('NO', WP_PLANNER_TEXTDOMAIN)).'<br />

									<br />

									<span style="text-decoration:underline;">'.__('Publishing settings', WP_PLANNER_TEXTDOMAIN).'</span><br />

									<strong>'.__('Privacy', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['post_privacy'].'<br />

									<strong>'.__('Published to', WP_PLANNER_TEXTDOMAIN).':</strong> '.(self::checkPostTo($task['post_to'])).'<br />

									<strong>'.__('Started share at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['started_at'].'<br />

									<strong>'.__('Ended share at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['ended_at'].'<br />

								';

								if(isset($task['repeat_interval']) && $task['repeat_interval'] > 0) {

									self::$utils['email_message'] .= '

										<strong>'.__('Next run at', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['run_date'].'<br />

										<strong>'.__('Repeat interval', WP_PLANNER_TEXTDOMAIN).':</strong> '.$task['repeat_interval'].' '.__('hour(s)', WP_PLANNER_TEXTDOMAIN).'<br />

									';

								}

								self::$utils['email_message'] .= '

									<strong>'.__('Executed', WP_PLANNER_TEXTDOMAIN).':</strong> '.$updatedTask['attempts'].' '.__('time(s)', WP_PLANNER_TEXTDOMAIN).'<br />

									<strong>'.__('Last response', WP_PLANNER_TEXTDOMAIN).':</strong> '.(trim($updatedTask['response']) != __('Published with success on Facebook', WP_PLANNER_TEXTDOMAIN) ? '<span style="color: red; font-weight: bold;">'.$updatedTask['response'].'</span>' : $updatedTask['response'])

								;

							}

						}

						

						$task_status = true;

					}

				}

			}

			

			if( (isset($task_status) && $task_status === true) && (isset(self::$utils['email_message']) && trim(self::$utils['email_message']) != '') ) {

				add_filter('wp_mail_content_type', create_function('', 'return "text/html";'));

				wp_mail( self::$utils['email_prompt'], self::$utils['email_subject'], self::$utils['email_message'] );

			}

		}

		

		return true;

	}

	

	public function wplanner_run_cron() {

		if( $this->getAllNewTasks() ) {

			return '[Facebook - Post Planner]: CRON started';

		}else{

			return '[Facebook - Post Planner]: No tasks to be run';

		}

	}
}

$wpPCron = wPlannerCron::getInstance();



// try to get non running tasks and execute then

echo $wpPCron->wplanner_run_cron();

