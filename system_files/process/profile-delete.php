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

$agreed_delete=$_POST['agreed_delete'];

if (isset($_POST['agreed_delete'])){
 if (isset($_SESSION['authorized'])){
$profile_details=$util->setSession();

$google_url="https://www.google.com/recaptcha/api/siteverify";
$secret=GOOGLE_CAPTCHA_SECRET;
$ip=$_SERVER['REMOTE_ADDR'];
$url=$google_url."?secret=".$secret."&response=".$_POST['g-recaptcha-response']."&remoteip=".$ip;
$res=file_get_contents($url);
$captcha_response= json_decode($res, true);

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
 #-------------------------------------
 $html->errorMessage("Preparing account deletion....");
 $util->deleteAccounts();
 $util->js_redirect(SERVER_URL."logout");
 die();
 
 }else{
 $util->js_redirect(SERVER_URL);
 die(); 
 }
}else{
$html->errorMessage("Please accept the terms of account deletion");  
die();   
}
?>