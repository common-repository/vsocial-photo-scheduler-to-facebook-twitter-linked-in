<style type="text/css">
   #listaposts .span3 {width:200px; height: 240px; margin: 10px;}
   #listaposts .thumbnail img {width: 150px; height: 100px;}
   #postsDone {padding:10px;}

.ui-tabs-vertical { width: 100%; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 15%; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; width: 100%;
outline: none;}
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: 80%;}
  .ui-tabs .ui-tabs-nav li.ui-tabs-active {
margin-bottom: 3px;
}
.ui-tabs {
	padding:0px !important;
	}
.ui-tabs .ui-tabs-nav {
padding: 2px 1px 50% 2px !important;
}
.ui-widget-header { background:#ccc !important; }
.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr {
border-top-right-radius: 0px !important;
border-bottom-right-radius: 0px !important;

}
.span3{ position:relative;}
.span3 .remove-post{ position:absolute; right:-8px; top:-8px; width:16px; height:16px; display:block;
background:url(<?php echo plugins_url()."/vbsocial-scheduler/inc/"; ?>img/remove-post.png); cursor:pointer; }
  </style>

</style>

<script type="text/javascript">

var $=jQuery;
url='<?php echo admin_url('admin-ajax.php'); ?>';
uploads_url='<?php echo $upload_dir['baseurl']. '/sobp_upload'; ?>';
jq(function(){
		jq(".chosen-select").chosen({});
		jq('input[type="file"]').inputfile({
			uploadText: '<span class="glyphicon glyphicon-upload"></span> Select a file',
			removeText: '<span class="glyphicon glyphicon-trash">X</span>',
			restoreText: '<span class="glyphicon glyphicon-remove"></span>',
			
			uploadButtonClass: 'btn btn-primary',
			removeButtonClass: 'btn btn-default'
		});	
		jq("#tabs-3").on('click','.remove-post',function(){
			jq(this).parent().fadeOut(300,function(){jq(this).remove(); });
			return false;
			});
});

function createUploader(){ 
    var uploader = new qq.FileUploader({
      element: document.getElementById('file-uploader-demo1'),
      action: url,
	  params: { action: 'do_upload',folder_to_upload:localStorage.folder_to_upload},
      debug: true,
      extraDropzones: [qq.getByClass(document, 'qq-upload-extra-drop-area')[0]]
    });           
  }
  
// in your app create uploader as soon as the DOM is ready
// don't wait for the window to load  
window.onload = createUploader;  
        jq('#puthere').change(function(event) {localStorage.folder_to_upload=this.value; 
		createUploader();
		}); 

        jq(document).ready(function (){

	      jq('.dropdown-toggle').dropdown();
          jq(".alert").alert();
          jq('.nav-tabs').button();

          jq("#startRss").on('click', function() {
            var feed = $('#urlRss').val();
            var pages = $('#pageRss').val();
            var time = $('#timeRss').val();
            if(feed===null || pages===null){
              alert("You need to select at least one page and one url feed");
              return;
            }
            jq("#startRss").hide();
           jq("#stopRss").show();
            var rssInterval = setInterval(function(){
              searchRss(feed,pages); 
            }, 1000*(time*60)); 
          });


          function timeConverter(UNIX_timestamp){
           var a = new Date(UNIX_timestamp*1000);
           var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
           var year = a.getFullYear();
           var month = months[a.getMonth()];
           var date = a.getDate();
           var hour = a.getHours();
           var min = a.getMinutes();
           var sec = a.getSeconds();
           var time = date+','+month+' '+year+' '+hour+':'+min+':'+sec ;
           return time;
         }

         jq(".addDescription").on('click', function() { 
          var option = $('#defaultText select').val();
          var text = $('#defaultText input.text').val();

          $( "ul.thumbnails li" ).each(function() {

            switch(option){
              case "0": 
              if($( this ).find('textarea').val().length==0){
                $( this ).find('textarea').val(function(i,val){
                  return  text + val;
                });
              }
              break;
              case "1": 
              if($( this ).find('textarea').val().length==0){
                $( this ).find('textarea').val(function(i,val){
                  return  val + text;
                });
              }
              break;
              case "2": 
              $( this ).find('textarea').val(function(i,val){
                return  text + val;
              });
              break;
              case "3": 
              $( this ).find('textarea').val(function(i,val){
                return  val + text;
              });
              break;
            }

          });
        });

jq("#generatePosts").on('click', function() {
      var todayDate = new Date();
      var nowTime =  parseInt(todayDate.getTime()/1000);
	  // console.log(nowTime+'==<?php echo time(); ?>');
	  var val = jq('#dataehora').val();
      var descriptionName = $('#useimagename').val();
      var time =  nowTime+parseInt(val);
     
      if((time-nowTime)<0){
        alert("<?php echo $l['error10minutes'];?>")
      }else{

        if (isNaN($('#postsperday').val())) {
          var postsPerDay = 1000;
        }else{
          var postsPerDay = $('#postsperday').val();
        }

        $('#dataehora').val(time);
		
        var data = $("#formPosts").serialize();
        $.ajax({
          url: url,
          type: 'post',
          data: data,
         dataType: 'json',
          success: function(json) {
			//console.log('res='+json);
			//return;
            $("#formPosts").hide();
            $('.thumbnails, .options, #defaultText').show();
            $('.thumbnails').html("");

            if(json.from=="folder"){
              var nextDay = parseInt(json.dataehora) + (24 * 3600);
              for (var i in json.images){
                var timeofpost = 0;
                if(i>0){
                  if(i%parseInt(postsPerDay)==0){
                    json.dataehora = nextDay;
                    nextDay = parseInt(json.dataehora) + (24 * 3600);
                  }else{
					  json.dataehora=parseInt(json.dataehora)+json.time;
                  }
                }
                timeofpost = json.dataehora;
                var namePage = "";
                var dataLabel = "";
                var description = "";
                dataLabel = timeConverter(timeofpost);
                  description = "";
                  if(descriptionName=="1"){
                    var descriptionGenerator = json.images[i].split("/");
                    descriptionGenerator = descriptionGenerator[descriptionGenerator.length-1].split(".");
                    descriptionGenerator = descriptionGenerator[0].split("_");
                    for (var k = descriptionGenerator.length - 1; k >= 0; k--) {
                     description = description + descriptionGenerator[k] + ' ';
                   };
                 }
                    if(json.images[i].indexOf('.jpg')>= 0 || json.images[i].indexOf('.gif')>= 0 || json.images[i].indexOf('.png')>= 0 ||  json.images[i].indexOf('.bmp')>= 0){
                          $('.thumbnails').append('<li class="span3"><a class="remove-post"></a><div class="thumbnail"><img src="'+uploads_url + '/' + json.images[i] + '" class="ourimg" alt=""><div class="caption"><p><textarea style="width:90%">' + description + '</textarea><input type="hidden" class="timeofpost" value="' + timeofpost + '" /><input type="hidden" class="type" value="image" /><input type="hidden" class="url" value="" /><input type="hidden" class="page" value="'+ json.page +'" /><input type="hidden" class="post_on_twitter" value="'+ json.post_on_twitter +'" /></p></div><span class="label label-warning">' + dataLabel +'</span></div></li>');
                    }else{
                         $('.thumbnails').append('<li class="span3"><a class="remove-post"></a><div class="thumbnail">'+uploads_url + '/' + json.images[i] + '<img src="' + json.folder + '/' + json.images + '" style="display:none" class="ourimg" alt=""><div class="caption"><p><textarea style="width:90%">' + description + '</textarea><input type="hidden" class="timeofpost" value="' + timeofpost + '" /><input type="hidden" class="type" value="video" /><input type="hidden" class="url" value="" /><input type="hidden" class="page" value="'+ json.page+'" /><input type="hidden" class="post_on_twitter" value="'+ json.post_on_twitter +'" /></p></div><span class="label label-warning">' + dataLabel +'</span></div></li>');
                    }
               
             }
           }else{
            var nextDay = parseInt(json.dataehora) + (24 * 3600);
            for (var i in json.content){
              var timeofpost = 0;
              if(i>0){
                if(i%parseInt(postsPerDay)==0){
                  json.dataehora = nextDay;
                  nextDay = parseInt(json.dataehora) + (24 * 3600);
                }else{
                  json.dataehora=parseInt(json.dataehora)+json.time;
                }
              }

              timeofpost = json.dataehora;
              var namePage = "";
              var dataLabel = "";
              dataLabel = timeConverter(timeofpost);

              for(var j in json.page){
                namePage = jQuery("select option[value=" + json.page[j] + "]").first().text();
                if(json.type[i]=="image"){
                  if(json.content[i].indexOf('.jpg')>= 0 || json.content[i].indexOf('.gif')>= 0 || json.content[i].indexOf('.png')>= 0 ||  json.content[i].indexOf('.bmp')>= 0){
                   $('.thumbnails').append('<li class="span3"><div class="thumbnail"><img src="' + uploads_url+ '/' + json.content[i] + '" class="ourimg" alt=""><div class="caption"><p><textarea style="width:90%">' + json.text[i] + '</textarea><input type="hidden" class="timeofpost" value="' + timeofpost + '" /><input type="hidden" class="type" value="image" /><input type="hidden" class="url" value="" /><input type="hidden" class="page" value="'+ json.page[j] +'" /></p></div><span class="label label-info">' + namePage +'</span><span class="label label-warning">' + dataLabel +'</span></div></li>');
                 }else{
                       $('.thumbnails').append('<li class="span3"><div class="thumbnail">' + uploads_url+ '/' + json.content[i] + '<img src="' + json.folder + '/' + json.content[i] + '" style="display:none" class="ourimg" alt=""><div class="caption"><p><textarea style="width:90%">' + json.text[i] + '</textarea><input type="hidden" class="timeofpost" value="' + timeofpost + '" /><input type="hidden" class="type" value="video" /><input type="hidden" class="url" value="" /><input type="hidden" class="page" value="'+ json.page[j] +'" /></p></div><span class="label label-info">' + namePage +'</span><span class="label label-warning">' + dataLabel +'</span></div></li>');
                 }
               }else{
                $('.thumbnails').append('<li class="span3"><div class="thumbnail"><div style="width: 150px;height: 100px;">' + json.content[i] + '</div><div class="caption"><p><textarea style="width:90%">' + json.text[i] + '</textarea><input type="hidden" class="timeofpost" value="' + timeofpost + '" /><input type="hidden" class="type" value="url" /><input type="hidden" class="url" value="' + json.content[i] + '" /><input type="hidden" class="page" value="'+ json.page[j] +'" /></p></div><span class="label label-info">' + namePage +'</span><span class="label label-warning">' + dataLabel +'</span></div></li>');
              }
            }
          }
        }
      
	  		}
    });
 }
});
$=jQuery;
$("#postall").on('click', function() {
    if ( $('ul.thumbnails').find('li').length >0 ) {
     // $('.options').hide();
      $('.progress-striped').show();
      var total = $('ul.thumbnails').find('li').length;
      var x = 1;
      var interval = setInterval(function(){
        if ($( 'ul.thumbnails li' ).length>0) {
			
          postOne(total, x);
          x = x + 1;
        } else {
          clearInterval(interval);
        }
          }, 3000); // every 100 milliseconds
    }
  });


function growBar(total, x){
var bar = 0;
bar = (100/total)*x;
$('.bar').css('width',  bar + '%');
if(total==x){
  $('.progress-striped').fadeOut(1000);
  $('.sucessoPost').fadeIn(2000);
}
return;    
}

function searchRss(feed, pages){
$.ajax({
  url: 'ajax/postRss.php',
  type: 'post',
  dataType: 'json',
  data: {'feed' : feed,'pages' : pages},
  success: function(json) {
	if(json===null){

	}else{
	  var id;
	  for (var i = json.length - 1; i >= 0; i--) {
	   id = json[i].id.split('_');
	   $('#postsDone').append('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert">&times;</button><a href="https://www.facebook.com/' + id[0]+ '/posts/' + id[1] + '" target="_blank">' + json[i].title[0] + '</a></div>');
	 }
	 
   }
 }
}); 
}



function postOne(total, x){
if($( 'ul.thumbnails li' ).length>0){
  var first = $( 'ul.thumbnails li' ).first();
  text = first.find('textarea').val();
  time = first.find('.timeofpost').val();
  type = first.find('.type').val();
  url =  first.find('.url').val();
  page = first.find('.page').val();
  post_on_twitter = first.find('.post_on_twitter').val();
  if(type=="image" || type=="video")
	image = first.find('.ourimg').attr('src');
  else
	image = "1";
  $.ajax({
	url: '<?php echo admin_url('admin-ajax.php'); ?>',
	type: 'post',
	data: {action:'posttofacebook','text' : text,'time' : time,'type' : type,'url' : url, 'image' : image, 'page' : page,post_on_twitter:post_on_twitter},
	success: function(data) {
		console.log(data);
		//return;
	  growBar(total, x);
	}
  }); 
 first.remove();
}
}

   var table = $('#example').dataTable({});
 });
   </script>
   <script type="text/javascript">
   var HashSearch = new function () {
     var params;

     this.set = function (key, value) {
      params[key] = value;
      this.push();
    };

    this.remove = function (key, value) {
      delete params[key];
      this.push();
    };


    this.get = function (key, value) {
     return params[key];
   };

   this.keyExists = function (key) {
     return params.hasOwnProperty(key);
   };

   this.push= function () {
     var hashBuilder = [], key, value;

     for(key in params) if (params.hasOwnProperty(key)) {
           key = escape(key), value = escape(params[key]); // escape(undefined) == "undefined"
           hashBuilder.push(key + ( (value !== "undefined") ? '=' + value : "" ));
         }

         window.location.hash = hashBuilder.join("&");
       };

       (this.load = function () {
         params = {}
         var hashStr = window.location.hash, hashArray, keyVal
         hashStr = hashStr.substring(1, hashStr.length);
         hashArray = hashStr.split('&');

         for(var i = 0; i < hashArray.length; i++) {
           keyVal = hashArray[i].split('=');
           params[unescape(keyVal[0])] = (typeof keyVal[1] != "undefined") ? unescape(keyVal[1]) : keyVal[1];
         }
       })();
     }

     if (typeof HashSearch.get('access_token')==='undefined') {
   // global variable v is defined
 } else {
   // global variable v is not defined
   if (HashSearch.get('access_token').length>20) { 
     $.ajax({
      type: "POST",
      url: '<?php echo admin_url('admin-ajax.php'); ?>',
      data: {action:'add_access_token',access_token:escape(HashSearch.get('access_token')),expires_in:escape(HashSearch.get('expires_in'))},
      dataType: "html",
      success: function(response) {
		  console.log(response);
      // window.location.href="http://demoaspire.com/2013/vbsocial/wp-admin/admin.php?page=vbsocial-scheduler";
     }
   });
   }
 }

 </script>
 <style type="text/css">
 #files div {width:150px; float:left;}
 .btn {
   -webkit-border-radius: 5px;
   -moz-border-radius: 5px;
   border-radius: 5px;
 }

 .container {background: #fff;    -webkit-border-radius: 10px;
   -moz-border-radius: 10px;
   border-radius: 10px;}
   </style>
 </div>