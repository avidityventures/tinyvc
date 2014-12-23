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
$util->isValidReferer();

$profile_details=$util->setSession();
$util->checkPostOwner($_REQUEST['v']);

if(isset($_REQUEST['v'])){
 $voice=$util->getVoices($_REQUEST['v']);
}else{
 $voice=false;
}
 if ($voice==false){
  $util->js_redirect(SERVER_URL);
  die();
 }
$id=$voice->uniqueidentifier;
$imgAttach=$util->checkAttachment($id);
$folder=$util->publicFolder($profile_details->email);

$updateStatus="update voices set publish='1' where uniqueidentifier='".$id."'";
$defaultMessage="I have just lodged a complaint on ".$voice->brands_services.", please support my effort if you agree with me.";
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
<? $html->progressBarVoice(1,1,1,0); ?>
 <div class="row">
  
  <form class="form-horizontal wrapper" name="form-share-social" method="post" enctype="multipart/form-data">

  <p>&nbsp;</p><p>&nbsp;</p>
  <div class="col-lg-1 col-md-1 ">
  
  </div>  
   <div class="col-lg-6 col-md-6 col-sm-12">
   <div class="panel panel-default panel-default-profilex">
     <ul class="list-group">
       <li class="list-group-item">
        <div class="row">
         <div class="col-sm-12"><label class="control-label-voice-out"> Share on social networks</label></div><!--col-sm-2-->
         <div class="col-sm-12"><span class="FS-search"></span>
          <textarea name="social_message" id="social_message" class="form-control voicetox input-lg textarea-box" style="height:150px;"><?=$defaultMessage?></textarea>
         </div><!--col-sm-12-->
         <div class="col-sm-12" style="padding-top:10px; padding-bottom: 10px;">
           Wite a short message to your friends or followers on your social network accounts to get support. 
         </div>
        
        </div><!--row-->
         <? if ($TOTAL_FB==1){ ?>
         <div style="border-top:1px dotted #c2c2c2; padding:10px;">
          <i class="fa fa-facebook-square profile-social-fb-selected pull-left" style="font-size:30px; margin-top:4px; margin-right:20px;"></i> <input class="switch-state" name="facebook_autopost" type="checkbox" value="1" <? if ($d_fb->autopost==1) {echo "checked"; } ?>/>&nbsp;&nbsp; Share on Facebook
         </div>
         <? } ?>
         <? if ($TOTAL_TWIT==1){ ?>
         <div style="border-top:1px dotted #c2c2c2; padding:10px;">
          <i class="fa fa-twitter profile-social-twit-selected pull-left" style="font-size:29px; margin-top:4px; margin-right:20px;"></i> <input class="switch-state" name="twitter_autopost" type="checkbox" value="1" <? if ($d_twit->autopost==1) {echo "checked"; } ?>/>&nbsp;&nbsp; Share on Twitter
         </div>
         <? } ?>
       </li><!--list-group-item-->
     </ul>
   </div><!--panel-->
   </div><!--col-md-6-->
  
  <div class="col-lg-5 col-md-5 col-sm-12 text-right" class="pull-right">
   <div id="results"></div>
   <div class="voice-confirm">
    <hr>
    <h1 style="font-weight:200;">Publish now and start receiving backers for your post. </h1><br>
    
    <button type="submit" class="btn btn-primary btn-lg tinyVoicesBtnRegister" id="saveFormShare"><div class="socialActions"> Share Now </div></button>
    
    <hr>
    Skip this step. Return to <a href="<?=SERVER_URL?>">homepage</a>
   </div>
  </div><!--col-md-6-->
  <input type="hidden" name="idval" value="<?=$id?>">
  </form>
 </div><!--row-->

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
    $("#social_message").characterCounter();
    $(document).ready(function(){
     $("#social_message").autosize();    
    });
    </script>
    
</body>
</html>