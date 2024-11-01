<?php
define('PATH', dirname(__file__));

require(PATH . '/lang.php');
require(PATH . '/config.php');
require(PATH . '/createDatabase.php');
require(PATH . '/processings.php');
?>
<style type="text/css">
.data {float:right;}
.label {margin: 0px 2px 2px 0px;}
.controls{ float:left; margin-right:40px}
.row {margin-left: 0px;}
</style>
<div class="container">
<?php if(!isCurl()) echo '<div id="message" class="error" style="color:red">The cUrl Extension is not installed. Please install and enable it run schedular properly.</div>'; ?>
<div class="container-fluid">
<script type="text/javascript">
var jq=jQuery;
if(!localStorage.tabsindex) localStorage.tabsindex=0;
if(!localStorage.acordindex) localStorage.acordindex=0;
jq(function(){
		jq( "#tabs" ).tabs({
			active: parseInt(localStorage.tabsindex),
			  activate: function(event,ui){
				  localStorage.tabsindex=ui.newTab.index();
				},
			}).addClass( "ui-tabs-vertical ui-helper-clearfix" );
		jq("#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
		jq("#accordion" ).accordion({
									active: parseInt(localStorage.acordindex),
									activate: function(event,ui){
									localStorage.acordindex= parseInt(ui.newHeader.index())/2;
									},		 
		 	heightStyle: "content"});	
});
</script>
<div id="tabs">
  <ul>
 	<li><a href="#tabs-3">Schedule Posts</a></li>
    <li><a href="#tabs-2">Facebook Pages</a></li>
    <li><a href="#tabs-4">Images directory</a></li>
   <?php /*?> <li><a href="#tabs-5">Rss feeds</a></li><?php */?>
    <li><a href="#tabs-1">Settings</a></li>
  </ul>
  
  <div id="tabs-3">
    <h2>Schedule Posts to Facebook, Twitter and LinkedIn</h2>
    <p style="color:grey;font-size:15px;">Go Pro to unlock all our awesome features and get our other awesome plugins at <a href="http://vbsocial.com/batch-image-facebook-twitter">vBSocial.com</a></p></style>
    <p>    
    <form class="form-horizontal" onsubmit="return false;" id="formPosts">
    <fieldset>
    <input type="hidden" name="action" value="sendtofacebook" />
    
    
    <!-- Select Basic -->
    <div class="control-group">
      <label class="control-label">Choose Facebook Pages</label>
      <div class="controls">
        <select id="page" name="page[]" class="chosen-select input-xlarge" multiple data-placeholder="Choose pages...">
          <?php 
          $result = $db->selectAll("pages");
          if(!empty($result)){
           foreach ($result as $value){
            echo '<option value="' , $value['ID'] , '">' , $value['url'] ,  '</option>';
          } 
        }
        ?>
      </select>
    </div>
    </div>
    
     <div class="control-group">
      <label class="control-label">Post on Twitter</label>
          <div class="controls">
            <select id="post_on_twitter" name="post_on_twitter" class="chosen-select input-xlarge" required>
              <?php 
              echo '<option value="0">' , $l['no'] ,  '</option>';
            ?>
          </select><p style="color:red;font-size:10px;">Unlock this premium setting <a href="http://vbsocial.com/batch-image-facebook-twitter">here.</a></p></style>
        </div>
    </div>
    
    <div class="control-group">
      <label class="control-label">Post on Linked-In</label>
          <div class="controls">
            <select id="post_on_linkedin" name="post_on_linkedin" class="chosen-select input-xlarge" required>
              <?php 
               
                echo '<option value="0">' , $l['no'] ,  '</option>';
            ?>
          </select><p style="color:red;font-size:10px;">Unlock this premium setting <a href="http://vbsocial.com/batch-image-facebook-twitter">here.</a></p></style>
        </div>
    </div>
    
    <!-- Select Basic -->
    
    <div class="control-group">
      <label class="control-label">Choose Images/Videos Folder(s)</label>
      <div class="controls">
        <select id="subfolder" name="subfolder[]" class="chosen-select input-xlarge" multiple data-placeholder="Choose Folders...">
          <?php 
          echo '<option value="' , $folder , '">' , $folder ,  ' (root) </option>';
          $folders = glob(PATH . '/' . $folder . "/*", GLOB_ONLYDIR );
          foreach ($folders as $subfolder){
            $subfolder = explode('/', $subfolder);
            echo '<option value="' , $folder , '/', $subfolder[count($subfolder)-1] ,  '">' , $folder , '/', $subfolder[count($subfolder)-1] , '/' ,  '</option>';
          } 
          ?>
        </select>
      </div>
    </div>
    <!-- Select Basic -->
    
    <div class="control-group">
      <label class="control-label">Choose Images Names as description</label>
          <div class="controls">
            <select id="useimagename" name="useimagename" class="chosen-select input-xlarge" required>
              <?php 
                echo '<option value="1">' , $l['yes'] ,  '</option>';
                echo '<option value="0">' , $l['no'] ,  '</option>';
            ?>
          </select>
        </div>
    </div>
    
    <!-- Multiple Radios (inline) -->
    <div class="control-group">
    <label class="control-label"><?php echo $l['numberofposts'];?></label>
    <div class="controls">
    	<input type="number" name="numposts" style="width:50px;" value="10" />
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label">First post after</label>
    <div class="controls">
        <input type="number" id="dataehora" name="dataehora" style="width:50px;" value="15"></input> Minutes
    </div>
    </div>
    
    <!-- Multiple Radios (inline) -->
    <div class="control-group">
    <label class="control-label">Time delay between each post
</label>
    <div class="controls">
      <label class="radio inline" style="padding-left: 0px;">
        <input type="number" name="delay_days" style="width:50px;" value="0" />
        Days
      </label>
       <label class="radio inline">
        <input type="number" name="delay_hours" style="width:50px;" value="0" />
        Hours
      </label>
       <label class="radio inline">
        <input type="number" name="delay_minutes" style="width:50px;" value="10" />
        Minutes
      </label>
       
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label"><?php echo $l['maxpostsperday'];?></label>
    <div class="controls">
      <div>
        <input type="number" id="postsperday" name="postsperday"  style="width:60px;" value="5" readonly></input><p style="color:red;font-size:10px;">Unlock this premium setting <a href="http://vbsocial.com/batch-image-facebook-twitter">here.</a></p></style>
      </div>
    </div>
    </div>
    
    <!-- Multiple Radios (inline) -->
    <div class="control-group">
    <label class="control-label"><?php echo $l['from'];?></label>
    <div class="controls">
      <label class="radio inline">
        <input type="radio" name="from" value="folder" checked="checked">
        <?php echo $l['randomimages'];?>
      </label>
      <label class="radio inline">
        <input type="radio" name="from" value="file">
        <?php echo $l['file'];?>
      </label>
    </div>
    </div>   
    
    <!-- Button -->
    <div class="control-group">
    <label class="control-label"></label>
    <div class="controls">
      <button id="generatePosts" name="singlebutton" class="btn btn-success"><?php echo $l['generate'];?></button>
    </div>
    </div>
    
    </fieldset>
    </form>
    
    <div class="progress progress-striped active" style="display:none">
    <div class="bar" style="width: 0%;"></div>
    </div>
    
    <div id="defaultText" style="display:none">
    <input type="text" val="" class="text" />
        <select class="input-xlarge" required>
        <?php 
            echo '<option value="0">' , $l['begginingempty'] ,  '</option>';
            echo '<option value="1">' , $l['endempty'] ,  '</option>';
            echo '<option value="2">' , $l['begginingall'] ,  '</option>';
            echo '<option value="3">' , $l['endall'] ,  '</option>';
        ?>
      </select>
    
    <button name="singlebutton" class="btn btn-success addDescription"><?php echo $l['add']; ?></button>
    </div>
    <ul class="thumbnails" id="listaposts">
    
    </ul>
    <br clear="all" />
    <div class="alert alert-success sucessoPost" style="display:none">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $l['successposts'];?>
    </div>
    
    
    <div class="options" style="display:none">
    
    <div class="alert alert-error">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <?php echo $l['dontclose'];?>
    </div>
    <hr>
    <button id="postall" name="singlebutton" class="btn btn-success"><?php echo $l['postall'];?></button>
    <a onClick="window.location.reload()" name="singlebutton" class="btn btn-error"><?php echo $l['deleteall'];?></a>
    
    </div>
    </p>
  </div>
    
  <div id="tabs-1">
    <h2>Configuration Settings</h2>
    <p style="color:grey;font-size:15px;">Go Pro to unlock all our awesome features and get our other awesome plugins at <a href="http://vbsocial.com/batch-image-facebook-twitter">vBSocial.com</a></p></style>
    <p>
    <?php if(isset($settings_updated)) echo "<p>$settings_updated</p>"; ?>  
 <form action="" method="post">
     <fieldset>
     	<legend>Facebook</legend>
   
      <div class="controls">
        <label class="control-label" for="page">App ID</label>
        <input id="facebook_app_id" name="facebook_app_id" type="text" placeholder="e.g 638263836299478" class="input-xlarge" value="<?php echo get_option('facebook_app_id'); ?>" />
      </div>
      
      <div class="controls">
        <label class="control-label" for="page">App Secret</label>
        <input id="facebook_app_secret" name="facebook_app_secret" type="text" placeholder="e.g ad73jdhsk288jdjfgks82hjsshf8hs" class="input-xlarge" value="<?php echo get_option('facebook_app_secret'); ?>">
      </div>
	  <?php if (count($db->selectAll("config"))>0) {
       //echo '<button class="btn btn-success">' . $l['youhavetoken'] . '</button>';
       echo '<a style="margin-top: 26px;
display: block;" href="https://www.facebook.com/dialog/oauth?client_id=' . $configs['facebook_app_id']. '&scope=manage_pages,publish_stream&response_type=token&redirect_uri=' .$canvasUrl. '"><button type="button" class="btn btn-mini btn-primary">Renew token</button></a>';
    
     } else {
       echo '<a style="margin-top: 26px;
display: block;" href="https://www.facebook.com/dialog/oauth?client_id=' .$configs['facebook_app_id']. '&scope=manage_pages,publish_stream&response_type=token&redirect_uri=' .$canvasUrl. '">
	   <button  type="button" class="btn btn-mini btn-primary">Authorize App</button></a>';
     } ?>
  </fieldset>
    
    
<fieldset>
<legend>Twitter  <p style="color:red;font-size:12px;">Unlock this premium setting <a href="http://vbsocial.com/batch-image-facebook-twitter">here.</a></p></style></legend>
      <div class="controls">
        <label class="control-label" for="page">Customer Key</label>
        <input id="facebook_app_id" name="twitter_customer_key" type="text" class="input-xlarge" value="<?php echo get_option('twitter_customer_key'); ?>"  readonly="readonly" />
      </div>
      
      <div class="controls">
        <label class="control-label" for="page">Customer Secret</label>
        <input id="facebook_app_secret" name="twitter_customer_secret" type="text" class="input-xlarge" value="<?php echo get_option('twitter_customer_secret'); ?>" readonly="readonly" />
      </div>
    
      <div class="controls">
        <label class="control-label" for="page">Access token</label>
        <input id="facebook_app_secret" name="twitter_access_token" type="text"  class="input-xlarge" value="<?php echo get_option('twitter_access_token'); ?>" readonly="readonly" />
      </div>
      
       <div class="controls">
        <label class="control-label" for="page">Access token Secret</label>
        <input id="facebook_app_secret" name="twitter_access_token_secret" type="text" class="input-xlarge" value="<?php echo get_option('twitter_access_token_secret'); ?>" readonly="readonly" />
      </div>
</fieldset>
     
     <fieldset>
     	<legend>Linked-In <p style="color:red;font-size:12px;">Unlock this premium setting <a href="http://vbsocial.com/batch-image-facebook-twitter">here.</a></style></legend>
              <div class="controls">
                <label class="control-label" for="page">API Key</label>
                <input id="facebook_app_id" name="linkedin_api_key" type="text" placeholder="e.g 638263836299478" class="input-xlarge" value="<?php echo get_option('linkedin_api_key'); ?>" readonly="readonly" />
              </div>
              
              <div class="controls">
                <label class="control-label" for="page">Secret Key</label>
                <input id="facebook_app_secret" name="linkedin_secret_key" type="text" placeholder="e.g ad73jdhsk288jdjfgks82hjsshf8hs" class="input-xlarge" value="<?php echo get_option('linkedin_secret_key'); ?>" readonly="readonly" />
              </div>
              
          
    	</fieldset>
        <div class="controls">
           <button id="update_fb" name="update_fb" class="btn btn-success">Update Details</button>
        </div>
        </form>
    </p>
  </div>
  
  <div id="tabs-2">
    <h2>Add/Manage Facebook Pages</h2>
    <p style="color:grey;font-size:15px;">Go Pro to unlock all our awesome features and get our other awesome plugins at <a href="http://vbsocial.com/batch-image-facebook-twitter">vBSocial.com</a></p></style>
    <p>
  
    <?php 
    if (isset($_POST['page'])) {
    if(!function_exists('getSslPage')) {
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
	}
    $info = json_decode(getSslPage('http://graph.facebook.com/' . $_POST['page']));
    if(isset( $info->name)){
      $album = json_decode(getSslPage('http://graph.facebook.com/' . $info->id . '/albums?fields=id'));
      $fotoAlbum = "";
      foreach ($album->data as $key => $value) {
        if($fotoAlbum=="" || @$album->data[$key]->name == "Timeline Photos"){
          $fotoAlbum = @$album->data[$key]->id;
        }
      }
      $db->insert("pages",array('ID' => $info->id , 'url' => $info->name, 'idalbum' => $fotoAlbum));
          unset($_POST);
    }
    }
    
    $result = $db->selectAll("pages");
    if(!empty($result)){
	echo '<p class="lead">All Pages</p>';
    echo '<table class="table" id="example">
    <thead>
    <tr>
    <td>' . $l['name'] . '</td>
    <td>' . $l['id'] . '</td>
    <td>' . $l['open'] . '</td>
    <td>' . $l['delete'] . '</td>
    </tr> 
    </thead>
    <tbody>';
    foreach ($result as $value){
    echo '<tr><td>' , $value['url'] ,  '</td><td>' , $value['ID'] , '</td><td><a target="_blank" href="https://www.facebook.com/' , $value['ID'] , '">' . $l['open'] . '</a></td><td><a href="'.$canvasUrl.'&id=' , $value['ID'] , '">' . $l['delete'] . '</a></td></tr>';
    } 
    echo '</tbody></table>';
    }
    ?>
      <form class="form-horizontal" method="POST">
      <fieldset>
    
        <!-- Form Name -->
        <legend>Add a new page</legend>
    
<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="page"><?php echo $l['pageurl'];?></label>
  <div class="controls">
    <input id="page" name="page" type="text" placeholder="https://www.facebook.com/your-page-url" class="input-xlarge" required="">
  </div>
</div>

<!-- Button -->
<div class="control-group">
  <label class="control-label" for="add"></label>
  <div class="controls">
    <button id="add" name="add" class="btn btn-success"><?php echo $l['add'];?></button>
  </div>
</div>
    
      </fieldset>
    </form>  
    
    </p>
  </div>
  
  <div id="tabs-4">
    <h2>Manage images and directories</h2>
    <p style="color:grey;font-size:15px;">Go Pro to unlock all our awesome features and get our other awesome plugins at <a href="http://vbsocial.com/batch-image-facebook-twitter">vBSocial.com</a></p></style>
    <p>
    <div id="accordion">
      <h3>Bulk Upload Images via Zip folder</h3>
      <div>

			<?php if(isset($message)) echo "<p>$message</p>"; ?>
            <form enctype="multipart/form-data" method="post" action="">
                <input type="file" id="zip_file" name="zip_file" required="required" />
                <br /><br />
                <label>Choose Folder to upload to:</label>
            	<select id="put_zip_here" name="put_zip_here" class="input-xlarge" required="required">
                  <?php 
                  echo '<option value="' , $folder , '">' , $folder ,  ' (root) </option>';
                  $folders = glob(PATH . '/' . $folder . "/*", GLOB_ONLYDIR );
                  foreach ($folders as $subfolder){
                    $subfolder = explode('/', $subfolder);
                    echo '<option value="' , $folder , '/', $subfolder[count($subfolder)-1] ,  '">' , $folder , '/', $subfolder[count($subfolder)-1] , '/' ,  '</option>';
                  } 
                  ?>
                </select>
                <br />
                <input type="submit" name="submit" class="btn btn-green" value="Begin Upload" />
            </form>
       
      </div>
      <h3>Upload multiple images to specific folder</h3>
      <div>
        <p>
        <label>Choose Folder:</label>
        <select id="puthere" name="puthere" class="input-xlarge" required="required">
          <?php 
          echo '<option value="' , $folder , '">' , $folder ,  ' (root) </option>';
          $folders = glob(PATH . '/' . $folder . "/*", GLOB_ONLYDIR );
          foreach ($folders as $subfolder){
            $subfolder = explode('/', $subfolder);
            echo '<option value="' , $folder , '/', $subfolder[count($subfolder)-1] ,  '">' , $folder , '/', $subfolder[count($subfolder)-1] , '/' ,  '</option>';
          } 
          ?>
        </select>
         <div id="file-uploader-demo1">    
            <noscript>      
            <p><?php echo $l['enablejavascript'];?></p>
            <!-- or put a simple form for upload here -->
            </noscript>         
         </div>
    
    <div class="qq-upload-extra-drop-area"><?php echo $l['dropfiles'];?></div>
      </div>
      <h3>Create a new folder</h3>
      <div>
      <?php if(isset($folder_msg)) echo "<p>$folder_msg</p>"; ?>
         <label>Enter Folder name:</label>
        <form method="post" action="">
        <input id="folder_name" name="folder_name" type="text" placeholder="e.g hotels, landscapes" class="input-xlarge" required="required" />
        <br />
        <input type="submit" name="submit" class="btn btn-success" value="Create Folder" />
        </form>
      </div>
      
       <h3>Delete Folder and all its Images</h3>
      <div>
        <p>
        <?php if(isset($folder_del_msg)) echo "<p>$folder_del_msg</p>"; ?>
        <label>Choose Folder:</label>
        <form method="post" action="">
            <select id="deleteFolder" name="deleteFolder" class="input-xlarge" required>
            <?php 
            $folders = glob(PATH . '/' . $folder . "/*", GLOB_ONLYDIR );
            foreach ($folders as $subfolder){
              $subfolder = explode('/', $subfolder);
              echo '<option value="' , $folder , '/', $subfolder[count($subfolder)-1] ,  '">' , $folder , '/', $subfolder[count($subfolder)-1] , '/' ,  '</option>';
            } 
            ?>
            </select><br />
            <input type="submit" name="submit" class="btn btn-danger" value="<?php echo $l['deletefolder'];?>" />
        </form>
        </p>
      </div>
      
    </div>


    </p>
  </div>
  
  <?php /*?><div id="tabs-5">
    <h2>Post Rss feeds to facebook</h2>
    <p>
      <form class="form-horizontal" method="POST">
      <fieldset>
    Not working yet
        <!-- Form Name -->
        <legend><?php echo $l['addyourrss'];?></legend>
    
        <!-- Text input-->
        <div class="control-group">
          <label class="control-label" for="page"><?php echo $l['rssurl'];?></label>
          <div class="controls">
            <input id="rss" name="rss" type="text" placeholder="https://www.something.com/rss" class="input-xlarge" required="">
            <p class="help-block"><?php echo $l['rssurl'];?></p>
          </div>
        </div>
    
        <!-- Button -->
        <div class="control-group">
          <label class="control-label" for="add"></label>
          <div class="controls">
            <button id="add" name="add" class="btn btn-success"><?php echo $l['add'];?></button>
          </div>
        </div>
    
      </fieldset>
    </form>
    
    <legend><?php echo $l['startpostlive'];?></legend>
    <?php echo $l['startpostlive'];?>
    
            <!-- Select Basic -->
    <div class="control-group">
      <label class="control-label"><?php echo $l['pickyourrss'];?></label>
      <div class="controls">
        <select id="urlRss" name="urlRss" class="input-xlarge" multiple required>
          <?php 
          $result = $db->selectAll("rss");
          if(!empty($result)){
           foreach ($result as $value){
            echo '<option value="' , $value['url'] , '">' , $value['url'] ,  '</option>';
          } 
        }
        ?>
      </select>
    </div>
    </div>
    
          <!-- Select Basic -->
    <div class="control-group">
      <label class="control-label"><?php echo $l['pages'];?></label>
      <div class="controls">
        <select id="pageRss" name="pageRss" class="input-xlarge" multiple required>
          <?php 
          $result = $db->selectAll("pages");
          if(!empty($result)){
           foreach ($result as $value){
            echo '<option value="' , $value['ID'] , '">' , $value['url'] ,  '</option>';
          } 
        }
        ?>
      </select>
    </div>
    </div>
    
    
                <!-- Select Basic -->
    <div class="control-group">
      <label class="control-label"><?php echo $l['timetocheck'];?></label>
      <div class="controls">
        <select id="timeRss" name="timeRss" class="input-xlarge" required>
              <option value="5">5 <?php echo $l['minutes'];?></option>
              <option value="10">10 <?php echo $l['minutes'];?></option>
              <option value="15">15 <?php echo $l['minutes'];?></option>
              <option value="20">20 <?php echo $l['minutes'];?></option>
              <option value="30">30 <?php echo $l['minutes'];?></option>
              <option value="45">45 <?php echo $l['minutes'];?></option>
              <option value="60">60 <?php echo $l['minutes'];?></option>
      </select>
    </div>
    </div>
    <button id="startRss" name="startRss" class="btn btn-success"><?php echo $l['startposting'];?></button>
    <button id="stopRss" name="stopRss" class="btn btn-info" style="display:none" onClick="window.location.reload()"><?php echo $l['stopposting'];?></button>
    <div id="postsDone"></div>
    </p>
  </div><?php */?>
  
</div>
</div>
<?php 
require(PATH . '/footer.php');
?>