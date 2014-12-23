<?php
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    </head>
<?php


$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
$tags="dinner";
$tweetURL="https://api.twitter.com/1.1/search/tweets.json";
$parameters="?q=%23".$tags."&result_type=recent&count=100";

#$fullParam="".$tweetURL."".$parameters."";
$fullParam="https://api.twitter.com/1.1/search/tweets.json?max_id=469164660000698371&q=%23dinner&count=100&include_entities=1&result_type=recent";

$results = $connection->get($fullParam);

foreach ($results as $tweets=>$tweetDetails){
 foreach ($tweetDetails as $tweetIndex=>$tweetValues){
   foreach ($tweetValues->entities->media  as $mediaArray=>$mediaValues){
    if (isset($mediaValues->media_url)){
     $tweetMedia[]="<img src='".$mediaValues->media_url."' width='80'>";
     $tweetUsername[]=$tweetValues->user->screen_name;
     $tweetText[]=$tweetValues->text;
     
    }
   }
  }
  
}

echo $nextURL=$tweetURL."".$results->search_metadata->next_results;

echo "<pre>";
print_r($tweetMedia);
echo "<hr>";
print_r($tweetUsername);
echo "<hr>";
print_r($tweetText);
echo "<hr>";
print_r($results);


#include('html.inc');
?>
</html>