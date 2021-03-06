
<?php
////////////////////////////// All datas from Database about a user //////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

class userDatas{
	 private $channelId;
	 private $subs=array();
	 private $bots=array();   // 0 : id (int)   1: userId (string)   2: lastHarvest (timestamp)   3:name(string)  4 createdate (timestamp)  5 : channels (array)
	 private $bdd;

	
	public function __construct($_bdd, $userChannelId, $subscriptionsList)
	{
		$this->channelId=$userChannelId;
		$this->bdd=$_bdd;
        $this->subs=$subscriptionsList;
		
		//retrieve bots
		$sqlreqBots=$this->bdd->query('SELECT * FROM bot WHERE user_id=\''.$this->channelId.'\'');
		
		if(!$sqlreqBots === FALSE)
			{
                $botsTemp=$sqlreqBots->fetchAll();


                foreach($botsTemp as $b)
                {


                    $this->bots[$b['id']]=array(  'id'=>$b['id'],
                                                'user_id'=> $b['user_id'],
                                               'lastHarvest'=>$b['lastHarvest'],
                                                'createDate'=>$b['createDate'],
                                                'name'=>$b['name']


                        );

                }
            }

		// retrieveBotsDatas
		$req='SELECT * FROM botChannel WHERE botId IN (\'';
		$countForFirst=0;
		foreach($this->bots as $b)
		{

			if ($countForFirst==0)
			{
				
				$req.=$b['id'].'\'';
			}
			else
			{
				$req.=',\''.$b['id'].'\'';
			}
		$countForFirst++;			
		}
        $req.=')';
        //var_dump($req);
		$reqbotChannels=$this->bdd->query($req);
        //var_dump($reqbotChannels);

		if ($reqbotChannels!=null)
		{
            $botsChannelsDatas=$reqbotChannels->fetchAll();
			//$this->botsChannels=$reqbotChannels->fetchAll();

            foreach($botsChannelsDatas as $bt)
            {
                $this->bots[$bt['botId']]['channels'][]=array('channelId'=>$bt['channelId'],'channelTitle'=>$bt['title']);
            }
		}
    }

    // set the $botId Lastharvest value in the database to now. return 1 if succeed, else 0
	public function setLastHarvestNow($botId)
	{
		global $bdd;
		$resp=$bdd->exec('UPDATE bot SET lastHarvest=\''.time().'\' WHERE id=\''.$botId.'\'');
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
				$sqlreqCreateBot=$bdd->exec('INSERT INTO bot(user_id,
															lastHarvest,
															createdate, 
															name
															 ) 
												VALUES (\''.$myChannel.'\',\''
															.$dateCrea.'\',\''
															.$dateCrea.'\',\''
															.$name .'\') '
															);
															
																											
					$sqlreqBotId=$bdd->query('SELECT id FROM bot
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

		
}		
?>