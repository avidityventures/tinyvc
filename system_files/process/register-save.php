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

if (isset($_POST['agreed'])){
$firstname=trim($_POST['firstname']);
$lastname=trim($_POST['lastname']);
$gender=trim($_POST['gender']);
$birth_dd=$_POST['birth_dd'];
$birth_mm=$_POST['birth_mm'];
$birth_yy=$_POST['birth_yy'];
$type=$_POST['type'];
$agreed=$_POST['agreed'];
$email=trim($_POST['email']);
$country=$_POST['country'];
$profileLink=$_POST['profileLink'];



$fullname=$firstname." ".$lastname;
$fullBirthDay=$birth_yy."/".$birth_mm."/".$birth_dd;
$checkBirthDayLength=strlen($fullBirthDay);
/*
$google_url="https://www.google.com/recaptcha/api/siteverify";
$secret=GOOGLE_CAPTCHA_SECRET;
$ip=$_SERVER['REMOTE_ADDR'];
$url=$google_url."?secret=".$secret."&response=".$_POST['g-recaptcha-response']."&remoteip=".$ip;
$res=file_get_contents($url);
$captcha_response= json_decode($res, true);
*/
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
/*
#-------------------------------------
# Check Captcha
#-------------------------------------
if (!isset($_POST['g-recaptcha-response'])){
$html->errorMessage("Please re-enter captcha to prove you're not a robot");  
die();  
}
if ($captcha_response['success']!=1){
$html->errorMessage("Please re-enter captcha to prove you're not a robot");  
die();  
}
*/
$folder=$util->userFolder($email);
$folderName=md5($email);
#-----------------------------------------------------
# Insert if facebook
#-----------------------------------------------------
 if ($type=="facebook"){
  $checkDuplicate=$db->db_query("select count(*) as TOTAL from members where email='".$email."'");
  $checkDuplicate_e=$db->db_fetch_array($checkDuplicate);
  $duplicateCheck=$checkDuplicate_e->TOTAL; 
   
  if ($duplicateCheck < 1){
  
   $FBTOKEN=$_SESSION['fb_171160536375932_access_token'];
   $FBID=$_SESSION['fb_171160536375932_user_id'];
  
   $insert="insert into members (fb_id,email,fname,lname,fullname,gender,birthdt,country,folder) values
                                ('".$FBID."','".$email."','".$firstname."','".$lastname."','".$fullname."','".$gender."','".$fullBirthDay."','".$country."','".$folderName."')"; 
   $rs=$db->db_query($insert);
   
   $deletOldToken=$db->db_query("delete from social_accounts where id='".$FBID."' and type='facebook'");
   
   $insert_social="insert into social_accounts (id,token,profile,type) values ('".$FBID."','".$FBTOKEN."','".$profileLink."','facebook')";
   $rs2=$db->db_query($insert_social);

  }else{
  $html->errorMessage("Email address <strong><u>".$email."</u></strong> has been used, please use another email address");
  die();
  }

#-----------------------------------------------------
# Insert if twitter
#-----------------------------------------------------
 }elseif($type=="twitter"){
   
   $checkDuplicate=$db->db_query("select count(*) as TOTAL from members where email='".$email."'");
   $checkDuplicate_e=$db->db_fetch_array($checkDuplicate);
   $duplicateCheck=$checkDuplicate_e->TOTAL; 
   
   if ($duplicateCheck < 1){
    $insert="insert into members (twit_id,email,fname,lname,fullname,gender,birthdt,country,folder) values
                                ('".$_SESSION['access_token']['user_id']."','".$email."','".$firstname."','".$lastname."','".$fullname."','".$gender."','".$fullBirthDay."','".$country."','".$folderName."')"; 
   $rs=$db->db_query($insert); 
   
   $deletOldToken=$db->db_query("delete from social_accounts where id='".$_SESSION['access_token']['user_id']."' and type='twitter'");
   
   $profileLink="https://twitter.com/".$_SESSION['access_token']['screen_name']."";
   
   $insert_social="insert into social_accounts (id,token,token_secret,profile,type) values ('".$_SESSION['access_token']['user_id']."','".$_SESSION['access_token']['oauth_token']."','".$_SESSION['access_token']['oauth_token_secret']."','".$profileLink."','twitter')";
   $rs2=$db->db_query($insert_social);
   }else{
   $html->errorMessage("Email address <strong><u>".$email."</u></strong> has been used, please use another email address");
   die(); 
   }
 }
#----------------------------------------------------- 
 
#----------------------------------------------------
# If Success Creating Account
#----------------------------------------------------
   if ($rs==1){
    $getID=$db->db_query("select id from members where email='".$email."' LIMIT 1");
    $getID_e=$db->db_fetch_array($getID);
    $profileIDGET=$getID_e->id;
    
    $db->db_query("insert into notifications_settings (id,field,value) values ('".$profileIDGET."','postings_notifications',1)");
    $db->db_query("insert into notifications_settings (id,field,value) values ('".$profileIDGET."','people_post_support',1)");
    $db->db_query("insert into notifications_settings (id,field,value) values ('".$profileIDGET."','people_post_deny',1)");
    $db->db_query("insert into notifications_settings (id,field,value) values ('".$profileIDGET."','people_post_comments',1)");

    #------------------------------------------------
    # Create Folder if Dont Exist
    #------------------------------------------------
    $util->createFolder(null,$email);
    $util->createFolder("media",$email);
    $fileCreate=md5($email); 
    if ($type=="facebook"){
     
     $photoURL="https://graph.facebook.com/".$FBID."/picture?type=large";
     $saveTo=$util->userFolder($email)."/media/".$fileCreate.".jpg";
     copy($photoURL,$saveTo);     
    }else{
     $saveTo=$util->userFolder($email)."/media/".$fileCreate.".jpg";
     copy($_SESSION['twitter_profile_img'],$saveTo);      
    }
    #------------------------------------------------
    $html->successMessage("Your account has been created successfully, please check your email to verify your account. &nbsp;<strong><a href='#' data-toggle='modal' data-target='#loginOptions' class='link-light-red'>Login Now</a></strong>");
    include_once"email.php";
    
    
   }else{
    $html->errorMessage("There was a problem creating your account. Please try again");  
   }
#---------------------------------------------------- 
 
}else{
 $html->errorMessage("Please make sure you have agreed to our Terms & Conditions");
 die();
}


?>