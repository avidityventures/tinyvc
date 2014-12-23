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
//$util->isValidReferer();
//$util->authAuthorized();


$link="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xaf1/v/t1.0-1/p200x200/10606529_10204243366584866_9113598510020472058_n.jpg?oh=04970166ca10da4b0773426edfcec23f&oe=54FF5BAE&__gda__=1426018613_b2ce366aefd75560f6a3e03189f6d228";


copy("https://graph.facebook.com/1248815425/picture?type=large", '/usr/local/zend/apache2/htdocs/tinyvc/system_files/users_data/a7f88964b08a384279b84b29e33e7977/media/111.png');



echo '<img src="/tmp/file11.jpeg">';

function GetImageFromUrl($link)

    {
echo $link;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_POST, 0);

    curl_setopt($ch,CURLOPT_URL,$link);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result=curl_exec($ch);

    curl_close($ch);

    return $result;

    }

    
    
   
    
    #fwrite($savefile, $sourcecode);
    #fclose($savefile);







/*
//$url=$_POST['url'];
$str="www.xyiry.com";

$url="https://api.embed.ly/1/extract?key=2c419230c289488fa9bf8c23f4a5faa0&url=".urlencode($str)."&maxwidth=500&format=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);

$rs=json_encode(curl_exec($ch));
print_r($rs);
*/
?>