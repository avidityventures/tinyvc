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

$voice_response=strip_tags($_POST['voice_response']);
$facebook_autopost=$_POST['facebook_autopost'];
$twitter_autopost=$_POST['twitter_autopost'];
$vid=$_POST['vid'];



$countCheck=$db->db_query("select count(*)as TOTAL from voters_responses where id='".$profile_details->id."' and response='".escape_string($voice_response)."'");
$countCheck_e=$db->db_fetch_array($countCheck);

$linkGenerate=$tempVoiceFeeds."/voice-view?v=".$vid."";
#-------------------------------------
# Check Response Message
#-------------------------------------
if (strlen($_POST['voice_response']) < 1){
echo json_encode(array('error' => 'Please state your response message'));
die();  
}

 if (ctype_space($_POST['voice_response'])) {
 echo json_encode(array('error' => 'Please state your response message'));
 die();     
 }
#-------------------------------------
if ($countCheck_e->TOTAL < 1){

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

 $params = array('link' => $linkGenerate, 'message' => $voice_response);
 $response = $facebook->api('/me/feed', 'POST', $params);
}


if ($twitter_autopost==1){
$twitProfileLink=$db->db_query("select token,token_secret from social_accounts where id='".$profile_details->twit_id."' and type='twitter' LIMIT 1");
$twitProfileLink_e=$db->db_fetch_array($twitProfileLink);

$twitProfileLink_e->token;
$twitProfileLink_e->token_secret;

require_once('../../system_files/modules/twitter/twitteroauth/twitteroauth.php');    
    
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $twitProfileLink_e->token, $twitProfileLink_e->token_secret);
$fullParam="https://api.twitter.com/1.1/account/verify_credentials.json";    

$fullmessage=$voice_response." ".$linkGenerate; 
$results = $connection->post('statuses/update', array('status' => $fullmessage)); 
$twit_id_val=$results->id;

}

$insertRecords=$db->db_query("insert into voters_responses (id,post_id,response,fb_post_id,twit_post_id) values
                            ('".$profile_details->id."','".escape_string($vid)."','".escape_string($voice_response)."',
                             '".$response['id']."','".$twit_id_val."')");

$country=$util->getCountry($profile_details->country);

if (isset($_POST['recordsEmpty'])){
echo json_encode(array('id' => $profile_details->id,
                       'fullname' => $profile_details->fullname,
                       'fd' => $profile_details->folder,
                       'response' => $voice_response,
                       'country' => $country,
                       'record' => 0,
                       'vid'=>$vid));
    
}else{
echo json_encode(array('id' => $profile_details->id,
                       'fullname' => $profile_details->fullname,
                       'fd' => $profile_details->folder,
                       'response' => $voice_response,
                       'country' => $country,
                       'vid'=>$vid));
}
                       
                       
}
?>