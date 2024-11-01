<?php 
/* Carlos Branco 2013 - You can use this file to translate your APP */

$l = array(
	'title' => 'Facebook post Generator', 
	'login' => 'Login', 
	'loginfail' => 'Login fail', 
	'errorcurl' => '<strong>Warning!</strong> You need CURL extension on your server to run this script. <br /> Please contact your host provider to activate CURL.', 
	'errorappid' => '<strong>Warning!</strong> Open the file config.php and define the id of your App. <br /> EG : $idApp = "yourid";',
	'errorappsecret' => '<strong>Warning!</strong> Open the file config.php and define the secret of your App. <br /> EG : $appSecret = "your app secret";', 
	'errorcanvas' => '<strong>Warning!</strong> Open the file config.php and define the your canvas url. Usually is the URL of this website. <br /> EG : $canvasUrl = "http://urlofthisSite.com";', 
	'errorfolder' => '<strong>Warning!</strong> Open the file config.php and define the name of your images folder. <br /> EG : $folder = "NameOfFolder";', 
	'errorzip' => '<strong>Warning!</strong> The file you are trying to upload is not a .zip file. Please try again.', 
	'successzip' => '<strong>Success!</strong> Your .zip file was uploaded and unpacked.', 
	'errorupload' => '<strong>Warning!</strong> There was a problem with the upload. Please try again.', 
	'config' => 'Config', 
	'pages' => 'Pages', 
	'posts' => 'Posts', 
	'folders' => 'Folders', 
	'liveposts' => 'Live Posts', 
	'logout' => 'Logout', 
	'configinfo' => 'You will need to ask a new token when you use this script for the first time. Time to time you shoud renew your token. Usually facebook tokens are valid for 60 days. <br />If you cant renew token make sure you have right configurations on config.php file.', 
	'configproblems' => 'If you have problems you can renew your token', 
	'youhavetoken' => 'You have your token', 
	'renewtoken' => 'Renew your token', 
	'gettoken' => 'Get your token', 
	'pagesinfo' => 'The option Pages let you Add and remove pages that you want to post.<br />Simply use your page url to add this page. To your list. Of course you will need to be has administrator of the page to add the page.', 
	'addpages' => 'Add your pages', 
	'pageurl' => 'Page url', 
	'add' => 'Add', 
	'name' => 'Name', 
	'id' => 'ID', 
	'open' => 'Open', 
	'delete' => 'Delete', 
	'foldersinfo' => 'The option folders let you upload new folders or images to folders that already exist.<br /> You delete old folders you dont need too.', 
	'uploadzipfolders' => 'Upload your zip folders with images', 
	'choosezip' => 'Choose a zip file to upload',
	'upload' => 'Upload', 
	'deletefolder' => 'Delete Folder',
	'uploadimagestofolder' => 'Upload multiple images to specific folder',
	'enablejavascript' => 'Please enable JavaScript to use file uploader.',
	'dropfiles' => 'Drop files here too',
	'rssinfo' => 'The option Live posts let you create posts from RSS feed. You can add multiple RSS feeds and then select one or more pages and set how many time you will wait between each verification. Then just click start posting. When a new post is found will post in all pages you select.<br />You can not close the page because that page will be working search for new posts. But you can open other tabs of course.',
	'addyourrss' => 'Add your RSS Feed',
	'rssurl' => 'RSS Url',
	'startpostlive' => 'Start posting live',
	'pickyourrss' => 'Pick your rss',
	'timetocheck' => 'Time to check the posts', 
	'minutes' => 'minutes',
	'startposting' => 'Start Posting',
	'stopposting' => 'Stop Posting',
	'postsinfo' => 'The option Posts generate posts from some specific folder or from some CSV (excell) file that you put in root of your script.<br />
You can choose the number of posts, the time of first post and the time that will pass between each post. Very important. You CANT define a date in past. And All posts for future are just valid if they are not in next 10 minutes.<br /> After the post done and you make sure you can see it in activity log of your page you can delete images you dont need to keep that images in your server.',
	'generateposts' => 'Generate posts',
	'folder' => 'Folder',
	'numberofposts' => 'Number of posts',
	'timefirstpost' => 'Time of first post',
	'timebettweenpost' => 'Time bettween each post',
	'hours' => 'hour(s)',
	'maxpostsperday' => 'MAX posts per day (if reach jump to next day)',
	'from' => 'From',
	'randomimages' => 'Random images from folder',
	'file' => 'File .cvs',
	'generate' => 'Generate',
	'successposts' => '<strong>Success!</strong> All posts are done. <a onClick="window.location.reload()">Click here to go back!</a>',
	'dontclose' => '<strong>Warning!</strong> After you click post dont close this page before all posts are done.',
	'postall' => 'Post all',
	'deleteall' => 'Delete all and show form again',
	'error10minutes' => 'Date and time you pick are not valid. The post must be at least 10 minutes in the future. (this is a facebook limitation not from the script).',
	'no' => 'No',
	'yes' => 'Yes',
	'usefilename' => 'Use image name as description (delimiter "_")',
	'begginingempty' => 'Add in beggining of all empty textareas',
	'endempty' => 'Add in the end of all empty textareas',
	'begginingall' => 'Add in beggining of all textareas',
	'endall' => 'Add in the end of all textareas'
	);
 ?>