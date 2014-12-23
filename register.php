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

    //$util->checkUsers("facebook");
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

if ($_REQUEST['tp']=="facebook"){
#-----------------------------------
# Class already initialize
# Get User ID
#-----------------------------------
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
    $type="facebook";

    $id=$profile['id'];
    $birthday=explode("/",$profile['birthday']);
    $birthday_dd=$birthday[1];
    $birthday_mm=$birthday[0];
    $birthday_yy=$birthday[2];
        
    $email=$profile['email'];
    $gender=strtoupper($profile['gender']);
    $first_name=$profile['first_name'];
    $middle_name=$profile['middle_name'];
    $last_name=$profile['last_name'];
    $name=$profile['name'];
    $profileLink=$profile['link'];
    
    $fullfirstname=$first_name." ".$last_name;
    
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
 } #end try
} elseif ($_REQUEST['tp']=="twitter"){
$access_token=$_SESSION['access_token'];

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$fullParam="https://api.twitter.com/1.1/account/verify_credentials.json";

$results = $connection->get($fullParam);


$oauth_token=$_SESSION['access_token']['oauth_token'];
$user_id=$_SESSION['access_token']['user_id'];
$screen_name=$_SESSION['access_token']['screen_name'];

$twitt_id=$results->id;
$fullname=$results->name;
$twitter_profile_image=str_replace("_normal.png",".png",$results->profile_image_url_https);
$_SESSION['twitter_profile_img']=$twitter_profile_image;
$fullfirstname=$fullname;

$type="twitter";
  
}
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
    <?/*<script src='https://www.google.com/recaptcha/api.js'></script>*/?>
  </head>

  <body>
  <? $html->header(); ?> 

<div class="jumbotron-common">
      <div class="container">
        <h1>Voices that makes sense</h1>
        <p>TinyVoices is the world's largest complaint platform, empowering people everywhere to create the change they want to see. Register now and start voicing out.</p>
      </div>
</div>
    
<? $html->progressBar(1,1,0,0); ?>
    
<div class="container" style="margin-top:50px;">
<div class="row">
  
  <div class="col-md-9" style="background-color:#f3f3f3; padding:30px;">
  
  <form class="form-horizontal" role="form" id="registrationForm">

  <div class="form-group form-group-lg">
    <label for="inputEmail3" class="col-sm-2 control-label">First Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control firstname" name="firstname" placeholder="Enter your first name" value="<?=trim($fullfirstname); ?>" required/>
    </div>
  </div>
  
  <div class="form-group form-group-lg">
    <label for="inputEmail3" class="col-sm-2 control-label">Last Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="lastname" placeholder="Enter your last name" value="<?=$profile['last_name']; ?>" />
    </div>
  </div>
  
  <div class="form-group form-group-lg">
    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      <input type="email" class="form-control" name="email" id="inputEmail3" placeholder="Enter your email" value="<?=$profile['email']; ?>" required/>
    </div>
  </div>  
  
 <div class="form-group form-group-lg">
  <label for="inputEmail3" class="col-sm-2 control-label">Gender</label>
    <div class="col-sm-10 gender-inline">
      <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio1" value="male" <? if ($profile['gender']=="male"){ echo "checked"; } ?>> Male</label>
      <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio2" value="female" <? if ($profile['gender']=="female"){ echo "checked"; } ?>> Female</label>
      <label class="radio-inline"><input type="radio" name="gender" id="inlineRadio2" value="notsure" <? if ($profile['gender']=="notsure"){ echo "checked"; } ?>> Not Sure</label>
   </div>
 </div>
 
 <div class="form-group form-group-lg">
    <label for="inputEmail3" class="col-sm-2 control-label">Country</label>
    <div class="col-sm-10">
        <select class="form-control" name="country">
        <option value="">Choose Country</option>
        <? 
        $rs=$db->db_query("select * from countries");
        while($rs_exec=$db->db_fetch_array($rs)){
         echo "<option value='".$rs_exec->ccode."'>".$rs_exec->country."</option>\n";		
        }
        ?>
      </select>
        
    </div>
  </div>  
 
  
  <div class="form-group form-group-lg">
    <label for="inputEmail3" class="col-sm-2 control-label">Birthday</label>
    <div class="col-sm-2">
      <select class="form-control" name="birth_dd" required/>
        <option value="">Day</option>
        <? for($a=1; $a<=31; $a++) { ?>
        <option value="<?=$a?>" <? if ($birthday_dd==$a){ echo "selected"; } ?>><?=$a?></option>
        <? } ?>
      </select>
    </div><!--col-sm-2-->
    
    <div class="col-sm-5">
    <select class="form-control" name="birth_mm" required/>
        <option value="">Month</option>
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
      <select class="form-control" name="birth_yy" required/>
        <option value="">Year</option>
        <? for($c=date('Y')-18; $c>=1940; $c--) { ?>
        <option value="<?=$c?>" <? if ($birthday_yy==$c){ echo "selected"; } ?>><?=$c?></option>
        <? } ?>
      </select>
    </div><!--col-sm-2-->
    
    
  </div><!--form-group form-group-lg-->
  
  
  <?/*    
   <div class="form-group form-group-lg" style="margin-top:25px; padding-bottom:0;">
    <label for="inputEmail3" class="col-sm-2 control-label">Human ?</label>
    <div class="col-sm-10">
       <div class="g-recaptcha" data-sitekey="<?=GOOGLE_CAPTCHA?>"> </div>
    </div>
  </div>  
  */?>
  
  <div style="border-top:1px dotted #c2c2c2; padding-top:0px; margin-top:40px;">
   <div id="results"></div>       
  <h2>Terms & Conditions</h2>  
  TinyVoices is an online service that enables users to connect to others who care about the same social causes, share ideas about how to best address the issues they care about, and engage with organizations working to advance those causes.
  <br><br>
 
  <div>
  <label>
      <input type="checkbox" name="agreed" value="1">&nbsp; I agree to the terms & conditions
  </label>
  <button type="button" class="btn btn-primary btn-lg tinyVoicesBtnRegister pull-right" id="saveForm"><div class="socialActions"> Register <i class="fa fa-chevron-circle-right"/></i></div></button>
  </div>
  </div>
  
  <input type="hidden" name="type" value="<?=$type?>">
  <input type="hidden" name="profileLink" value="<?=$profileLink?>">
  
  </form>
  </div><!--col-md-9-->
  
  
  <div class="col-md-3"></div>




</div><!--row-->
<? $html->search(); ?>
<? $html->footer(); ?>  
</div><!--container-->



<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
  </body>
</html>