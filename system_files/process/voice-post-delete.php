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

$delete=$db->db_query("delete from voices where uniqueidentifier='".escape_string($_POST['id'])."' and id='".$profile_details->id."'");
$delete=$db->db_query("delete from voters where post_id='".escape_string($_POST['id'])."'");
$delete=$db->db_query("delete from voters_responses where post_id='".escape_string($_POST['id'])."'");
$delete=$db->db_query("delete from voices_reportflag where post_id='".escape_string($_POST['id'])."'");

$folder=$util->userFolder($profile_details->email)."/media/";
 $getAttachments=$db->db_query("select image from voices_attachment where id='".$_POST['id']."'");
 while($getAttachments_e=$db->db_fetch_array($getAttachments)){
 $fileLocation=$folder."".$getAttachments_e->image;
  if(file_exists($fileLocation)){
     unlink($fileLocation);
     $delete=$db->db_query("delete from voices_attachment where id='".$_POST['id']."'");
  } #End if
 } #End While

 if ($delete==1){
  echo json_encode(array('status_del' => 1));
 }else{
  echo json_encode(array('status_del' => 0));   
 }
?>