<?
#---------------------------------------------------
# FWW Framework
# Programmer : Mohd Izzairi Yamin
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
class defaultsite{   
#----------------------------------------------------------------------------------------------------
# Desc: To set website meta header
#----------------------------------------------------------------------------------------------------
public function metaHeader(){
    echo '
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="It\'s about brands and services">
    <meta name="author" content="The TinyVoices App">
    <title>TinyVoices . A Crowdfunded Opinion Community</title>
    <link rel="shortcut icon" href="'.SERVER_URL.'layout/img/general/fav.ico" />
    <link rel="apple-touch-icon" href="'.SERVER_URL.'layout/img/general/fav.icns"/>
    ';
    
}
#----------------------------------------------------------------------------------------------------
# Desc: To get template directory
#----------------------------------------------------------------------------------------------------
public function header(){
global $html,$util,$loginUrl,$twitLoginUrl;

 echo '
  <!-- Static navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="'.SERVER_URL.'"><img src="'.SERVER_URL.'layout/img/logo/tinyvoices-logo2.png" class="logo"></a>
        </div>
        
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="#discover">#Discover</a></li>
            <li><a href="#analytics">#Analytics</a></li>
            <li><a href="#search" class="searchTop"><span class="fa fa-search" style="font-weight:900;"></span> Search</a></li>
          </ul>';

         
         if (isset($_SESSION['authorized'])){
         $rs=$util->setSession();
         $fullname=$rs->fullname;
         $folder=$rs->folder;
         $email=$rs->email;
         $fb_id=$rs->fb_id;
         $twit_id=$rs->twit_id;

         echo '          
         <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"> <img src="'.$util->profilePhoto().'" class="img-circle avatar-header"> '.$fullname.' <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="'.SERVER_URL.'profile">Profile</a></li>
                <li><a href="'.SERVER_URL.'my-voices">My Voices</a></li>
                <li class="divider"></li>
                <li><a href="'.SERVER_URL.'logout">Logout</a></li>
              </ul>
            </li>
          </ul>';
         }else{
         echo '          
         <ul class="nav navbar-nav navbar-right">
          <li><a href="#" data-toggle="modal" data-target="#loginOptions" role="button" aria-expanded="false"> <i class="fa fa-child"></i> Sign In </a></li>
         </ul>';
         }          
         echo '  
        </div><!--/.nav-collapse -->
      </div>
    </nav>';
 #-----------------------------------------------
 # Modal Header
 #-----------------------------------------------
 if (!isset($_SESSION['authorized'])){
     echo '
         <!-- Modal -->
         <div class="modal fade" id="loginOptions" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
           <div class="modal-dialog" style="margin-top:100px;">
             <div class="modal-content" style="margin:0;">
               <div class="modal-bodyx text-center">
               <div style="border-bottom:1px dotted #c2c2c2; padding:20px;" style="text-center"><center><img src="'.SERVER_URL.'layout/img/logo/tinyvoices-logo-white.png" class="img-responsive" width="80%"></center></div>
               <h2 class="white">Sign In Options</h2>
                <div class="mainSliderBtn">
                  <a href="'.$loginUrl.'" type="button" class="btn btn-primary btn-lg facebookBG"><div class="socialActions"><i class="fa fa-facebook"></i> Facebook </div></a>
                  <a href="'.$twitLoginUrl.'" type="button" class="btn btn-primary btn-lg twitterBG"><div class="socialActions"><i class="fa fa-twitter"></i> Twitter </div></a>  
                 <span style="color:#fff;">'.$html->footer(1).'</span>
                 </div>
               </div><br><br>
             </div>
           </div>
         </div>
         ';
 #----------------------------------------------- 
 }

}
#----------------------------------------------------------------------------------------------------
# Desc: To show left bar on main page
#----------------------------------------------------------------------------------------------------
public function leftBar(){
echo '
 <div class="col-md-3">
  <h3>Voice Out Now</h3> 
  <div class="mainpage-leftbar">
      Feeling unhappy about a service, felt that you are being short-changed by the company ? Want to see some changes and wake the people up ? <br><br>
      Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedbacks. This is a crowdfunded opinion community platform.
    </div> 
  <!--mainpage-leftbar-->
  <a href="'.SERVER_URL.'voice" type="button" class="btn btn-primary btn-lg visible-lg-block visible-md-block tinyVoicesBtnBG-leftBar hidden-phone"><div class="socialActions"><i class="fa fa-comment"></i> Start a Conversation </div></a>
  <div class="mainpage-leftbar-trending">
   <h4>Trending #</h4>
   <div class="mainpage-leftbar-trending-hashtags">
   <p>#cheated</p>
   <p>#feelingcheated</p>
   <p>#loyalcustomer</p>
   <p>#verydirty</p>
   <p>#alwaysnostock</p>
   <p>#veryrudesupport</p>
   </div>
  </div>
  </div><!--col-md-3-->
  <br> ';
}
#----------------------------------------------------------------------------------------------------
# Desc: To show right bar on main page
#----------------------------------------------------------------------------------------------------
public function rightBar(){
echo '  <div class="col-md-3">
    <div role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active text-center"><a href="#latest" aria-controls="latest" class="nav-tabs-link" role="tab" data-toggle="tab"><div class="tabright"><i class="fa fa-flash"></i></div> <div>LATEST</div></a></li>
      <li role="presentation" class="text-center"><a href="#current" aria-controls="current" role="tab" data-toggle="tab"><div class="tabright"><i class="fa fa-bomb"></i></div> <div>CURRENT</div></a></li>
      <li role="presentation" class="text-center"><a href="#explosive" aria-controls="explosive" role="tab" data-toggle="tab"><div class="tabright"><i class="fa fa-frown-o"></i></div> <div>EXPLODE</div></a></li>
    </ul>
  
    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="latest">
       <table class="table table-hover">
        <tr>
          <td>
            <span class="rightbar-trending-title">Starbucks Malaysia</span>
            <div>Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedback. This is crowdfunded opinion community platform</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">Honda SS2</span>
            <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">AEON SS2</span>
            <div>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo</div>
          </td>
        </tr>
       </table>
      </div><!--latest-->
      
      
      <div role="tabpanel" class="tab-pane" id="current">
       <table class="table table-hover">
        <tr>
          <td>
            <span class="rightbar-trending-title">Starbucks Malaysia - Current</span>
            <div>Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedback. This is crowdfunded opinion community platform</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">Honda SS2 - Current</span>
            <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">AEON SS2 - Current</span>
            <div>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo</div>
          </td>
        </tr>
       </table> 
      </div><!--current-->
      
      <div role="tabpanel" class="tab-pane" id="explosive">
       <table class="table table-hover">
        <tr>
          <td>
            <span class="rightbar-trending-title">Starbucks Malaysia - Explosive</span>
            <div>Voice it out here and be heard. Have a conversation with the brands you engaged with and get their feedback. This is crowdfunded opinion community platform</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">Honda SS2 - Explosive</span>
            <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</div>
          </td>
        </tr>
        
        <tr>
          <td>
            <span class="rightbar-trending-title">AEON SS2 - Explosive</span>
            <div>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo</div>
          </td>
        </tr>
       </table>         
      </div><!--explosive-->
      
    </div><!--tab-content-->
    </div><!--tab-panel--> 
  </div><!--col-md-3-->';
 
}
#----------------------------------------------------------------------------------------------------
# Desc: To set search modal
#----------------------------------------------------------------------------------------------------
public function search(){
echo '
<div id="search">
    <form id="searchform" action="'.SERVER_URL.'search">
     <input name="find" type="search"  placeholder="Search Keywords..." autocomplete="off"/>
     <button type="button" name="submit" class="btn btn-primary btn-lg  tinyVoicesBtnRegister" id="searchformsubmit">
         <div class="socialActions">Search Keywords</div>
     </button>  
    </form>
</div><!--search-->';
}
#----------------------------------------------------------------------------------------------------
# Desc: To show success message
#----------------------------------------------------------------------------------------------------
public function successMessage($message){
 echo '<div class="success-message">'.$message.'</div>';
}
#----------------------------------------------------------------------------------------------------
# Desc: To show error message
#----------------------------------------------------------------------------------------------------
public function errorMessage($message){
 echo '<div class="error-message">'.$message.'</div>';
}
#----------------------------------------------------------------------------------------------------
# Desc: To set progress bar level
#----------------------------------------------------------------------------------------------------
public function progressBar($level1,$level2,$level3,$level4){

if ($level1==1){
 $status1="complete"; 
}else{
 $status1="disabled"; 
}

if ($level2==1){
 $status2="complete"; 
}else{
 $status2="disabled"; 
}

if ($level3==1){
 $status3="complete"; 
}else{
 $status3="disabled"; 
}

if ($level4==1){
 $status4="complete"; 
}else{
 $status4="disabled"; 
}


echo '
<div class="container">
            <div class="row bs-wizard" style="border-bottom:0;">
                <div class="col-xs-3 bs-wizard-step '.$status1.'">
                  <div class="text-center bs-wizard-stepnum">Step 1</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Create Account</div>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$status2.'"><!-- complete -->
                  <div class="text-center bs-wizard-stepnum">Step 2</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Profile Setup</div>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$status3.'"><!-- complete -->
                  <div class="text-center bs-wizard-stepnum">Step 3</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Email Verification</div>
                </div>
                
                <div class="col-xs-3 bs-wizard-step '.$status4.'"><!-- complete -->
                  <div class="text-center bs-wizard-stepnum">Step 4</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Complete</div>
                </div>
            </div><!--row bs-wizard-->
</div><!--container-->';
 
}
#----------------------------------------------------------------------------------------------------
# Desc: To set progress bar level for creating new voice
#----------------------------------------------------------------------------------------------------
public function progressBarVoice($level1,$level2,$level3,$level4){

if ($level1==1){
 $status1="complete";
 $classActive1="class='voice-active'";
}else{
 $status1="disabled";
 $classActive1="";
}

if ($level2==1){
 $status2="complete";
 $classActive2="class='voice-active'";
}else{
 $status2="disabled";
 $classActive2="";

}

if ($level3==1){
 $status3="complete";
 $classActive3="class='voice-active'";
}else{
 $status3="disabled";
 $classActive3="";
}

if ($level4==1){
 $status4="complete";
 $classActive4="class='voice-active'";
}else{
 $status4="disabled";
 $classActive4="";
}


echo '
<div class="container">
            <div class="row bs-wizard" style="border-bottom:0;">
                <div class="col-xs-4 bs-wizard-step '.$status1.'">
                  <div class="text-center bs-wizard-stepnum">&nbsp;</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center"><h4 '.$classActive1.'>Voice Out</h4> <div class="visible-lg-block visible-md-block ">Follow the prompts to tell <br>a compelling story.</div></div>
                </div>
                
                <div class="col-xs-4 bs-wizard-step '.$status2.'"><!-- complete -->
                  <div class="text-center bs-wizard-stepnum">&nbsp;</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center"><h4 '.$classActive2.'>Preview & publish</h4><div class="visible-lg-block visible-md-block ">Preview your voice and <br>publish to collect signatures.</div></div>
                </div>
                
                <div class="col-xs-4 bs-wizard-step '.$status3.'"><!-- complete -->
                  <div class="text-center bs-wizard-stepnum">&nbsp;</div>
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center"><h4 '.$classActive3.'>Share</h4> <div class="visible-lg-block visible-md-block ">Recruit supporters from <br> Facebook, Twitter, and email.</div></div>
                </div>
                
               
            </div><!--row bs-wizard-->
</div><!--container-->';
 
}
#----------------------------------------------------------------------------------------------------
# Desc: To show voices record post
#----------------------------------------------------------------------------------------------------
public function voicePosts($uniqID=null,$mode=null){
global $db,$util,$noBottomHR,$category_code;

        if ($uniqID==null){
         #-----------------------------
         # Check if user is logged in
         #-----------------------------
          if ($mode=="edit"){
          $profile_details=$util->setSession(); 
          $voicesGet=$db->db_query("select * from voices where id='".$profile_details->id."' order by uid desc");
          }elseif ($mode=="categories"){
          
          $sql="select A.uid,A.id,A.feelings,A.brands_services,A.problems_complaints,A.url_expander,A.url_media,
                       A.url_type,A.foursquare_id,A.countrycode,A.address,A.locationLat,A.locationLong,A.category,A.category_icon,
                       A.publish,A.fb_post_id,A.twit_post_id,A.datetime,A.uniqueidentifier from
                voices A, categories B, categories_child C
                  where
                publish='1' and
                C.parent_category_id='".escape_string($category_code)."' and
                A.category=C.category_id and
                C.parent_category_id=B.category_id
                order by A.uid desc LIMIT 20";
                
          $voicesGet=$db->db_query($sql); 
          }elseif ($mode==null){
          $voicesGet=$db->db_query("select * from voices where publish='1' order by uid desc LIMIT 20"); 
          }
        }else{
         $voicesGet=$db->db_query("select * from voices where uniqueidentifier='".escape_string($uniqID)."'"); 
        }
        while($voice=$db->db_fetch_array($voicesGet)){
        //print_r();
        $getUsers=$db->db_query("select fullname,country,city,folder,email from members where id='".$voice->id."' LIMIT 1");
        $users=$db->db_fetch_array($getUsers);
        
        $imgAttach=$util->checkAttachment($voice->uniqueidentifier);
        $folder=$util->publicFolder($users->email);
        
        $totalBackers=$util->totalVoters($voice->uniqueidentifier,"backers");
        $totalDeniers=$util->totalVoters($voice->uniqueidentifier,"deniers");
        
        $checkMyVote=$util->myVotes($voice->uniqueidentifier);
        
        if ($checkMyVote=="backers"){
          $setStyleBackers="timeline-plus-selected"; 
        }else{
          $setStyleBackers="timeline-plus"; 
        }
        
        if ($checkMyVote=="deniers"){
          $setStyleDeniers="timeline-minus-selected"; 
        }else{
          $setStyleDeniers="timeline-minus"; 
        }
        
        if ($checkMyVote==false){
          $setLoginOptions='data-toggle="modal" data-target="#loginOptions"';
        }else{
          $setLoginOptions=null;
        }

        
                if ($imgAttach!=false){
                 foreach ($imgAttach as $aa=>$bb){
                 $img=$folder.''.$bb;
                 }
                }  
              
                if ($imgAttach==false){
                 
                 if ($voice->url_type=="video"){
                  $img=$voice->url_media;
                
                 }if ($voice->url_type=="image"){ 
                  $img=$voice->url_media;
                 
                 }else{
                  if ($uniqID==null){
                   $img="https://api.tiles.mapbox.com/v4/".MAPBOX_ID."/url-https%3A%2F%2Fmapbox.com%2Fguides%2Fimg%2Fpages%2Frocket.png(".$voice->locationLong.",".$voice->locationLat.")/".$voice->locationLong.",".$voice->locationLat.",15/640x640.png?access_token=".MAPBOX_ACCESS_TOKEN."";
                  }
                 }
                 
                }
       
       
       
       
       if ($uniqID==null){ 
       $class="col-lg-4 col-md-6 col-sm-12 col-xs-12";
       $class2="complaint-box-main voice-post-box-".trim($voice->uniqueidentifier)."";
       $class3="complaint-box-contents";
       $brandsTitle=$voice->brands_services;
       }else{
       $class="col-md-12";
       $class2="complaint-box-main-inside";
       $class3=null;
       $brandsTitle=$voice->brands_services;
       }

$getHash=$util->get_hashtags($voice->problems_complaints);
$contents=str_replace($getHash,"",$voice->problems_complaints);
$countryFullName=$util->getCountry($users->country);
 if (strlen($users->city) > 0){
  $cityFullName=$users->city.","; 
 }else{
  $cityFullName=null;  
 }

 $cleanURLSEO=$util->cleanURL($brandsTitle);
       
echo '
<div class="'.$class.'">
  
<div class="'.$class2.'">';
 if ($uniqID==null){
  echo'<div class="complaint-cover "  style="background: url(\''.$img.'\') center center; background-size: 100%;">
       
       <a href="'.SERVER_URL.'voice-view?q='.$cleanURLSEO.'&v='.$voice->uniqueidentifier.'">
       <div class="complaint-user">';
       if ($mode=="edit"){
        if ($voice->publish==1){
        echo '<i class="fa fa-check-circle-o pull-right" style="font-size:30px; color:#336E7B;  margin-top:-90px; margin-right:-10px;">
              <div style="font-size:15px; font-weight:bold; margin-left:10px; margin-top:8px;" class="pull-right">Published</div>
              </i>';
        }else{
        echo '<i class="fa fa-bullseye pull-right" style="font-size:30px; color:#336E7B;  margin-top:-90px; margin-right:-10px;">
              <div style="font-size:15px; font-weight:bold; margin-left:10px; margin-top:8px;" class="pull-right">Pending</div>
              </i>'; 
        }
       }
       
       echo'<img src="'.$util->publicPhoto($users->folder).'" class="img-circle complaint-avatar"/>
       <div class="complaint-user-name"><span class="title-name">'.$users->fullname.'</span><div class="title-country">'.$cityFullName.' '.$countryFullName.'</div></div>
       </div><!--complaint-user-->
       </a>
       </div>';
 }else{
  echo '<a href="'.SERVER_URL.'voice-view?v='.$voice->uniqueidentifier.'">
        <div class="complaint-user-inside">
        <img src="'.$util->publicPhoto($users->folder).'" class="img-circle complaint-avatar"/>
        <div class="complaint-user-name"><span class="title-name">'.$users->fullname.'</span><div class="title-country">'.$cityFullName.' '.$countryFullName.'</div></div>
        </div><!--complaint-user-->
        </a>'; 
 }

 
 
echo '
<div class="'.$class3.'">
<div class="complaint-cover-title"><a href="'.SERVER_URL.'voice-view?q='.$cleanURLSEO.'&v='.$voice->uniqueidentifier.'" class="complaint-title-link">'.$brandsTitle.'</a></div>';
if (strlen($voice->address) >1){
echo '<div class="complaint-cover-location">'.$voice->address.'&nbsp;</div>';
$setMinHeightContents=null;
}else{
$setMinHeightContents='style="min-height:95px"'; 
}

                if ($uniqID!=null){
                  if ($imgAttach!=false){
                  foreach ($imgAttach as $aa=>$bb){
                  echo '<div class="complaint-show-photos"><img src="'.$folder.''.$bb.'" class="img-responsive" width="100%"></div>';   
                  }
                  }  
                  
                  if (strlen($voice->url_media) > 0){
                  
                  }
                  
                  
                  //if ($imgAttach==false){
                   
                   if ($voice->url_type=="video"){
                   $arrayReplace=array('http:','https:');
                   echo '<div class="complaint-show-photos">';
                   echo $util->expandURL($voice->url_media);
                   echo '</div><div style="margin-bottom:10px;"></div>';
                   }if ($voice->url_type=="image"){
                   echo '<div class="complaint-show-photos" class="img-responsive" width="100%"><img src="'.$voice->url_media.'" class="img-responsive" width="100%"></div><div style="margin-bottom:10px;"></div>';
                   }
                   echo '<div style="margin-bottom:10px;"></div>';
                  //}
                }
  if ($uniqID!=null){                
   echo "<div class='timeline-time'>";
   echo $newDate = date("l jS, F Y", strtotime($voice->datetime));
   
   echo "</div>";
  }

echo '<div '.$setMinHeightContents.'>';
if ($uniqID==null){
 echo '<div class="complaint-description-short">';
 echo $contents_short=strip_tags($contents);
 echo '</div><!--complaint-description-->';
}else{
 echo '<div class="complaint-description-long">';
 echo $contents_short=$contents;
 echo '</div><!--complaint-description-->';
}


 $countHash=count($getHash);
 if ($uniqID!=null){
  if ($countHash > 0){
   echo '<div class="timeline-hashtags-complaints">';
   foreach ($getHash as $hashA=>$hashB){
    echo $hashB." ";  
   }
   echo '</div>';
  }
 }else{
  if ($mode=="edit"){
   if ($countHash < 1){
   
   echo "<div class='timeline-hashtags-complaints hide_div_".trim($voice->uniqueidentifier)."' style='color:#1F1F1F'>{ No #hashtags found }</div>"; 
   echo '<div id="delete_confirm_post_'.trim($voice->uniqueidentifier).'"></div>';
   }else{
   
   echo '<div class="timeline-hashtags-complaints hide_div_'.trim($voice->uniqueidentifier).'">';
   echo '<div id="delete_confirm_post_'.trim($voice->uniqueidentifier).'"></div>';
    foreach ($getHash as $hashA=>$hashB){
     echo $hashB." ";  
    }
    echo '</div>'; 
   }
  }else{
    echo '<div class="timeline-hashtags-complaints">';
    foreach ($getHash as $hashA=>$hashB){
     echo $hashB." ";  
    }
    echo '</div>';
  }
 }


echo '</div>';
#---------------------------
# If user own voices
#---------------------------
if ($mode=="edit"){
echo '<br><div class="complaint-actions hide_div_'.trim($voice->uniqueidentifier).'" style="margin-left:-5px;">';
echo '<button type="button" class="btn btn-primary tinyVoicesBtnBG delete-post" id="'.trim($voice->uniqueidentifier).'">Delete Voice</button>';
echo '<a href="'.SERVER_URL.'voice?v='.$voice->uniqueidentifier.'" type="button" class="btn btn-primary tinyVoicesBtnRegister">Edit Voice</a>';
echo '</div>'; 
}else{
 echo '<br>
 <div class="complaint-actions">
 <a href="#post_'.$voice->uniqueidentifier.'" '.$setLoginOptions.' class="'.$setStyleBackers.' postAction  replace_plus_'.$voice->uniqueidentifier.'" style="margin:0;" rel="backers" id="'.$voice->uniqueidentifier.'"><i class="fa fa-plus-circle"></i></a>
 <a href="#post_'.$voice->uniqueidentifier.'" '.$setLoginOptions.' class="'.$setStyleDeniers.' postAction replace_minus_'.$voice->uniqueidentifier.'" style="margin:0;" rel="deniers" id="'.$voice->uniqueidentifier.'"><i class="fa fa-minus-circle"></i></a>';
  echo '<span class="pull-right timeline-more-info" style="margin-top:15px; font-family:arial;"><button type="button" class="btn btn-primary btn-xs btn-deny" id="resultsDeniers_Plus_'.$voice->uniqueidentifier.'">'.$util->voterTypes("deniers",$totalDeniers).'</button></span>                    
        <span class="pull-right"  style="margin-top:15px; font-family:arial;"><button type="button" class="btn btn-primary btn-xs btn-backers" id="resultsBackers_Plus_'.$voice->uniqueidentifier.'">'.$util->voterTypes("backers",$totalBackers).'</button></span>';
 echo '</div>';
}

if ($uniqID!=null){
 if ($noBottomHR!=1){
  echo '<hr>';
 }
}

echo '</div><!--complaint-box-contents--></div><!--complaint-box-main--></div><!--col-md-4-->';
        
        /*
        echo '
        <article class="timeline-entry">
            <div class="timeline-entry-inner">
               <img src="'.$util->publicPhoto($users->folder).'" class="img-circle avatar-feeds">
               <a href="#post_'.$voice->uniqueidentifier.'" '.$setLoginOptions.' class="'.$setStyleBackers.' postAction  replace_plus_'.$voice->uniqueidentifier.'" style="margin:0;" rel="backers" id="'.$voice->uniqueidentifier.'"><i class="fa fa-plus-circle"></i></a>
               <a href="#post_'.$voice->uniqueidentifier.'" '.$setLoginOptions.' class="'.$setStyleDeniers.' postAction replace_minus_'.$voice->uniqueidentifier.'" style="margin:0;" rel="deniers" id="'.$voice->uniqueidentifier.'"><i class="fa fa-minus-circle"></i></a>
              
                <div class="timeline-label">
                    <div class="timeline-name"><a href="'.SERVER_URL.'voice-view?v='.$voice->uniqueidentifier.'" class="voice-readmore">'.$voice->brands_services.'</a></div>
                    <div class="timeline-post-title">'.$users->fullname.'</div><div class="timeline-location-details"> '.$voice->address.'</div>
                    
                    <div style="border-top:1px dotted #c2c2c2; padding-top: 10px; margin-top:10px;">
         ';
        
                if ($imgAttach!=false){
                foreach ($imgAttach as $aa=>$bb){
                echo '<div class="item active"><img src="'.$folder.''.$bb.'" class="img-responsive" width="100%"></div>';   
                }
                echo '<div style="margin-bottom:10px;"></div>';
                }  
                
                if (strlen($voice->url_media) > 0){
                
                }
               
              
                if ($imgAttach==false){
                 
                 if ($voice->url_type=="video"){
                 $arrayReplace=array('http:','https:');
                 echo $util->expandURL($voice->url_media);
                 }if ($voice->url_type=="image"){
                 //echo '<img src="'.SERVER_URL.'layout/img/logo/default-img-post.png" class="img-responsive"><br>'; 
                 echo '<img src="'.$voice->url_media.'" class="img-responsive"><br>';
                 }
                 
                }
             
               
               
               $getHash=$util->get_hashtags($voice->problems_complaints);
               if ($uniqID==null){
               echo "<div style='max-height:110px; overflow-y:hidden'>";
               $contents=str_replace($getHash,"",$voice->problems_complaints);
               $contents_short=substr(strip_tags($contents,"<p>"),0,250);
               if (strlen($contents) > 249){
                echo $contents_short."....";
               }else{
                echo $contents_short;
               }
               
               echo "</div>";
               }else{
               echo "<div>";
               echo $contents=str_replace($getHash,"",$voice->problems_complaints);
               echo "</div>"; 
               }
               
               
               echo '<div class="timeline-hashtags">';
               foreach ($getHash as $hashA=>$hashB){
               echo $hashB." ";  
               }
               echo '</div>';
       

          echo '          
          <div class="timeline-actions">';
          if ($uniqID==null){
          echo '<a href="'.SERVER_URL.'voice-view?v='.$voice->uniqueidentifier.'" class="voice-readmore">Read the voices <i class="fa fa-arrow-right"></i></a>';
          }else{
          echo "<a href='#' data-toggle='modal' data-target='#reportPost' class='report-post'><i class='fa fa-flag'></i> Report inappropriate</a>";
          }
          
          //if ($uniqID==null){
          echo '<span class="pull-right timeline-more-info"><button type="button" class="btn btn-primary btn-xs btn-deny" id="resultsDeniers_Plus_'.$voice->uniqueidentifier.'">'.$util->voterTypes("deniers",$totalDeniers).'</button></span>                    
                <span class="pull-right"><button type="button" class="btn btn-primary btn-xs btn-backers" id="resultsBackers_Plus_'.$voice->uniqueidentifier.'">'.$util->voterTypes("backers",$totalBackers).'</button></span>';
          //}
          
          echo '
          </div><!--timeline-actions-->
          </div>
        </div><!--timeline-label-->
        </div>
        </article><!--timeline-entry-->';
        
        
        
        */
        
        
       }

}
#----------------------------------------------------------------------------------------------------
# Desc: To show website footer
#----------------------------------------------------------------------------------------------------
public function footer($type=0){

$str= '<div class="containerx footer"><br>&copy; 2015 TinyVoices. All Rights Reserved </div>';
 if ($type==0){
  echo $str;
 }else{
  return $str;
 }

}


} #End Class
?>