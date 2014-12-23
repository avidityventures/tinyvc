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

require 'system_files/modules/facebook/facebook.php';
require_once('system_files/modules/twitter/twitteroauth/twitteroauth.php');
#---------------------------------------------------------------------
# Personal profile details
#---------------------------------------------------------------------
$profile_details=$util->setSession();
#---------------------------------------------------------------------
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
    if (isset($_REQUEST['code']) && (isset($_REQUEST['state']))){
      $db->db_query("update members set fb_id='".$profile['id']."' where id='".$profile_details->id."'");
      $db->db_query("delete from social_accounts where id='".$profile_details->id."' and type='facebook'");
      $db->db_query("insert into social_accounts (id,token,profile,autopost,type) values ('".$profile['id']."','".$_SESSION['fb_171160536375932_access_token']."','".$profile['link']."',1,'facebook')");
      $util->js_redirect(SERVER_URL."profile");
    }
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}
$loginUrl = "". $facebook->getLoginUrl(array('scope' => "".FB_PERMISSION.""));
#---------------------------------------------------------------------
# Twitter connect account
#---------------------------------------------------------------------
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
$twitLoginUrl = $connection->getAuthorizeURL($token);


if (strlen($profile_details->twit_id) > 2){
 $d_twit=$util->socialData($profile_details->twit_id);
}

if (strlen($profile_details->fb_id) > 2){
 $d_fb=$util->socialData($profile_details->fb_id);
}
#-----------------------------------------------
# Set profile photo
#-----------------------------------------------
if ($_SESSION['logintype']=="facebook"){
 $profilePhoto=$util->fbPhoto($profile_details->fb_id);
}else{
 $profilePhoto=$_SESSION['twitter_profile_img'];
}
#-----------------------------------------------
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

    <!-- Custom styles for this template -->
    <link href="<?=SERVER_URL?>layout/css/navbar-static-top.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src='https://www.google.com/recaptcha/api.js'></script>
  </head>

  <body style="background-color:#ececec;">
  <? $html->header(); ?> 


<div class="jumbotron-common text-center" style="padding-bottom: 40px; padding-top:120px; box-shadow: 0 0 12px rgba(0, 0, 0, 0.12);">
  <img src="<?=$util->profilePhoto()?>" class="img-circle profile-img">
  <hr class="profile-hr">
  <h1><?=$profile_details->fullname?></h1>
  <div style="font-size:14px; margin-top:-5px;" class="gray"><i class="fa fa-globe gray"></i> <?=$util->getCountry($profile_details->country);?></div>
</div>


<div class="container" style="margin-top:50px;">
<div class="row">
<form class="form-horizontal wrapper" name="form-profile" method="post" enctype="multipart/form-data">
  
<div class="col-md-3">
 <div class="list-group visible-lg-block visible-md-block profile-left-menu">
  <nav>
  <a href="#1" data-scroll="1" class="list-group-item" style="padding:12px;">Account Information</a>
  <a href="#2" data-scroll="2" class="list-group-item" style="padding:12px;">Social Accounts</a>
  <a href="#3" data-scroll="3" class="list-group-item" style="padding:12px;">Notifications</a>
  <a href="#4" data-toggle="modal" data-target="#deleteAccount" class="list-group-item" style="padding:12px;">Delete Account</a>
  </nav>
</div>
</div><!--col-md-3"-->
<div  id="1"></div>
<div class="col-md-9" >
<div class="panel panel-default panel-default-profile">
  <div class="panel-heading" >
    <h1 class="panel-title">Account Information</h1>
  </div>
    <ul class="list-group">
    
    
    
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">First Name</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <input type="text" class="form-control input-lg firstname" name="firstname" placeholder="Enter your first name" value="<?=$profile_details->fname; ?>" required/>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Last Name</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <input type="text" class="form-control input-lg firstname" name="lastname" placeholder="Enter your first name" value="<?=$profile_details->lname; ?>"/>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <?/*
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Profile Photo</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <input id="profilePhoto" class="file" name="mediaUpload[]" type="file" multiple="false">
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    */?>    
    
     <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Email Address</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <input type="text" class="form-control input-lg firstname" name="email" placeholder="Enter your first name" value="<?=$profile_details->email; ?>" required/>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <li class="list-group-item">
     <div class="row" style="padding-top:5px; padding-bottom:7px;">
      <div class="col-sm-2"><label class="control-label-profile" style="margin-top:5px;">Gender</label></div><!--col-sm-2-->
      <div class="col-sm-10">
        <div class=" gender-inline">
          <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio1" value="male" <? if ($profile_details->gender=="male"){ echo "checked"; } ?>> Male</label>
          <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio2" value="female" <? if ($profile_details->gender=="female"){ echo "checked"; } ?>> Female</label>
          <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio2" value="notsure" <? if ($profile_details->gender=="notsure"){ echo "checked"; } ?>> Not Sure</label>
        </div>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
     <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Country</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <select class="form-control input-lg profile-select" name="country">
        <option value="">Choose Country</option>
        <? 
        $rs=$db->db_query("select * from countries");
        while($rs_exec=$db->db_fetch_array($rs)){
          if ($profile_details->country==$rs_exec->ccode){
           echo "<option value='".$rs_exec->ccode."' selected>".$rs_exec->country."</option>\n";
          }else{
           echo "<option value='".$rs_exec->ccode."'>".$rs_exec->country."</option>\n";  
          }
        }
        ?>
      </select>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
     <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">City / Town</label></div><!--col-sm-2-->
      <div class="col-sm-10">
       <input type="text" class="form-control input-lg" name="city" placeholder="Enter the city you live" value="<?=$profile_details->city; ?>" />
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Birthdate</label></div><!--col-sm-2-->
       <div class="col-sm-2">
      <?
      $birthDate=explode("-",$profile_details->birthdt);
      $birthday_yy=$birthDate[0];
      $birthday_mm=$birthDate[1];
      $birthday_dd=$birthDate[2];
      ?>
      
      <select class="form-control profile-select input-lg" name="birth_dd" required/>
        <? for($a=1; $a<=31; $a++) { ?>
        <option value="<?=$a?>" <? if ($birthday_dd==$a){ echo "selected"; } ?>><?=$a?></option>
        <? } ?>
      </select>
    </div><!--col-sm-2-->
    
    <div class="col-sm-5">
    <select class="form-control profile-select input-lg" name="birth_mm" required/>
        <option value="01" <? if ($birthday_mm=="01") { echo "selected"; } ?>>January</option>
        <option value="02" <? if ($birthday_mm=="02") { echo "selected"; } ?>>February</option>
        <option value="03" <? if ($birthday_mm=="03") { echo "selected"; } ?>>March</option>
        <option value="04" <? if ($birthday_mm=="04") { echo "selected"; } ?>>April</option>
        <option value="05" <? if ($birthday_mm=="05") { echo "selected"; } ?>>May</option>
        <option value="06" <? if ($birthday_mm=="06") { echo "selected"; } ?>>June</option>
        <option value="07" <? if ($birthday_mm=="07") { echo "selected"; } ?>>July</option>
        <option value="08" <? if ($birthday_mm=="08") { echo "selected"; } ?>>August</option>
        <option value="09" <? if ($birthday_mm=="09") { echo "selected"; } ?>>September</option>
        <option value="10" <? if ($birthday_mm=="10") { echo "selected"; } ?>>October</option>
        <option value="11" <? if ($birthday_mm=="11") { echo "selected"; } ?>>November</option>
        <option value="12" <? if ($birthday_mm=="12") { echo "selected"; } ?>>December</option>
      </select>
    </div><!--col-sm-3-->
    
    <div class="col-sm-3">
      <select class="form-control profile-select input-lg" name="birth_yy" required/>
        <? for($c=date('Y')-18; $c>=1940; $c--) { ?>
        <option value="<?=$c?>" <? if ($birthday_yy==$c){ echo "selected"; } ?>><?=$c?></option>
        <? } ?>
      </select>
    </div><!--col-sm-3-->
     </div><!--row-->
    </li><!--list-group-item-->
    
   
  </ul>
</div>  
</div> 

<?
#--------------------------------------------------------------------------------------------------------
# Social Accounts
#--------------------------------------------------------------------------------------------------------
#---------------------------------------------
# Check if user has connected facebook account
#---------------------------------------------
$facebookCheck=$db->db_query("select count(*)as TOTAL_FB from social_accounts where id='".$profile_details->fb_id."' and type='facebook'");
$facebookCheck_e=$db->db_fetch_array($facebookCheck);
$TOTAL_FB=$facebookCheck_e->TOTAL_FB;

if ($TOTAL_FB==1){
 $fbProfileLink=$db->db_query("select profile from social_accounts where id='".$profile_details->fb_id."' and type='facebook' LIMIT 1");
 $fbProfileLink_e=$db->db_fetch_array($fbProfileLink);
 $profileURLFB=$fbProfileLink_e->profile;
  $facebookStatus="profile-social-fb-selected";
  $facebook_connected_status="Connected";
  $connectButtonFB=null;
}else{
  $facebookStatus=null;
  $facebook_connected_status="Not Connected";
  $connectButtonFB='<a href="'.$loginUrl.'" type="button" class="btn btn-primary btn-sm pull-right facebookBG"><i class="fa fa-facebook"></i> Connect</a>';
}

#---------------------------------------------
# Check if user has connected twitter account
#---------------------------------------------
$twitterCheck=$db->db_query("select count(*)as TOTAL_TWIT from social_accounts where id='".$profile_details->twit_id."'  and type='twitter'");
$twitterCheck_e=$db->db_fetch_array($twitterCheck);
$TOTAL_TWIT=$twitterCheck_e->TOTAL_TWIT;

if ($TOTAL_TWIT==1){
 $twitProfileLink=$db->db_query("select profile from social_accounts where id='".$profile_details->twit_id."' and type='twitter' LIMIT 1");
 $twitProfileLink_e=$db->db_fetch_array($twitProfileLink);
 $profileURLTwit=$twitProfileLink_e->profile;
  $twitterStatus="profile-social-twit-selected";
  $twitter_connected_status="Connected";
  $connectButtonTWIT=null;
}else{
  $twitterStatus=null;
  $twitter_connected_status='Not Connected';
  $connectButtonTWIT='<a href="'.$twitLoginUrl.'" type="button" class="btn btn-primary btn-sm pull-right twitterBG"><i class="fa fa-twitter"></i> Connect</a>';
}
?>
<p>&nbsp;</p>
<div class="col-md-3">&nbsp;</div>

<div class="col-md-9" id="2" data-anchor="2">
<div class="panel panel-default panel-default-profile">
  <div class="panel-heading">
    <h1 class="panel-title">Social Accounts</h1>
  </div>
    <ul class="list-group">
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-1 col-xs-2"><label class="control-label-profile <?=$facebookStatus?>"><i class="fa fa-facebook-square" style="font-size:40px;"></i> </label></div><!--col-sm-2-->
      <div class="col-sm-11 col-xs-10 pull-left">
       <div style="margin-top:16px; font-size:15px;" ><?=$facebook_connected_status?> <strong class="visible-lg-inline visible-md-inline "><?=$profileURLFB?></strong> <?=$connectButtonFB?></div>
       
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <? if ($TOTAL_FB==1){ ?>
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile" style="margin-top:5px;">Post to Timeline</label></div><!--col-sm-2-->
      <div class="col-sm-10">
      <input class="switch-state" name="facebook_autopost" type="checkbox" value="1" <? if ($d_fb->autopost==1) {echo "checked"; } ?>/>
       &nbsp; <small>Post on your facebook wall</small>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    <? } ?>
    
    
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-1 col-xs-2"><label class="control-label-profile <?=$twitterStatus?>"><i class="fa fa-twitter" style="font-size:40px;"></i> </label></div><!--col-sm-2-->
      <div class="col-sm-11 col-xs-10">
       <div style="margin-top:16px; font-size:15px;" ><?=$twitter_connected_status?> <strong class="visible-lg-inline visible-md-inline "><?=$profileURLTwit?></strong> <?=$connectButtonTWIT?></div>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <? if ($TOTAL_TWIT==1){ ?>
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile" style="margin-top:5px;">Tweet your Post</label></div><!--col-sm-2-->
      <div class="col-sm-10">
      <input class="switch-state" name="twitter_autopost" type="checkbox" value="1" <? if ($d_twit->autopost==1) {echo "checked"; } ?> />
       &nbsp; <small>Tweet your on your timeline</small>
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    <? } ?>
    
    
    </ul>
</div>
</div>



<?
#--------------------------------------------------------------------------------------------------------
# Notifications
#--------------------------------------------------------------------------------------------------------



?>
<p>&nbsp;</p>
<div class="col-md-3">&nbsp;</div>

<div class="col-md-9" id="3"  data-anchor="3">
<div class="panel panel-default panel-default-profile">
  <div class="panel-heading">
    <h1 class="panel-title">Notifications</h1>
    <div class="panel-title-sub">We'll always let you know about important changes, but you pick what else you want to hear about.</div>
  </div>
    <ul class="list-group">
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profile">Notifications </label></div><!--col-sm-2-->
      <div class="col-sm-10">
        <div class="gender-inline">
          <? $postings_notifications=$util->notificationsSettings('postings_notifications'); ?>
          <label class="radio-inline"><input type="radio" name="postings_notifications" id="inlineRadio1" value="1" <? if ($postings_notifications=="1"){ echo "checked"; } ?>>From everyone</label>
          <label class="radio-inline"><input type="radio" name="postings_notifications" id="inlineRadio1" value="2" <? if ($postings_notifications=="2"){ echo "checked"; } ?>>From people you follow</label>
        </div>
        
       
      </div><!--col-sm-10-->
     </div><!--row-->
    </li><!--list-group-item-->
    
    <li class="list-group-item">
     <div class="row">
      <div class="col-sm-2"><label class="control-label-profilex" style="font-size:16px;">Email</label></div><!--col-sm-2-->
      <div class="col-sm-10">
      <input class="switch-state" name="people_post_support" type="checkbox" value="1" <? if ($util->notificationsSettings('people_post_support')==1) {echo "checked"; } ?>/>
       &nbsp; <small>Backers who supports your post</small>
      </div><!--col-sm-10-->
     </div>
      
    <div class="row" style="margin-top:5px;">
      <div class="col-sm-2"></div><!--col-sm-2-->
      <div class="col-sm-10">
      <input class="switch-state" name="people_post_deny" type="checkbox" value="1" <? if ($util->notificationsSettings('people_post_deny')==1) {echo "checked"; } ?>/>
       &nbsp; <small>People who denies your post</small>
      </div><!--col-sm-10-->
    </div>
      
     <div class="row"  style="margin-top:6px;"> 
      <div class="col-sm-2"></div><!--col-sm-2-->
      <div class="col-sm-10">
      <input class="switch-state" name="people_post_comments" type="checkbox" value="1" <? if ($util->notificationsSettings('people_post_comments')==1) {echo "checked"; } ?>/>
       &nbsp; <small>People who comments on your posts</small>
      </div><!--col-sm-10-->
      
     </div><!--row-->
    </li><!--list-group-item-->
    
   
    
    </ul>
</div>
 <div id="results"></div>  
<button type="submit" class="btn btn-primary btn-lg tinyVoicesBtnRegister pull-right" id="saveFormProfile"><div class="socialActions"> Save Profile &nbsp; <i class="fa fa-chevron-circle-right"/></i></div></button>
</div>

</form>

<!-- Modal -->
<div class="modal fade" id="deleteAccount" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="margin-top:80px;">
        <h1 class="modal-title white" id="myModalLabel">Delete Account ? </h1>
      </div>
      <form id="confirmDeleteForm">
      <div class="modal-body white">
        Deleting you account means no one will be able to see your Posts or anything else from your activities in tinyvoices. You also won't be linked to Facebook or Twitter anymore. You will need to create a new account in order to post.
       
       <div class="g-recaptcha" data-sitekey="<?=GOOGLE_CAPTCHA?>" data-theme="dark" style="margin-top:30px;"> </div>
       
      <div style="margin-top:30px;">
       <label>
        <input type="checkbox" name="agreed_delete" value="1">&nbsp; I understand the terms and wish to delete my account.
       </label>
       </div>
       
       <div id="resultsDelete"></div> 
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-lg" id="confirmDelete">Proceed <i class="fa fa-warning"></i></button>
      </div>
      
      </form>
      
    </div>
  </div>
</div>
</div><!--row-->
<? $html->footer(); ?>
<? $html->search(); ?>
</div><!--container-->

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/switch.js"></script>
    <?/*
    <script src="<?=SERVER_URL?>layout/js/audio/recorderUtilx.js"></script>
    <script src="<?=SERVER_URL?>layout/js/audio/recorderx.js"></script>
    */?>
    <script src="<?=SERVER_URL?>layout/js/editor/tinymce.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/fileinput.js"></script>
    
    <script src="<?=SERVER_URL?>layout/js/typeahead/typeahead.js"></script>
    <script src="<?=SERVER_URL?>layout/js/typeahead/hogan-2.0.0.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
  </body>
</html>