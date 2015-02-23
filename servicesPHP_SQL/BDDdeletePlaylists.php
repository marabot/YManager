<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// delete all the playlists of user on his youtube account. return 1 if ok
/////////////////////////////////////////////////////////

	require_once '../google-api-php-client/autoload.php';

	session_start();
	
	
	$youtube=$_SESSION['youtube'];
	$myChannel=$_SESSION['myChannel'];
	
	try{
			global $youtube, $myChannel;
		$channelsResponse = $youtube->playlists->listPlaylists('contentDetails', array(
																				  'channelId' => $myChannel,
																				  'maxResults'=> 50
																				  )
															);
															
		foreach($channelsResponse['items'] as $channel)
		{			
			$deleteResponse=$youtube->playlists->delete($channel['id']);	
		}
		
		echo '1';
	}
	catch (Google_ServiceException $e)
	{
		
		echo '<div>0 :'.$e.'</div>';		
	}
	catch(Google_Exception $e)
	{			
	
		echo '<div>0 :'.$e.'</div>';
	}
?>