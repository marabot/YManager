<?php
/////////////////////////////////////////////////////////
// PHP Script to bel call with XMLHttpRequest
// create a playlist for the connected user on his youtube account. return 1 if ok
/////////////////////////////////////////////////////////


//récup du singleton bdd
include $_SERVER['DOCUMENT_ROOT'] . '/bdd.php';
$bdd=bddConnect::getBdd();

$botId=$_GET['botId'];

$reqDelBot=('DELETE FROM robots WHERE (id=\''.$botId.'\')');

$bdd->exec($reqDelBot);

$reqDelChansBot=('DELETE FROM botChannel WHERE (botId=\''.$botId.'\')');


?>