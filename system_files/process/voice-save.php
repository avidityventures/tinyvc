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

$brands_services=trim($_POST['brands_services']);
$problems_complaints=trim($_POST['problems_complaints']);
$url_expander=$_POST['url_expander'];
$foursquare_id=$_POST['foursquare_id'];
$locationLat=$_POST['locationLat'];
$locationLong=$_POST['locationLong'];
$mediatype=$_POST['mediatype'];
$mediaurl=$_POST['mediaurl'];
$feelings=null;
$mode=$_POST['mode'];
#-------------------------------------
# Check brands or service name
#-------------------------------------
if (strlen($brands_services) < 1){
echo json_encode(array('status' => 'error','message' =>'Name the brands or services you are voicing out'));
die();  
}
#-------------------------------------
# Check problems complaints
#-------------------------------------
if (strlen($problems_complaints) < 1){
echo json_encode(array('status' => 'error','message' =>'Your message of problems or complaints cannot be empty'));
die();  
}
#-------------------------------------
# Check for lat long
#-------------------------------------
if (strlen($locationLat) < 1){
echo json_encode(array('status' => 'error','message' =>'Please move the marker and plot on the map'));
die();  
}
#-------------------------------------
# Check for lat long
#-------------------------------------
if (strlen($locationLong) < 1){
echo json_encode(array('status' => 'error','message' =>'Please move the marker and plot on the map'));
die();  
}
#-------------------------------------
if (strlen($foursquare_id) >0){
$fsDetails="https://api.foursquare.com/v2/venues/".$foursquare_id."?client_id=Q4EYGAQKVBB3PE55L2KPDBOZVVCIYRPJ4BZXELADAQZW45LG&client_secret=X5JF2RIKSVGYAFPI4IWWPDZ4YNKH540TFEOBK35HYAV2U3NA&v=20141208";
$fsDetails_e=file_get_contents($fsDetails);
$rs=json_decode($fsDetails_e, true);
$categoryName=$rs['response']['venue']['categories'][0]['name'];
$icon_prefix=$rs['response']['venue']['categories'][0]['icon']['prefix'];
$icon_suffix=$rs['response']['venue']['categories'][0]['icon']['suffix'];
$categoryIcon=$icon_prefix."bg_64".$icon_suffix;

$countryCode=$rs['response']['venue']['location']['cc'];
$locationLat=$rs['response']['venue']['location']['lat'];
$locationLong=$rs['response']['venue']['location']['lng'];
$address=$rs['response']['venue']['location']['formattedAddress'][0].", ".$rs['response']['venue']['location']['formattedAddress'][1];
}
$publish=2;
#-------------------------------------------------------------
# If New Mode
#-------------------------------------------------------------
if ($mode=="new"){
$uniqueID=$util->dataUnique();
#--------------------------------------
# Upload Media
#--------------------------------------
$util->createFolder("media",$profile_details->email);
$folder=$util->userFolder($profile_details->email)."/media/";
$filenames=$util->filenames();
$util->mediaUpload($folder,$filenames);

 foreach ($filenames as $aa=>$bb){
  if (strlen($bb) > 0){
   $db->db_query("insert into voices_attachment (id,image) values ('".$uniqueID."','".$bb."')");
  }
 }
#--------------------------------------
# If found URL Attach photo link
#--------------------------------------
if (strlen($mediaurl) > 2 && $mediatype=="image"){
 $photoURL=$mediaurl;
 
 $extension = pathinfo($photoURL, PATHINFO_EXTENSION);
 $new= md5(uniqid($filename)).'.'.$extension;
 $folderPath=SERVER_URL."system_files/users_data/".$profile_details->folder."/media/".$new."";
 $mediaurl=$folderPath;
 
 $saveTo=$util->userFolder($profile_details->email)."/media/".$new."";
 copy($photoURL,$saveTo);
 $insertMediaUrl=$db->db_query("insert into voices_attachment (id,image,type) values ('".$uniqueID."','".$new."','URL')"); 
}
#--------------------------------------
$insert="insert into voices (id,feelings,brands_services,problems_complaints,url_expander,url_media,url_type,foursquare_id,countrycode,address,locationLat,locationLong,category,category_icon,publish,uniqueidentifier) values
                            ('".$profile_details->id."','".escape_string($feelings)."','".escape_string($brands_services)."','".escape_string($problems_complaints)."','".$url_expander."','".$mediaurl."','".$mediatype."','".$foursquare_id."',
                            '".$countryCode."','".$address."','".$locationLat."','".$locationLong."','".escape_string($categoryName)."','".escape_string($categoryIcon)."','".$publish."','".$uniqueID."')";
$db->db_query($insert);
#--------------------------------------------------------------------------
echo json_encode(array('status' => 'success','action' =>$uniqueID));
die();
#--------------------------------------------------------------------------
}# End if edit mode
#-------------------------------------------------------------
# If Edit Mode
#-------------------------------------------------------------
elseif ($mode=="edit"){

if (isset($_POST['removePhotos'])){
 $folder=$util->userFolder($profile_details->email)."/media/";
 $getAttachments=$db->db_query("select image from voices_attachment where id='".$_POST['uniqueId']."'");
 while($getAttachments_e=$db->db_fetch_array($getAttachments)){
 $fileLocation=$folder."".$getAttachments_e->image;
  if(file_exists($fileLocation)){
     unlink($fileLocation);
     $delete=$db->db_query("delete from voices_attachment where id='".$_POST['uniqueId']."'");
  } #End if
 } #End While
}else{
#--------------------------------------
# Upload Media
#--------------------------------------
$util->createFolder("media",$profile_details->email);
$folder=$util->userFolder($profile_details->email)."/media/";
$filenames=$util->filenames();
$util->mediaUpload($folder,$filenames); 

  if(isset($filenames)){
   foreach ($filenames as $aa=>$bb){
    if (strlen($bb) > 0){
     $db->db_query("insert into voices_attachment (id,image) values ('".$_POST['uniqueId']."','".$bb."')");
    } #End if strlen
   } #End foreach
  } #End if filename
 } #Else if Re-Upload Photos
 
 
#--------------------------------------
# If found URL Attach photo link
#--------------------------------------
if (strlen($mediaurl) > 2 && $mediatype=="image"){
 $folder=$util->userFolder($profile_details->email)."/media/";
 
 $getAttachments=$db->db_query("select image from voices_attachment where id='".$_POST['uniqueId']."' and type='URL'");
 $getAttachments_e=$db->db_fetch_array($getAttachments);
 
  $fileLocation=$folder."".$getAttachments_e->image;
  if(file_exists($fileLocation)){
     unlink($fileLocation);
     $delete=$db->db_query("delete from voices_attachment where id='".$_POST['uniqueId']."' and type='URL'");
  } #End if
 
 
 $photoURL=$mediaurl;
 
 $extension = pathinfo($photoURL, PATHINFO_EXTENSION);
 $new= md5(uniqid($filename)).'.'.$extension;
 $folderPath=SERVER_URL."system_files/users_data/".$profile_details->folder."/media/".$new."";
 $mediaurl=$folderPath;
 
 $saveTo=$util->userFolder($profile_details->email)."/media/".$new."";
 copy($photoURL,$saveTo);
 $insertMediaUrl=$db->db_query("insert into voices_attachment (id,image,type) values ('".$_POST['uniqueId']."','".$new."','URL')"); 
}
#-------------------------------------- 
 
 
$update="update voices set feelings='".escape_string($feelings)."',
                           brands_services='".escape_string($brands_services)."',
                           problems_complaints='".escape_string($problems_complaints)."',
                           url_expander='".$url_expander."',
                           url_media='".$mediaurl."',
                           url_type='".$mediatype."',
                           foursquare_id='".$foursquare_id."',
                           countrycode='".$countryCode."',
                           address='".$address."',
                           locationLat='".$locationLat."',
                           locationLong='".$locationLong."',
                           category='".escape_string($categoryName)."',
                           category_icon='".escape_string($categoryIcon)."',
                           publish='".$publish."'
                           where uniqueidentifier='".escape_string($_POST['uniqueId'])."' and id='".$profile_details->id."'";
$db->db_query($update);


 
#--------------------------------------------------------------------------
echo json_encode(array('status' => 'success','action' =>$_POST['uniqueId']));
die();
#--------------------------------------------------------------------------
} #End if edit mode
?>