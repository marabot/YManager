<?php

	include $_SERVER['DOCUMENT_ROOT'].'/userDatas.php';
	include $_SERVER['DOCUMENT_ROOT'].'/bdd.php';	
	include $_SERVER['DOCUMENT_ROOT'] . '/servicesPHP_SQL/BDDTools.php';

    // retrieve Database Connection singleton
	$bdd=bddConnect::getBdd();
	
	//retrieve user channelID
	$myChannel=getMyChannelId($youtube);	
	$_SESSION['myChannel']=$myChannel;
	
	//retrieve user subscriptions
	$subscriptionsList=mySubscriptions();
	$_SESSION['chansTab']=$subscriptionsList;
    $subscriptionsIds=array();

	foreach ($subscriptionsList as $sub)
	{
		$subscriptionsIds[]=$sub['snippet']['resourceId']['channelId'];
	}
	//var_dump($subscriptionsIds);
	
	// check is user is registered in database, if not, register him
	checkIsDbUser($myChannel, $bdd);
	
	// create userDatas object
	$userDatas=new userDatas($bdd, $myChannel,$subscriptionsList);

			
	/////////////  display robots list
	$bots=$userDatas->getBots();
	$botChannels=$userDatas->getbotsChannels();
	affBots($bots);	

    /////////////display subscriptions list
    affSubscriptions($subscriptionsList);


//  temporary storage by display the value in html with background color
if ($userDatas->getBots())	{
    $bots=$userDatas->getBots();
    $subs.='<span id="tempId" ><font color="white">'.$bots[0]['id'].'   </font></span>';
}

/////////////display subscriptions list
function affSubscriptions($subscriptionsList)
 {
     global $subs, $botChannels, $userDatas, $bots;

     $testArray=array('nom' => 'bi', 'prenom'=> 'bu');


     $subs.="<h3>your subscriptions :</h3>   <ul>";
     foreach($subscriptionsList as $sub)
     {
        // $inList=$userDatas->isInBotList($userDatas,$sub['snippet']['resourceId']['channelId'], $bots[0]['id']);


         $subs.='<div>'.$sub['snippet']['title'].'</div>';
/*
         if ($inList)
         {
             $subs.=$inList.'</div><br>';
         }else
         {

         }
*/
     }
 }

/// return an array with all datas about user bots
function getMyBots(){
	global $myChannel, $bdd;
	$sqlreqBots=$bdd->query('SELECT * FROM robots WHERE userId=\''.$myChannel.'\'');
		
	if($sqlreqBots === FALSE) { 
    die('merde dans la requete robots, ou 0 résultats'); // TODO: erreur à gérer
	}
	
	return $sqlreqBots;
}

///  display robots list
function affBots($tabBots){
	global $htmlBody, $subscriptionsIds, $infoBot;
	
	if ($tabBots!=null)
	{
		
		$htmlBody.='<table class="table table-hover"><tr ><th>Last playlist </th><th></th></tr>';
		foreach($tabBots as $row)
		{
				$newVids=getsVideosFromChans($subscriptionsIds, $row['lastHarvest'],'-1');
				
				$newVidsCount=count($newVids);
				$htmlBody.='<tr>';
                // <td>'.$row['name'].'</td>'
                //

                $htmlBody.='<td>'.date('l jS \of F Y h:i:s A',$row['lastHarvest']).'</td>';
				
				if(count($newVids)==0)
				{
					$htmlBody.='<td><button class="btn btn-default" id="harvest'.$row['id'].'" disabled="disabled">There is no new video</button></td><div id="createBotResp"></div>';
				}
				else{
					$htmlBody.='<td><button class="btn btn-default" id="harvest'.$row['id'].'">Make a playlist with all new videos : '.$newVidsCount.'</button></td><br><div id="createBotResp">(could take some dozen of seconds if many videos)</div>';

                    $infoBot.='<ul><br>';
					foreach($newVids as $vid)
					{
						$infoBot.='<li><div class="text-left">'.$vid['snippet']['title'].'</div></li>';
					}

                    $infoBot.='</ul></td>';
				}
            $htmlBody.='</tr>';
		}
	$htmlBody.='</table>';
		
	}
	else
	{
		$htmlBody.='<div id="createBotContainer"><button class="btn btn-default" id="createbot">Create a bot for all subscriptions</button></div>';
	}
	
}

// return an array with all datas about user subscriptions
function mySubscriptions(){
	global $youtube;
	$channelsResponse=$youtube->subscriptions->listSubscriptions('snippet', array('mine' => true, 'maxResults'=>'50'));
	
	$subscriptionsList=array();
		
	// ajout des chaine aux tbleaux des chaines
	foreach($channelsResponse['items'] as $channel)
	{
		$subscriptionsList[]=$channel;
	//	$htmlBody.='<li>'.$channel['snippet']['resourceId']['channelId'].' : '.$channel['snippet']['description'].'</li>';
	}	
	
	return $subscriptionsList;
}

// return user channelId
function getMyChannelId(){	

	global $youtube;
	$myChanResponse=$youtube->channels->listChannels('id', array('mine'=>true));
	$myChan=$myChanResponse['items']['0']['id'];
	
	return $myChan;
}

// check is user is registered in database, if not, register him
function checkIsDbUser($myChan, $bdd){		

		$sqlreq=$bdd->query('SELECT * FROM user WHERE channelid=\''.$myChan.'\'');
		
		$result=$sqlreq->fetch();
		
		if (!isset($result['channelid']))
		{			
			$sqlreq=$bdd->query('INSERT INTO user(channelid) VALUES (\''. $myChan  . ' \' )' );
		}		
	}


	  ?>