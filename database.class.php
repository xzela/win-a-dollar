<?php
class Database {
	var $conn;
	
	function open() {
		$dbhost = 'localhost';
		$dbuser = 'phpuser';
		$dbpass =  'wsbhexz';
		$this->conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
		mysql_select_db('winadollar01');
	}
	
	function query($query) {
		return mysql_query($query);
	}

	function close() {
		mysql_close($this->conn);
		
	}
}


?>