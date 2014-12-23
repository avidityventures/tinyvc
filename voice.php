<?
#---------------------------------------------------
# FWW Framework
# Programmer : Mohd Izzairi Yamin
# Email      : izzairi.yamin@gmail.com
# Blog    	 : mohdizzairi.com
#---------------------------------------------------
#---------------------------------------------------
# @version		Id: 13/12/2009 5:25AM
# @package		FWW (Flexible Website Wizard)
# @copyright		Copyright (C) 2009 All rights reserved.
# @license		GNU/GPL, see LICENSE.php
# FWW! is not a free software. This version is distributed and includes or
# is derivative of works licensed under the GNU General Public License.
# See COPYRIGHT.php for copyright notices and details.
#---------------------------------------------------
include("system_files/lib/global.php");

$db=new connection();  
$db->db_conn();
$util=new util();
$html=new defaultsite();

$util->authAuthorized();
$profile_details=$util->setSession();

if (isset($_REQUEST['v'])){
 $util->checkPostOwner($_REQUEST['v']);
 $voice=$util->getVoices($_REQUEST['v']); 
 $id=$voice->uniqueidentifier;
 $imgAttach=$util->checkAttachment($id);
 $folder=$util->publicFolder($profile_details->email);
 $mode="edit";
}else{
 $mode="new"; 
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <? $html->metaHeader(); ?>
    
    <!-- Bootstrap core CSS -->
    <link href="<?=SERVER_URL?>layout/css/main.css" rel="stylesheet"/>
    <link href='<?=SERVER_URL?>layout/css/switch.css' rel='stylesheet'/>  
    <link href="<?=SERVER_URL?>layout/css/tinyvoices.css" rel="stylesheet"/>
    <link href='<?=SERVER_URL?>layout/css/font-awesome.css' rel='stylesheet'/>
    <link href='<?=SERVER_URL?>layout/css/fileinput.css' rel='stylesheet'/>
    <link href='<?=SERVER_URL?>layout/css/scrollbar.min.css' rel='stylesheet'/>
    

    <!-- Custom styles for this template -->
    <link href="<?=SERVER_URL?>layout/css/navbar-static-top.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
    
  </head>
  
  
<body>
  <? $html->header(); ?> 

    
<div class="container" style="margin-top:50px;">
<? $html->progressBarVoice(1,0,0,0); ?>

<form id="voiceSaveForm" name="submitVoice" method="post"  enctype="multipart/form-data">
<div class="row">
 
<p>&nbsp;</p><p>&nbsp;</p>

<div class="col-lg-4 col-md-12 col-sm-12" id="mapExpand">
 <div class="panel panel-default panel-default-profilex">
  <div class="panel-heading">
    <h1 class="panel-title">  <!--<i class="fa fa-location-arrow"></i> &nbsp;-->Current City </h1>
  </div>
  
  <ul class="list-group">
    <li class="list-group-item"  style="padding:0;">
     <div class="row google-map-holder" id="google_canvas"></div><!--row-->
    </li><!--list-group-item-->
  </ul><!--list-group-->
  
  
 </div><!--panel panel-default panel-default-profilex-->
 <div style="margin-top:-10px; color:#c2c2c2; margin-bottom:10px;">
  Move the marker to find places accurately near you. Locations powered by <i class="fa fa-foursquare"></i>oursquare</div>
</div><!--col-md-4-->



<div class="col-lg-8 col-md-12 col-sm-12">
<div class="panel panel-default panel-default-profilex">
  <div class="panel-heading" >
    <h1 class="panel-title"><!--<i class="fa fa-bullhorn"></i> &nbsp;--> Voice Out <span id="resultsLoadingCount"></span> <span id="resultsLoading" class="pull-right" style="display:none;"><i class="fa fa-cog fa-spin"></i></span></h1>
  </div>

  <ul class="list-group">
    
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-12"><label class="control-label-voice-out"> Whom do you want to voice out to ?</label></div><!--col-sm-2-->
      <div class="col-sm-12"><span class="FS-search"></span>
       <input type="text" id="brands_services" class="form-control voicetox input-lg" name="brands_services" placeholder="Search for Brands or Services..." value="<? $util->setValue($voice->brands_services) ?>"/>
      </div><!--col-sm-12-->
      <div class="col-sm-12" style="padding-top:10px; padding-bottom: 10px;">
        Your voice will be a  message to the public who thinks you are on the right track. Name the brand or service you are voicing out. The more backers you have on your voice the better it'll be.
      </div>
     </div><!--row-->
    </li><!--list-group-item-->
        
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-12"><label class="control-label-voice-out">Problems or complaints</label></div><!--col-sm-2-->
      <div class="col-sm-12">
        <textarea name="problems_complaints" id="problems_complaints"><? $util->setValue($voice->problems_complaints) ?></textarea>
      </div><!--col-sm-12-->
      <div class="col-sm-12" style="padding-top:10px; padding-bottom: 10px;">
        Your voice will be a public message to one or more people who thinks you are right. Name the brand or service you are voicing out. The more backers you have on your voice the better it'll be.
      </div>
     </div><!--row-->
    </li><!--list-group-item-->
    

   
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-12"><label class="control-label-voice-out">Upload media </label></div><!--col-sm-2-->
      <div class="col-sm-12">
       <?
        echo '<div class="photoUploaded-'.$voice->uniqueidentifier.'">';       
        if ($imgAttach!=false){
         echo '<div class="close fileinput-remove text-right removePhotos" id="'.$voice->uniqueidentifier.'"><i class="fa fa-remove"></i></div>';
          foreach ($imgAttach as $aa=>$bb){
           $photo=$folder."".$bb; 
           $strImg=$util->resize($photo,200,200);
           echo '<img src="'.$strImg.'" width="200" style="width:200px; border:1px solid #DDDDDD; box-shadow:#A2958A 1px 1px 5px 0; padding:5px; margin:5px; margin-bottom:10px;">';   
           echo '<input type="hidden" name="photosUploaded[]" value="'.$bb.'">';
          }
        }
        
        echo '</div>';
       ?>
       
       <input id="mediaUpload" class="file" name="mediaUpload[]" type="file" multiple="multiple">
       
      </div><!--col-sm-12-->
      <div class="col-sm-12" style="padding-top:10px; padding-bottom: 10px;">
        Uploaded photos should be at least 512 x 384 pixels. 
      </div>
     </div><!--row-->
    </li><!--list-group-item-->
    <!--<div class="resultUpdateSet"></div>-->
    
     <li class="list-group-item">
     <div class="row">
      <div class="col-sm-12"><label class="control-label-voice-out">Image or video link  </label> <span id="resultsLoadingEmbed" class="pull-right" style="display:none;"><i class="fa fa-circle-o-notch fa-spin"></i></span><span id="resultsLoadingEmbedError" class="pull-right"></span></div><!--col-sm-2-->
      <div class="col-sm-12">
       <div id="embedMediaHolder">
        <?
        if ($voice->url_type=="video"){
         echo $util->expandURL($voice->url_media,"100%","400"); 
        }elseif ($voice->url_type=="image"){
         if (strlen($voice->url_media) > 0){
         echo "<img src='".$util->resize($voice->url_media,800,400)."' class='img-responsive embedImg'>";
         }
        }
        
        ?>
       </div><!--embedMediaHolder--> 
       <input type="text" id="url_expander" class="form-control voicetox input-lg" name="url_expander" placeholder="http://" value="<? $util->setValue($voice->url_expander) ?>"/>
      </div><!--col-sm-12-->
      <div class="col-sm-12" style="padding-top:10px; padding-bottom: 10px;">
        Paste url link of images or videos from websites
      </div>
     </div><!--row-->
    </li><!--list-group-item-->
    <!--<div class="resultUpdateSet"></div>-->
  </ul>

</div>
</div><!--col-md-9-->

<div class="col-md-4"></div>
<div class="col-md-8"><div id="resultSave"></div></div>

<div class="col-md-12 submitMessage text-right pull-right"></div>
<div class="col-md-12 submitButton">

<button type="submit" name="save" class="btn btn-primary btn-lg  tinyVoicesBtnRegister pull-right" id="postVoiceSubmit">
  <div class="socialActions"> Preview &nbsp; <i class="fa fa-chevron-circle-right"/></i></div></button>
</div>
</div><!--row-->

<input type="hidden" name="foursquare_id" id="resultUpdateSet" value="<? $util->setValue($voice->foursquare_id) ?>">
<input type="hidden" name="locationLat" id="resultUpdateSetLat" value="<? $util->setValue($voice->locationLat) ?>">
<input type="hidden" name="locationLong" id="resultUpdateSetLong" value="<? $util->setValue($voice->locationLong) ?>">
<input type="hidden" name="mediaurl" id="mediaurl" value="<? $util->setValue($voice->url_media) ?>">
<input type="hidden" name="mediatype" id="mediatype" value="<? $util->setValue($voice->url_type) ?>">
<input type="hidden" name="mode" value="<?=$mode ?>">
<? if ($mode=="edit") { ?>
<input type="hidden" name="uniqueId" value="<?=$id ?>">
<div class="removeUploadedPhotos"></div>
<? } ?>
</form>

<? $html->search(); ?>
<? $html->footer(); ?>  
</div><!--container-->

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/switch.js"></script>

    <script src="<?=SERVER_URL?>layout/js/editor/tinymce.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/fileinput.js"></script>
    
    <script src="<?=SERVER_URL?>layout/js/typeahead/typeahead.js"></script>
    <script src="<?=SERVER_URL?>layout/js/typeahead/hogan-2.0.0.js"></script>
   
    <script src="<?=SERVER_URL?>layout/js/scrollbar.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
    
    <script>$('.tt-dropdown-menu').perfectScrollbar();</script>
    <script>
    <? if ($mode=="new") { ?>
    $("button[name='save']").hide();
    <? } ?>
     
    $('#brands_services').keyup(function() {
     var brands_services=$("#brands_services").val().length;

     if(brands_services < 1) {
     $("button[name='save']").hide();
     }else{
     $("button[name='save']").show();
     }
    });

    </script>
    <? if ($mode=="edit") { ?>
    <script>
     <? if ($imgAttach!=false){ ?>
     $(".file-input").hide();
     $('.removePhotos').click(function() {
     var id=$('.removePhotos').attr('id');
     $(".photoUploaded-"+id).html("");
     $(".removeUploadedPhotos").html("<input type='hidden' name='removePhotos' value='"+id+"'>");
     $(".file-input").show();
     });
     <? } ?>
     
     
      $('#resultUpdateSetLat').val(<? $util->setValue($voice->locationLat) ?>);
      $('#resultUpdateSetLong').val(<? $util->setValue($voice->locationLong) ?>);
      
       var pinkParksStyles = [
			{
				featureType: 'road.highway',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 72 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'water',
				elementType: 'all',
				stylers: [
					{ hue: '#30a5dc' },
					{ saturation: 47 },
					{ lightness: -31 },
					{ visibility: 'simplified' }
				]
			},{
				featureType: 'road',
				elementType: 'all',
				stylers: [
					{ hue: '#cccccc' },
					{ saturation: -100 },
					{ lightness: 44 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'landscape',
				elementType: 'all',
				stylers: [
					{ hue: '#ffffff' },
					{ saturation: -100 },
					{ lightness: 100 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'poi.park',
				elementType: 'all',
				stylers: [
					{ hue: '#d2df9f' },
					{ saturation: 12 },
					{ lightness: -4 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'road.arterial',
				elementType: 'all',
				stylers: [
					{ hue: '#e5e5e5' },
					{ saturation: -100 },
					{ lightness: 56 },
					{ visibility: 'on' }
				]
			},{
				featureType: 'administrative.locality',
				elementType: 'all',
				stylers: [
					{ hue: '#000000' },
					{ saturation: 0 },
					{ lightness: 0 },
					{ visibility: 'on' }
				]
			}
		];
     
     var pinkMapType = new google.maps.StyledMapType(pinkParksStyles,{name: "Pink Parks"});
               var mapOptions = {
                   center: new google.maps.LatLng(<? $util->setValue($voice->locationLat) ?>, <? $util->setValue($voice->locationLong) ?>),
                   zoom: 15,
                   mapTypeId: google.maps.MapTypeId.ROADMAP,
                   panControl: false,
                   zoomControl: true,
                   scaleControl: true,
                   mapTypeControl: false,
                   mapTypeControlOptions: {
                   mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_layout_styles']
                   }
               };
               
              var map = new google.maps.Map(document.getElementById("google_canvas"),mapOptions);
              map.mapTypes.set('map_layout_styles',pinkMapType);
              map.setMapTypeId('map_layout_styles'); 
               
              var center = new google.maps.LatLng(<? $util->setValue($voice->locationLat) ?>, <? $util->setValue($voice->locationLong) ?>);
              map.setZoom(15);
              map.panTo(center);
              map.setZoom(15);
              
             var myMarker = new google.maps.Marker({
             position: new google.maps.LatLng(<? $util->setValue($voice->locationLat) ?>, <? $util->setValue($voice->locationLong) ?>),
             draggable: true
             });
             
             google.maps.event.addListener(myMarker, 'dragend', function(evt){
                //document.getElementById('current').innerHTML = '<p>Marker dropped:  ' + evt.latLng.lat().toFixed(3) + ',' + evt.latLng.lng().toFixed(3) + '</p>';
             $('#resultUpdateSetLat').val(evt.latLng.lat());
             $('#resultUpdateSetLong').val(evt.latLng.lng());
             });
             
             google.maps.event.addListener(myMarker, 'dragstart', function(evt){
             $('#resultUpdateSetLat').val(evt.latLng.lat());
             $('#resultUpdateSetLong').val(evt.latLng.lng());
             });
             
             myMarker.setMap(map);
             map.panTo(<? $util->setValue($voice->locationLat) ?>, <? $util->setValue($voice->locationLong) ?>);
     
    </script>
    <? } ?>
  </body>
</html>