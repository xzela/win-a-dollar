<?php
include 'database.class.php';
session_start();

define("SALT", 'salty');
define("USER_SALT", 'five taste chicken');
define("TEN_THOUNSAND", 101); //number of squares
define("ONE_HUNDRED", 10); //square per row

class Dollars {

	public $conn;
	public $id;

	public function __construct() {
		$this->conn = new Database();
		$this->conn->open(); /// yeah open database connection!
		$this->id = $this->hashUserSession(session_id());
	}

	/**
	* returns the hashed session id, this is used
	* for all players.
	*
	* @param $id session_id to hash
	*
	* @return hash string
	*/
	public function hashUserSession($id) {
		return hash('sha256', USER_SALT . $id);
	}

	/**
	* tests the user to see if they already have a
	* number[yes|no], We need to make sure the user is in
	* the session_hash table. else they'll never find
	* the number. because no number would exist for them
	*
	* test is based on their hashed session id and whichever
	* active number belongs to them
	*
	* @return bool;
	*/
	public function testCurrentUser() {
		$b = false;
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT session_id FROM session_hash WHERE session_id = "' . $this->id . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $this->conn->query($sql);
		if(mysql_num_rows($result) > 0 ) {
			$b = true;
		}
		return $b;
	}

	/**
	* Creates a winning number for thie user
	*
	* @return null
	*/
	public function createUserWinningNumber() {
		$hash = $this->generateWinningNumber(); //create a winning number for this user
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = 'INSERT INTO session_hash (session_id, number_hash, active, ip) VALUES ("' . $this->id . '","' . $hash . '",1,"' . $ip . '")'; //that's right, that's your ip right there.
		$this->conn->query($sql);
	}

	/**
	* returns the winning number. This is dangeroius!
	* It may be possible to somehow gain access to this method
	* will have to do more research to lock this method
	*
	* @return $hash [string]
	*/
	public function getUsersWinningNumber() {
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT number_hash FROM session_hash WHERE session_id = "' . $this->id . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $this->conn->query($sql);
		$hash = null; //set the hash to null
		while($row = mysql_fetch_array($result)) {
			$hash = $row['number_hash'];
		}
		return $hash;
	}

	/**
	* this is the main cog
	* this is called everytime the user clicks on a square
	* tests to see if the passed hash string is the correct hash.
	*
	* @param $hash [string] = hash value of a number
	*
	* @return $b [bool];
	*/
	public function checkUsersWinningNumber($hash) {
		$b = false;
		$hash = mysql_real_escape_string($hash);
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT number_hash FROM session_hash WHERE session_id = "' . $this->id . '" AND number_hash = "' . $hash . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $this->conn->query($sql);
		if(mysql_num_rows($result) > 0) {
			$b = true;
			$won_query = 'UPDATE session_hash SET active = 0, winner = 1 WHERE session_id = "' . $this->id . '" AND number_hash = "' . $hash . '" LIMIT 1';
			$this->conn->query($won_query);
		}
		else {
			$lost_query = 'UPDATE session_hash SET active = 0, winner = 0 WHERE session_id = "' . $this->id . '" AND winner = 0';
			$this->conn->query($lost_query);
		}
		$this->createUserWinningNumber();
		return $b;
	}

	public function generateWinningNumber() {
		$num = rand(1, TEN_THOUNSAND);
		$number = hash('sha256', SALT . $num);
		return $number;
	}

}
?>
