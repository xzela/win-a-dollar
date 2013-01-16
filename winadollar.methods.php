<?php
include 'database.class.php';
session_start();

define("SALT", 'salty');
define("USER_SALT", 'five taste chicken');
define("TEN_THOUNSAND", 101); //number of squares
define("ONE_HUNDRED", 10); //square per row

	/**
	* returns the hashed session id, this is used
	* for all players. 
	* 
	* @param $id session_id to hash
	*
	* @return hash string
	*/
	function hashUserSession($id) {
		return hash('sha256', USER_SALT . $id); //
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
	function testCurrentUser() {
		$b = false;
		$id = hashUserSession(session_id());
		$conn = new Database();
		$conn->open();
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT session_id FROM session_hash WHERE session_id = "' . $id . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $conn->query($sql);
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
	function createUserWinningNumber() {
		$id = hashUserSession(session_id()); //hash the session_id
		$hash = generateWinningNumber(); //create a winning number for this user
		$ip = $_SERVER['REMOTE_ADDR'];
		$conn = new Database();
		$conn->open();
		$sql = 'INSERT INTO session_hash (session_id, number_hash, active, ip) VALUES ("' . $id . '","' . $hash . '",1,"' . $ip . '")';
		$conn->query($sql);
	}
	
	/**
	* returns the winning number. This is dangeroius! 
	* It may be possible to somehow gain access to this method
	* will have to do more research to lock this method
	* 
	* @return $hash [string]
	*/
	function getUsersWinningNumber() {
		$id = hashUserSession(session_id()); //hash the session_id
		$conn = new Database();
		$conn->open();
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT number_hash FROM session_hash WHERE session_id = "' . $id . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $conn->query($sql);
		$hash = null;
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
	function checkUsersWinningNumber($hash) {
		$b = false;
		$id = hashUserSession(session_id()); //hash the session_id
		$conn = new Database();
		$current_time = date('Y-m-d H:i:s');
		$date = $new_time = date('Y-m-d H:i:s', strtotime($current_time . ' + 1 hour')); //
		$sql = 'SELECT number_hash FROM session_hash WHERE session_id = "' . $id . '" AND number_hash = "' . $hash . '" AND active = 1 AND entry_date < "' . $date . '" LIMIT 1';
		$result = $conn->query($sql);
		if(mysql_num_rows($result) > 0) {
			$b = true;
			$won_query = 'UPDATE session_hash SET active = 0, winner = 1 WHERE session_id = "' . $id . '" AND number_hash = "' . $hash . '" LIMIT 1';
			$conn->query($won_query);
		}
		else {
			$lost_query = 'UPDATE session_hash SET active = 0, winner = 0 WHERE session_id = "' . $id . '" AND winner = 0';
			$conn->query($lost_query);
		}
		createUserWinningNumber();
		
		return $b;
	}

	function generateWinningNumber() {
		$num = rand(1, TEN_THOUNSAND);
		$number = hash('sha256', SALT . $num);
		return $number;
	}
?>
