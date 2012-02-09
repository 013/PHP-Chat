<?php

/* ----------------
   ___  _ _____ 
  / _ \/ |___ / 
 | | | | | |_ \ 
 | |_| | |___) |
  \___/|_|____/

---------------- */

// Open the database
check_ban($_SERVER['REMOTE_ADDR']);
$dbhandle = open_db();

if ($_POST) {
	if (isset($_POST['new_message'])) {
		insert_data($dbhandle, $_SERVER['REMOTE_ADDR'], $_POST['nick'], $_POST['newmsg']);
	} elseif (isset($_POST['get_message'])) {
		print get_data($dbhandle, $_POST['old_message']);
	
	}
} elseif ($_GET) { // Just for testing
	if (isset($_GET['new_message'])) {
		insert_data($dbhandle, $_SERVER['REMOTE_ADDR'], $_GET['nick'], $_GET['message']);
	} elseif (isset($_GET['get_message'])) {
		get_data($dbhandle, $_GET['old_message']);
	
	}
} else {
	// Currently for testing
	//insert_data($dbhandle, $_SERVER['REMOTE_ADDR'], "Ryan", "MY MEssage");
	$search = array('&', '"', '\'', '<', '>');
	$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;');
	
	print clean("<b>hi");
	
	//echo str_replace($search, $replace, "<hi>");
	//print get_data($dbhandle);
}

function open_db() {
	(!file_exists('chat.db')) ? $create = True : $create = False;
	// If the database does not exists, create
	$dbhandle = sqlite_open('chat.db', 0666, $error);
	if (!$dbhandle) return $error;
	
	$table = "CREATE TABLE messages
			(
			 ID integer AUTOINCREMENT,
			 Time timestamp NOT NULL,
			 User_IP varchar(15) NOT NULL,
			 Nick varchar(50) NOT NULL,
			 Message varchar(300) NOT NULL,
			 PRIMARY KEY (ID)
			)";
	if ($create) $query = sqlite_exec($dbhandle, $table, $error);
	
	return $dbhandle;
}

function get_data($dbhandle, $old_message = 0) {
	$old_message = (int) $old_message;
	
	$HTML = "";
	
		$sql = "SELECT COUNT(*) FROM messages;";
		$result = sqlite_array_query($dbhandle, $sql);
		$total_messages = $result[0][0];
		
		if ($total_messages == $old_message) {
			return $total_messages;
		}
		
		if ($total_messages >= 5 && $old_message == 0) {
			$old_message = $total_messages - 5;
		}
	//}
	
	$old_message = sqlite_escape_string($old_message);
	$sql = "SELECT * FROM messages WHERE ID > $old_message";
	
	$result = sqlite_array_query($dbhandle, $sql);
	foreach ($result as $entry) {
		/*if (clean($entry['ID']) > $old_message) {
			;//Greater == newer;
		}*/
		/* print "ID: " . clean($entry['ID']) . 
			  " Time: " . clean($entry['Time']) . 
			  " Nick: " . clean($entry['Nick']) . 
			  " Message: " . clean($entry['Message']) . 
			  "<br>\n";
		*/
		//"ID: " . clean($entry['ID']) . 
		
		$HTML .= html(clean($entry['ID']), clean($entry['Time']), clean($entry['Nick']), clean($entry['Message']));
	}
	
	return $total_messages . $HTML;
}

function insert_data($dbhandle, $user_ip, $nick, $message) {
	$user_ip = sqlite_escape_string($user_ip);
	$nick = sqlite_escape_string($nick);
	$message = sqlite_escape_string($message);
	$sql = "INSERT INTO messages VALUES (NULL, " . time() . ", '$user_ip', '$nick', '$message')";

	sqlite_query($dbhandle, $sql);
}

function clean($string) {
	// Simple replace function to stop any injection
	$search = array('&', '"', '\'', '<', '>');
	$replace = array('&amp;', '&quot;', '&#39;', '&lt;', '&gt;');

	$string = str_replace($search, $replace, $string);
	return $string;
}

function html($id, $time, $nick, $message) {
	$time = gmdate("H:i:s", $time); // GMT Time Zone
	
	$id = $id % 2;
	$HTML = <<<HTML
<div class="msg{$id}"> 
[{$time}] &lt;{$nick}&gt; {$message}</div>
HTML;
	
	return $HTML;
}

function check_ban($user_ip) { //wipwipwipwipwip
	;
}

?>

