<?php

error_reporting(-1);
ini_set('display_errors', 'On');
require './chat.php';

$user = new user();
$chat = new chat($user);

?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<html>
	<head>
		<title>PHP Chat</title>
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
				height: 75%;
				margin: 10px auto 0;
				width: 90%;
				padding-top: 10px;
				border: 1px solid;
				border-width: 1px 1px ;
				border-color: #e5e6e9 #dfe0e4 #d0d1d5;
				border-radius: 2px;
			}
			#messages {
				height: 100%;
			}
			#input {
				height: 55px;
				border-top: 1px solid #dfe0e4;
				margin-top: -55px;
			}
			.username {
				line-height: 55px;
				width: 20%;
				float: left;
				text-align: center;
			}
			.chgUsername {
				height: 100%;
				border: 0;
				text-align: center;
			}
			.textinput {
				background: none repeat scroll 0 center rgba(0, 0, 0, 0);
				height: 100%;
				width: 67%;
				float: left;
				border: 0px solid;
				border-left: 1px solid #ccc;
				border-radius: 2px;
				padding: 0 0 0 8px;
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
			<span class="username"><?=$user->username;?></span>
			<input type="text" class="textinput">
			<button class="sendbutton">Send</button>
			</div>
		</div>

	</body>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript">
$(document).ready(function() {
	var changingUN = false;
	var unChanged = false; 
	var username = '<?=$user->username;?>';
	var lastID = 0;

	$('html').on('click', function() {
		usernameOut();
	});
	// Pressing 'enter' when changing username
	$('#container').on('keypress', '.chgUsername', function(e) {
		if (e.keyCode == 13) {
			changingUN = false;
			var username = $('.chgUsername').val();
			$('.username').html(username);
			$('.textinput').focus();
		}
	});

	// Pressing 'enter' when writing a message
	$('#container').on('keypress', '.textinput', function(e) {
		if (e.keyCode == 13) {
			send();
		}
	});

	$('#container').on('click', '.sendbutton', function() {
		send();
	});

	// Click to change username
	$('#input').on('click', '.username', function(event) {
		event.stopPropagation();

		if (!changingUN) {
			changingUN = true;
			var username = $('.username').html();
			$('.username').html("<input class='chgUsername' value='"+username+"' >");
			$('.chgUsername').select();
		}
	});

	function usernameOut() {
		changingUN = false;
		var newUN = $('.chgUsername').val();

		if (newUN != username) {
			unChanged = true;
			username = newUN;
		}
		
		// If not taken
		$('.username').html(newUN);
		
	}

	function send() {
		$.post( "chat.php", { action: 'newmsg', message: $(".textinput").val() }).done(function( data ) {
			console.log(data);
		});
	}

	var updateInt = setInterval(update, 5000);

	function update() {
		var data = {};

		if (unChanged) {
			data['username'] = username;
			unChanged = false;
		}
		
		//console.log(JSON.stringify(data));
		$.post( "chat.php", { action: 'update', data: JSON.stringify(data), lastid: lastID }).done(function( data ) {
			//console.log(data);
			console.dir(JSON.parse(data));
		});
	}
});
	</script>
</html>

