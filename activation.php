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
error_reporting(127);
?>
<!DOCTYPE html>
<html lang="en">
   <head>
    <? $html->metaHeader(); ?>
    
    <!-- Bootstrap core CSS -->
    <link href="<?=SERVER_URL?>layout/css/main.css" rel="stylesheet"/>
    <link href="<?=SERVER_URL?>layout/css/activation-email.css" rel="stylesheet"/>
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
<?


 if (isset($_REQUEST['v'])){
 $activationID=trim($_REQUEST['v']);
 
 $check=$db->db_query("select count(*)as TOTAL from members where folder='".escape_string($activationID)."'  LIMIT 1");
 $check_rs=$db->db_fetch_array($check);
 
 $TOTAL=$check_rs->TOTAL;

 if ($TOTAL==1){    
 $updateActivation=$db->db_query("update members set activated='1' where folder='".escape_string($activationID)."'");  

  echo ' 
    <div class="container text-center">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="box">
                <div class="box-icon">
                    <span class="fa fa-4x fa-meh-o"></span>
                </div>
                <div class="info">
                    <h4 class="text-center">Your account has been verified</h4>
                    <p>Feeling unhappy about a service, felt that you are being short-changed by the company ? Want to see some changes and wake the people up ? <br><br>Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedbacks. This is a crowdfunded opinion community platform. </p>
                    <br><center><a href="'.SERVER_URL.'" class="btn btn-lg btn-danger">Start Now</a></center>
                </div>
            </div>
        </div>
    <div class="col-md-3"></div>';
    echo '</div>';  
    echo "<hr>";
    
    $html->footer();
 }else{
   $util->js_redirect(SERVER_URL);
   die();
 }
}else{
   $util->js_redirect(SERVER_URL);
   die();  
}
?>
  
  </body>
<script>
var SERVER_URL="<?=SERVER_URL?>";
</script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="<?=SERVER_URL?>layout/js/utilities.js"></script>
    <script src="<?=SERVER_URL?>layout/js/tinyvoices.js"></script>
</html>