<?php

	include $_SERVER['DOCUMENT_ROOT'].'/userDatas.php';
	include $_SERVER['DOCUMENT_ROOT'].'/db.php';
	include $_SERVER['DOCUMENT_ROOT'] . '/servicesPHP_SQL/BDDTools.php';



    // retrieve Database Connection singleton
	$bdd=dbConnect::getDB();

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

	affBots($bots);

    /////////////display subscriptions list
    affSubscriptions($subscriptionsList);
    affLastHarvestTool();



/////////////display subscriptions list
function affSubscriptions($subscriptionsList)
 {
     global $subs, $userDatas, $bots;

    $subs.='<table id=""subs">';

     $subs.="<h3>your subscriptions :</h3>   <ul>";
     foreach($subscriptionsList as $sub)
     {
         $subs.='<tr ><td>'.$sub['snippet']['title'].'</td><td><img class="selectSubsForNewBot" id="'.$sub['snippet']['resourceId']['channelId'].'" src="res/unchecked_checkbox.png"/></td></tr>';
     }
     $subs.='<tr><td></br>ALL</td><td></br><img class="selectSubsForNewBot" id="all" src="res/unchecked_checkbox.png"/></td></tr></table>';
     $subs.='<br>bot name :<div><input type="text" id="createName" size="30"></div><div><button class="btn btn-default createBot" id="createBot">Create a bot with checked subscriptions</button></div>';
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
      global $htmlBody, $newVids, $newVidsContainer, $botChannels;

		
		$htmlBody.='<table class="table table-hover" id="listBots"><tr><th>Name</th><th>Last playlist </th><th>new Videos</th><th></th></tr>';
    if ($tabBots!=null)
    {
		foreach($tabBots as $row)
		{
            $subscriptionsIds=array();

            // retrieve the chans of the bot
            foreach ($row['channels'] as $sub)
            {
                $subscriptionsIds[]=$sub['channelId'];
            }

				$newVids=getsVideosFromChans($subscriptionsIds, $row['lastHarvest'],'-1');
				
				$newVidsCount=count($newVids);
				$htmlBody.='<tr class="selectBot" id="selectBot'.$row['id'].'"><td>'.$row['name'].'</td>';

                //<td><img id="selectBot'.$row['id'].'" src="res/unchecked_checkbox.png"/></td>    => case à cocher


                $htmlBody.='<td>'.date('l jS \of F Y h:i:s A',$row['lastHarvest']).'</td><td>'.count($newVids).'</td><td><img class="delBot" src="res/delete_icon.png" /></td></tr>';


                if(count($newVids)==0)
                {
                    $newVidsContainer.='<table class="botNewVids hide" id="botNewVids'.$row['id'].'">
                                                    <tr>
                                                            <td><button class="btn btn-default" id="harvest'.$row['id'].'" disabled="disabled">There is no new video</button></td>

                                                    </tr>';
                }
                else{
                    $newVidsContainer.='<table class="botNewVids hide" id="botNewVids'.$row['id'].'">
                                                            <tr>
                                                                <td><button class="btn btn-default harvest" id="harvest'.$row['id'].'">Make a playlist with all new videos : '.$newVidsCount.'</button></td>

                                                            </tr>';

                    $newVidsContainer.='<tr><td class="newVids" id="'.$row['id'].'"><ul>';
                    foreach($newVids as $vid)
                    {
                        $newVidsContainer.='<li><div class="text-left">'.$vid['snippet']['title'].'</div></li>';
                    }
                    $newVidsContainer.='</ul></td></tr>';
                }

            $newVidsContainer.='</table>';
            $htmlBody.='</tr>';

            $botChannels.=affBotChannels($row['id']);
		}
	}
	else
	{
		$htmlBody.='<tr id="createBotAllRow"><td><div id="createBotContainer"><button class="btn btn-default" id="createBotAll">Create a bot for all subscriptions</button></div></td></tr>';
	}
    $htmlBody.='</table>';
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
	}	
	
	return $subscriptionsList;
}

// return html listed of $botId channels, ready to be display
function affBotChannels ($botId)
{
    $resp='';
    global $userDatas;
    $resp.='<div class="botChannels hide" id="botChannels'.$botId.'"><table class="botChans">';

    foreach($userDatas->getBots()[$botId]['channels'] as $c)
    {
        $resp.='<tr id="'.$c["channelId"].$botId.'"><td><div>'.$c["channelTitle"].'</div></td><td><img class="delBotChan" id="delBotChan'.$c["channelId"].'" name="'.$botId.'" src="res/delete_icon.png"/></td></tr>';
    }

    $resp.='</table></div>';
    return $resp;
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


		if ($sqlreq->fetch()==null)
		{
			$sqlreq=$bdd->query('INSERT INTO user(channelId) VALUES (\''. $myChan  . ' \' )' );
		}else
        {

            $result=$sqlreq->fetch();

        }
	}

// display a datepicker and a button to change the lastHarvestvalue of a bot in Database
function affLastharvestTool(){
    global $testTools, $bots;
    $testTools.='
        	<span> For Test :Modify lastHarvest Date in database :</span>
				<div id="lastHarvestDatePicker">
					<div class="col-md-2 input-group date" id="datePicker">
						<input type="text" class="form-control" id="newlastharvest"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
					</div>
					 <select id="botToChange">';

    foreach($bots as $b)
    {
        $testTools.='<option value="'.$b['id'] .'">'.$b['name'].'</option>';
    }


    $testTools.=' </select>
					<button  class="btn btn-default" id="changeLastHarvest">change lastharvest Date </button>
					<span id="changelastHarvestresult"></span>
				</div>
        ';
}
	  ?>