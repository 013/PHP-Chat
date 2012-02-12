<?php

// Open the database
$dbhandle = open_db();
check_ban($dbhandle, $_SERVER['REMOTE_ADDR']);

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
	insert_data($dbhandle, $_SERVER['REMOTE_ADDR'], "Nick Name", "NIgijdsf");
	ban_user($dbhandle, "gay","gay");
	$foo = "Ban";
	if (preg_match("/ban/i", $foo)) {
		print "Match";
	}
	//header("Location: /");
}

function open_db() {
	(!file_exists('chat.db')) ? $create = True : $create = False;
	// If the database does not exists, create
	$dbhandle = sqlite_open('chat.db', 0666, $error);
	if (!$dbhandle) return $error;
	
	if ($create) create_tb($dbhandle);
	
	return $dbhandle;
}

function create_tb($dbhandle) {
	$table_m = "CREATE TABLE messages
			(
			 ID integer AUTOINCREMENT,
			 Time timestamp NOT NULL,
			 User_IP varchar(15) NOT NULL,
			 Nick varchar(50) NOT NULL,
			 Message varchar(300) NOT NULL,
			 PRIMARY KEY (ID)
			)";
	$table_b = "CREATE TABLE banned
			(
			 ID integer AUTOINCREMENT,
			 Time timestamp NOT NULL,
			 User_IP varchar(15) NOT NULL,
			 Nick varchar(50) NOT NULL,
			 Reason varchar(300) NOT NULL,
			 PRIMARY KEY (ID)
			)";
	$table_u = "CREATE TABLE users
			(
			 ID integer AUTOINCREMENT,
			 Time timestamp NOT NULL,
			 User_IP varchar(15) NOT NULL,
			 Nick varchar(50) NOT NULL,
			 Permission varchar(300) NOT NULL,
			 PRIMARY KEY (ID)
			)";
	$query = sqlite_exec($dbhandle, $table_m, $error);
	$query = sqlite_exec($dbhandle, $table_b, $error);
	$query = sqlite_exec($dbhandle, $table_u, $error);
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
	
	$old_message = sqlite_escape_string($old_message);
	$sql = "SELECT * FROM messages WHERE ID > $old_message";
	
	$result = sqlite_array_query($dbhandle, $sql);
	foreach ($result as $entry) {
		
		$HTML .= html(clean($entry['ID']), clean($entry['Time']), clean($entry['Nick']), clean($entry['Message']));
	}
	
	return $total_messages . $HTML;
}

function insert_data($dbhandle, $user_ip, $nick, $message) {
	$user_ip = sqlite_escape_string($user_ip);
	$nick = sqlite_escape_string($nick);
	$message = sqlite_escape_string($message);
	
	check_input($user_ip, $nick, $message);
	$sql = "INSERT INTO messages VALUES (NULL, " . time() . ", '$user_ip', '$nick', '$message')";

	sqlite_query($dbhandle, $sql);
}

function check_input($user_ip, $nick, $message) {
	if ($message[0] == '/') {
		$command = substr($message, 1);
		$command = explode(" ", $command);
		if (preg_match("/ban/i", $command[0])) {
			$user = $command[1];
			$reason = $command[2];
			ban_user($dbhandle, $user, $reason);
		} elseif (preg_match("/unban/i", $command[0])) {
			$user = $command[1];
			unban_user($dbhandle, $user, $reason);
		}
	}
}

function ban_user($dbhandle, $user, $reason) {
	$sql = "SELECT User_IP FROM messages where Nick='$user'";
	// sql to find the users ip
	
	$result = sqlite_array_query($dbhandle, $sql);
	foreach ($result as $entry) {
		$user_ip = $entry['User_IP'];
	}
	
	$sql = "INSERT INTO banned VALUES (NULL, " . time() . ", '$user_ip', '$nick', '$reason')";
	sqlite_query($dbhandle, $sql);
}

function unban_user($dbhandle, $user) {
	$sql = "DELETE FROM banned WHERE Nick='$user'";
	sqlite_query($dbhandle, $sql);
}

function check_ban($dbhandle, $user_ip) {
	$sql = "SELECT * FROM banned WHERE User_IP='$user_ip'";
	
	$result = sqlite_array_query($dbhandle, $sql);
	if (count($result) >= 1) {
		die();//Banned
	}
	/*foreach ($result as $entry) {
		$user_ip = $entry['User_IP'];
	}*/
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
<span class="time">[{$time}]</span>&lt;{$nick}&gt; {$message}</div>
HTML;
	
	return $HTML;
}


?>

