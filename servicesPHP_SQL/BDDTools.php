<?php
//////////////////////////////////////////////////////////////
// Tools used in PHP Scripts to be call with XMLHttpRequest //
//////////////////////////////////////////////////////////////


//  Create a playlist with new videos on $botId Channels since lastharvest Date. return the Id of the new Playlist
function harvest($botId) {
	 global $youtube, $videoList, $htmlBody, $userDatas,$bdd;

	 $chansToHarvest=getChansFromBots($botId);

	 $dateAfter=$userDatas->getBots()[$botId]['lastHarvest'];
	
	 $videoList=getsVideosFromChans($chansToHarvest, $dateAfter, '-1');	// tableau des videos
     $titre='videos du '.date('r',$dateAfter).' au '.date('r');


    $videoList=orderVideoList($videoList);


	 try{
		$newPlaylist=createPrivPlaylist($titre,' ');


		AddTabVideosToChans($newPlaylist,$videoList);	
		changelastHarvest($botId, time());
		//echo'playlistcreated created with '.count($videoList).' new videos';
		
		
	  } catch (Google_ServiceException $e) {
		$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
			
	  } catch (Google_Exception $e) {
		$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));			
	 }

    return $newPlaylist['id'];
 }

//	Create a playlist with new videos on $botId Channels from $dateAfter to $dateBefore, return the Id of the new Playlist
function customHarvest($botId, $dateAfter, $dateBefore){
	 global $youtube, $videoList, $htmlBody, $userDatas,$bdd;
			 
	 $chansToHarvest=getChansFromBots($botId);
	
	 
	 $videoList=getsVideosFromChans($chansToHarvest, $dateAfter, $dateBefore);
	
	 try{	
	
	 	$newPlaylist=createPrivPlaylist('testCustom','ben du test');
			
		$videoListToAdd=array();
		foreach($videoList as $video)
		{
			$videoListToAdd=$video['id'];
		}	
		
		AddTabVideosToChans($newPlaylist,$videoListToAdd, $youtube);		
		$bdd->exec('UPDATE robots SET lastHarvest='.time().' WHERE id='.$botId.''  );
		echo'playlistcreated created with '.count($videoList).' new videos';
		
		
	  } catch (Google_ServiceException $e) {
		$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));
			
	  } catch (Google_Exception $e) {
		$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage()));			
	 }
    return $newPlaylist['id'];
 }

// order a videos List from older to earlier release, return the new videoList
function orderVideoList($videoList )
{
    $newVideoList=new SimpleHeap();

    foreach ($videoList as $vid)
    {
        $newVideoList->insert($vid);
    }

    return $newVideoList;
}

// change the $botId LastHarvest value in DataBase with $newDate, return 1 if ok, 0 if fail
function changeLastHarvest($botId,$newDate)
{
    global $bdd;
    $req='UPDATE bot SET lastHarvest='.$newDate.' WHERE id='.$botId.'' ;

    return $bdd->exec($req);

}

// create a empty videolist
function createPrivPlaylist($title, $description){

	global $youtube;

    try{//create snippet
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
        $playlistResponse = $youtube->playlists->insert('snippet,status',  $youTubePlaylist, array());
    }
    catch(exception $e){
        echo $e;
    }
  
	return $playlistResponse;
}


// fill $newPlaylist with the $videoList Videos, return 1 if ok, else 0
function AddTabVideosToChans($newPlaylist,$videoList){

	global $youtube;
	$playlistItemResponse=array();

		foreach($videoList as $video)
		{
			$playlistItemResponse=addVid($video['id']['videoId'],$newPlaylist['id'], $youtube);				
		}	
		return $playlistItemResponse;
}	

// Add the $vidId video to the $playListId playlist(already created), return 1 if ok, else 0
function addVid($vidId, $playlistId)
{

    global $youtube;

    try {
        // defining the resource
        $resourceId = new Google_Service_YouTube_ResourceId();
        $resourceId->setVideoId($vidId);
        $resourceId->setKind('youtube#video');

        //  snippet for the playlist item.
        $playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
        $playlistItemSnippet->setTitle('First video in the test playlist');
        $playlistItemSnippet->setPlaylistId($playlistId);
        $playlistItemSnippet->setResourceId($resourceId);

        //create playlistItem and add it
        $playlistItem = new Google_Service_YouTube_PlaylistItem();

        $playlistItem->setSnippet($playlistItemSnippet);

        $playlistItemResponse = $youtube->playlistItems->insert(
            'snippet,contentDetails', $playlistItem, array());
    }
    catch(exception $e){
       echo $e;
    }
	return $playlistItemResponse;
}


// retrieve $botId Channels from object userDatas, return a channelId Array
function getChansFromBots($botId){
        global $userDatas;
		$chans=array();
		
		foreach($userDatas->getbots()[$botId]['channels'] as $chan)
		{
			$chans[]=$chan['channelId'];
		}
		return $chans;
	}
	
// retrieve video from subscribed channels, plublished between $dateAfter and $dateBefore.( use '-1' instead of $dateBefore if there is not one), return a videos ID Array
function getsVideosFromChans($chansList, $dateAfter, $dateBefore){
		
		$videoList=array();

	  foreach($chansList as $chan)
	{

		$videoListToMerge=searchVidsFromTo($chan,$dateAfter,$dateBefore);
		$videoList=array_merge($videoList, $videoListToMerge);				
	}
	return $videoList;	  
  }
  

  //  retrieve all the videos from channel $chan , published after $dateAfter, return a videos List
function searchVidsFromTo($chan, $dateAfter, $dateBefore){

	global $youtube;	
	$videoList=array();


    try{
        $dateAfterRFC=date('c',$dateAfter);
        if ($dateBefore!='-1')
        {
            $dateBeforeRFC=date('c', $dateBefore);
        }

        if ($dateBefore=='-1')
        {
            $videosResponse=$youtube->search->listSearch('snippet',
                array(
                    'channelId'=>$chan,
                    'publishedAfter'=>$dateAfterRFC,
                    'order'=>'date',
                    'maxResults'=>'50',
                    'type'=>'video'
                )
            );

        }else
        {
            $videosResponse=$youtube->search->listSearch('snippet',
                array(
                    'channelId'=>$chan,
                    'publishedAfter'=>$dateAfterRFC,
                    'publishedBefore'=>$dateBeforeRFC,
                    'order'=>'date',
                    'maxResults'=>'50',
                    'type'=>'video'
                )
            );
        }

        // add Videos to the return array
        foreach($videosResponse['items'] as $vid)
        {
            if ($vid['id']['kind']=='youtube#video' )
            {
                //$videoList[]=array("name"=>$vid[],"id"=>$vid['id']['videoId']);
                $videoList[]=$vid;
            }
        }
    }
    catch (exception $e) {
        echo $e;
    }
		
	return $videoList;
}

// max Heap List
class SimpleHeap extends SplHeap
{

    public function  compare( $value1, $value2 ) {
        $stamp1=strtotime($value1['snippet']['publishedAt']);
        $stamp2=strtotime($value2['snippet']['publishedAt']);

      if ( $stamp1 > $stamp2)
          {
              $resp=-1;
          }
        else if ($stamp1==$stamp2)
        {
            $resp=0;
        }
        else{
            $resp=1;
        }

        return $resp;
    }
}
?>