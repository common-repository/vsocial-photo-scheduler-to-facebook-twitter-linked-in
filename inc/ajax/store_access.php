<?php 

function sobp_add_access_token(){ 

define('PATH_ACCESS_TOKEN', dirname(__file__) . '/..');



require(PATH_ACCESS_TOKEN . '/config.php');

require(PATH_ACCESS_TOKEN . '/class/JsonDB.class.php');

$db = new JsonDB( PATH_ACCESS_TOKEN . "/db/");

//print_r($_POST);

if(isset($_POST['access_token']) && strlen($_POST['access_token'])>20){

	$_POST = array_map('strip_tags_deep', $_POST);

	$token = getSslPage('https://graph.facebook.com/oauth/access_token?client_id=' . $configs['facebook_app_id'] . 

	'&client_secret=' .$configs['facebook_app_secret']. '&grant_type=fb_exchange_token&fb_exchange_token=' . $_POST['access_token']);

	$paramsfb = null;

	parse_str($token, $paramsfb);

	//print_r($token);

	if(isset($paramsfb['access_token']) && strlen($paramsfb['access_token'])>20){

		$db->deleteAll ( "config" );

		$token = $paramsfb['access_token'];

		$expire = $_POST['expires_in'];

		if($db->insert("config",array('token' => $token, 'expire' => $expire))) echo "[{'success' : 1}]";

	}}



exit(0);

}

add_action("wp_ajax_add_access_token","sobp_add_access_token");

function getSslPage($url) {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	curl_setopt($ch, CURLOPT_HEADER, false);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_REFERER, $url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

	$result = curl_exec($ch);

	curl_close($ch);

	return $result;

}

?>