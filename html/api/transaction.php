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
	$type = $_REQUEST['type'];

	switch ($type) {
		case 'credit':
			// Get form parameters and escape
			$amount = $db->real_escape_string($_POST['amount']);
			$institution = $db->real_escape_string($_POST['institution']);
			$account = $db->real_escape_string($_POST['account']);

			// Execute transaction
			$transaction = new Transaction($db, 'credit', $amount, $institution, $account);

			// Return results in JSON format
			if ($transaction->status == 'commit') {
				$response = array('status' => 'SUCCESS',
					'transaction_id' => $transaction->id
				);
			} else {
				$response = array('status' => 'ERROR',
					'message' => $transaction->status_info
				);
			}
			echo json_encode($response);
			break;
		case 'debit':
			// Get form parameters and escape
			$amount = $db->real_escape_string($_POST['amount']);
			$institution = $db->real_escape_string($_POST['institution']);
			$account = $db->real_escape_string($_POST['account']);

			// Execute transaction
			$transaction = new Transaction($db, 'debit', $amount, $institution, $account);

			// Return results in JSON format
			if ($transaction->status == 'commit') {
				$response = array('status' => 'SUCCESS',
					'transaction_id' => $transaction->id
				);
			} else {
				$response = array('status' => 'ERROR',
					'message' => $transaction->status_info
				);
			}
			echo json_encode($response);
			break;
		case 'transfer':
			// Get form parameters and escape
			$amount = $db->real_escape_string($_POST['amount']);
			$institution = $db->real_escape_string($_POST['institution']);
			$issuer = $db->real_escape_string($_POST['issuer']);
			$acquirer = $db->real_escape_string($_POST['acquirer']);

			// Execute transaction
			$transaction = new Transaction($db, 'transfer', $amount, $institution, $issuer, $acquirer);

			// Return results in JSON format
			if ($transaction->status == 'commit') {
				$response = array('status' => 'SUCCESS');
			} else {
				$response = array('status' => 'ERROR',
					'message' => $transaction->status_info
				);
			}
			echo json_encode($response);
			break;
		case 'get':
			// Get form parameters and escape
			$transaction_id = $db->real_escape_string($_GET['transaction_id']);

			// Retreive transaction details
			$transaction = Transaction::get($db, $transaction_id);

			// Return results in JSON format
			if ($transaction) {
				$response = array('status' => 'SUCCESS',
					'transaction' => $transaction
				);
			} else {
				$response = array('status' => 'ERROR');
			}
			echo json_encode($response);
			break;
		case 'list':
			// Get form parameters and escape
			$account_id = $db->real_escape_string($_GET['account_id']);
			$start = $db->real_escape_string($_GET['start']);
			$end = $db->real_escape_string($_GET['end']);

			// Retrieve transactions list
			$transactions = Transaction::listByAcc($db, $account_id, $start, $end);

			// Return results in JSON format
			if ($transactions !== false) { // empty array OK
				$response = array('status' => 'SUCCESS',
					'transactions' => $transactions
				);
			} else {
				$response = array('status' => 'ERROR');
			}
			echo json_encode($transactions);
			echo "<br>";
			echo json_encode($response);
			break;
		default: 
			echo "Error! No valid API specified.";
			exit();
	}

	$db->close();
?>