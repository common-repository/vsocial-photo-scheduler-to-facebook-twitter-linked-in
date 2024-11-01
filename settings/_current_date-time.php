<?php
if( $_REQUEST['act'] === 'current_time' && isset($_REQUEST['randval']) ) {
	$tmp_path = explode('wp-content', __FILE__);
	require_once( $tmp_path[0] . 'wp-config.php' );

	$current_time = explode(':', current_time('mysql'));

	echo $current_time[0] .':'. $current_time[1];
}else{
	header("HTTP/1.0 404 Not Found");
}
?>
