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
	include $_SERVER['DOCUMENT_ROOT'] . '/db.php';
	$bdd=dbConnect::getDB();
			
	$myChannel=$_SESSION['myChannel'];
	$mysubscriptions=$_SESSION['chansTab'];
	
	// création de l'objet userDatas de l'utilisateur 
	$userDatas=new userDatas($bdd, $myChannel,$mysubscriptions);
	
	$youtube=$_SESSION['youtube'];

	if ($dateBefore=='-1')
	{
		$playlistId=harvest($botId);
	}




//echo  $playlistId;

echo"bliiii";

?>