<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a playlist for the connected user on his youtube account. return 1 if ok
/////////////////////////////////////////////////////////


//récup du singleton bdd
include $_SERVER['DOCUMENT_ROOT'] . '/db.php';
$bdd=dbConnect::getDb();

$botId=$_GET['botId'];

$reqDelBot=('DELETE FROM bot WHERE (id=\''.$botId.'\')');

$bdd->exec($reqDelBot);

$reqDelChansBot=('DELETE FROM botChannel WHERE (botId=\''.$botId.'\')');


?>