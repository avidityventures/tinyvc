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


require 'system_files/modules/facebook/facebook.php';
require_once('system_files/modules/twitter/twitteroauth/twitteroauth.php');
#---------------------------------------------------------------------
# Facebook connect account
#---------------------------------------------------------------------
$user = $facebook->getUser();
#---------------------------------------------------------------------
# Check if user authenticated
#---------------------------------------------------------------------
if ($user) {
  try {
#---------------------------------------------------------------------
# Proceed knowing you have a logged in user who's authenticated.
#---------------------------------------------------------------------
    $profile = $facebook->api('/me');  

    $util->checkUsers("facebook");
    $rs=$util->setSession();
   
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}
$loginUrl = "". $facebook->getLoginUrl(array('scope' => "".FB_PERMISSION.""));

$profile_details=$util->setSession();
#---------------------------------------------------------------------
# Twitter connect account
#---------------------------------------------------------------------
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

/* If last connection failed don't display authorization link. */
switch ($connection->http_code) {
  case 200:
    /* Build authorize URL and redirect user to Twitter. */
    $twitLoginUrl = $connection->getAuthorizeURL($token);
    break;
  default:
    /* Show notification if something went wrong. */
    //echo 'Could not connect to Twitter. Refresh the page or try again later.';
}
#--------------------------------------------------------------------------------------------------------
# Social Accounts
#--------------------------------------------------------------------------------------------------------
# Check if user has connected facebook account
#---------------------------------------------
$facebookCheck=$db->db_query("select count(*)as TOTAL_FB from social_accounts where id='".$profile_details->fb_id."' and type='facebook'");
$facebookCheck_e=$db->db_fetch_array($facebookCheck);
$TOTAL_FB=$facebookCheck_e->TOTAL_FB;
#---------------------------------------------
# Check if user has connected twitter account
#---------------------------------------------
$twitterCheck=$db->db_query("select count(*)as TOTAL_TWIT from social_accounts where id='".$profile_details->twit_id."' and type='twitter'");
$twitterCheck_e=$db->db_fetch_array($twitterCheck);
$TOTAL_TWIT=$twitterCheck_e->TOTAL_TWIT;
#---------------------------------------------
if (strlen($profile_details->twit_id) > 2){
 $d_twit=$util->socialData($profile_details->twit_id);
}

if (strlen($profile_details->fb_id) > 2){
 $d_fb=$util->socialData($profile_details->fb_id);
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
    <script src='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.js'></script>
    <link href='https://api.tiles.mapbox.com/mapbox.js/v2.1.4/mapbox.css' rel='stylesheet' />
    <!-- Custom styles for this template -->
    <link href="<?=SERVER_URL?>layout/css/navbar-static-top.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
  </head>
<style>

</style>
  
<body>
  <? $html->header(); ?>
<style>
.timeline-centered .timeline-entry .timeline-entry-inner {
  margin-left:-20px;
  margin-right:20px;
  position:relative;
} 
</style>
<div class="container" style="margin-top:50px;">
 <div class="row" >
 <form class="form-horizontal wrapper" name="form-response-voice" method="post" enctype="multipart/form-data">

  <p>&nbsp;</p><p>&nbsp;</p>


  
  <div class="col-md-6 " id="voice-view-map">
  <? $html->voicePosts($_REQUEST['v']); ?>
  <?
  $checkVoices=$db->db_query("select count(*)as TOTAL from voters_responses where post_id='".escape_string($_REQUEST['v'])."'");
  $checkVoices_e=$db->db_fetch_array($checkVoices);
  
  $checkLatLong=$db->db_query("select locationLat,locationLong,category_icon from voices where uniqueidentifier='".escape_string($_REQUEST['v'])."'");
  $checkLatLong_e=$db->db_fetch_array($checkLatLong);
  
  if ($checkVoices_e->TOTAL > 0){
   echo "<hr>"; 
  }
  ?>

  
  
  <div id="topResponse"></div>
  <div class="text-center" style="margin-bottom: 30px;">

  <?
  $colors=array('#95A5A6','#4ECDC4','#52B3D9','#C0392B','#336E7B','#913D88','#F5D76E','#D2527F','#26C281');
 
  if ($checkVoices_e->TOTAL > 0){
  echo "<div class='voiceComments-header-title'>VOICES OF RESPONDERS</div>";
  echo '<div id="commentsBox"></div>';
  $getAllVoices=$db->db_query("select uid,id,response,datetime from voters_responses where post_id='".escape_string($_REQUEST['v'])."' order by uid desc");
  while($getAllVoices_e=$db->db_fetch_array($getAllVoices)){
   
  $randColor=array_rand($colors,1);
  
  $getUsers=$db->db_query("select fullname,country,folder,email from members where id='".$getAllVoices_e->id."' LIMIT 1");
  $users=$db->db_fetch_array($getUsers);
  $unixTime=strtotime($getAllVoices_e->datetime);
  $time=$util->time_stamp($unixTime,1);
  
  ?>
  <div class="voicesComments-response voice-comments-box_<?=$getAllVoices_e->uid?>" style="border-bottom:1px solid <?=$colors[$randColor]?>">
   <? if ($getAllVoices_e->id==$profile_details->id){ ?>
   <a  class="hide-delete-comment delete-comment" id="<?=$getAllVoices_e->uid?>"><i class="fa fa-remove pull-right "></i></a>
   <? } ?>
   <div id="delete_confirm_comment_<?=$getAllVoices_e->uid?>"></div>
   <img src="<?=$util->publicPhoto($users->folder)?>" class="img-circle avatar-voices">
   <h4 style="display: inline;"><?=$users->fullname?> . <small><?=$util->getCountry($users->country)?></small><div></div></h4>
   <div class="voicesComments-time"><?=$time?></div>
   <div class="voiceComments-paragraph"><?=$getAllVoices_e->response?></div>
  </div>
  <?  
  }
  

  }else{
  echo "<div class='voiceComments-header-title hidediv'>VOICES OF RESPONDERS</div>"; 
  echo '<div id="commentsBox"></div>';
  echo '<input type="hidden" name="recordsEmpty" value="0">';
  }
  
  ?>
  </div>
  
  <?
  if ($_SESSION['authorized']==1){
   
   $util->showResponseInput($_REQUEST['v']);
  }
  ?>
  
  </div><!--col-md-6-->


   
  </form>

</div><!--row-->
 
<div id='map-marker'> </div>

<? $html->search(); ?>
</div><!--container--> 
<!-- Modal-->
<div class="modal fade" id="reportPost" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="margin-top:80px;">
        <h1 class="modal-title white" id="myModalLabel">Report inappropriate ? </h1>
      </div>
      <form id="confirmReportForm">
      <div class="modal-body white">
        <h4>Why are you reporting this post ?</h4><br>
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="1" checked="">
          Nudity or pornography
        </label>
        </div>
        
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="2" checked="">
          Violence or harmful voice
        </label>
        </div>
        
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="3" checked="">
          Hateful speech or symbols
        </label>
        </div>
        
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="4" checked="">
          Sexually explicit contents
        </label>
        </div>
        
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="5" checked="">
          Spam or Scam
        </label>
        </div>
        
        <div class="radio">
        <label>
          <input type="radio" name="optionsRadios" id="optionsRadios1" value="6" checked="">
          Inaccurate Information
        </label>
        </div>
      <input type="hidden" name="id" value="<?=$_REQUEST['v']?>">
       <div id="resultsDelete"></div> 
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger btn-lg" id="confirmReport">Report Post</button>
      </div>
      </form>
      </div>
      
    </div>
</div>

<!--modal-->
<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/switch.js"></script>
    <script src="<?=SERVER_URL?>layout/js/autosize.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>

</body>
<script>
L.mapbox.accessToken = '<?=MAPBOX_ACCESS_TOKEN?>';
var map = L.mapbox.map('map-marker', '<?=MAPBOX_ID?>').setView([<?=$checkLatLong_e->locationLat?>, <?=$checkLatLong_e->locationLong?>], 14);
var marker = new L.Marker(new L.LatLng(<?=$checkLatLong_e->locationLat?>, <?=$checkLatLong_e->locationLong?>));
map.addLayer(marker);
map.scrollWheelZoom.disable();
map.panBy([-200, 0]);
</script>
</html>