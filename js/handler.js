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


// handler changeLastharvest Button
  $('#changeLastHarvest').click(function(){
										
									$botToChange=$('#tempId').text();
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
										alert ('php clear');
										$.get(
											'servicesPHP_SQL/BDDdeletePlaylists.php',
												function($resp){
														$('#clearplaylistsResp').html($resp);
												}									
											)	
									});
							
	// handler harvest button
	$('button').click(function ()
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
                                         $('#vidPlayer').html('<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/embed?listType=playlist&list='+ $.trim($resp)+'" frameborder="0"/>');
                                            //window.open('https://www.youtube.com/watch?v=_IrMqQkR8cU&list='+$resp);
										}
									)					
								}
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
 


