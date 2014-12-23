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
include("../../system_files/lib/global.php");
$db=new connection();
$db->db_conn();
$util=new util();
$html=new defaultsite();
$util->isValidReferer();
$util->authAuthorized();

$profile_details=$util->setSession();
$util->checkPostOwner($_POST['idval']);

$social_message=$_POST['social_message'];
$facebook_autopost=$_POST['facebook_autopost'];
$twitter_autopost=$_POST['twitter_autopost'];
$idval=$_POST['idval'];
$publish=1;

$linkGenerate=$tempVoiceFeeds."/voice-view?v=".$idval."";

if ($facebook_autopost==1){
require '../../system_files/modules/facebook/facebook.php';
#-----------------------------------
# Class already initialize
# Get User ID
#-----------------------------------
$fbProfileLink=$db->db_query("select token,id from social_accounts where id='".$profile_details->fb_id."' and type='facebook' LIMIT 1");
$fbProfileLink_e=$db->db_fetch_array($fbProfileLink);

$_SESSION['fb_171160536375932_access_token']=$fbProfileLink_e->token;
$_SESSION['fb_171160536375932_user_id']=$fbProfileLink_e->id;

 $params = array('link' => $linkGenerate, 'message' => $social_message);
 $response = $facebook->api('/me/feed', 'POST', $params);
 $updateFB=$db->db_query("update voices set fb_post_id='".$response['id']."' where uniqueidentifier='".$idval."' and id='".$profile_details->id."'");
}

if ($twitter_autopost==1){
$twitProfileLink=$db->db_query("select token,token_secret from social_accounts where id='".$profile_details->twit_id."' and type='twitter' LIMIT 1");
$twitProfileLink_e=$db->db_fetch_array($twitProfileLink);


$twitProfileLink_e->token;
$twitProfileLink_e->token_secret;

require_once('../../system_files/modules/twitter/twitteroauth/twitteroauth.php');    
    
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitProfileLink_e->token, $twitProfileLink_e->token_secret);
$fullParam="https://api.twitter.com/1.1/account/verify_credentials.json";    

$fullmessage=$social_message." ".$linkGenerate; 
$results = $connection->post('statuses/update', array('status' => $social_message)); 
$twit_id_val=$results->id;

$updateTWIT=$db->db_query("update voices set twit_post_id='".$twit_id_val."' where uniqueidentifier='".$idval."' and id='".$profile_details->id."'");
}
$updatePublish=$db->db_query("update voices set publish='".$publish."' where uniqueidentifier='".$idval."' and id='".$profile_details->id."'");

$util->js_redirect(SERVER_URL);
?>