<?php

function harvest($botId) {
	 global $videoList;


	 $chansToHarvest=getChansFromBots($botId); // récupération des chaines pour un bot spécifique, dans le but de lancer une récolte

	 $dateAfter=findLastharvest($botId);
	
	 $videoList=getsVideosFromChans($chansToHarvest, $dateAfter, '-1');	// tableau des videos
     $titre='videos du '.date('r',$dateAfter).' au '.date('r');

    $videoList=orderVideoList($videoList);

	 try{
		$newPlaylist=createPrivPlaylist($titre,' ');

		//var_dump($videoList);
		AddTabVideosToChans($newPlaylist,$videoList);	
		$reqHarvestChange=changelastHarvest($botId, time());
		//echo'playlistcreated created with '.count($videoList).' new videos';
		$return=$newPlaylist['id'];

		
	  } catch (Google_ServiceException $e) {
		$return="<p>A service error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage())";
			
	  } catch (Google_Exception $e) {
         $return= "'<p>An client error occurred: <code>%s</code></p>',
			htmlspecialchars($e->getMessage())";
	 }

    //return $return;
    return $reqHarvestChange;
 }

/*
function customHarvest($botId, $dateAfter, $dateBefore){
	 global $youtube, $videoList, $htmlBody, $userdatas,$bdd;	
			 
	 $chansToHarvest=getChansFromBots($botId); // récupération des chaines pour un bot spécifique, dans le but de lancer une récolte
	
	 
	 $videoList=getsVideosFromChans($chansToHarvest, $dateAfter, $dateBefore);	// tableau des videos
	
	 try{	
	
	 	$newPlaylist=createPrivPlaylist('testCustom','ben du test');
			
		$videoListToAdd=array();
		foreach($videoList as $video)
		{
			$videoListToAdd=$video['id'];
		}	
		
		AddTabVideosToChans($newPlaylist,$videoListToAdd, $youtube);		
		$bdd->exec('UPDATE bot SET lastHarvest='.time().' WHERE id='.$botId.''  );
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
*/

// classe une liste de vidéo de la plus ancienne à la plus récente
function orderVideoList($videoList )
{
    $newVideoList=new SimpleHeap();

    foreach ($videoList as $vid)
    {
        $newVideoList->insert($vid);
    }

    return $newVideoList;
}

function changeLastHarvest($botId,$newDate)
{
    global $bdd;

    $botId=(int)$botId;
    $newDate=(int)$newDate;
    $req='UPDATE bot SET lastharvest='.trim($newDate).' WHERE id='.trim($botId) ;

    //return $bdd->exec($req);
    return $req;
}


function createPrivPlaylist($title, $description){
	//var_dump ('passe par là');
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
	$playlistResponse = $youtube->playlists->insert('snippet,status',  $youTubePlaylist, array());
  
	return $playlistResponse;
}
function findLastharvest($botId){
	global $userdatas;

	foreach($userdatas->getBots() as $bot)
	{		
		if ($bot[0]==$botId)
		{
			$lastHarvest=$bot[2];
			
		}
		return $lastHarvest;
	}	
}
//remplissage de la playlist $newplaylist avec les vidéos du tableau $videoList
function AddTabVideosToChans($newPlaylist,$videoList){

	global $youtube;
	$playlistItemResponse=array();
		// remplissage de la playlist
		foreach($videoList as $video)
		{
				//var_dump($video['id']['videoId']);
			$playlistItemResponse=addVid($video['id']['videoId'],$newPlaylist['id'], $youtube);				
		}	
		return $playlistItemResponse;
}	

function addVid($vidId, $playlistId){
	
	global $youtube;
	
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
	
	return $playlistItemResponse;

}
function getChansFromBots($botId){
		global $userdatas;
		$chans=array();
		
		foreach($userdatas->getbotsChannels() as $botChannel)
		{
			
			if ($botChannel[0]==$botId){
			
			$chans[]=$botChannel[1];
			}
		}		
		
		return $chans;
	}
	
// recupère les vidéos des chaines de $chanList, publiées entre $dateAfter et $dateBefore (mettre -1 si pas de $datebefore), renvoie un tableau d'id de vidéos
function getsVideosFromChans($chansList, $dateAfter, $dateBefore){
		
		$videoList=array();

	  foreach($chansList as $chan)
	{
		//$htmlBody.='-----------------------<br><div>channel : '.$chan.'</div>';			
		// récupération des vidéos pour la chaine $chan
		$videoListToMerge=searchVidsFromTo($chan,$dateAfter,$dateBefore);	
		//$videoListToMerge=searchVidsFromTo($chan['id'],$dateAfter,$dateBefore);	
		
		$videoList=array_merge($videoList, $videoListToMerge);				
	}
	
	return $videoList;	  
  }
  

  // retourne un tableau des id des videos d'une chaine , publiées après $dateAfter
function searchVidsFromTo($chan, $dateAfter, $dateBefore){
	//var_dump($chan);
	global $youtube;	
	$videoList=array();
	   
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
	//var_dump($videosResponse['items']);
		// ajout des vidéos au tableau des vidéos		
		foreach($videosResponse['items'] as $vid)
		{					 
			if ($vid['id']['kind']=='youtube#video' )
			{					
				//$videoList[]=array("name"=>$vid[],"id"=>$vid['id']['videoId']);		
				$videoList[]=$vid;		
			}					
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