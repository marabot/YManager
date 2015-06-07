<?php
// retrieve Database Connection Singleton
include $_SERVER['DOCUMENT_ROOT'] . '/db.php';
$bdd=dbConnect::getDb();

session_start();

$chanId=substr($_GET['chanId'],10 );
$botId=$_GET['botId'];

$req=('DELETE FROM botchannel WHERE ( channelId=\''.$chanId.'\' AND botId=\''.$botId.'\')');
echo $req;
$sqlreqDelChanBot=$bdd->exec($req);

return $sqlreqDelChanBot;
?>