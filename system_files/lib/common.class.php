<?
#---------------------------------------------------
# FWW Framework
# Programmer : Mohd Izzairi Yamin
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
class util{
#---------------------------------------------------------------------------------------------------- 
# Function to redirect page
#---------------------------------------------------------------------------------------------------- 
public function js_redirect ($url=null) {
 echo "<script language=\"JavaScript\"><!--\n";
 echo "location.href = \"$url\";\n";
 echo " //--></script>\n";
}
#---------------------------------------------------------------------------------------------------- 
# Function to validate email
#---------------------------------------------------------------------------------------------------- 
public function emailvalidation($email=null){
 return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}
#---------------------------------------------------------------------------------------------------- 
# Function to show FB Photo
#---------------------------------------------------------------------------------------------------- 
public function fbPhoto($fbid,$type="large"){
 
 return "".SERVER_URL."system_files/modules/resizer/imgresize?src=https://graph.facebook.com/".$fbid."/picture?type=".$type."&w=250&h=250&zc=1&a=t";
}
#---------------------------------------------------------------------------------------------------- 
# Function to show Profile Photo
#---------------------------------------------------------------------------------------------------- 
public function profilePhoto(){
 $profile_details=$this->setSession();
 $userFolder=$profile_details->folder;
 $folder=SERVER_URL."system_files/users_data/".$userFolder."/media/".$userFolder.".jpg";
 
 return "".SERVER_URL."system_files/modules/resizer/imgresize?src=".$folder."&w=250&h=250&zc=1&a=t";
}
#---------------------------------------------------------------------------------------------------- 
# Function to show Public Profile Photo
#---------------------------------------------------------------------------------------------------- 
public function publicPhoto($folderUser){
 $folder=SERVER_URL."system_files/users_data/".$folderUser."/media/".$folderUser.".jpg";
 return "".SERVER_URL."system_files/modules/resizer/imgresize?src=".$folder."&w=250&h=250&zc=1&a=t";
}

#---------------------------------------------------------------------------------------------------- 
# Function to resize image
#---------------------------------------------------------------------------------------------------- 
public function resize($url,$width=null,$height=null){
 
 if ($height!=null){
 $heightSet="&h=".$height.""; 
 }
 return "".SERVER_URL."system_files/modules/resizer/imgresize?src=".$url."&w=".$width."".$heightSet."&zc=1&a=t";
}
#---------------------------------------------------------------------------------------------------- 
# Function to show FB Photo
#---------------------------------------------------------------------------------------------------- 
public function grab_image($url,$email){
$saveto=$this->userFolder($email)."/media/";
 
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}
#---------------------------------------------------------------------------------------------------- 
# Function to create folder if empty
#---------------------------------------------------------------------------------------------------- 
function createFolder($newFolder,$email){
$newFolder=$this->userFolder($email)."/".$newFolder; 
   if (!file_exists($newFolder)) {
     mkdir($newFolder, 0777, true);
   return true;
   }else{
   return false; 
   }
}
#---------------------------------------------------------------------------------------------------- 
# Function to set users base folder
#---------------------------------------------------------------------------------------------------- 
public function userFolder($email){
 $folder=md5($email);  
 $users_folder=BASE_UPLOADER_RAW."".$folder;
 return $users_folder;
}
#---------------------------------------------------------------------------------------------------- 
# Function to set users public folder
#---------------------------------------------------------------------------------------------------- 
public function publicFolder($email){
 $folder=md5($email);  
 $users_folder=PUBLIC_URL."system_files/users_data/".$folder."/media/";
 return $users_folder;
}
#---------------------------------------------------------------------------------------------------- 
# Function to generate file name for uploading files
#----------------------------------------------------------------------------------------------------
public function filenames(){
for($i=0; $i<count($_FILES['mediaUpload']['name']); $i++) {
  $tmpFilePath = $_FILES['mediaUpload']['tmp_name'][$i];

  if ($tmpFilePath != ""){
  
   $filename  = basename($_FILES['mediaUpload']['name'][$i]);
   $extension = pathinfo($filename, PATHINFO_EXTENSION);
   $new[]= md5(uniqid($filename)).'.'.$extension;
  

  }
 } # End Foreach

  return $new;
}
#---------------------------------------------------------------------------------------------------- 
# Function to upload files to user folder
#----------------------------------------------------------------------------------------------------
public function mediaUpload($users_folder,$filename){
for($i=0; $i<count($_FILES['mediaUpload']['name']); $i++) {
  $tmpFilePath = $_FILES['mediaUpload']['tmp_name'][$i];

  if ($tmpFilePath != ""){
   /*
   $filename  = basename($_FILES['mediaUpload']['name'][$i]);
   $extension = pathinfo($filename, PATHINFO_EXTENSION);
   $new       = md5(uniqid($filename)).'.'.$extension;
   */
    $newFilePath = $users_folder . $filename[$i];

    if(move_uploaded_file($tmpFilePath, $newFilePath)) {
    
    }
  }
 } # End Foreach 
}
#---------------------------------------------------------------------------------------------------- 
# Function to remove folder including contents
#----------------------------------------------------------------------------------------------------
function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
 }
#---------------------------------------------------------------------------------------------------- 
# Function to permanently remove users data and files
#----------------------------------------------------------------------------------------------------
public function deleteAccounts(){
  global $db;
  $profile_details=$this->setSession();
  
  $db->db_query("delete from notifications_settings where id='".$profile_details->id."'");
  $db->db_query("delete from social_accounts where id='".$profile_details->fb_id."' and type='facebook'");
  $db->db_query("delete from social_accounts where id='".$profile_details->twit_id."' and type='twitter'");
  $db->db_query("delete from voices where id='".$profile_details->id."'");
  
  $users_folder=$this->userFolder($profile_details->email);
  $this->rrmdir($users_folder);
  $db->db_query("delete from members where id='".$profile_details->id."'");
 } 
#---------------------------------------------------------------------------------------------------- 
# Function to generate data unique ID
#---------------------------------------------------------------------------------------------------- 
public function dataUnique($type=null){
 $rs=$this->setSession();
 return $uniqueID=md5(uniqid($rs->email, true));
}
#---------------------------------------------------------------------------------------------------- 
# Function to check users registration
#---------------------------------------------------------------------------------------------------- 
public function checkUsers($type=null){
global $db,$util,$profile;

if ($type=="facebook"){
 $profileID=trim($profile['id']);
 $condition="fb_id='".escape_string($profileID)."'";
 $loginType="facebook";
}elseif ($type=="twitter"){
 $profileID=trim($_SESSION['access_token']['user_id']);
 $condition="twit_id='".escape_string($profileID)."'"; 
 $loginType="twitter";
}

$count=$db->db_query("select count(*)as TOTAL from members where ".$condition."");
$count_e=$db->db_fetch_array($count);
$TOTAL=$count_e->TOTAL;

if ($TOTAL < 1){
 
 #-------------------------------
 # Logged in with facebook
 #-------------------------------
 if ($type=="facebook"){
  $code=$_REQUEST['code'];
  $state=$_REQUEST['state'];
  if ($code!="" && $state!=""){
   $util->js_redirect("".SERVER_URL."register?code=".$code."&state=".$state."&tp=facebook");
  die(); 
 }
 #-------------------------------
 # Logged in with twitter
 #-------------------------------
 }elseif ($type=="twitter"){
 
 if (isset($_SESSION['access_token'])){
  $util->js_redirect("".SERVER_URL."register?tp=twitter");
  die(); 
 }
 
 
 }

}elseif($TOTAL==1){
 $_SESSION['logintype']=$loginType;
 $_SESSION['user']=$profileID;
 $_SESSION['authorized']=1;
 return true;
}
 
}
#---------------------------------------------------------------------------------------------------- 
# Function check script post referer
#----------------------------------------------------------------------------------------------------
public function isValidReferer(){
global $util; 
 $valid_referer = strpos("".$_SERVER['HTTP_REFERER']."",SERVER_URL);
 if ($valid_referer === false) {
 $util->js_redirect(SERVER_URL);
 die();	
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function get profile data
#----------------------------------------------------------------------------------------------------
public function setSession(){
global $db,$util;

 if (isset($_SESSION['authorized'])){
   if ($_SESSION['logintype']=="facebook"){
    $condition="where fb_id='".escape_string($_SESSION['user'])."'"; 
   }elseif ($_SESSION['logintype']=="twitter"){
    $condition="where twit_id='".escape_string($_SESSION['user'])."'"; 
   }
   
   $user_value=$db->db_query("select * from members ".$condition." LIMIT 1");
   $users=$db->db_fetch_array($user_value);
   
   return $users;
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function get social data
#----------------------------------------------------------------------------------------------------
public function socialData($id){
global $db,$util;
 
 if (isset($_SESSION['authorized'])){

  $user_value=$db->db_query("select * from social_accounts where id='".escape_string($id)."' LIMIT 1");
  $users=$db->db_fetch_array($user_value);
  
  return $users;
 }

}
#---------------------------------------------------------------------------------------------------- 
# Function get voices
#----------------------------------------------------------------------------------------------------
public function getVoices($value){
global $db;

$rs=$db->db_query("select * from voices where uniqueidentifier='".escape_string($value)."'");
$rs_e=$db->db_fetch_array($rs);
 
 if ($rs_e){
  return $rs_e;
 }else{
  return false; 
 }
 
}
#---------------------------------------------------------------------------------------------------- 
# Function check if authorized
#----------------------------------------------------------------------------------------------------
public function authAuthorized(){
global $db;
 if (!isset($_SESSION['authorized'])){
  $this->js_redirect(SERVER_URL."logout");
  die();
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function check full country name
#----------------------------------------------------------------------------------------------------
public function getCountry($code=null){
 global $db;
 $check=$db->db_query("select country from countries where ccode='".$code."' LIMIT 1");
 $check_e=$db->db_fetch_array($check);
 return $check_e->country;
}
#---------------------------------------------------------------------------------------------------- 
# Function to retrieve data from notification settings
#----------------------------------------------------------------------------------------------------
public function notificationsSettings($field){
global $db;
$profile=$this->setSession();
$q1=$db->db_query("select value from notifications_settings where id='".$profile->id."' and field='".$field."' LIMIT 1");
$q1_e=$db->db_fetch_array($q1);
return $q1_e->value;
}
#---------------------------------------------------------------------------------------------------- 
# Function to get hashtags from postings
#----------------------------------------------------------------------------------------------------
public function get_hashtags($tweet)
{
    $matches = array();
    preg_match_all('/#([^\s]+)/', $tweet, $matches);
    return $matches[0];
   
}
#---------------------------------------------------------------------------------------------------- 
# Function to set values if exist
#----------------------------------------------------------------------------------------------------
public function setValue($value=null,$type=1){
$value=trim($value);
if (strlen($value) > 0){
  if ($type==1){
  echo $value;  
  }else{
  return $value;  
  } 
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function to check file attachment
#----------------------------------------------------------------------------------------------------
public function checkAttachment($uniqueID){
global $db;
 $values=array();
 $rs=$db->db_query("select image from voices_attachment where id='".$uniqueID."' and type='attach'");

 while($rs_e=$db->db_fetch_array($rs)){
 $values[]=$rs_e->image;  
 }

 if (count($values) < 1){
  return false;  
  }else{ 
  return $values;
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function to users own vote
#----------------------------------------------------------------------------------------------------
public function myVotes($postid){
global $db,$profile_details;

 if (isset($profile_details->id)){
 $total=$db->db_query("select type from voters where post_id='".escape_string($postid)."' and id='".$profile_details->id."' LIMIT 1");
 $total_e=$db->db_fetch_array($total);
 return $total_e->type;
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function to count total votes
#----------------------------------------------------------------------------------------------------
public function totalVoters($postid,$type){
global $db;

$total=$db->db_query("select count(*) as TOTAL from voters where type='".$type."' and post_id='".escape_string($postid)."'");
$total_e=$db->db_fetch_array($total);
return $total_e->TOTAL;
}
#---------------------------------------------------------------------------------------------------- 
# Function to display voter types
#----------------------------------------------------------------------------------------------------
public function voterTypes($type,$total){
if ($type=="backers"){
  if  ($total < 2){
   $str="<i class='fa fa-plus'></i> ".$total; 
  }else{
   $str="<i class='fa fa-plus'></i> ".$total; 
  }
 }
 
 if ($type=="deniers"){
  if  ($total < 2){
   $str="<i class='fa fa-minus'></i> ".$total; 
  }else{
   $str="<i class='fa fa-minus'></i> ".$total; 
  }
 }
 
 return $str;
}
#---------------------------------------------------------------------------------------------------- 
# Function to show input message box for voice view
#----------------------------------------------------------------------------------------------------
public function showResponseInput($identifyID){
global $profile_details,$TOTAL_FB,$TOTAL_TWIT;


if (strlen($profile_details->twit_id) > 2){
 $d_twit=$this->socialData($profile_details->twit_id);
}

if (strlen($profile_details->fb_id) > 2){
 $d_fb=$this->socialData($profile_details->fb_id);
}

if (strlen($profile_details->city) > 1){
 $cityGet=$profile_details->city.", ";
}


  echo '
 
   <div class="voice-confirm response-box text-right">
    <h3 class="no-margin-padding"><img src="'.$this->profilePhoto().'" class="img-circle avatar-feeds"> MY VIEWS ON THIS</h3>
    '.$profile_details->fullname.' . '.$cityGet.''.$this->getCountry($profile_details->country).'
    <hr>
    <textarea name="voice_response" id="voice_response" class="form-control voicetox input-lg textarea-box" style="height:140px;" placeholder="My response to this....."></textarea>';
         if ($TOTAL_FB==1){
         if ($d_fb->autopost==1) { $status_fb="checked"; } else{ $status_fb=null; }
         echo'
         <div class="text-left fb-social-share">
          <i class="fa fa-facebook-square profile-social-fb-selected pull-left" style="font-size:30px; margin-top:4px; margin-right:20px;"></i> <input class="switch-state" name="facebook_autopost" type="checkbox" value="1"  '.$status_fb.'/>&nbsp;&nbsp; <span class="visible-lg-inline visible-md-inline">Share on your Facebook</span>
         </div>';
         } 
         if ($TOTAL_TWIT==1){
          if ($d_twit->autopost==1) { $status_twit="checked"; } else{ $status_twit=null; } 
         
         echo '
         <div class="text-left twit-social-share">
          <i class="fa fa-twitter profile-social-twit-selected pull-left" style="font-size:29px; margin-top:4px; margin-right:20px;"></i> <input class="switch-state" name="twitter_autopost" type="checkbox" value="1" '.$status_twit.'/>&nbsp;&nbsp; <span class="visible-lg-inline visible-md-inline">Share on your Twitter</span>
         </div>';
         }
         
    echo '
    <div id="resultsThrown"></div>
    <input type="hidden" name="vid" value="'.$identifyID.'">
    <button type="submit" class="btn btn-primary  tinyVoicesBtnRegister"><div class="socialActions"> Post my views </div></button>
   </div><!--voice-confirm-->
  ';
}
#---------------------------------------------------------------------------------------------------- 
# Function to check owner post
#----------------------------------------------------------------------------------------------------
public function checkPostOwner($value){
global $db,$util,$profile_details;
 $checkOwner=$db->db_query("select count(*)as OWNER from voices where id='".$profile_details->id."' and uniqueidentifier='".escape_string($value)."'");
 $checkOwner_e=$db->db_fetch_array($checkOwner);
 $isOwner=$checkOwner_e->OWNER;
 if ($isOwner!=1){
   $util->js_redirect(SERVER_URL);
   die();
 }
}
#---------------------------------------------------------------------------------------------------- 
# Function to show time in short description format
#----------------------------------------------------------------------------------------------------
public function time_stamp($session_time,$display=null) 
{ 
  
$time_difference = time() - $session_time ; 
$seconds = $time_difference ; 
$minutes = round($time_difference / 60 );
$hours = round($time_difference / 3600 ); 
$days = round($time_difference / 86400 ); 
$weeks = round($time_difference / 604800 ); 
$months = round($time_difference / 2419200 ); 
$years = round($time_difference / 29030400 ); 

if($seconds <= 60)
{
$returns="few seconds ago"; 
}
else if($minutes <=60)
{
   if($minutes==1)
   {
     $returns="one minute ago"; 
    }
   else
   {
   $returns="$minutes minutes ago"; 
   }
}
else if($hours <=24)
{
   if($hours==1)
   {
   $returns="1 hour ago";
   }
  else
  {
  $returns="$hours hours ago";
  }
}
else if($days <=7)
{
  if($days==1)
   {
   $returns="1 day ago";
   }
  else
  {
  $returns="$days days ago";
  }


  
}
else if($weeks <=4)
{
  if($weeks==1)
   {
   $returns="one week ago";
   }
  else
  {
  $returns="$weeks weeks ago";
  }
 }
else if($months <=12)
{
   if($months==1)
   {
   $returns="one month ago";
   }
  else
  {
  $returns="$months months ago";
  }
 
   
}

else
{
if($years==1)
   {
   $returns="one year ago";
   }
  else
  {
  $returns="$years years ago";
  }


}
 
if ($display==null){
echo  $returns;	
}else{
return $returns;	
}

} 


function expandURL($url,$width="100%",$height="280"){
$returns = "";
if(!empty($url)){
if(eregi("youtu",$url) or eregi("youtube",$url)){
        if(eregi("v=",$url))
    $splits = explode("=",$url);
    else
    $splits = explode("be/",$url);

       if(!empty($splits[1])){
	preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
        $returns = '<iframe width="'.$width.'" height="'.$height.'" src="https://www.youtube.com/embed/'.$matches[0].'??wmode=opaque&allow_embed=1&theme=light&hd=1" frameborder="0"></iframe>';
        }
} else if(eregi("vimeo",$url)){
	
        $splits = explode("com/",$url);
        $returns = '<iframe src="https://player.vimeo.com/video/'.$splits[1].'?title=0&amp;byline=0&amp;portrait=0" width="'.$width.'" height="'.$height.'" frameborder="0"></iframe><div style="color:#5b5b5b; padding-bottom:10px;">&nbsp;</div>';
}
}
return $returns;
}

} #End Class
?>