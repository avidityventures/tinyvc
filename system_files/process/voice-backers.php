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
$type=$_REQUEST['tp'];

$delete=$db->db_query("delete from voters where id='".$profile_details->id."' and post_id='".escape_string($_REQUEST['v'])."' ");
$insert=$db->db_query("insert into voters (id,post_id,type) values ('".$profile_details->id."','".escape_string($_REQUEST['v'])."','".$type."')");
$total=$db->db_query("select count(*) as TOTAL from voters where type='backers' and post_id='".escape_string($_REQUEST['v'])."'");
$total_e=$db->db_fetch_array($total);

$total2=$db->db_query("select count(*) as TOTAL from voters where type='deniers' and post_id='".escape_string($_REQUEST['v'])."'");
$total2_e=$db->db_fetch_array($total2);

echo json_encode(array('value_backers' => $total_e->TOTAL, 'value_deniers' => $total2_e->TOTAL,));
?>