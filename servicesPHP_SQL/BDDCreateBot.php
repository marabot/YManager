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
	$chansTemp=explode(';',$_GET['chansTab']);
    $name=$_GET['name'];

    $i;
    for($i=0;$i<count($chansTemp);$i=$i+2)
    {
        $chans[$i/2]=array('channelId'=>$chansTemp[$i], 'title'=>$chansTemp[$i+1]);
    }

	createBot($name);
		
	function createBot($name){		
			
			global $bdd, $myChannel,$chans;
			$dateCrea=time();
			$bot=-1;

        try{
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
                echo $row['id']; // return of this php script  : botId
            }

            foreach($chans as $chan)
            {
                $bdd->exec('INSERT INTO botchannel (botId, channelId, title)
															VALUES (\''.$bot.'\',\''.$chan['channelId'].'\',\''.$chan['title'] .'\')'
                );
            }
        }
        catch(exception $e){
            echo $e;
        }

	}			
	
	function fixObject ($object)
{
  if (!is_object ($object) && gettype ($object) == 'object')
    return ($object = unserialize (serialize ($object)));
  return $object;
}
?>