<?php
	// Load database configuration
	require '../config.php';
	require '../classes/Account.php';
	require '../classes/Transaction.php';

	$db = new mysqli($host, $user, $password, $database);

	if ($db->connect_errno > 0) {
		echo "CONNECT_FAIL";
		exit();
	}

	// Set JSON header
	header('Content-Type: application/json; charset=utf-8');

	/* Get the API
	 * ===========
	 * Possible values:
	 * 'credit' - add funds to an account balance
	 * 'debit' - removes funds from an account
	 * 'transfer' - transfer funds between two accounts
	 * 'list' - lists all account transactions
	 */
	$api = $_REQUEST['api'];

	switch ($api) {
		case 'credit':
			break;
		default: 
			echo "Error! No valid API specified.";
			exit();
	}

	$db->close();
?>