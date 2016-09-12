<!DOCTYPE html>

<?php

error_reporting('E_ERROR');

include("header.php");
include("sinusbot.class.php");
include("config.php");
session_start();

if(isset($_GET['id'])){
	$id = $_GET['id'];
	if(!is_numeric((int)$id)){
		echo "Error: Invalid value for \"id\".";
		return;
	}
	if($id > count($instanceIDS) - 1){
		echo "Error: There are only " . count($instanceIDS) . " instances. Chose a number between 0 and " . (count($instanceIDS) -1) . ".";
		return;
	}
	$inst = $instanceIDS[$id];
	$_SESSION['inst'] = $id;
	setcookie("inst", $id, time() + (86400*30), "/");
}else if(isset($_SESSION['inst'])){
	$inst = $instanceIDS[$_SESSION['inst']];
	setcookie("inst", $id, time() + (86400*30), "/");
}else if(isset($_COOKIE['inst'])){
	$_SESSION['inst'] = $id;
	$inst = $instanceIDS[$_COOKIE['inst']];
}else{
	$inst = $instanceIDS[$defaultInstance];
	$_SESSION['inst'] = $defaultInstance;
	setcookie("inst", $defaultInstance, time() + (86400*30), "/");
}


$sinusbot = new SinusBot($ipport);
$sinusbot->login($user, $passwd);
$sinusbot->selectInstance($inst);
$token = $sinusbot->getWebStreamToken($inst);


?>

<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://bootswatch.com/darkly/bootstrap.min.css">

	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<link href="http://vjs.zencdn.net/4.3/video-js.css" rel="stylesheet">
	<script src="http://vjs.zencdn.net/4.3/video.js"></script>

	<script type="text/javascript">
		function loadSong() {
			var xhttp = new XMLHttpRequest();
			xhttp.open("GET", "getSong.php", true);
			xhttp.send();
		} 

		function loadImg() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (xhttp.readyState == 4 && xhttp.status == 200) {
					var videoposter = xhttp.responseText;
					$('.vjs-poster').css({
						'background-image': 'url('+videoposter+')',
						'display': 'block',
						'background-size': 'cover'

					});
				}
			};
			xhttp.open("GET", "getImg.php", true);
			xhttp.send();
		}

		function loadSearch() {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (xhttp.readyState == 4 && xhttp.status == 200) {
					document.getElementById("songname").innerHTML = xhttp.responseText;
				}
			};
			xhttp.open("GET", "getSongURL.php", true);
			xhttp.send();
		}

		setInterval(function() {
			loadImg();
			loadSong();
			loadSearch();
		}, 3500); 
	</script>
	<title><?php echo $title; ?></title>
</head>
<body>
	<div class="container">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="."><?php echo $title; ?></a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="https://github.com/Zahzi/SinusBot-Stream" target="_blank"><img src="resources/github.png" class="img-responsive" height="25px" width="22px" alt="Open source on GitHub!"></img></a></li>
						<?php
						if($teamspeakJoinLink != ""){
							echo '<li><a href="'.$teamspeakJoinLink.'"><img src="resources/teamspeak.png" class="img-responsive" height="25px" width="22px" alt="Join TS3!"></img></a></li>';
						}
						echo '<li><a href="http://'.$ipport.'" target="_blank"><img src="resources/login.png" class="img-responsive" height="25px" width="22px" alt="Login to SinusBot webpanel."></img></a></li>';
						?>
					</ul>
					<ul class="nav navbar-nav">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Select Bot <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?php 
								for($i = 0; $i < count($instanceNames); $i++){
									if($i == array_search($inst, $instanceIDS)){
										echo '<li class="active"><a href="?id='.$i.'">'.$instanceNames[$i].'</a></li>';
									}else{
										echo '<li><a href="?id='.$i.'">'.$instanceNames[$i].'</a></li>';
									}
								}
								?>
							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
		<div class="embed-responsive embed-responsive-16by9">
				<video id="player" class="player video-js vjs-default-skin embed-responsive-item"
				controls preload="none" autoplay
				data-setup='{
				"height": "100%",
				"width": "100%",
				"children": {
				"controlBar": {
				"children": {
				"liveDisplay": false,
				"fullscreenToggle": true,
				"durationDisplay": false,
				"currentTimeDisplay": false,
				"timeDisplay": false,
				"timeDivider": false
			}
		}
	}}'>
	<!-- <source src="test.mp3" type="audio/mp3"> -->
	<source src="http://<?php echo $ipport; ?>/api/v1/bot/i/<?php echo $inst; ?>/stream/<?php echo $sinusbot->getWebStreamToken($inst); ?>" type="audio/mp3">
	</video>

</div>
<div id="songnamediv" align="center"><h5 id="songname">Loading song name...</h5></div>
</div>

</body>
</html>