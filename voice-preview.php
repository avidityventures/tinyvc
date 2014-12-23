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
$util->authAuthorized();

$profile_details=$util->setSession();
$util->checkPostOwner($_REQUEST['v']);

if(isset($_REQUEST['v'])){
 $voice=$util->getVoices($_REQUEST['v']);
}else{
 $voice=false;
}
 if ($voice==false){
  $util->js_redirect(SERVER_URL);
  die();
 }
$id=$voice->uniqueidentifier;
$imgAttach=$util->checkAttachment($id);
$folder=$util->publicFolder($profile_details->email);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <? $html->metaHeader(); ?>
    
    <!-- Bootstrap core CSS -->
    <link href="<?=SERVER_URL?>layout/css/main.css" rel="stylesheet"/>
    <link href='<?=SERVER_URL?>layout/css/switch.css' rel='stylesheet'/>  
    <link href="<?=SERVER_URL?>layout/css/tinyvoices.css" rel="stylesheet"/>
    <link href="<?=SERVER_URL?>layout/css/tinyvoices-preview.css" rel="stylesheet"/>
    <link href='<?=SERVER_URL?>layout/css/font-awesome.css' rel='stylesheet'/>

    <!-- Custom styles for this template -->
    <link href="<?=SERVER_URL?>layout/css/navbar-static-top.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  
<body>
  <? $html->header(); ?>
  
<div class="container" style="margin-top:50px;" >
<? $html->progressBarVoice(1,1,0,0); ?>
 <div class="row">
 <br>
 <hr>
 <br>
  <div class="col-lg-1 col-md-1 ">
  
  </div>

   <div class="col-lg-6 col-md-6 col-sm-12 voice-preview">
    
    <?
    $noBottomHR=1;
    $html->voicePosts($_REQUEST['v']);
    ?>
  </div><!--col-md-6-->
  
  <div class="col-lg-5 col-md-5 col-sm-12 text-right" class="pull-right">
   <div class="voice-confirm "  style="">
    <hr>
    <h1 style="font-weight:200;">Publish now and start <br>receiving backers for<br> your post. </h1><br>
    
    <a href="<?=SERVER_URL?>voice-publish?v=<?=$id?>" class="btn btn-primary btn-lg tinyVoicesBtnRegister"> <div class="socialActions"> Ready to Publish</div> </a>
    
    <hr>
    I'm not done yet. Go back and <a href="<?=SERVER_URL?>voice?v=<?=$id?>">edit.</a>
   </div>
  </div><!--col-md-6-->
  

 </div><!--row-->

<? $html->search(); ?>
<? $html->footer(); ?>  
</div><!--container--> 

<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/switch.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
</body>
</html>