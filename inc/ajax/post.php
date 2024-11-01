<?php 

function sobp_posttofacebook(){

define('PATH', dirname(__file__) . '/..');

require(PATH . '/config.php');

require (PATH . '/src/facebook.php');

require(PATH . '/class/JsonDB.class.php');



if (isset($_POST['text']) && isset($_POST['time']) && isset($_POST['type']) && isset($_POST['url']) && isset($_POST['image'])) {

$pages_array=explode(",",$_POST['page']);

  $facebook = new Facebook(array('appId'  => $configs['facebook_app_id'],'secret' => $configs['facebook_app_secret'], 'cookie' => true,  )); 

  Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;

  Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;

  $facebook->setFileUploadSupport(true);

  $db = new JsonDB( PATH . "/db/");

  $result = $db->selectAll("config");

  if(isset($result[0]['token']))

 $token = $result[0]['token'];

  $result = $db->select("pages", "ID", $_POST['page']);

  /*if (isset($result[0]['idalbum'])) {

    $album = $result[0]['idalbum'];

  }else{

    exit(0);

  }*/

  

$image_url=str_replace("c//","c/",$_POST['image']);

$image=str_replace(get_bloginfo('url').'/','',$_POST['image']);

$realpath='@' .ABSPATH .$image;



if( isset($_POST['post_on_twitter']) && $_POST['post_on_twitter']=='1'){

//post_on_twitter_linkedin($_POST['text'],ABSPATH.$image,$image_url,$configs);

$timee=$_POST['time'];

wp_schedule_single_event($timee,'schedule_post_on_twitter_linkedin', array($_POST['text'],ABSPATH.$image,$image_url,$configs));

echo $image;

	}	

	

if($_POST['type']=="image"){

if(!empty($pages_array)){

foreach($pages_array as $pagee){

  $pages_arr = array('access_token'=>$token ,'fields'=>'access_token' );

// Get Page access_token

$page_token = $facebook->api('/'.$pagee, 'get',$pages_arr);

$page_access_token=$page_token['access_token']; // get page access_token	

    $args = array(

		'message' => $_POST['text'],

		'scheduled_publish_time' => $_POST['time'],

		'image' => $realpath,

		//'aid' => $album,

		// 'no_story' => 1,

		'access_token' => $page_access_token,

		'published' => "0",

     );

    $args = json_encode($args);

    $args = json_decode($args, true);

	$photo = $facebook->api('/' .$pagee . '/photos', 'post', $args);

}

}

}

 

elseif ($_POST['type']=="video") {

  $args = array(

     'message' => $_POST['text'],

     'scheduled_publish_time' => $_POST['time'],

     'image' => '@' . realpath(PATH . '/' .$_POST['image']),

     'aid' => $album,

       // 'no_story' => 1,

     'access_token' => $page_access_token,

     'published' => "0",

     );

    $args = json_encode($args);

    $args = json_decode($args, true);

    $photo = $facebook->api('/' . $_POST['page'] . '/videos', 'post', $args);}

elseif ($_POST['type']=="url"){

 $args = array(

   'message' => $_POST['text'],

   'scheduled_publish_time' => $_POST['time'],

   'link' => $_POST['url'],

   'access_token' => $page_access_token,

   'published' => "0",

   ); 

 $args = json_encode($args);

 $args = json_decode($args, true);

 $link = $facebook->api('/' . $_POST['page'] . '/feed', 'post', $args);}

 

}

die();

}

add_action("wp_ajax_posttofacebook","sobp_posttofacebook");



function sobp_post_on_twitter_linkedin($text, $image_path, $image_url,$configs){

require "codebird232.php";

Codebird::setConsumerKey($configs['twitter_customer_key'],$configs['twitter_customer_secret']);

$cb = Codebird::getInstance();

$cb->setToken($configs['twitter_access_token'],$configs['twitter_access_token_secret']);

 

$params = array(

  'status' => $text ,

  'media[]' => $image_path 

);

$reply = $cb->statuses_updateWithMedia($params);

/*		

require ($configs['root_path'].'wp-content/plugins/vbsocial-scheduler/inc/src/simplelinkedin.class.php');	

$ln = new SimpleLinkedIn($configs['linkedin_api_key'], $configs['linkedin_secret_key']);

$ln->addScope('rw_nus');



if($ln->authorize()){

$ln->fetch('POST','/v1/people/~/shares',array('comment' =>$text,'content' => array('title' => $text,'submittedUrl' => $image_url),'visibility' => array('code' => 'anyone' )));

mail("compengr.uet@gmail.com",'mail sent',$image_url);

}

if($ln->authorize()){

    print_r ($ln->fetch('POST','/v1/people/~/shares',

        array(

            'comment' => $text,

            'content' => array(

                'title' => $text,

                'description' =>$text,

                'submittedUrl' => $image_url

            ),

            'visibility' => array('code' => 'anyone' )

        )

    ));

}

*/





}
add_action( 'schedule_post_on_twitter_linkedin', 'sobp_post_on_twitter_linkedin', 10, 4);
?>