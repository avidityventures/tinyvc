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
$cat1="4d4b7105d754a06374d81259";
$cat2="4d4b7105d754a06379d81259";
$cat3="4d4b7105d754a06375d81259";
$cat4="4d4b7105d754a06378d81259";
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <? $html->metaHeader(); ?>

    <!-- Bootstrap core CSS -->
    <link href="<?=SERVER_URL?>layout/css/main.css" rel="stylesheet"/>
    <link href="<?=SERVER_URL?>layout/css/tinyvoices.css" rel="stylesheet"/>
    <link href="<?=SERVER_URL?>layout/css/tinyvoices-home.css" rel="stylesheet"/>
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

<div class="container">
<div class="row">


<h1 class="section-title">BY CATEGORIES</h1>

<div style="margin-bottom: 50px;">
  


<div class="col-sm-12 col-md-12 col-lg-6">
<a href="<?=SERVER_URL?>categories?v=<?=$cat1?>">
<div class="maincatBox-1">
  <div class="box-description">
   <div class="col-lg-12 title-box">Food & Beverages</div>
  </div>
</div><!--maincatBox-->
</a>
</div>



<div class="col-sm-12 col-md-12 col-lg-3">
<a href="<?=SERVER_URL?>categories?v=<?=$cat2?>">
<div class="maincatBox-2">
  <div class="box-description">
    <div class="col-lg-12 title-box">Travels</div>
  </div>
</div><!--maincatBox-->
</a>
</div>


<div class="col-sm-12 col-md-12 col-lg-3">
<a href="<?=SERVER_URL?>categories?v=<?=$cat3?>">
<div class="maincatBox-3">
  <div class="box-description">
    <div class="col-lg-12 title-box">Professional</div>
  </div>
</div><!--maincatBox-->
</a>
</div>

<div class="col-lg-12" style="margin-top:10px;"></div>
<div class="clearfix"></div>

<div class="col-sm-12 col-md-12 col-lg-6">
<a href="<?=SERVER_URL?>categories?v=<?=$cat4?>">
<div class="maincatBox-4">
  <div class="box-description">
    <div class="col-lg-12 title-box">Products & Services</div>
  </div>
</div><!--maincatBox-->
</a>
</div>



<div class="col-sm-12 col-md-12 col-lg-6">
<a href="<?=SERVER_URL?>categories?v=trending-voices">
<div class="maincatBox-5">
  <div class="box-description">
    <div class="col-lg-12 title-box">Trending Voices</div>
  </div>
</div><!--maincatBox-->
</a>
</div>
</div>


<div class="clearfix"></div>



<h1 class="section-title">RECENT VOICES</h1>
<? $html->voicePosts(); ?>






</div><!--row-->


</div><!--container-->

<div class="jumbotron-categories">
<div class="container">
<h2 class="text-center">Feeling unhappy about a service or brand</h2>
<div class="col-md-3"></div>
<div class="col-md-6">

<p class="text-center">
Felt that you are being short-changed by the company or services ? Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedbacks. This is a crowdfunded opinion community platform.
</p>

<center>
  <? if (isset($_SESSION['authorized'])){ ?>
  <a href="<?=SERVER_URL?>voice" type="button" class="btn btn-primary btn-lg tinyVoicesBtnBG"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
  <? }else{ ?>
  <a href="#" type="button" data-toggle="modal" data-target="#loginOptions" class="btn btn-primary btn-lg tinyVoicesBtnBG"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
  <? } ?>
</center>
</div>
<div class="col-md-3"></div>  
</div><!--container-->
</div><!--jumbotron-categories-->

<div class="container">
<div class="row">
<? $html->footer(); ?>
</div>
</div>


<? $html->search(); ?>
<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>

  </body>
</html>