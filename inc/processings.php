<?php 
$upload_dir = wp_upload_dir();
if(isset($_GET['id']) && is_numeric($_GET['id'])){
  $db->delete("pages", "ID",  $_GET['id']); 
  echo '<script type="text/javascript">
<!--
window.location = "index.php"
//-->
</script>';
  exit(0);
}


if (isset($_POST['rss'])) {
  if(strlen($_POST['rss'])>5){
	  $db->insert("rss",array('url' => strip_tags($_POST['rss'])));
  }
}
if (isset($_POST['facebook_app_id']) || isset($_POST['twitter_customer_key']) || isset($_POST['linkedin_api_key'])) {
extract($_POST);
update_option('facebook_app_id',$facebook_app_id);
update_option('facebook_app_secret',$facebook_app_secret);

update_option('twitter_customer_key',$twitter_customer_key);
update_option('twitter_customer_secret',$twitter_customer_secret);
update_option('twitter_access_token',$twitter_access_token);
update_option('twitter_access_token_secret',$twitter_access_token_secret);

update_option('linkedin_api_key',$linkedin_api_key);
update_option('linkedin_secret_key',$linkedin_secret_key);
$settings_updated='<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
	Settings updated Successfully.
    </div>';
}
if(!function_exists('curl_init')){
  echo '<div class="alert alert-error">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  ' . $l['errorcurl'] . '
  </div>';
  exit(0);
}


if(isset($_FILES["zip_file"]["name"])) {
  $filename = $_FILES["zip_file"]["name"];
  $source = $_FILES["zip_file"]["tmp_name"];
  $type = $_FILES["zip_file"]["type"];
  $put_zip_here=$_POST['put_zip_here'];
     if(!empty($put_zip_here)) $folder =$put_zip_here;
	 else  $folder="";
  $name = explode(".", $filename);
  $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
  foreach($accepted_types as $mime_type) {
    if($mime_type == $type) {
      $okay = true;
      break;
    } 
  }
  
  $continue = strtolower($name[1]) == 'zip' ? true : false;
  if(!$continue) {
    $message = '<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    ' . $l['errorzip'] . '
    </div>';
  }

  $target_path = $upload_dir['basedir']. '/sobp_upload'. "/" . $folder . "/" .$filename;  // change this to the correct site path
  if(move_uploaded_file($source, $target_path)) {
    $zip = new ZipArchive();
    $x = $zip->open($target_path);
    if ($x === true) {
      $zip->extractTo($upload_dir['basedir']. '/sobp_upload'. "/" . $folder . "/"); // change this to the correct site path
      $zip->close();

      unlink($target_path);
    }
    $message = '<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    ' . $l['successzip'] . '
    </div>';
  } else {  
    $message = '<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    ' . $l['errorupload'] . '
    </div>';
  }
}

if(isset($_POST['deleteFolder'])){

  function sobp_rmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
    rmdir($dir);
   }
 }

if(is_dir($upload_dir['basedir'] . '/sobp_upload/' . $_POST['deleteFolder'])){
  sobp_rmdir($upload_dir['basedir'] . '/sobp_upload/' . $_POST['deleteFolder']);
   $folder_del_msg = '<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
	Folder has been deleted successfully. 
    </div>';
}


}

if(isset($_POST['folder_name'])){ 
$dir=str_replace(" ","_",$_POST['folder_name']);
$dir=str_replace("-","_",$dir);
	if(!is_dir($upload_dir['basedir']. '/sobp_upload/' . $dir)){
	  mkdir($upload_dir['basedir']. '/sobp_upload/' .$dir,0777,true);
	   $folder_msg = '<div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
	Folder has been created successfully. 
    </div>';
	}
	else{
	$folder_msg = '<div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
	Sorry! Folder already exists.
    </div>';
		}
}
?>