<?php

require './config.php';
error_reporting(-1);
ini_set('display_errors', 'On');

class chat {
	function __construct(user $user) {
		$this->user = $user;
		try {
			$this->db = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		} catch (PDOException $exception) {
			die();
		}
	}

	function newMessage($data) {
		$message = $data['message'];
		$username = $this->user->username;
		//echo $message . " " . $username;
		$sql = "INSERT INTO messages VALUES(NULL, :username, :message, NULL);";
		$st = $this->db->prepare($sql);
		$st->bindParam(':username', $username);
		$st->bindParam(':message', $message);

		$st->execute();

	}

}

class user {
	function __construct() {
		try {
			$this->db = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		} catch (PDOException $exception) {
			die();
		}
		
		session_start();
		//unset( $_SESSION['uid'] );
		//var_dump($_SESSION);
		if (!isset($_SESSION['uid'])) {
			// Create new user
			$_SESSION['uid'] = 	"usr".uniqid();
			$this->uid =		$_SESSION['uid'];
			$this->username	= 	$uid;
			$ip =			$_SERVER['REMOTE_ADDR'];

			$sql =	"INSERT INTO users VALUES (:uid, :username, INET_ATON(:ip), NULL);";
			$st =	$this->db->prepare($sql);
			$st->bindParam('uid', $uid);
			$st->bindParam('username', $username);
			$st->bindParam('ip', $ip);

			$st->execute();
		} else {
			// Get username from uid
			$this->uid =	$_SESSION['uid'];
			$sql =		"SELECT username FROM users WHERE uid=:uid;";
			$st =		$this->db->prepare($sql);
			$st->bindParam(':uid', $this->uid);
			$st->execute();

			$row = $st->fetch();
			if ( !$row ) {
				die('Username error');
			}
			
			$this->username = $row['username'];
		}
	}

	function updateLastAlive() {
		$sql = "UPDATE users SET lastalive=NULL WHERE uid=:uid";
	}

}

if (isset($_POST['action'])) {
	if (!isset($_POST['message'])) {
		$data = array('message'=>"abcdef");
	} else {
		$data = $_POST;
	}

	$action = "newmsg";//$_POST['action'];
	$user = new user();
	$user->updateLastAlive();

	switch ($action) {
		case 'newmsg':
			$chat = new chat($user);
			$chat->newMessage($data);
			break;
		default:
			break;
	}
	//var_dump($_POST);
}

?>

