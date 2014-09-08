<?php
	// Database function requirements
	if (!defined("ROOT"))
		define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/");
	require_once ROOT."inc/config.php";
	
	/*
	 * Connect to MySQL Database
	 * Uses PDO. Configuration in '/inc/config.php'
	 */
	function db_connect() {
		try {
			$db = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->exec("SET NAMES 'utf8'");
		} catch (Exception $e) {
			return "CONNECT_FAIL";
		}
		
		return $db;
	}

	/* 
	 * Function to run prepared SQL.
	 * May become deprecated with move to PDO
	 */
	function db_query($db, $sql) {
		try {
			$result = $db->query($sql);
		} catch (Exception $e) {
			return "QUERY_FAIL";
		}
		
		return $result;
	}

	/* 
	 * Close PDO database connection.
	 */
	function db_close($db) {
		$db = NULL; // For PDO, set to NULL
	}

?>