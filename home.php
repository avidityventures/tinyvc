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
#---------------------------------------------------------------------
$profile_details=$util->setSession();

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
         <? if (isset($_SESSION['authorized'])){ ?>
         <a href="<?=SERVER_URL?>voice" type="button" class="btn btn-primary btn-lg tinyVoicesBtnBG"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
         <? }else{ ?>
         <a href="#" type="button" data-toggle="modal" data-target="#loginOptions" class="btn btn-primary btn-lg tinyVoicesBtnBG"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
         <? } ?>

         <? if (!isset($_SESSION['authorized'])){ ?>
         <a href="<?=$twitLoginUrl?>" type="button" class="btn btn-primary btn-lg twitterBG"><div class="socialActions"><i class="fa fa-twitter"></i> Twitter </div></a>
         <? } ?>
        </div><!--mainSliderBtn-->

      </div><!--container text-center-->
    </div>

<style>
body{background-color:#f7f7f7;}

.maincatBox-1{
  background:url(http://freefoodphotos.com/imagelibrary/vegetables/slides/roast_vegetables.jpg) center center; background-size: 100%;
  height:200px;
  color:#fff;
  margin-bottom:10px;
}

.maincatBox-2{
  background-color:#00B16A;
  height:200px;
  padding:55px;
  color:#fff;
  margin-bottom:10px;

}

.maincatBox-3{
  background-color:#F4D03F;
  height:200px;
  padding:55px;
  color:#fff;
  margin-bottom:10px;
}

.title-box{

 padding:0;
 width:100%;
 padding:50px;
 font-size:40px;
}
</style>

<div class="container">
<div class="row">


<div style="margin-bottom: 50px;">
<div class="col-lg-6">
<div class="maincatBox-1 text-center">
  <div class="col-lg-12 title-box">Food Premises</div>
</div><!--maincatBox-->
</div>

<div class="col-lg-3">
<div class="maincatBox-2 text-center">
  <h1>Telcos</h1>
</div><!--maincatBox-->
</div>


<div class="col-lg-3">
<div class="maincatBox-3 text-center">
  <h1>Food</h1>
</div><!--maincatBox-->
</div>
</div>
<div class="clearfix"></div>



<h1 style="margin-left:15px; font-size:28px; margin-bottom:20px;">LATEST VOICES</h1>
<? $html->voicePosts(); ?>

</div><!--row-->
<? $html->footer(); ?>

<? $html->search(); ?>

</div><!--container-->

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>

  </body>
</html>
