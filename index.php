<!doctype html>
<?php
include 'auth.php';

?>

<html>
  <head>
  <meta charset="UTF-8">

  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   
   
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
   
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
	
 
    <title>Search</title>
	<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
	 

  </head>
  <body>


<div><button class="btn btn-default" id="clearplaylists"> clear playlists  : </button></div>
	<div id="clearplaylistsResp"> response clear pl</div>


<div class="container">

    
		<div class="row">
				<div class="text-center col-lg-12">
					 <h1 class="">Youtube Manager</h1>

					<p class="lead">
						In one playlist, get all the new videos from your youtube's subscriptions</p>
				</div>
		</div>

        <div class="row">
                <div class="col-md-7">
                    <div class="row">
                        <?=$htmlBody?>
                    </div>
                    <div class="row">
                        <div id="vidPlayer">
                            <?=$infoBot?>
                        </div>
                    </div>
                    <div class="row">
                        <?=$htmlTest?>
                    </div>
                </div>

                <div class="col-md-1">
                </div>

                <div class="col-md-4">
                    <?=$subs?>
                </div>
        </div>

			<BR>

        <div class="row">
			<span> For Test :Modify lastHarvest Date in database :
				<div id="lastHarvestDatePicker">
					<div class="col-md-2 input-group date" id="datePicker">
						<input type="text" class="form-control" id="newlastharvest"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
					</div>
					<button  class="btn btn-default" id="changeLastHarvest">change lastharvest Date :</button>
					<span id="changelastHarvestresult"><span>
				</div>
		</div>
	</div>

  <script src="js/handler.js" ></script>
   
  </body>
</html>

