<?php
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
define('ABSOULTE_PATH','/usr/local/zend/apache2/htdocs/tinyvc/system_files/lib/');
include_once"".ABSOULTE_PATH."conf.php";
session_name(SESSION_SET);
session_start();
include"".ABSOULTE_PATH."common.class.php"; 
include"".ABSOULTE_PATH."default.view.class.php";

class connection {
#-----------------------------------------------------
# Connect To MySQL 
#-----------------------------------------------------
public function db_conn(){
global $get_server_type,$get_db_user,$get_db_pass,$get_db_name,$mysqli;	

$mysqli = new mysqli(DB_LOC_SET,DB_USER_SET,DB_PASS_SET,DB_NAME_SET);      
}

public function db_query($sql) {
global $mysqli;    
$res_rs = $mysqli->query($sql);
return $res_rs;
}

public function db_fetch_array($res_rs) {
$arr=$res_rs->fetch_object(); 
return $arr;
}

public function db_fetch_rows($res_rs) {
$arr = mysql_num_rows($res_rs);
return $arr;
   }   

public function exec_array($value){
echo "<pre>";
print_r($value);
   }  
#------------------------------
} 
# end class
#------------------------------
function escape_string($value){
global $mysqli;
return mysqli_real_escape_string($mysqli,$value);    
}
?>