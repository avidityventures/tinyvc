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
#-------------------------------------------------------------
# Facebook connect account
#-------------------------------------------------------------
$user = $facebook->getUser();
#-----------------------------------
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
#-------------------------------------------------------------
# Twitter connect account
#-------------------------------------------------------------
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
    echo 'Could not connect to Twitter. Refresh the page or try again later.';
}


$string="Old Town White Coffee'";
echo cleanURL($string) ;


function cleanURL($str, $delimiter='-') {
setlocale(LC_ALL, 'en_US.UTF8');
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
	return $clean;
}

die();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <? $html->metaHeader(); ?>
    
    <!-- Bootstrap core CSS -->
    <link href="<?=SERVER_URL?>layout/css/main.css" rel="stylesheet"/>
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

    <div class="jumbotron">
      <div class="container text-center">
        <h5 class="mainSliderSlogan">A Crowdfunded Opinion Community That Solves Problems</h5>
        <h1 class="mainSliderSloganLg">Broadcast insightful conversation</h1>
        <h3 class="mainSliderLongDesc">Where people <u>vent</u> about brands and services that needs improvement</h3>
        
        <div class="mainSliderBtn">
         <? if (!isset($_SESSION['authorized'])){ ?>
         <a href="<?=$loginUrl?>" type="button" class="btn btn-primary btn-lg facebookBG"><div class="socialActions"><i class="fa fa-facebook"></i> Facebook </div></a>
         <? } ?>
         <a href="<?=SERVER_URL?>voice/" type="button" class="btn btn-primary btn-lg tinyVoicesBtnBG"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
         <? if (!isset($_SESSION['authorized'])){ ?>
         <a href="<?=$twitLoginUrl?>" type="button" class="btn btn-primary btn-lg twitterBG"><div class="socialActions"><i class="fa fa-twitter"></i> Twitter </div></a>
         <? } ?>
        </div><!--mainSliderBtn-->
        
      </div><!--container text-center-->
    </div>

<div class="container">  
<div class="row">
  
<? $html->leftBar(); ?>  
<style>
.main-timeline{ 

}

.main-timeline-title{
font-size:22px;
font-weight:bold;

}

.main-title-small{
font-size:12px;
font-weight:200;
}

.main-title-brands{
margin-top:10px;  
font-size:25px;
}

.main-title-location{
  
}

.main-timeline-contents{
margin-top:10px;
color:#909090;
}

.main-timeline-users{
  
}

.main-timeline-fullname{
font-size:16px;
margin-left:20px;
margin-top:10px;
font-weight:bold;
}

.main-timeline-actions {
  background:#1ABC9C;
  color:#FFFFFF;
 border-radius:4px;
  padding:6px 6px;
}
</style>


  <div class="col-md-6">
    
     
        <?
        $voicesGet=$db->db_query("select * from voices where publish='1' order by id desc LIMIT 20");
        while($voices=$db->db_fetch_array($voicesGet)){
        
        $getUsers=$db->db_query("select fullname,country,folder,email from members where id='".$voices->id."' LIMIT 1");
        $users=$db->db_fetch_array($getUsers);
        
        $imgAttach=$util->checkAttachment($voices->uniqueidentifier);
        $folder=$util->publicFolder($users->email);
        
        ?>
        <div class="main-timeline">
          <?          
          if ($imgAttach!=false){
            foreach ($imgAttach as $aa=>$bb){
             echo '<img src="'.$folder.''.$bb.'" class="img-responsive">';   
            }
          }  
          ?>
          <div class="main-title-brands"><?=$voices->brands_services?></div>
          <div class="main-timeline-contents">
            
          <?
           $getHash=$util->get_hashtags($voices->problems_complaints);
           echo str_replace($getHash,"",$voices->problems_complaints);
          ?> 
          </div>
         <hr>
          <div class="main-timeline-users">
          <div class="col-md-1"><img src="<?=$util->publicPhoto($users->folder)?>" class="img-circle pull-left" width="50"></div>  
          <div class="col-md-9"><div class="main-timeline-fullname pull-left"> <?=$users->fullname?></div></div>
          <div class="col-md-3"><div class="main-timeline-actions"><i class="fa fa-comments"/></i><small> 1500 Backers</small></div></div>
          </div>
          
           
          <?/*
          <div class="main-timeline-title"><?=$users->fullname?> <small class="main-title-small">. <?=$util->getCountry($users->country)?></small> <img src="<?=$util->publicPhoto($users->folder)?>" class="img-circle avatar-feeds"> </div>
          <div class="main-title-brands"><?=$voices->brands_services?></div>
          <div class="main-title-location"><i class="fa fa-map-marker"></i> <?=$voices->address?></div>
          <div class="main-timeline-contents"></div> 
          */?>
        </div>
        <br><br><br><br><br><br>
        
        
        
        
        <? } ?>
      
    
  </div><!--col-md-6-->
  
<? $html->rightBar(); ?>
<? $html->search(); ?>


</div><!--row-->
<? $html->footer(); ?>
</div><!--container-->

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
  </body>
</html>