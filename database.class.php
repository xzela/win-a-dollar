<?php
class Database {
	public $conn;

	public function open() {
		$dbhost = 'localhost';
		$dbuser = 'winadollar';
		$dbpass =  '';
		$this->conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');
		mysql_select_db('winadollar01');
	}

	public function query($query) {
		return mysql_query($query);
	}

	public function close() {
		mysql_close($this->conn);

	}
}


?>