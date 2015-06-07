function onPlayerReady(event) {
    event.target.playVideo();
}


$('document').ready(function (){

    $('#lastHarvestDatePicker .input-group.date').datepicker({
    });

    $('#dateAfter .input-group.date').datepicker({
    });

    $('#dateBefore .input-group.date').datepicker({
    });

    $('#datePicker')
        .datepicker({
            format: 'mm/dd/yyyy'
        })
        .on('changeDate', function(e) {
            // Revalidate the date field
            $('#eventForm').formValidation('revalidateField', 'date');
        });

    $('#datePicker2')
        .datepicker({
            format: 'mm/dd/yyyy'
        })
        .on('changeDate', function(e) {
            // Revalidate the date field
            $('#eventForm').formValidation('revalidateField', 'date');
        });

    $('#eventForm').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            name: {
                validators: {
                    notEmpty: {
                        message: 'The name is required'
                    }
                }
            },
            date: {
                validators: {
                    notEmpty: {
                        message: 'The date is required'
                    },
                    date: {
                        format: 'MM/DD/YYYY',
                        message: 'The date is not a valid'
                    }
                }
            }
        }
    });
});

// handler delete ChannelsButton
$('#lienDrop').click(function()
{
    $text=$(this).find('a').text();

    $('#dropResult').text($text);
}) ;


// handler select bot checkBox
$('img').click(function(){
                $id=$(this).attr('id');
                $botId=$id.substring(9, ($id.length));
                if ($id.substring(0,9)=='selectBot') {

                    // voir pour utiliser les classes pour reconnaitre les boutons
                    //$('#selectBot


                }

});

// handler changeLastharvest Button
  $('#changeLastHarvest').click(function(){
										
									$botToChange=$('#botToChange').val();
									$newDate=$('#newlastharvest').val();
									
									
									
									$.get(
										'servicesPHP_SQL/BDDChangeLastHarvest.php',
										{id:$botToChange,newDate:$newDate},
										function ($resp)
										{
                                            if ($resp==1)
                                            {
                                                $('#changelastHarvestresult').html('lastharvest date changed to '+$newDate+ ' for the bot with id '+$botToChange +' --->  refresh  browser to see the playlist available with new lastharvestDate');
                                            }
                                            else
                                            {
                                                $('#changelastHarvestresult').html('lastharvest date couldn\'t be changed');
                                            }

										}										
									)								
									
									});


/*
 $('button').click(function(){																	
									
										$id=$(this).attr('id');		
										$dateAfter=$('#date').val();
										$dateBefore=$('#date2').val();
										// TODO prendre en compte la dropdownbox
											$botId=$id.substring(14, $id.length);
										
								
										if ($id.substring(0,14)=='customPlaylist')
										{
														
												$.get(
													'servicesPHP_SQL/BDDPlaylist.php',
													{ id: $botId, dateAfter:$dateAfter, dateBefore:$dateBefore },
													function ($resp)
													{
														$('#createBotResp').html($resp);											
													}
												)														
										}
	 
										});
  */

// handler clear all playlist button
	$('#clearplaylists').click(function (){

										$.get(
											'servicesPHP_SQL/BDDdeletePlaylists.php',
												function($resp){
														$('#clearplaylistsResp').html($resp);
												}									
											)	
									});
							
	// handler harvest button
	$('.harvest').click(function ()
							{
								$id=$(this).attr('id');		
								$botId=$id.substring(7, ($id.length));
								if ($id.substring(0,7)=='harvest')
								{
									$.get(
										'servicesPHP_SQL/BDDPlaylist.php',
										{ id: $botId },
										function ($resp)
										{

                                            $botVidContainer='#botNewVids'+$botId;
                                         $($botVidContainer).html('<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed?listType=playlist&list='+ $resp.trim()+'" frameborder="0"/>');
                                            //window.open('https://www.youtube.com/watch?v=_IrMqQkR8cU&list='+$resp);
                                             $('#title').html($resp);
										}
									)					
								}
							});



$('.selectSubsForNewBot').click(function(){
    if ($(this).attr('src')=='res/unchecked_checkbox.png')
    {
        $(this).attr('src', 'res/checked_checkbox.png');
    }else{
        $(this).attr('src', 'res/unchecked_checkbox.png');
    }

    if ($(this).attr('id')=='all'){
        $newState=$(this).attr('src');
        $('.selectSubsForNewBot').each(function(){
           $(this).attr('src',$newState);
        });
    }
    else{
            $('#all').attr('src','res/unchecked_checkbox.png');
    }
});

// handler delete BotChannel Button
$('body').on('click', '.delBotChan',function(){
        $chanId=$(this).attr('id');
        $botId=$(this).attr('name');

    $.get(
        'servicesPHP_SQL/BDDDeleteBotChan.php',
        {chanId: $chanId, botId: $botId},
        function ($resp){
                $('#botChannels').append('<div>delete resp :'+$resp+'</div>');
            $toRemove='#'+$chanId.substring(10)+$botId;

            $($toRemove).remove();

           //$('.botChan').remove(":contains('.$.')");

        }
    );
});

// handler delete bot button
$('body').on('click','.delBot',function(){
            $botToDel=$(this).parent().parent().attr('id').substring(9);

            $.get('servicesPHP_SQL/BDDDeleteBot.php',
                {botId : $botToDel},
                function ($resp){
                    $selectToHide='#selectBot'+ $botId;
                    $botChanToHide='#botChannels' +$botId;
                    $newVidsToHide='#botNewVids'+$botId;

                    $($selectToHide).remove();
                    $($botChanToHide).remove();
                    $($newVidsToHide).remove();

                }
            );
});

$('body').on('click','#createBotAll',function(){
                   $tabChan = '';
                   $name='All subscriptions';
                    $first = true;
                    $preListChanshtml='';

                    $('.selectSubsForNewBot').each(function(){

                        if ($(this).attr('id')!='all'){
                            if (!$first){

                                $tabChan=$tabChan+';'+$(this).attr('id')+';'+$(this).parent().parent().text();
                                $preListChanshtml+='<tr id="'+$(this).attr('id')+'"><td><div>'+$(this).parent().parent().text()+'</div></td><td><img class="delBotChan" id="delBotChan'+$(this).attr('id')+'" src="res/delete_icon.png"/></td></tr>';
                            }else{

                                $tabChan=$(this).attr('id')+';'+$(this).parent().parent().text();
                                $first=false;
                                $preListChanshtml+='<tr><td><div>'+$(this).parent().parent().text()+'</div></td><td><img class="delBotChan" id="delBotChan'+$(this).attr('id')+'" src="res/delete_icon.png"/></td></tr>';
                            }

                        }

                        });



                            $.get(
                                'servicesPHP_SQL/BDDCreateBot.php',
                                {chansTab : $tabChan, name : $name},
                                function ($code_html){

                        $listChansHtml='<div class="botChannels" id="botChannels'+ $.trim($code_html)+'"><table class="botChans">'+$preListChanshtml+'</table></div></div>';
                        $newVidsHtml='<table class="botNewVids hide" id="botNewVids'+ $.trim($code_html)+'"><tr><td><button class="btn btn-default" id="harvest' +$.trim($code_html)+'" disabled="disabled">There is no new video</button></td><div id="createBotResp"></div></tr></table></table>';

                        $('#listBots').append('<tr class="selectBot" id="selectBot'+$.trim($code_html)+'"><td>'+$name+'</td><td>just created</td><td>0</td><td><img class="delBot" src="res/delete_icon.png" /></td></tr>');
                        $('#botsChannels').append($listChansHtml);
                        $('#botsChannels').find('img').attr('name', $code_html);

                        $('#botChans').find('tr').each(function(){
                            $newId=$(this).attr('id')+$code_html;
                            $(this).attr('id',$newId);

                            $botChansCont='#botChannels'+$code_html;
                            $botNewVidsCont='#botNewVids'+$code_html;

                            $('.botNewVids').addClass('hide');
                            $('.botChannels').addClass('hide');
                            $($botChansCont).removeClass('hide');
                            $($botNewVidsCont).removeClass('hide');
                        });

                        $('#vidPlayer').append($newVidsHtml);
                        $('#botToChange').add('<option value="'+ $.trim($code_html) +'">'+$name+'</option>');
                        $('#createBotAllRow').remove();
                    });


 });

// handler createBot button wiht selected subscriptions
$('body').on('click','#createBot',function(){

    $name=$('#createName').val();
    if ($name=='') $name='No Name';
    $first='true';
    $preListChanshtml='';

    $('.selectSubsForNewBot').each(function(){

        if ($(this).attr('src')=='res/checked_checkbox.png'){
            if ($first=='false'){
                $resp=$resp+';'+$(this).attr('id')+';'+$(this).parent().parent().text();
                $preListChanshtml+='<tr id="'+$(this).attr('id')+'"><td><div>'+$(this).parent().parent().text()+'</div></td><td><img class="delBotChan" id="delBotChan'+$(this).attr('id')+'" src="res/delete_icon.png"/></td></tr>';
            }else{

                $resp=$(this).attr('id')+';'+$(this).parent().parent().text();
                $first='false';
                $preListChanshtml+='<tr><td><div>'+$(this).parent().parent().text()+'</div></td><td><img class="delBotChan" id="delBotChan'+$(this).attr('id')+'" src="res/delete_icon.png"/></td></tr>';
            }
        }
    }
    );


    $.get(
        'servicesPHP_SQL/BDDCreateBot.php',
        {chansTab : $resp, name : $name},
        function ($code_html){

            $listChansHtml='<div class="botChannels" id="botChannels'+ $.trim($code_html)+'"><table class="botChans">'+$preListChanshtml+'</table></div></div>';
            $newVidsHtml='<table class="botNewVids hide" id="botNewVids'+ $.trim($code_html)+'"><tr><td><button class="btn btn-default" id="harvest' +$.trim($code_html)+'" disabled="disabled">There is no new video</button></td><div id="createBotResp"></div></tr></table></table>';

            $('#listBots').append('<tr class="selectBot" id="selectBot'+$.trim($code_html)+'"><td>'+$name+'</td><td>just created</td><td>0</td><td><img class="delBot" src="res/delete_icon.png" /></td></tr>');
            $('#botsChannels').append($listChansHtml);
            $('#botsChannels').find('img').attr('name', $code_html);
            $('#botChans').find('tr').each(function(){
                $newId=$(this).attr('id')+$code_html;
                $(this).attr('id',$newId);

                $botChansCont='#botChannels'+$code_html;
                $botNewVidsCont='#botNewVids'+$code_html;

                $('.botNewVids').addClass('hide');
                $('.botChannels').addClass('hide');
                $($botChansCont).removeClass('hide');
                $($botNewVidsCont).removeClass('hide');

                $('#botToChange').add('<option value="'+ $.trim($code_html) +'">'+$name+'</option>');
                $('#vidPlayer').append($newVidsHtml);
            });
        });
});

// handler selectBot Display
$('body').on('click','.selectBot',function (){
    $botId= $(this).attr('id').substring(9);

    $('.selectBot').css('background-color','#ffffff');
    $(this).css('background-color','#dddddd');
    $botChansCont='#botChannels'+$botId;
    $botNewVidsCont='#botNewVids'+$botId;

    $('.botNewVids').addClass('hide');
    $('.botChannels').addClass('hide');
    $($botChansCont).removeClass('hide');
    $($botNewVidsCont).removeClass('hide');
});


//handler createbot button
  $('#createbot').click(function  ()
							{

								$.get(
									'servicesPHP_SQL/BDDCreateBot.php',
									function ($code_html){
									
									$('#resultcreatebot').html($code_html);
									 $('#createBotContainer').html('<div>Main bot created <br> refresh to see the new bot</div><div>'+$code_html+'</div>');
									
									}									
								);
							}
  );


function removeClass($id, $class)
{
    document.getElementById($id).className =document.getElementById($id.className.replace($class,''));
}
