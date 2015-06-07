<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a bot for the connected user. return 1 if ok
/////////////////////////////////////////////////////////

require_once '../google-api-php-client/autoload.php';
$mysubscriptions;

session_start();

 // retrieve Database Connection Singleton
	include $_SERVER['DOCUMENT_ROOT'] . '/db.php';
	$bdd=dbConnect::getDb();
			
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
			$dateCreation=time();
			$bot=-1;

        try{
            $bdd->exec('INSERT INTO bot (name,
															lastHarvest,
															createDate,
															user_id
															 )
												VALUES (\''.$name.'\',\''
                .$dateCreation.'\',\''
                .$dateCreation.'\',\''
                .$_SESSION['myChannel'] .'\') '
            );

            $sqlreqBotId=$bdd->query('SELECT id FROM bot
												WHERE user_id=\''.$myChannel.'\' AND
													  createDate=\''.$dateCreation.'\'');

           $row=$sqlreqBotId->fetch();

            if (isset($row))
            {
                $bot=$row[0];
                echo $row[0]; // return of this php script  : botId
            }

            foreach($chans as $chan)
            {
                $bdd->exec('INSERT INTO botChannel (botId, channelId, title)
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