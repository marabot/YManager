<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a bot for the connected user. return 1 if ok
/////////////////////////////////////////////////////////
session_start();

	include $_SERVER['DOCUMENT_ROOT'] . '/userDatas.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/servicesPHP_SQL/BDDTools.php';
	
	$botId=$_GET['id'];
	$newDate=strtotime($_GET['newDate']);
		//retrieve Database Connection Singleton
	include $_SERVER['DOCUMENT_ROOT'] . '/bdd.php';
	$bdd=bddConnect::getBdd();	
	
	echo (changeLastHarvest($botId, $newDate));   	
?>