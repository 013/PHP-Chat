<?php
/* info 

Full message syntax:
time//nick//message
1327243567//ryan//hello everyone

*/

function NewLines($text) {
	return preg_match("/(%0A|%0D|\\n+|\\r+)/i", $text) == 1;
}

function newMesFile() {
	$file = fopen("messages.txt", 'w') or die("Message file could not be created, try checking permissions");
	$firstmessage = time() . "//Server//Empty Chat~\n";
	$firstmessage .= time() . "//Server//Send a message!\n";
	fwrite($file, $firstmessage);
	fclose($file);
}

function checkban($userip) {
	if ($userip == 1) {
		return true;
	} else {
		return false;
	}
}

function savemessage($message) {
	$txtfile = "./messages.txt";
	$fh = fopen($txtfile, 'a') or die("Error opening file");
	fwrite($fh, $message . "\n"); 
	/*A new line also needs appending, since
	  one does not prepend it*/
	fclose($fh);
}

function htmlit($content, $well = 1) {
	$content = preg_split("/(?<!\\\)\/\//", $content);
	$time = $content[0]; //Unix timestamp needs to be made readable
	$time = date("H:i:s" , $time);
	$nick = $content[1];
	$message = $content[2];
	//
	$HTML = <<<HTML
<div class="msg{$well}">
	[{$time}] &lt;{$nick}&gt; {$message}</div>
HTML;
	
	return $HTML;
}

function newMsg($newMsg, $nick) {
	/* All HTML and '<,>' get removed at the minute */
	$newMsg = replacehtml($newMsg);
	$nick	= replacehtml($nick);
	
	$message = strval(time()) . '//' . $nick . '//' . $newMsg;
	
	savemessage($message);
}

function checkMsg($lastMsg, $nick) { 
	if (!NewLines($lastMsg)) {
		/* There is not a new line so one needs to be appended
		   Although there is no reason why there shouldn't be a 
		   new line at the end. */
		$lastMsg .= "\n";
	}

	// $nick may not be needed, but just futureproof
	$txtfile = file("./messages.txt");
	// Any message newer than the current $lastMsg need to be sent back
	$mesAm = count($txtfile); 
	$lstML = 9999; //The line # of the lastMsg

	/* Message amount will be, the amount of message +1,
	   since there is a new line at the end */
	foreach ($txtfile as $line_num => $line) {
		if ($line == $lastMsg) {
			$lstML = $line_num;
			if ($line_num == ($mesAm - 1)) {
				/* The last message is the last one
				   that the user received */
				print "0";
				die();
			}
		} elseif ($line != $lastMsg && $line_num > $lstML) {
			print $line;
			die();
		}
	}
}

function amountof() {
	$txtfile = file("./messages.txt");
	$am_o_l = count($txtfile);
	
	foreach ($txtfile as $line_num => $line) {
		if ($line_num == ($am_o_l - 2)) {
			checkMsg($line, 0);
		}
	}
}

function replacehtml($string) {
	//if (preg_match("/<.*?/>/", $string)) { //?/> -> ?\/>
		$string= preg_replace("/</", "(", $string);
		$string = preg_replace("/>/", ")", $string);
	//}
	return $string;
}

/*----------*/
#           #
/*----------*/

if (!file_exists("./messages.txt")) {
	newMesFile();
}

if ($_POST && !isset($_POST['htmlit']) && !isset($_POST['getnewest'])) {
	checkban($_SERVER['REMOTE_ADDR']);
	if (isset($_POST['newmsg'])  && isset($_POST['nick'])) {
		newMsg($_POST['newmsg'], $_POST['nick']);
	} elseif (isset($_POST['lastmsg'])  && isset($_POST['nick'])) {
		checkMsg($_POST['lastmsg'], $_POST['nick']);
	}
} elseif (isset($_POST['htmlit']) && !isset($_POST['getnewest'])) {
	print htmlit($_POST['htmlit'], strval($_POST['msgnum']));
} elseif (isset($_POST['getnewest'])) {
	print amountof();
} else { 
	/* If a user is connecting properly then they should always be
	   sending something. If not then they are either connecting
	   directly to the page, or something has gone wrong.         */
	header("Location: /");
	//print htmlit("1327716089//Ryan//stufudfjds &lt; akjdasd", 1);
	//testing();
}


function testing() {
	if (preg_match("/<.*?>/", "test < test")) {
		print "It Matches";
	}
}

?>
