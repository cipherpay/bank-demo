<?php
	function db_connect() {
		// Load database configuration file
		include 'config.php';
		
		$db = new mysqli($host, $user, $password, $database);

		if ($db->connect_errno > 0)
			return "CONNECT_FAIL";
		else
			return $db;
	}

	function db_query($db, $sql) {
		$result = $db->query($sql);

		if (!$result)
			return "QUERY_FAIL";
		else
			return $result;
	}

	function db_close($db) {
		$db->close();
	}

?>