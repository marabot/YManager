<?php
// call google php api
require_once 'google-api-php-client/autoload.php';


session_start();
$htmlBody='';
$htmlTest='';
$newVidsContainer='';
$subs='';
$testTools='';
$botChannels='';


/////////////////////// Authentification Oauth
$OAUTH2_CLIENT_ID ='973217920762-o75fsne0pg2677lq72jfoa7e4t7ujolm.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = '5o-Wt8w3A1hvpqDN99_DArbN';

$client = new Google_Client();

$client->setClientId($OAUTH2_CLIENT_ID);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);
$client->setScopes('https://www.googleapis.com/auth/youtube');
$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
    FILTER_SANITIZE_URL);
$client->setRedirectUri($redirect);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);
$_SESSION['youtubeClient']=$youtube;
$_SESSION['youtube']=$youtube;

// if token is expired
if($client->isAccessTokenExpired() && (isset($_GET['refreshToken']))) {
	unset($_SESSION['token']);
	$htmlBody.='token expiré :'. $_SESSION['token'] . ' bli';	
	exit();
    
   $htmlBody.=' !!!!!!!!Access Token Expired !!!!!!' ; 

    $client->authenticate($_GET['code']);
    $NewAccessToken = json_decode($client->getAccessToken());
    $client->refreshToken($NewAccessToken->refresh_token);
	$htmlBody.='<p >refreshToken : '.$NewAccessToken->refresh_token.'</p>';
	}


// if it is a response to an authentification
if (isset($_GET['code'])) {
		
	echo 'state session: '.strval($_SESSION['state']).'<br>';
	echo 'state get: '.strval($_GET['state']).'<br>';
	
	if (strval($_SESSION['state']) !== strval($_GET['state'])) {
    die('The session state did not match.');
     }

      $client->authenticate($_GET['code']);
        $htmlBody.='<div> refresh token : '.$client->getRefreshToken().'</div>';
         $htmlBody.='<div> code : '.$_GET['code'].'</div>';
      $_SESSION['refreshToken']=$client->getRefreshToken();
      $_SESSION['token'] = $client->getAccessToken();

      header('Location: ' . $redirect);
}

// if authentification is already done and token registered in SESSION
if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

// if the  access token was successfully acquired.
if ($client->getAccessToken()) {
	$_SESSION['token'] = $client->getAccessToken();
	include $_SERVER['DOCUMENT_ROOT'] . '/main.php';
 
} else {
  // If the user hasn't authorized the app, initiate the OAuth flow
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $stateStr=strval($state);
  $client->setApprovalPrompt('force');
  $client->setAccessType('offline');
  
  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END

  <br><div class="text-center"><p>You need to <a href="$authUrl">Click here to authorize access to Youtube Account</a> before using Youtube Manager.<p></div>

END;

}

?>
