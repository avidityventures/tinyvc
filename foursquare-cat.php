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

$version="20141130";
$nearLatLong=$_REQUEST['near'];
$query=$_REQUEST['query'];

$str="https://api.foursquare.com/v2/venues/search?near=".$nearLatLong."&query=".$query."&v=".$version."&client_id=".FOURSQUARE_CLIENT."&client_secret=".FOURSQUARE_CLIENT_SECRET."";
$str="https://api.foursquare.com/v2/venues/categories?oauth_token=C0CCJCCN15GFZ5JVQNO4HGWYHGBS3GD4HJRUE04YDY3SIRNF&v=20141225";
//$str="https://api.foursquare.com/v2/venues/search?near=2.9491696,101.77617389999999&query=nike&v=20141130&client_id=Q4EYGAQKVBB3PE55L2KPDBOZVVCIYRPJ4BZXELADAQZW45LG&client_secret=X5JF2RIKSVGYAFPI4IWWPDZ4YNKH540TFEOBK35HYAV2U3NA";
$json=file_get_contents($str);
$json_clean=json_decode($json);

#echo "<pre>";
#print_r($json_clean->response->categories[0]);

$cat=$json_clean->response->categories;

foreach ($cat as $aa=>$bb){
 $mainCategory=$bb->name;
 $mainCategoryID=$bb->id;

 echo "<h1>".$mainCategory ." : ".$mainCategoryID."</h1>";
 echo "<hr>";
 $childCategories=$bb->categories;
 
 
 
 
 foreach ($childCategories as $cc=>$dd){
 
 
 
 $childCategory=$dd->name;
 $childCategoryID=$dd->id;
 
 echo htmlentities($childCategory) ." : ".$childCategoryID."<br>";
 
#$insertMain=$db->db_query("insert into categories_child (category,category_id,parent_category_id) values ('".htmlentities($childCategory)."','".$childCategoryID."','".$mainCategoryID."')"); 
 }

  
}


?>