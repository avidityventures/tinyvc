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

require_once('system_files/modules/twitter/twitteroauth/twitteroauth.php');

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);



/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;
if (200 == $connection->http_code) {

 if (isset($_SESSION['authorized'])){
  $rs=$util->setSession();
 
  $profileLink="https://twitter.com/".$_SESSION['access_token']['screen_name'].""; 
  $db->db_query("update members set twit_id='".$_SESSION['access_token']['user_id']."' where id='".$rs->id."'");
  $db->db_query("delete from social_accounts where id='".$_SESSION['access_token']['user_id']."' and type='twitter'");
  $db->db_query("insert into social_accounts (id,token,token_secret,profile,autopost,type) values ('".$_SESSION['access_token']['user_id']."','".$_SESSION['access_token']['oauth_token']."','".$_SESSION['access_token']['oauth_token_secret']."','".$profileLink."',1,'twitter')");  
    
  $util->js_redirect(SERVER_URL."profile/");
 }else{
 $check=$util->checkUsers("twitter");
 
 if($check==1){
  $fullParam="https://api.twitter.com/1.1/account/verify_credentials.json";
  $results = $connection->get($fullParam);  
  $twitter_profile_image=str_replace("_normal.png",".png",$results->profile_image_url_https);
  $_SESSION['twitter_profile_img']=$twitter_profile_image;
  
  $rs=$util->setSession();
  $util->js_redirect(SERVER_URL);
 }
 
 }
 
 
}else{
 $util->js_redirect(SERVER_URL);
}
?>