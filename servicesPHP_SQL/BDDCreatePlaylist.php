<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a playlist for the connected user on his youtube account. return 1 if ok
/////////////////////////////////////////////////////////


	session_start();

	//récup du singleton bdd
	include $_SERVER['DOCUMENT_ROOT'] . '/bdd.php';
	$bdd=bddConnect::getBdd();	
	
	$_GET[''];
	try{
		//create Playlist
		$newPlaylist=createPrivPlaylist('test','ben du test', $youtube);
		getTabVideosFromChans($newPlaylist,$videoList, $youtube);
			
		$htmlBody .= "<h3>New Playlist</h3><ul>";
		$htmlBody .= sprintf('<li>%s (%s)</li>',
			$newPlaylist['snippet']['title'],
			$newPlaylist['id']);
		$htmlBody .= '</ul>';
	
	  } catch (Google_ServiceException $e) {
		$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
	  } catch (Google_Exception $e) {
		$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
	 }


	function createPrivPlaylist($title, $description){
	
	global $youtube;
	
	//create snippet
	$playlistSnippet=new Google_Service_YouTube_PlaylistSnippet();
	$playlistSnippet->setTitle($title);
	$playlistSnippet->setDescription($description);
	
	// set status
	$playlistStatus=new Google_Service_YouTube_PlaylistStatus();
	$playlistStatus->setPrivacyStatus('public');
	
	// create playlist and associate resources
	$youTubePlaylist = new Google_Service_YouTube_Playlist();
    $youTubePlaylist->setSnippet($playlistSnippet);
    $youTubePlaylist->setStatus($playlistStatus);

	// call of the create method
	$playlistResponse = $youtube->playlists->insert('snippet,status',
    $youTubePlaylist, array());
   
	return $playlistResponse;
}
	  ?>