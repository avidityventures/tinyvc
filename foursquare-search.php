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

$version="20141130";
$nearLatLong=$_REQUEST['near'];
$query=$_REQUEST['query'];

$str="https://api.foursquare.com/v2/venues/search?near=".$nearLatLong."&query=".$query."&v=".$version."&client_id=".FOURSQUARE_CLIENT."&client_secret=".FOURSQUARE_CLIENT_SECRET."";
//$str="https://api.foursquare.com/v2/venues/search?near=2.9491696,101.77617389999999&query=nike&v=20141130&client_id=Q4EYGAQKVBB3PE55L2KPDBOZVVCIYRPJ4BZXELADAQZW45LG&client_secret=X5JF2RIKSVGYAFPI4IWWPDZ4YNKH540TFEOBK35HYAV2U3NA";
echo $json=file_get_contents($str);
?>