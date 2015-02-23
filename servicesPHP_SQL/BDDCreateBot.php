<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a bot for the connected user. return 1 if ok
/////////////////////////////////////////////////////////

require_once '../google-api-php-client/autoload.php';
$mysubscriptions;

session_start();

 // retrieve Database Connection Singleton
	include $_SERVER['DOCUMENT_ROOT'] . '/bdd.php';
	$bdd=bddConnect::getBdd();	
			
	$myChannel=$_SESSION['myChannel'];
	$mysubscriptions=$_SESSION['chansTab'];
	
	createBot('MainBot');
		
	function createBot($name){		
			
			global $bdd, $myChannel,$mysubscriptions;
			$dateCrea=time();
			$name='Main Bot';
			$bot=-1;
			
			$sqlreqCreateBot=$bdd->exec('INSERT INTO robots(userId,
															lastHarvest,
															createdate, 
															name
															 ) 
												VALUES (\''.$myChannel.'\',\''
															.$dateCrea.'\',\''
															.$dateCrea.'\',\''
															.$name .'\') '
															);
															
			$sqlreqBotId=$bdd->query('SELECT id FROM robots
												WHERE userId=\''.$myChannel.'\' AND
													  createdate=\''.$dateCrea.'\'');
			$row=$sqlreqBotId->fetch();
			
			if (isset($row['id']))
				{			
						$bot=$row['id'];					
				}

			foreach($mysubscriptions as $chan)
			{
				$reqcreatechanbot=$bdd->exec('INSERT INTO botchannel (botId, channelId, inPlaylist)
															VALUES (\''.$bot.'\',\''.$chan['snippet']['resourceId']['channelId'].'\',true)'
													);

			}
			return $sqlreqCreateBot;
	}			
	
	function fixObject ($object)
{
  if (!is_object ($object) && gettype ($object) == 'object')
    return ($object = unserialize (serialize ($object)));
  return $object;
}
?>