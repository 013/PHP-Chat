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

	function updateUser() {
		$returnData = array();
		if (strlen($_POST['data']) >= 3) {
			// The user has updated their username / ...
			$data = substr($_POST['data'], 1, -1);
			$mstr = explode(",",$data);
			$a = array();
			foreach($mstr as $nstr ) {
				    $narr = explode(":",$nstr);
				    $narr[0] = str_replace("\x98","",$narr[0]);
				    $narr[0] = str_replace('"',"",$narr[0]);
				    $narr[1] = str_replace('"',"",$narr[1]);
				    $a[$narr[0]] = $narr[1];
			}
			
			if (isset($a["username"])) {
				$username = $this->user->setUsername($a["username"]);
			}
		}
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
		//var_dump($_SESSION);
		if (!isset($_SESSION['uid'])) {
			// Create new user
			$_SESSION['uid'] = 	"usr".uniqid();
			$this->uid =		$_SESSION['uid'];
			$this->username	= 	substr($this->uid, 0, 7);
			$this->ip =		$_SERVER['REMOTE_ADDR'];

			$sql =	"INSERT INTO users VALUES (:uid, :username, INET_ATON(:ip), NULL);";
			$st =	$this->db->prepare($sql);
			$st->bindParam('uid', $this->uid);
			$st->bindParam('username', $this->username);
			$st->bindParam('ip', $this->ip);

			$st->execute();
		} else {
			// Get username from uid
			$this->uid =	$_SESSION['uid'];
			$this->ip =	$_SERVER['REMOTE_ADDR'];
			$sql =		"SELECT username FROM users WHERE uid=:uid;";
			$st =		$this->db->prepare($sql);
			$st->bindParam(':uid', $this->uid);
			$st->execute();

			$row = $st->fetch();
			if ( !$row ) {
				unset( $_SESSION['uid'] );
				die('Username error');
			}
			
			$this->username = $row['username'];
		}
	}

	function updateLastAlive() {
		$sql = "UPDATE users SET lastalive=NULL WHERE uid=:uid";
	}

	function setUsername($username) {
		$sql = "SELECT * FROM users WHERE username=:username AND lastalive > DATE_SUB(NOW(), INTERVAL 10 SECOND);";
		$st = $this->db->prepare($sql);
		$st->bindParam(':username',$username);
		$st->execute();

		$row = $st->fetch();

		if ( !$row ) {
			// Username is free to have
			$sql = "UPDATE users SET lastalive=NULL, username=:username WHERE uid=:uid";
			$st = $this->db->prepare($sql);
			$st->bindParam(':username', $username);
			$st->bindParam(':uid', $this->uid);
			$st->execute();
			$this->username = $username;
		}
		return $this->username;
	}

}

if (isset($_POST['action'])) {
	$action = $_POST['action'];
	$user = new user();
	$chat = new chat($user);

	$user->updateLastAlive();

	switch ($action) {
		case 'newmsg':
			$chat->newMessage($data);
			break;
		case 'update':
			$chat->updateUser();
			break;
		default:
			break;
	}
	//var_dump($_POST);
}

?>

