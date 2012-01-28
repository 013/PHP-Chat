<!DOCTYPE html>
<html lang="en">
<meta charset="utf-8">
<title>Chat</title>
<link rel='stylesheet' type='text/css' href='style.css'>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
	GetLatest(); //First get all messages
	setInterval("MakeRequest();", 3000); //Every three seconds look for a new message
	
	function getXMLHttp() {
		var xmlHttp
		try {
			//Good browsers will work here
			xmlHttp = new XMLHttpRequest();
		} catch(e) {
			alert("Your browser is shit!");
			return false;
		}
		return xmlHttp;
	}
	
	function MakeRequest() {
		//Make a request to the server for new messages
		var xmlHttp = getXMLHttp();
		
		xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState == 4) {
				return HandleResponse(xmlHttp.responseText);
			}
		}
		
		xmlHttp.open("POST", "ajax.php", true);
		xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		try {
			nick = document.getElementById("nick").value;
			/* The last message will be sent to the server to see 
			   what new messages need to be sent back */
			lastmsg = document.getElementById("lastmsg").innerHTML;
		} catch(e) {
			nick = "Anon";
			lastmsg = "0";
		}
		xmlHttp.send("nick=" + nick + "&lastmsg=" + lastmsg);
		
	}
	
	function sendMessage() { //Works (mostly)
		var xmlHttp = getXMLHttp();
		
		xmlHttp.open("POST", "ajax.php", true);
		xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		nick = document.getElementById('nick').value;
		lastmsg = document.getElementById('lastmsg').innerHTML;
		newmsg = document.getElementById('message').value;
		
		if (nick.length < 1) {
			document.getElementById('nick').value = "A Name is needed!";
			document.getElementById('nick').focus();
		} else if (newmsg.length < 1) {
			document.getElementById('message').value = "You need to send a message!";
			document.getElementById('message').focus();
		} else {	
			xmlHttp.send("nick=" + nick + "&lastmsg=" + lastmsg + "&newmsg=" + newmsg);
			document.getElementById('message').value = "";
			document.getElementById('message').focus();
		}
	}
	
	function GetLatest() {
		var xmlHttp = getXMLHttp();
		
		xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState == 4) {
				return HandleResponse(xmlHttp.responseText);
			}
		}
		
		xmlHttp.open("POST", "ajax.php", true);
		xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlHttp.send("getnewest=true");
		
	}

	function HandleResponse(response) {
		var xmlHttp = getXMLHttp();
		if (response != "0") { //"0" is if the latest message has already been recieved
			document.getElementById('lastmsg').innerHTML = response;
			
			//htmlify the message
			
			xmlHttp.onreadystatechange = function() {
				if (xmlHttp.readyState == 4) {
					$('#dataDisplay').append(xmlHttp.responseText).fadeIn("slow");
				}
			}

			var msgnum = document.getElementById('msgnum').innerHTML;
			
			if (msgnum == "1") {
				document.getElementById('msgnum').innerHTML = "2";
			} else {
				document.getElementById('msgnum').innerHTML = "1";
			}
			
			xmlHttp.open("POST", "ajax.php", true);
			xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlHttp.send("htmlit=" + response + "&msgnum=" + msgnum);

			//$('#dataDisplay').append(response).fadeIn("slow");
			//document.getElementById("#dataDisplay").scrollTop = document.getElementById("#dataDisplay").scrollHeight;
		} else {
			return response;
		}

	}
	
</script>
</head>

<body>

<div id="wrapper">
<div id="lastmsg" style="display: none;"></div> <!--style="display: none;"-->
<div id="msgnum" style="display: none;">1</div>
<div id="dataDisplay">
</div>
<div id="messageinput">
<!--<form  name="messageinput" >-->
<input type="text" class="nick" id="nick" placeholder="Nick">
<input type="text" class="message" id="message" placeholder="Message">
<input type="submit" value="Send" onClick="sendMessage();">
<!--</form>-->
</div>
</div>

</body>
</html>
