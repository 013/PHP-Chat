<!DOCTYPE html>
<html lang="en">
	<meta charset="utf-8">
		<title>Chat</title>
		<link rel='stylesheet' type='text/css' href='style.css'>
		<script type="text/javascript" src="js/libs/jquery.min.js"></script>
		<script type="text/javascript" src="js/libs/jquery.cookie.js"></script>
		<script type="text/javascript" src="js/script.js"></script>
	</head>
	<body>
		<div id="wrapper">
			<div id="lastmsg" style="display: none;">0</div>
			<div id="dataDisplay">
			</div>
			<div id="messageinput">
				<input type="text" class="nick" id="nick" placeholder="Nick">
				<input type="text" class="message" id="message" placeholder="Message">
				<input type="submit" value="Send" id="sendbutton">
			</div>
		</div>
	</body>
</html>
