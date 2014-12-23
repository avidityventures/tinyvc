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

$profile_details=$util->setSession();

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

    <!-- Custom styles for this template -->
    <link href="<?=SERVER_URL?>layout/css/navbar-static-top.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  
<body>
  <? $html->header(); ?>
  
<div class="container" style="margin-top:50px;" >
 <div class="row">
 <form class="form-horizontal wrapper" name="form-response-voice" method="post" enctype="multipart/form-data">

  <p>&nbsp;</p><p>&nbsp;</p>

   <div class="col-md-1">
  
  </div>  
  
  <div class="col-md-6">
  <? $html->voicePosts($_REQUEST['v']); ?>
  </div><!--col-md-6-->

  <div class="col-md-4 text-right no-margin-padding">
  <? $util->showResponseInput($_REQUEST['v']); ?> 
  </div><!--col-md-4-->
  
  
  
  
    <div class="col-md-1"></div>
  </form>
  </div><!--row-->
 
 

 <div class="row">

  <div class="col-md-1"></div>
  <div class="col-md-6 text-center">

  <?
  $colors=array('#95A5A6','#4ECDC4','#52B3D9','#C0392B');
  
  
  $checkVoices=$db->db_query("select count(*)as TOTAL from voters_responses where post_id='".mysql_real_escape_string($_REQUEST['v'])."'");
  $checkVoices_e=$db->db_fetch_array($checkVoices);
  
  if ($checkVoices_e->TOTAL > 0){
  echo "<hr><h3>VIEWS OF RESPONDERS</h3><hr>"; 
  $getAllVoices=$db->db_query("select id,response,datetime from voters_responses where post_id='".mysql_real_escape_string($_REQUEST['v'])."' order by uid desc");
  while($getAllVoices_e=$db->db_fetch_array($getAllVoices)){
   
  $randColor=array_rand($colors,1);
  
  $getUsers=$db->db_query("select fullname,country,folder,email from members where id='".$getAllVoices_e->id."' LIMIT 1");
  $users=$db->db_fetch_array($getUsers);
  
  $unixTime=strtotime($getAllVoices_e->datetime);
  $time=$util->time_stamp($unixTime,1);
  
  ?>
  <div style="border-bottom:5px solid <?=$colors[$randColor]?>; padding-bottom:30px; padding:10px; padding-bottom: 20px;">
   <img src="<?=$util->publicPhoto($users->folder)?>" class="img-circle avatar-voices">
   <h4 style="display: inline;"><?=$users->fullname?> . <small><?=$util->getCountry($users->country)?></small><div></div></h4>
   <div style="margin-top:-20px; font-size:12px;"><?=$time?></div>
   <div style="margin-top:20px; font-family: arial;"><?=$getAllVoices_e->response?></div>
  </div>
  <?  
  }
  

  }
  
  ?>

   
   
  </div>

 </div>


<? $html->search(); ?>
<? $html->footer(); ?>  
</div><!--container--> 

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/switch.js"></script>
    <script src="<?=SERVER_URL?>layout/js/count-char.js"></script>
    <script src="<?=SERVER_URL?>layout/js/autosize.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
    <script>
    $("#voice_response").characterCounter();
    $(document).ready(function(){
     $("#voice_response").autosize();    
    });
    </script>
</body>
</html>