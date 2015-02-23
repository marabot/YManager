<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a playlist and fill it with videos from channels subscriptions since lastHarvestValue of the bot. return 1 if ok
/////////////////////////////////////////////////////////

require_once '../google-api-php-client/autoload.php';

		// script pour appel AJAX, crée une playlist et la remplit avec les videos des chaines souscrites
session_start();

	include $_SERVER['DOCUMENT_ROOT'] . '/userDatas.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/servicesPHP_SQL/BDDTools.php';
	
	$playlistId;
		$botId=$_GET['id'];
		if (isset($_GET['dateAfter']))
		{
			$dateAfter=strtotime($_GET['dateAfter']);
		}else
		{
			$dateAfter='-1';
		}
		
		if (isset($_GET['dateBefore']))
		{
			$dateBefore=strtotime($_GET['dateBefore']);
		}else
		{
			$dateBefore='-1';
		}
		
		
	//récup du singleton bdd		
	include $_SERVER['DOCUMENT_ROOT'] . '/bdd.php';
	$bdd=bddConnect::getBdd();	
			
	$myChannel=$_SESSION['myChannel'];
	$mysubscriptions=$_SESSION['chansTab'];
	
	// création de l'objet userDatas de l'utilisateur 
	$userDatas=new userDatas($bdd, $myChannel,$mysubscriptions);
	
	$youtube=$_SESSION['youtube'];

/*
    $plId=$youtube->search->listSearch(
                                        'snippet',
                                           array(
                                                'channelId'=>$myChannel,
                                               'publishedAfter'=>$dateAfterRFC,
                                               'maxResults'=>'3'
                                           )
                                        );
*/
	if ($dateBefore=='-1')
	{
		$playlistId=harvest($botId);
	}
    /*
	else{
        $playlistId=customHarvest($botId, $dateAfter, $dateBefore);
	}

$test=searchMyChannel();
echo  $test;
*/

$test=searchMyChannel();

echo  $test;

// temporary
function searchMyChannel() {
    global $youtube, $myChannel;
    $plId=-1;
    $lastPl=0;
    $dateAfterRFC=  date('c', time()-100000);

// TODO : récupérer la dernière playlist
                        $plSearch=$youtube->playlists->listPlaylists(
                            'snippet',
                            array(
                                'mine'=>true,

                            )
                        );
        foreach($plSearch['items'] as $pl)
        {


            if (strtotime($pl['snippet']['publishedAt'])>$lastPl)
            {

                $lastPl=strtotime($pl['snippet']['publishedAt']);
                $plId=$pl['id'];
            }
        }

        return $plId;}

?>