<?php 

function sobp_sendtofacebook(){
$upload_dir = wp_upload_dir();
define('PATH_POST', $upload_dir['basedir']. '/sobp_upload');

extract($_POST);	

if (isset($_POST['subfolder']) ) {

	$delay_time=(int)$delay_days*60*60*24+(int)$delay_hours*60*60+(int)$delay_minutes*60;

	$posts = array('dataehora' => $_POST['dataehora'], 'time' => $delay_time, 'folder' => $_POST['subfolder'], 'from' => $_POST['from']);

	if(isset($_POST['page'])) $posts['page'] = $_POST['page'];

	else $posts['page']='';

		

	$posts['post_on_twitter']=$post_on_twitter;

	if($_POST['from']=="folder"){

	foreach($_POST['subfolder'] as $subfolder){

		if(is_dir(PATH_POST . "/" . $subfolder) && is_numeric($_POST['numposts'])){

		  $images = glob(PATH_POST . "/" . $subfolder . "/*");

		  if (is_array($images) && !empty($images)){

			shuffle($images);

			$x = 0;

			foreach ($images as $key => $value){

					if($x<$_POST['numposts']){

				  if(!is_dir($value)){

						$value = explode('/', $value);

						$posts['images'][] =  $subfolder.'/'.$value[count($value)-1];

					  }

				}else

						break;         

					++$x;

			}

		  }

		}

	}

	

	}else{

      $fileCsv = glob(PATH_POST . ".csv");

      if (is_array($fileCsv) && !empty($fileCsv)){

         $lines = readCSV($fileCsv[0]);

         unset($lines[0]);

         foreach ($lines as $key => $value){

          $value = array_map('utf8_encode', (array) $value);

          if(count($value)>2){

            $value[1] = $value[1];

            $value[2] = $value[2]; 

            list($posts['type'][],$posts['content'][],$posts['text'][]) = $value;

          }

            

        }

      }

	}

	

  $posts = json_encode($posts);

  $posts = json_decode($posts, true);

echo json_encode($posts);



}

	

die();



}

add_action("wp_ajax_sendtofacebook","sobp_sendtofacebook");

function strip_tags_deep($value){

	return is_array($value) ?

	array_map('strip_tags_deep', $value) :

	strip_tags($value);}



function sobp_readCSV($csvFile){

  $file_handle = fopen($csvFile, 'r');

  while (!feof($file_handle) ) {

    $line_of_text[] = fgetcsv($file_handle, 1024);

  }

  fclose($file_handle);

  return $line_of_text;}

?>