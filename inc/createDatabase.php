<?php 

require(PATH . '/class/JsonDB.class.php');

$upload_dir = wp_upload_dir();
$path=$upload_dir['basedir'];
if (!is_dir($path. '/sobp_db/')) {

	mkdir($path . '/sobp_db/');

	chmod($path . '/sobp_db/', 0755);

}



$db = new JsonDB( $path . "/sobp_db/");



if (!file_exists($path . '/sobp_db/config.json')) {

	file_put_contents($path . '/sobp_db/config.json', "");

	chmod($path. '/sobp_db/config.json', 0640);

}



if (!file_exists($path . '/sobp_db/pages.json')) {

	file_put_contents($path . '/sobp_db/pages.json', "");

	chmod($path . '/sobp_db/pages.json', 0640);

}



if (!file_exists($path . '/sobp_db/rss.json')) {

	file_put_contents($path . '/sobp_db/rss.json', "");

	chmod($path . '/sobp_db/rss.json', 0640);

}





if (!file_exists($path . '/sobp_db/rssPosts.json')) {

	file_put_contents($path . '/sobp_db/rssPosts.json', "");

	chmod($path . '/sobp_db/rssPosts.json', 0640);

}



/*

			JsonDB -> selectAll ( "table" )  - Returns the entire file as array

			JsonDB -> update ( "table", "key", "value", ARRAY ) - Replaces the line which corresponds to the key/value with the array-data

			JsonDB -> updateAll ( "table", ARRAY ) - Replaces the entire file with the array-data

			JsonDB -> insert ( "table", ARRAY ) - Appends a row, returns true on success

			JsonDB -> delete ( "table", "key", "value" ) - Deletes all lines which corresponds to the key/value, returns number of deleted lines

			JsonDB -> deleteAll ( "table" ) - Deletes the whole data, returns "true" on success



$db->insert("config",array('token' => '123'));

$db->update ( "config", "token", "123", array('token' => '1234') )

$result = $db->selectAll("config");

var_dump($db->selectAll("config"));

$db->deleteAll ( "config" );

echo count($db->selectAll("config"));

*/



?>