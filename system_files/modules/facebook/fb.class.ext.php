<?
class FaceBookExtends
{
	

	
	// *** Required settings
	private $_appId;
	private $_secretId;
	private $_redirectURL;
	//private $_primaryUserId;
	protected $_userId;
	protected $_user;
	
	// *** Facebook Object
	protected $_facebookObj;
	protected $_token;
	
	// *** Profile data
	protected $_profileDataArray = array();
	
	// *** Error handling
	protected $_errorArray = array();
	protected $_debugArray = array();
	protected $_trace = array();
	
	// *** Permissions
	private $_permissions = 'manage_pages,read_requests,read_mailbox,email,offline_access,read_stream,publish_stream,user_events,friends_events,user_birthday,user_location,user_work_history,user_about_me,user_hometown,user_photos,user_status,friends_photos,user_relationships,create_event';
	
	// *** Memory cache
	private $_memoryCache = array();
	
	// *** Enable for automatic data cache clearing
	private $_dev = false;


	private $_isPage;
	
## _____________________________________________________________________________	
## ________                _____________________________________________________
## ________ PUBLIC METHODS _____________________________________________________
## _____________________________________________________________________________
##		
	
	public function __construct($appId, $secretId, $redirectURL, $tokenedUser=null, $publicUser=null, $cookies=true)
	{
		
		if ($this->_dev) {
			CacheManager::clearCache();
		}
					
		// *** Save these for later
		$this->_appId = $appId;
		$this->_secretId = $secretId;
		$this->_redirectURL = $redirectURL;
		

		// *** Unset facebook user id
		$user = null; 
		
		// *** include the facebook api class
		try{
			require_once ('facebook.php');
		}
		catch(Exception $o){
			error_log($o);
		}
		
		// *** Create our Application instance.
		$this->_facebookObj = new Facebook(array(
		  'appId'  => $appId,
		  'secret' => $secretId,
		  'cookie' => $cookies,
		));	
		
		
		// *** Get the UID of the connected user (this is before we switch users)
		//$this->_primaryUserId = $this->_facebookObj->getUser();
		//echo 'uiser1:: ' . $this->_primaryUserId . '<br />';
		
		// *** Try set an access token (if applicable)
		$isOK = $this->_userAccessToken($tokenedUser);
		if (!$isOK) {
			$this->_errorArray[] = 'User\'s token not authenticated.';
			$this->_debugArray[] = 'User\'s token not authenticated. Run fb_tools/auth_user.php.';	
		}
		
		// *** Get the UID of the connected user.
		$this->_user = $this->_facebookObj->getUser();

	
		
		if (USE_PAGE_ID == false) {	

			// *** If we want to check out our profile	
			$this->_userId = $this->_user;
		} else {	
		
			// *** Or, if we wish to act on behalf of a public profile
			$this->_userId = $publicUser;
		}	

			
		if (CACHE_ENABLED) {
			//check timestamp, remove files if need be.
			CacheManager::manageCache(CACHE_MINUTES, $this->_userId);
		}
		
		$this->_profileDataArray = $this->_getUserProfileRaw();

		// *** 
		if (USE_PAGE_ID) {
			$this->_isPage = true;
		}

	}
	
	## --------------------------------------------------------
	
	public function __destruct() 
	{
		if (isset($this->_memoryCache)) {
			unset($this->_memoryCache);
		}
	}


	/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-* 
	 *	Login / Logout Methods
	 * 	
	 */
	
	public function getLoginURL()
	# Return the login URL link
	{
		$loginUrl = $this->_facebookObj->getLoginUrl(
				array(
					'scope'         => $this->_permissions,
					'redirect_uri'  => $this->_redirectURL
				)
		);		
		
		return $loginUrl;
	}
	
	## --------------------------------------------------------
	
	public function loginRedirect()
	# Automatically redirect you to the facebook login page
	{
		if (!$this->_user) {				
			header("Location:{$this->_facebookObj->getLoginUrl(array('scope' => $this->_permissions, 'redirect_uri'  => $this->_redirectURL))}");
			exit;
		}
	}
	
	## --------------------------------------------------------
	
	public function getLogoutURL()
	# Return the logout URL link. Doesn't remove the cookie
	{
		$logoutUrl  = $this->_facebookObj->getLogoutUrl();
		return $logoutUrl;
	}
	
	## --------------------------------------------------------
	
	public function logout()
	{
		// *** Remove the cookie
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				setcookie($name, '', time()-1000);
				setcookie($name, '', time()-1000, '/');
			}
		}
	}
	
	## --------------------------------------------------------
	
	public function getLogInOutLink()
	# Show link depending on the users login/logout status
	{
		if ($this->_user) {
		  return $this->_facebookObj->getLogoutUrl();
		} else {
		  return $this->_facebookObj->getLoginUrl();
		}		
	}
	
	## --------------------------------------------------------
	
	public function isLoggedIn()
	# Test if user is logged in and authenticated
	{
		if ($this->_user) {
			return true;
		} else {
			return false;
		}
	}
	
	public function saveProfileImage($path, $quality = 100)
	{		
		if ($this->_user) {
			try {
				$data = file_get_contents('https://graph.facebook.com/' . $this->_userId . '/picture');
				$im = imagecreatefromstring($data);
				imagejpeg($im, $path, $quality);
				if (TRACE) {$this->_trace[] = 'saveProfileImage'; }
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not save profile image.';
				$this->_debugArray[] = $e;
				return false;
			}					
		}
	}
	
	
	public function getProfileImageURL($size='square',$userid)
	{
		$size = $this->_resolveImageSize($size);
		
		if ($userid) {
			try {
				//if (TRACE) {$this->_trace[] = 'getProfileImageURL'; }
				return 'https://graph.facebook.com/' . $userid . '/picture?type=' . $size;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not get profile image URL.';
				$this->_debugArray[] = $e;
				return false;
			}			
		}	
	}
	
	## --------------------------------------------------------
	
	public function getAlbumNames($includeProfileAlbum = false)
	{
		// *** Get album data
		$albumsData = $this->_getAlbumDataRaw();

		$albumNamesArray = array();
		
		if (count($albumsData['data']) > 0) {
			
			// *** Loop through album data
			foreach ($albumsData['data'] as $album) {
				
				// *** Test if we want to include the Profile Pictures album
				if (($includeProfileAlbum || strtolower($album['name']) != 'profile pictures')) {
				
					$albumNamesArray[$album['id']] = $album['name'];
				}
			}
		}
		unset($albumsData);
		return $albumNamesArray;
	}		
	
	## --------------------------------------------------------
		
	public function getAlbumId($albumName)
	{
		// *** Test if already an id
		if ($this->_testIfId($albumName)) {
			return $albumName;
		}

		$id = 0;
		
		// *** Get album names
		$albumNamesArray = $this->getAlbumNames();

		if (count($albumNamesArray) > 0) {

			// *** Loop through each album
			foreach ($albumNamesArray as $albumId => $value) {
				
				// *** If the name already exists...
				if (strtolower($albumName) == strtolower($value)) {

					// *** ...return the id
					return $albumId;
				}
			}
		}	
		
		unset($albumNamesArray);
		return $id;
	}	
		
	## --------------------------------------------------------
	
	public function getAlbumDataRaw($albumName = '')
	#	Used in:	FB Album Gallery
	#	
	{
		if ($albumName == '') {
		
			// *** Get all the data
			return $this->_getAlbumDataRaw();
		} else {
			$dataArray = $this->_getAlbumDataRaw();

			if ($dataArray['data']) {
				foreach ($dataArray['data'] as $key => $albumArray) {

					if ($albumArray['name'] == $albumName || $albumArray['id'] == $albumName) { # Fix. Added album id check (v1.3)

						return $albumArray;
					}
				} 
			}
		}
	}
	
	## --------------------------------------------------------
	
	public function getAlbumPhotoDataRaw($albumName, $limit=150, $offset=0) 
	#	Used in:	FB Album Gallery
	#			
	{
		return $this->_getAlbumPhotoDataRaw($albumName, $limit, $offset);
	}
	
	## --------------------------------------------------------
	
	public function getFriendId($friendName)
	{
		// *** Test if already an id
		if ($this->_testIfId($friendName)) {
			return $friendName;
		}

		$id = 0;
		
		// *** Get friend names
		$friendDataArray = $this->_getFriendsRaw();
	
		if (count($friendDataArray['data']) > 0) {

			// *** Loop through each friend
			foreach ($friendDataArray['data'] as $value) {
				
				// *** If the name already exists...
				if (strtolower($friendName) == strtolower($value['name'])) {

					// *** ...return the id
					return $value['id'];
				}
			}
		}	
		
		unset($friendDataArray);
		return $id;
	}		
	
	
	/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-* 
	 *	Facebook Update/Add Methods
	 * 	
	 */

	public function addCustom($api, $dataArray, $name='custom')
	#	Used in:	FB Simple APi
	#			
	{
	
		if ($this->_user) {
			
			try {
							
				if ($this->_isPage && REPLY_AS_PAGE) {
					$dataArray['access_token'] = $this->_getPageToken();
					$_SESSION['TOKEN']=$dataArray['access_token'];
				}					
								
				$publishStream = $this->_facebookObj->api('/' . $this->_userId . '/' . $api, 'post', $dataArray);
				
				if (TRACE) {$this->_trace[] = $name; }
				return true;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set ' . $api . '.';
				$this->_debugArray[] = $e;
				return false;
			}
		}
	}

	## --------------------------------------------------------

	
	public function addStatus($status,$description)
	#	Used in:	FB Simple APi
	#			
	{
	
		if ($this->_user) {
			
			try {    
				  
				/*
				  $dataArray = array(
					'message' => $status,
					'place'   =>"latitude : 3.0622138,longitude:101.743599,country: malaysia"
					);
				*/	
				$status_clean=stripslashes($status);
				
	                        $UID=$_SESSION['fb_340456482717973_user_id'];
	                        $link_username="Follow @".$UID."";
	                        $link_action="https://wikiabout.me/".$UID."";
				
				$dataArray = array(
					'message' => $status_clean,
					/*'actions' => array('name'=>''.$link_username.'','link'=>''.$link_action.''),*/
					);

									
				
				if ($this->_isPage && REPLY_AS_PAGE) {
					$dataArray['access_token'] = $this->_getPageToken();
				}					
								
				$publishStream = $this->_facebookObj->api('/'.$this->_userId.'/feed', 'post', $dataArray);
				
				if (TRACE) {$this->_trace[] = 'addStatus'; }
				return $publishStream['id'];
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}
	}

public function appRequest($status,$ownerID,$appID,$recipientID,$AccessToken)
	#	Used in:	FB Simple APi
	#			
	{
	
		if ($this->_user) {
			
			try {    
				  
				/*
				  $dataArray = array(
					'message' => $status,
					'place'   =>"latitude : 3.0622138,longitude:101.743599,country: malaysia"
					);
				*/	
				$status_clean=stripslashes($status);
				$dataArray = array(
					'message' => $status_clean,
					'request_id'=> $ownerID,
					'app_id'=> $appID,
					);

				
						
				
			
					$dataArray['access_token'] = $AccessToken;
				
						print_r($dataArray);				
				$publishStream = $this->_facebookObj->api('/'.$recipientID.'/apprequest', 'post', $dataArray);
				
				print_r($publishStream);
				
				if (TRACE) {$this->_trace[] = 'addStatus'; }
				return $publishStream['id'];
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}
	}


	## --------------------------------------------------------

	public function addNotes($note, $subject)
	#	Used in:	FB Simple APi
	#			
	{
	
		if ($this->_user) {
			
			try {
				
				$dataArray = array(
					'message' => $note,
					'subject' => $subject 
					);					
				
				if ($this->_isPage && REPLY_AS_PAGE) {
					$dataArray['access_token'] = $this->_getPageToken();
				}					
								
				$publishStream = $this->_facebookObj->api('/' . $this->_userId . '/notes', 'post', $dataArray);
				
				if (TRACE) {$this->_trace[] = 'addNotes'; }
				return true;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set note.';
				$this->_debugArray[] = $e;
				return false;
			}
		}
	}

		
	## --------------------------------------------------------
	
	public function addLink($status, $link='', $caption='', $clearCacheFile=true)
	#	Used in:	FB Simple APi
	#	Notes:		$image HAS to be a URL		
	{
	/*
	$attachment = array('message' => $message, 'access_token' => $ACCESS_TOKEN);

	$result = $facebook->api('/'.$PAGE_ID.'/feed', 'post', $attachment);
*/

		if ($this->_user) {
			
			try {

   
	$dataArray = array(
					'message' => $status_clean, 
					'link'    => $link,
					'caption' => $caption,
					'type' => "link",
					);	
	
       
					

				if ($this->_isPage && REPLY_AS_PAGE) {
				
					$dataArray['access_token'] = $this->_getPageToken();
				}	

				$publishStream = $this->_facebookObj->api('/' . $this->_userId . '/feed', 'post', $dataArray);
			
		
				
				if ($clearCacheFile) { 	cacheManager::clearCache('_getStatus'); }
				if ($clearCacheFile) { 	cacheManager::clearCache('_getFeed'); }
				if (TRACE) {$this->_trace[] = 'addStatus'; }
				return $publishStream['id'];
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}
	}	
	
	
	public function addLinkPages($accessToken,$pageID,$link='', $caption='', $clearCacheFile=true)
	#	Used in:	FB Simple APi
	#	Notes:		$image HAS to be a URL		
	{
	/*
	$attachment = array('message' => $message, 'access_token' => $ACCESS_TOKEN);

	$result = $facebook->api('/'.$PAGE_ID.'/feed', 'post', $attachment);
*/

$dataArray = array(
					'link'    => $link,
					'caption' => $caption,
					'type' => "link",
					);

$dataArray['access_token'] = $accessToken;
$publishStream = $this->_facebookObj->api('/' . $pageID . '/feed', 'post', $dataArray);
return $publishStream['id'];

		/*if ($this->_user) {
			
			try {


       
					$dataArray = array(
					'message' => "test", 
					'link'    => $link,
					'caption' => $caption,
					'type' => "link",
					);

				if ($this->_isPage && REPLY_AS_PAGE) {
				
					$dataArray['access_token'] = $this->_getPageToken();
				}	

				$publishStream = $this->_facebookObj->api('/' . $pageID . '/feed', 'post', $dataArray);
			
		
				
				if ($clearCacheFile) { 	cacheManager::clearCache('_getStatus'); }
				if ($clearCacheFile) { 	cacheManager::clearCache('_getFeed'); }
				if (TRACE) {$this->_trace[] = 'addStatus'; }
				return $publishStream['id'];
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}*/
	}	
	
	
	
	## --------------------------------------------------------
	
	public function addComment($objId, $comment, $clearCacheFile=true)
	#
	#	Author:		Jarrod Oberto
	#	Date:
	#	Purpose:	
	#	Params in:	(int) $objId:
	#				(str) $comment:
	#				(bo0l) $clearCacheFile: If we don't clear this file from the
	#					we won't be able to immediatley display the comment as
	#					it won't exist in the cache!
	#	Params out:
	#	Notes:	
	#	Permission:	publish_stream
	#	Used in:	FB Album Gallery
	#
	{

		if ($this->_user) {
			try {
				
				$comment_clean=stripslashes($comment);
				$dataArray = array(
					'message' => $comment_clean
					);						
				
				if ($this->_isPage && REPLY_AS_PAGE) {
					$dataArray['access_token'] = $this->_getPageToken();
				}	
				
				$id = $this->_facebookObj->api('/' . $objId. '/comments', 'post', $dataArray);
				if ($clearCacheFile) { 	cacheManager::clearCache('_getAlbumPhotoDataRaw'); }
				if (TRACE) {$this->_trace[] = 'addComment'; }
				return $id;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}		
	}
	
		public function addCommentPages($token,$objId, $comment, $clearCacheFile=true)
	
	{

		if ($this->_user) {
			try {
				
				$comment_clean=stripslashes($comment);
				$dataArray = array(
					'message' => $comment_clean
					);						
				
				
				$dataArray['access_token'] = $token;
			
				
				$id = $this->_facebookObj->api('/' . $objId. '/comments', 'post', $dataArray);
				if ($clearCacheFile) { 	cacheManager::clearCache('_getAlbumPhotoDataRaw'); }
				if (TRACE) {$this->_trace[] = 'addComment'; }
				return $id;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}		
	}
	
	
	public function Like($objId,$type,$tokenPages=null,$clearCacheFile=true)
	{

		if ($this->_user) {
			try {
							
				
				if ($this->_isPage && REPLY_AS_PAGE && $tokenPages==null) {
					$dataArray['access_token'] = $this->_getPageToken();
				}else{
					$dataArray['access_token'] = $tokenPages;
				}
				
				$id = $this->_facebookObj->api('/' . $objId. '/likes', ''.$type.'');
				
				if ($clearCacheFile) { 	cacheManager::clearCache('_getAlbumPhotoDataRaw'); }
				if (TRACE) {$this->_trace[] = 'addComment'; }
				return $id;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}		
	}
	
	public function LikePages($token,$objId,$type,$clearCacheFile=true)
	{

				$dataArray['access_token'] = $token;
			
				
				$id = $this->_facebookObj->api('/' . $objId. '/likes', ''.$type.'');
				echo "<pre>";
				print_r($id);
				
				if ($clearCacheFile) { 	cacheManager::clearCache('_getAlbumPhotoDataRaw'); }
				if (TRACE) {$this->_trace[] = 'addComment'; }
				return $id;
	}
	
	
	## --------------------------------------------------------
	
	public function addEvent($name, $location='', $start='', $end='', $description='', $image='', $clearCacheFile=true)
	#
	#	Author:		Jarrod Oberto
	#	Date:
	#	Purpose:	
	#	Params in:	(int) $objId:
	#				(str) $comment:
	#				(bo0l) $clearCacheFile: If we don't clear this file from the
	#					we won't be able to immediatley display the comment as
	#					it won't exist in the cache!
	#	Params out:
	#	Notes:		If left the param $image incase I enable this in the future
	#				as it can be used - but it's not documented!
	#	Permission:	create_event
	#	Used in:	FB Album Gallery / Events
	#
	{

		if ($this->_user) {
			try {
				
				$dataArray = array(
					'name' => $name,
					'location' => $location,
					'start_time' => $start,
					'end_time' => $end,
					'description'=> $description
					);	
				
				if ($this->_isPage && REPLY_AS_PAGE) {
					$dataArray['access_token'] = $this->_getPageToken();
				}	
				
				
				$id = $this->_facebookObj->api('/' . $objId. '/events', 'post', $dataArray);
				if ($clearCacheFile) { 	cacheManager::clearCache('_getEvents'); }
				if (TRACE) {$this->_trace[] = 'addComment'; }
				return $id;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not set status.';
				$this->_debugArray[] = $e;
				return false;
			}
		}		
	}
	
	## --------------------------------------------------------
	
	public function getUserId()
	{
		return $this->_userId;
	}

	## --------------------------------------------------------

	private function _getPageToken($save=false)
	#	If we save it, we can retrienve it with this: 
	#			$token = $this->_readAccessToken(PAGE_ID);
	{
	
		$accountsArray = $this->_getAccountsRaw();
					
		if ($accountsArray['data']) {
			foreach($accountsArray['data'] as $account){
				if($account['id'] == PAGE_ID){

					$accessToken = $account['access_token'];
					
					if ($save) {
						#$this->_saveAccessToken(PAGE_ID, $accessToken);
					}
					return $accessToken;
				}
			}
		}
	}	
	
	## --------------------------------------------------------
	
	public function _customApiCall($api, $useCurrentUser=true, $name='_customApiCall')
	{
		if ($useCurrentUser) {
			$call = '/' . $this->_userId . '/' . $api;
			return $this->_getAPI($call, $name);	
		} else {
			return $this->_getAPI($api, $name);		
		}
	}		
		
	/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-* 
	 *	Error Handling Methods
	 * 	
	 */
	
	public function getLastError()
	{
		
	}
	
	## --------------------------------------------------------
	
	public function getErrors()
	{
		return $this->_errorArray;
	}
	
	## --------------------------------------------------------
	
	public function getError()
	{
		if (isset($this->_errorArray[0])) {
			return  $this->_errorArray[0];
		} else {
			return '';
		}
	}
	
	## --------------------------------------------------------
	
	public function getDebugErrors()
	{
		return $this->_debugArray;
	}
	
	## --------------------------------------------------------
	
	public function getTrace()
	{
		return $this->_trace;
	}	
	

## _____________________________________________________________________________	
## ________                 ____________________________________________________
## ________ PRIVATE METHODS ____________________________________________________
## _____________________________________________________________________________
		
	protected function _getUserProfileRaw()
	#
	#	Author:		Jarrod Oberto
	#	Date:		Jun 11
	#	Purpose:	Get user profile information through FB API
	#	Params in:
	#	Params out:	(array) Facebook data
	#	Notes:	
	#	Used in:	All
	#
	{	
		$name = '_getUserProfileRaw_'.$_SESSION['fb_340456482717973_user_id'].'';
		$api = '/' . $this->_userId;
		return $this->_getAPI($api, $name);				
	}	
	
	## --------------------------------------------------------

public function _getFriendProfile($id)
{
# 574641862
# 500303954
		$name = '_getFriendProfile_'.$_SESSION['fb_340456482717973_user_id'].'';
		$api = '/' . $id . '/';
		return $this->_getAPI($api, $name);

}

	## --------------------------------------------------------
	
	protected function _getAlbumPhotoDataRaw($albumName, $limit=999, $offset=0, $useCache=true)
	{
		$albumId = $this->getAlbumId($albumName);
	
		$name = '_getAlbumPhotoDataRaw_'.$_SESSION['fb_340456482717973_user_id'].'';
		$api = '/' . $albumId . '/photos?limit=' . $limit . '&offset=' . $offset  . '&date_format=U' ;
		return $this->_getAPI($api, $name, $albumId);			
	}		
	
	## --------------------------------------------------------
	
	protected function _getAlbumDataRaw()
	{	
		// *** Make cache file unique for different users
		#$this->_userId;
		$fileKey =$_SESSION['fb_340456482717973_user_id'];
		$fileKey = Helper::makeFilename($fileKey);		
		
		$name = '_getAlbumDataRaw-' . $fileKey;
		$api = '/' . $this->_userId . '/albums';
		return $this->_getAPI($api, $name);			
	}		
	
	## --------------------------------------------------------
	
	protected function _getFriendsRaw() 
	#	FB Simple API 
	{	
		$name = '_getFriendsRaw_'.$_SESSION['fb_340456482717973_user_id'].'';
		$api = '/' . $_SESSION['fb_339619740062_user_id'] . '/friends';
		return $this->_getAPI($api, $name);		
	}	
	
	## --------------------------------------------------------
	
	protected function _getStatusRaw()
	#	requires: read_stream
	#	FB Simple API
	{
		$name = '_getStatus';
		$api = '/' . $_SESSION['fb_340456482717973_user_id'] . '/statuses?date_format=U';
		return $this->_getAPI($api, $name);
	}
	
	## --------------------------------------------------------
	
	protected function _getFeedRaw()
	#	requires: read_stream
	#	FB Simple API
	{
		$name = '_getFeed';
		$api = '/' . $_SESSION['fb_340456482717973_user_id'] . '/feed?date_format=U';
		return $this->_getAPI($api, $name);
	}	
	
	## --------------------------------------------------------
	
	protected function _getRequests()
	#	TO BE COMPLETED
	{
		$name = '_getRequests';
		$api = '/' . $_SESSION['fb_340456482717973_user_id'] . '/invitations';
		return $this->_getAPI($api, $name);
	}	
	
	## --------------------------------------------------------
	
	protected function _getMail_legacy()
	#	FACEBOOK ARE IN THE MIDDLE OF CHANGING THIS - this is the current stable
	#		but is soon to be superseded
	{
		$name = '_getMail';
		$api = '/' . $_SESSION['fb_340456482717973_user_id'] . '/inbox';
		return $this->_getAPI($api, $name);
	}	
	
	public function _getMail()
	#	NEW MAIL API  - currently not stable
	{
		$name = '_mailbox_folder';
		//$api = '/' . $_SESSION['fb_339619740062_user_id'] . '/threads';

		return $this->_getAPI($api, $name);
	}		
	
	
	## --------------------------------------------------------
	
	protected function _getEventsRaw($dateFormat='U')
	#	requires:	user_events - for non-public events
	#				friends_events - for non-public events of the user's friends
	#	FB Simple API
	{
		$name = '_getEvents';
		$api = '/' . $_SESSION['fb_340456482717973_user_id'] . '/events?date_format=' . $dateFormat;
		return $this->_getAPI($api, $name);
	}	
	
	## --------------------------------------------------------
	
	protected function _getAccountsRaw()
	#	requires:	manage_pages
	{
		$name = '_getAccountsRaw';
		$api = '/' . $this->_user . '/accounts'; #FIX
		return $this->_getAPI($api, $name);		
	}
	
	## --------------------------------------------------------
	
	protected function _getAPI($api, $name, $dataId=0, $useCache=true)
	{
		// *** Try from memory
		if (MEMORY_CACHE_ENABLED) {
			if (isset($this->_memoryCache[$name][$dataId])) {
				if (TRACE) {$this->_trace('memory', $name); }
				return $this->_memoryCache[$name][$dataId];
			}
		}	
		
		// *** Try from cache
		if (CACHE_ENABLED && $useCache) {
			$data = CacheManager::readFromCache($name, $dataId);
			if ($data) {		
				
				if (MEMORY_CACHE_ENABLED) {
					$this->_memoryCache[$name][$dataId] = $data;	
				}
				
				if (TRACE) {$this->_trace('file', $name); }
				return $data;
			}
		}
		
		// *** Try from FB
		if ($this->_user) {
			try {		

				$data = $this->_facebookObj->api($api); 
		
				if (MULTI_USER) {
					$name = $_SESSION['fb_340456482717973_user_id'] . $name;
				}

				if (MEMORY_CACHE_ENABLED) {
					$this->_memoryCache[$name][$dataId] = $data;	
				}
				
				if (CACHE_ENABLED) {
					CacheManager::writeToCache($name, $data, $dataId);
				}

				if (TRACE) {$this->_trace('api', $name); }
				return $data;
			} catch (FacebookApiException $e) {
				$this->_errorArray[] = 'Could not get ' . $name . ' data.';
				$this->_debugArray[] = $e;		
				return false;
			}
		}			
	}	

	/*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-* 
	 *	OTHER
	 * 	
	 */		

	private function _trace($path, $name)
	{
		$this->_trace[$path][] = $name;
	}
	
	## --------------------------------------------------------
	
	protected function _testIfId($value)
	# helper
	{
		if ((strlen($value) >= 10 && strlen($value) <= 20) && is_numeric($value)) {
			return true;
		} else {
			return false;
		}
	}
	
	## --------------------------------------------------------
	
	protected function _resolveImageSize($size)
	{
		$size = strtolower($size);
		switch ($size) {
			case 'square':
				$returnSize = 'square';
				break;
			case 'small':
				$returnSize = 'small';
				break;
			case 'normal':
				$returnSize = 'normal';
				break;
			case 'large':
				$returnSize = 'large';
				break;

			default:
				$returnSize = 'normal';
				break;
		}
		
		return $returnSize;
	}	
	
	## --------------------------------------------------------
	
	private function _userAccessToken($tokenedUser)
	{		
		if (isset($tokenedUser) && $tokenedUser != 'me') {
			
			/*
			 *	If a token is specified, change to that token
			 * 
			 */
					
			$token = '';
			
			if (!MULTI_USER) {
				$tokenedUser = 'primary';
			}
			
			
			// *** Get the token from file
			$tokenArray = $this->_readAccessToken($tokenedUser);

			if (isset($tokenArray['token'])) {
				$token = trim($tokenArray['token']);
			}
			
			if (isset($tokenArray['id'])) {
				$userId = $tokenArray['id'];
			}			
			
			if ($token != '' && $userId == $tokenedUser) {
				try{
					
					// *** Try to log in with this token
					if ($this->_facebookObj->setAccessToken($token)) {
						$this->_token = $token;
						unset($tokenArray);
						return true;
					}
				} catch (FacebookApiException $e) {
					$this->_errorArray[] = 'Could not set access token.';
					$this->_debugArray[] = $e;
				}	
			} else {
				$this->_debugArray[] = 'User token not set.';				
			}
		} else {
			
			/*
			 *	Else, keep using the current token.
			 *	This path is taken when authorising a user
			 * 
			 */			
			
			$this->_debugArray[] = 'Tokened user not specified. Using current token.';
			return true; # return true as this is valid
		}
		return false;
	}
		
	## --------------------------------------------------------
	
	private function _readAccessToken($tokenedUser)
	{
		
		$tokenArray['token'] = $_SESSION['fb_340456482717973_access_token'];
		$tokenArray['id'] = $_SESSION['fb_340456482717973_user_id'];
		
		return $tokenArray;
	}
	
	## --------------------------------------------------------
	
	protected function _saveAccessToken($id, $token)
	{
	/*	
		
		if (USE_CUSTOM_DATA_STORE) {

			return $this->_customSaveAccessToken($id, $token);
		}

		try{		

			$jsonData = '';
			
			// *** Set the token file filename
			$filename = dirname(__FILE__) . '/../signatures/tokens.txt';

			// *** If this a single user account I.E., user is "primary". THe MULTI_USER overrides the single user
			if (strtolower(AUTH_USER) == 'primary' && MULTI_USER == false) {
				
				// *** Set the id to "primary" instead of the user id
				$id = 'primary';
			}
				
			// *** See if the file already exists
			if (file_exists($filename)) {
				
				// *** If so, get the file contents so we can add to it
				$jsonData = file_get_contents($filename, $jsonData);

				// *** Decode the json contents into an array
				$currentDataArray = json_decode($jsonData, true);
	
			} else {

				// *** Else, just create a new array to add our data to
				$currentDataArray = array();
			}
					
			// *** Add our id and token to our array
			$currentDataArray[$id] = $token;

			
			// *** JSON encode
			$jsonData = json_encode($currentDataArray);

			// *** Get the path
			$pathInfo = pathinfo($filename)	;			
			
			// *** Make sure it writable
			if (is_writable($pathInfo['dirname'])) {
				
				// *** Save array contents
				file_put_contents($filename, $jsonData);
				
				return true;
			} else {
				$this->_debugArray[] = 'Directory not writable.';	
				return false;
			}
			
		} catch (FacebookApiException $e) {
			$this->_errorArray[] = 'Could not save access token.';
			$this->_debugArray[] = $e;
			return false;
		}	
	*/	
	}
	
	
	## --------------------------------------------------------

	private function _customReadAccessToken($id)
	{

		// *** DB select
		
	}
	
	## --------------------------------------------------------
	
	private function _customSaveAccessToken($id, $token)
	{

		// *** DB insert/update
		
	}	

	## --------------------------------------------------------
	
}


class Helper
{
	
	public static function makeFilename($string)
	{
		// *** Trim
		$string = trim($string);

		// *** Replace spaces with underscores
		$string = str_replace(' ', '_', $string);

		// *** Replace double underscores with a single underscore
		$string = str_replace('__', '_', $string);

		// *** Replace '&' (ampersand) with the word 'and'
		$string = str_replace('&', 'and', $string);

		// *** Convert acsented chars to their english equivelant.
		$string = self::normalize($string);

		// *** Remove dirty chars (keep: alphas nums _ : -)
		$string = preg_replace('/[^a-zA-Z0-9_:-]/', '', $string);

		// *** Make string lowercase
		$string = strtolower($string);

		return $string;
	}	
	
	## --------------------------------------------------------
	
	public static function normalize ($string)
	{
		$table = array(
			'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
			'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
			'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
			'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
			'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
			'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
			'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r',
		);

		return strtr($string, $table);
	}	
	
}


class CacheManager
{
	
	private static $_timestamp;
	private static $_cacheMinutes;
	private static $_cacheId;
	
	public static function manageCache($cacheMinutes = 120, $userId='')
	{
		
		if (MULTI_USER) {
			self::$_timestamp = dirname(__FILE__) . '/' . CACHE_FOLDER . '/' . $userId . '_timestamp.txt';
			self::$_cacheId = $userId;
		} else {
			self::$_timestamp = dirname(__FILE__) . '/' . CACHE_FOLDER . '/timestamp.txt';
		}		

		self::$_cacheMinutes = $cacheMinutes;
		$cacheTime = $cacheMinutes * 60; 
		
		// Serve from the cache if it is younger than $cachetime
		if (file_exists(self::$_timestamp) && (time() - $cacheTime > filemtime(self::$_timestamp))) {		
			
			/*
			 *	If $cacheTime time expires, delete the cached contents 
			 * 
  			 */

			// *** delete all files in the folder	
			CacheManager::clearCache();
			
		} else {
			


		}
	}	
	
	## --------------------------------------------------------
	
	public static function clearCache($cacheName='', $force=false)
	{

		if (!CACHE_DELETABLE && !$force) {
			return false;
		}
		
		$cachDir = dirname(__FILE__) . '/' . CACHE_FOLDER;
		
		// *** Select single cache file to remove
		if ($cacheName != '') {
			
			if (MULTI_USER) {
				$cacheName = self::$_cacheId . $cacheName;
			}

			$cachDir = $cachDir . '/' . $cacheName . '.txt';
			
			@unlink($cachDir);
			
		} else {
			
			// *** Delete the entire cache
			
			$cachDir = $cachDir . '/*.txt';

			/*
			 * Glob on windows may need a '/' at the begining of the path!
			 * http://www.php.net/manual/en/function.glob.php#49483
			 * 
			 */
			$winFix = '';
			if (self::_is_windows_server()) {
				$winFix = "/";
			}

			$filesArray = glob($cachDir);

			if ( is_array ( $filesArray ) ) {
				if (count($filesArray) > 0) {
					foreach ($filesArray as $filename) {

						if (MULTI_USER && strpos($filename, self::$_cacheId) !== false) {
							unlink($filename);
						} 
						else if (!MULTI_USER) {
							unlink($filename);
						}
					}
				}
			}
					
		}
	}
	
	## --------------------------------------------------------
	
	public static function writeToCache($file, $data, $dataId = 0) 
	{
		
		try{		

			$jsonData = '';
			
			$filename = dirname(__FILE__) . '/' . CACHE_FOLDER . '/' . $file . '.txt';
			
			
			if (file_exists($filename)) {
				$jsonData = file_get_contents($filename, $jsonData);

				$currentDataArray = json_decode($jsonData, true);
			} else {
				$currentDataArray = array();
			}

			$currentDataArray[$dataId] = $data;

			$jsonData = json_encode($currentDataArray);


			$pathInfo = pathinfo($filename)	;			
			if (is_writable($pathInfo['dirname'])) {
				file_put_contents($filename, $jsonData);
				CacheManager::writeCacheTimeStamp();
				return true;
			} else {
				$this->_debugArray[] = 'Directory not writable.';	
				return false;
			}
			
		} catch (FacebookApiException $e) {
			$this->_errorArray[] = 'Could not cache data.';
			$this->_debugArray[] = $e;
			return false;
		}	
	}	
	
	## --------------------------------------------------------
	
	public static function writeCacheTimeStamp()
	{
		//$timestamp = dirname(__FILE__) . '/' . CACHE_FOLDER . '/timestamp.txt';
		$timestamp = self::$_timestamp;
		
		
		if (!file_exists($timestamp)) {
			$currentTime = date("F j, Y, g:i a");
			$expireTime = 'Expiry set to: '. self::$_cacheMinutes . ' minutes. (' . self::$_cacheMinutes / 60 . ' hrs)';
			@file_put_contents($timestamp,$currentTime . "\n" . $expireTime);
		}
		
	}
	
	## --------------------------------------------------------
	
	public static function readFromCache($file, $dataId=0)
	{
		$filename = dirname(__FILE__) . '/' . CACHE_FOLDER . '/' . $file . '.txt';
		
		$jsonData = '';
		$dataArray = array();
	
		
		if (file_exists($filename)) {
			$jsonData = file_get_contents($filename);

			$dataArray = json_decode($jsonData, true);

			unset($jsonData);
			if (isset($dataArray[$dataId])) { 
		
				return $dataArray[$dataId];
				
			} else {
				return false;
			}
			
		} else {
			
			return false;
		}		
	}
	
	## --------------------------------------------------------

	public static function _is_windows_server()
	{
		return in_array(strtolower(PHP_OS), array("win32", "windows", "winnt"));
	}	
		
	## --------------------------------------------------------

}
?>
