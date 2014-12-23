<?php
# Global Configuration Settings                                  
#*===============================================================
#   Module     : PHP Config file                                 
#   Programmer : Mohd Izzairi Yamin                          
#   Date       : 11/27/2007 12:11PM                              
#   Modified   : 18/09/2009 04:07AM                              
#================================================================
error_reporting(0);
#-------------------------------------- 
# Database Configuration
#--------------------------------------
define('DB_TYPE_SET','mysql');
define('DB_LOC_SET','127.0.0.1'); 
define('DB_PORT_SET','36000');
define('DB_NAME_SET','tinyvoices');
define('DB_USER_SET','root'); 
define('DB_PASS_SET','root');
#--------------------------------------
# General Configuration
#--------------------------------------
define('SESSION_SET','FWW_TINYVOICES_SET');

$hostType="localhost";
$tempVoiceFeeds="http://www.wikiabout.me/xyiry";

define('PUBLIC_URL','http://'.$hostType.'/tinyvc/');
define('SERVER_URL','http://'.$hostType.'/tinyvc/');

define('BASE_UPLOADER_RAW','/usr/local/zend/apache2/htdocs/tinyvc/system_files/users_data/');
define('BASE_UPLOADER','/tinyvc/system_files/users_data/');
define('GOOGLE_MAP_KEY','AIzaSyDP29DCFJtukyL6Bt5Xn8GEguHHyZGO23U');
#--------------------------------------
# ReCaptcha Settings
#--------------------------------------
define('GOOGLE_CAPTCHA','6Lf-u_4SAAAAALTpmZQpMHb8aFQA_KjCltVmfdo3');
define('GOOGLE_CAPTCHA_SECRET','6Lf-u_4SAAAAAAZ8pQBvLdlEJG_9O3RvaDw5OAJk');
#--------------------------------------
# Foursquare Settings
#--------------------------------------
define('FOURSQUARE_CLIENT','Q4EYGAQKVBB3PE55L2KPDBOZVVCIYRPJ4BZXELADAQZW45LG');
define('FOURSQUARE_CLIENT_SECRET','X5JF2RIKSVGYAFPI4IWWPDZ4YNKH540TFEOBK35HYAV2U3NA');
#--------------------------------------
# Twitter Settings
#--------------------------------------
define('CONSUMER_KEY', 'S7HC0ErUhoIlajlsA7Doqa9mA');
define('CONSUMER_SECRET', 'lsEP8p2lAG2L9XQbWPSpOrRpaI2Dx0TxFbToQfe4fhrJa9xqGj');
define('OAUTH_CALLBACK', 'http://'.$hostType.'/tinyvc/twitt-callback/');
#--------------------------------------
# MapBox Settings
#--------------------------------------
define('MAPBOX_ACCESS_TOKEN', 'pk.eyJ1IjoieHlpcnkiLCJhIjoiUHNzaFZScyJ9.FpHOGvlPKozhyclcGpkMnw');
define('MAPBOX_ID', 'xyiry.57ab2920');
#--------------------------------------
date_default_timezone_set("ASIA/KUALA_LUMPUR");
#================================================================
?>