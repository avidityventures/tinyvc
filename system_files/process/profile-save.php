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

if (isset($_SESSION['authorized'])){
$profile_details=$util->setSession();

  $checkDuplicate=$db->db_query("select count(*) as TOTAL from members where email='".escape_string($_POST['email'])."' and id!='".$profile_details->id."'");
  $checkDuplicate_e=$db->db_fetch_array($checkDuplicate);
  $duplicateCheck=$checkDuplicate_e->TOTAL; 
   
if ($duplicateCheck < 1){
#----------------------------------
# Personal profile
#----------------------------------
$firstname=trim($_POST['firstname']);
$lastname=trim($_POST['lastname']);
$email=trim($_POST['email']);
$gender=trim($_POST['gender']);
$country=$_POST['country'];
$city=$_POST['city'];
$birth_dd=$_POST['birth_dd'];
$birth_mm=$_POST['birth_mm'];
$birth_yy=$_POST['birth_yy'];

$fullname=$firstname." ".$lastname;
$fullBirthDay=$birth_yy."/".$birth_mm."/".$birth_dd;
$checkBirthDayLength=strlen($fullBirthDay);

#-------------------------------------
# Check First Name
#-------------------------------------
if (strlen($firstname) < 1){
$html->errorMessage("Please enter your first name");  
die();  
}
#-------------------------------------
# Check Gender
#-------------------------------------
if (!isset($_POST['gender'])){
$html->errorMessage("Please select your gender");  
die();  
}
#-------------------------------------
# Check Country
#-------------------------------------
if (strlen($country) < 1){
$html->errorMessage("Please select your country");  
die();  
}
#-------------------------------------
# Check Country
#-------------------------------------
if (strlen($city) < 1){
$html->errorMessage("Please specify the city you live in");  
die();  
}
#-------------------------------------
# Check BirthDay Length
#-------------------------------------
if (strlen($checkBirthDayLength) > 8){
$html->errorMessage("Select your date of birth");  
die(); 
}
#-------------------------------------
# Check Email
#-------------------------------------
if (strlen($email) < 1){
$html->errorMessage("Please enter your email address");  
die();  
}else{
 $checkEmail=$util->emailvalidation($email);
 if ($checkEmail==false){
  $html->errorMessage("Please make sure you have entered a valid email address");  
  die(); 
 }
}
#-------------------------------------
# Check Country
#-------------------------------------
if (strlen($country) < 1){
$html->errorMessage("Please select your country");  
die();  
}
#-------------------------------------
/*
$checkUpload=strlen($_FILES['mediaUpload']['name'][0]);
if ($checkUpload > 0){
 $filenames=$util->filenames();
 $folder=$util->userFolder($profile_details->email)."/media/";
 
 $deleteFile=$folder."".$profile_details->avatar;
 unlink($deleteFile);
 
 $updateAvatar=$db->db_query("update members set avatar='".$filenames[0]."' WHERE  id='".$profile_details->id."'");
 $util->mediaUpload($folder,$filenames); 
}
*/

$update="update members set email='".$email."',
                            fname='".$firstname."',
                            lname='".$lastname."',
                            fullname='".$fullname."',
                            gender='".$gender."',
                            birthdt='".$fullBirthDay."',
                            country='".$country."',
                            city='".escape_string($city)."'
                            WHERE
                            id='".$profile_details->id."'";
$db->db_query($update);
#----------------------------------
# Social accounts settings
#----------------------------------
$facebook_autopost=$_POST['facebook_autopost'];
$twitter_autopost=$_POST['twitter_autopost'];

if (isset($_POST['facebook_autopost'])){
 $updateFB="update social_accounts set autopost=1 where id='".$profile_details->fb_id."' and type='facebook'";
 $db->db_query($updateFB);
}else{
 $updateFB="update social_accounts set autopost=0 where id='".$profile_details->fb_id."' and type='facebook'";
 $db->db_query($updateFB);
}

if (isset($_POST['twitter_autopost'])){
 $updateTWIT="update social_accounts set autopost=1 where id='".$profile_details->twit_id."' and type='twitter'";
 $db->db_query($updateTWIT);
}else{
 $updateTWIT="update social_accounts set autopost=0 where id='".$profile_details->twit_id."' and type='twitter'";
 $db->db_query($updateTWIT);
}
#----------------------------------
# Notifications settings
#----------------------------------
$postings_notifications=$_POST['postings_notifications'];
$people_post_support=$_POST['people_post_support'];
$people_post_deny=$_POST['people_post_deny'];
$people_post_comments=$_POST['people_post_comments'];

$db->db_query("update notifications_settings set value='".$postings_notifications."' where id='".$profile_details->id."' and field='postings_notifications'");
$db->db_query("update notifications_settings set value='".$people_post_support."' where id='".$profile_details->id."' and field='people_post_support'");
$db->db_query("update notifications_settings set value='".$people_post_deny."' where id='".$profile_details->id."' and field='people_post_deny'");
$db->db_query("update notifications_settings set value='".$people_post_comments."' where id='".$profile_details->id."' and field='people_post_comments'");



}else{ 
 $html->errorMessage("Email address <strong><u>".$email."</u></strong> has been used, please use another email address");
 die(); 
}


$util->js_redirect(SERVER_URL."profile");
} else{
$util->js_redirect(SERVER_URL);
die(); 
}




?>