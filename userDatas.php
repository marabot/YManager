
<?php
////////////////////////////// All datas from Database about a user //////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

class userDatas{
	 private $channelId;
	 private $subs=array();
	 private $bots=array();
	 private $bdd;
	 private $botsChannels=array();
	
	public function __construct($_bdd, $userChannelId, $subscriptionsList)
	{
		$this->channelId=$userChannelId;
		$this->bdd=$_bdd;
        $this->subs=$subscriptionsList;
		
		//retrieve bots
		$sqlreqBots=$this->bdd->query('SELECT * FROM robots WHERE userId=\''.$this->channelId.'\'');
		
		if(!$sqlreqBots === FALSE)
			{
				$this->bots=$sqlreqBots->fetchAll();				
			}

		// retrieveBotsDatas
		$req='SELECT * FROM botchannel WHERE botId IN (\'';
		$countForFirst=0;
		foreach($this->bots as $b)
		{

			if ($countForFirst==0)
			{
				
				$req.=$b[0].'\'';
			}
			else
			{
				$req.=',\''.$b[0].'\'';
			}
		$countForFirst++;			
		}
        $req.=')';

		$reqbotChannels=$this->bdd->query($req);

        // make a array with channelID subscription as key of channelbot array
        $tabSubs=array();
        
        /*
        // foreach subscriptions of each bot, create a entry in the bots array to store list state
        foreach($this->getbots() as $bot)
        {
            $this->bots['botChannels']=array();
           // array_push($this->bots,array('botChannels'=>null));
            foreach($subscriptionsList as $sub)
            {
                $this->bots['botChannels'][$sub['snippet']['resourceId']['channelId']]= 0;
            }
        }
*/

		if ($reqbotChannels!=null)
		{
			$this->botsChannels=$reqbotChannels->fetchAll();
            /*
            foreach($this->getbotsChannels() as $bt)
            {
                $this->bots[$bt['botId']]['botChannels'][$bt['channelId']]=$bt['inPlaylist'];
            }
            */
		}

	}
	
	//  return 1 if $channelId is in the Channels to harvest by $botId, else return 0
	public function isInBotList($userdatas,$channelId, $botId){


        $resp=0;

        foreach ($userdatas->getbotsChannels() as $botChan)
        {

            if ($botChan['channelId']==$channelId && $botChan['botId']==$botId )
            {
                $resp=$botChan['inPlaylist'];
            }
        }
        return $resp;
    }

    // set the $botId Lastharvest value in the database to now. return 1 if succeed, else 0
	public function setLastHarvestNow($botId)
	{
		global $bdd;
		$resp=$bdd->exec('ALTER robots SET lastharvest=\''.time().'\' WHERE id=\''.$botId.'\'');
		if ($resp==1)
		{
			return 1;			
		}else
		{
			return 0;
		}
		
	}
	
	// create a bot, and create all the channalbot entries about the user. If ok, return the new bot id, else return -1
	public function createSimpleBot($name){	
			
			global $bdd, $myChannel,$mysubscriptions, $bots;
			$dateCrea=time();
						
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
						}				
					
					foreach($mysubscriptions as $chan)			{					
						$reqcreatechanbot=$bdd->exec('INSERT INTO botchannel (botId, channelId)
																	VALUES (\''.$bot.'\',\''
																				.$chan.'\')'
															);													
						//echo '<br>insert channelbot ? : '.$reqcreatechanbot;
					}	
				}			
			catch(exception $e)
				{
					return '-1';
					
				}
			
			return $bot;
	}

	// GETTER
	public function getChannelId()	{
		return $this->channelId;
	}
	public function getSubscriptions(){
		return $this->subs;
	}
	public function getbots(){
		return $this->bots;
	}
	public function getbotsChannels(){
		return $this->botsChannels ;
	}
		
}		
?>