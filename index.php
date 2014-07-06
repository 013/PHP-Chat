<?php

require './chat.php';

$user = new user();
$chat = new chat($user);

?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<html>
	<head>
		<style>
			body {
				background: #fff;
				font-family: 'Helvetica', serif;
				margin: 0;
				padding: 0;
				overflow: hidden;
			}
			header {
				background: none repeat scroll 0 0 rgba(0, 0, 0, 0.86);
				color: #fff;
				font-size: 3.5em;
				position: relative;
				text-align: center;
			}
			#container {
				height: 80%;
				margin: 10px auto 0;
				width: 90%;
				padding-top: 10px;
				border: 1px solid;
				border-color: #e5e6e9 #dfe0e4 #d0d1d5;
				border-radius: 2px;
			}
			#messages {
				height: 90%;
			}
			#input {
				height: 10%;
				border-top: 1px solid #dfe0e4;
				margin-top: -1px;
			}
			.textinput {
				height: 100%;
				width: 87%;
				float: left;
				border: 1px solid #333;
				border-radius: 2px;
				padding: 0 0 0 8px;
				border: 0px solid;
			}
			.sendbutton {
				padding: 0;
				height: 100%;
				width: 10%;
				float: right;
			}
		</style>
	</head>
	<body>
		<header>PHP-Chat</header>
		<div id="container">
			<div id="messages">
			
			</div>
			<div id="input">
			<span><?=$user->username;?></span>
			<input type="text" class="textinput">
			<button class="sendbutton" >Send</button>
			</div>
		</div>

	</body>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript">
$(document).ready(function() {
	// Pressing 'enter'
	$('#container').on('keypress', '.textinput', function(e) {
		if (e.keyCode == 13) {
			send();
		}
	});

	$('#container').on('click', '.sendbutton', function() {
		send();
	});

	function send() {
		$.post( "chat.php", { action: 'newmsg', message: $(".textinput").val() }).done(function( data ) {
			console.log(data);
		});
	}
});
	</script>
</html>

