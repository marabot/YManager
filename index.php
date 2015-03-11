<!doctype html>
<?php
include 'auth.php';

?>

<html>
  <head>
  <meta charset="UTF-8">
  
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   
   <link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
 
    <title>Search</title>
	<script src="js/bootstrap-datepicker.min.js"></script>

  </head>
  <body>

<!--
<div><button class="btn btn-default" id="clearplaylists"> clear playlists  : </button></div>
	<div id="clearplaylistsResp"> response clear pl</div>
-->

<div class="container">

    
		<div class="row">
				<div class="text-center col-lg-12">
					 <h1 class="">Youtube Manager</h1>

					<p class="lead">
						Retrieve in one playlist the last videos from your subscriptions.</p>

				</div>
		</div>

        <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <?=$htmlBody?>
                    </div>
                    <div class="row">

                        <div class="col-md-3" id="botsChannels"><?=$botChannels?></div>

                        <div id="vidPlayer">
                            <?=$newVidsContainer?>
                        </div>
                    </div>
                    <div class="row">
                        <div> </div>
                        <div><?=$htmlTest?></div>

                    </div>
                </div>

                <div class="col-md-1">
                </div>

                <div class="col-md-4">
                    <?=$subs?>
                </div>
        </div>

			<BR>


        <div class="row" >
			<?=$testTools?>
		</div>
	</div>

  <script src="js/handler.js" ></script>
   
  </body>
</html>

