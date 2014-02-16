<?php
	// Load database configuration
	require '../config.php';
	require '../classes/Customer.php';

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
	 * 'add' - adds a customer to db
	 * 'remove' - removes customer from db
	 * 'get' - returns customer data
	 * 'list' - lists all customers
	 * 'add_account' - adds a bank account
	 * 'del_account' - removes an account
	 */
	$api = $_REQUEST['API'];

	switch ($api) {
		case 'add':
			// Get form parameters and escape
			$f_name = $db->real_escape_string($_POST['f_name']);
			$m_name = $db->real_escape_string($_POST['m_name']);
			$l_name = $db->real_escape_string($_POST['l_name']);

			// Add customer to database
			$id = Customer::add($db, $f_name, $m_name, $l_name);

			// Return results
			if ($id !== "ERROR") {
				$response = array('status' => 'SUCCESS',
								  'customer_id' => $id);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Failed to add customer."
				);
				echo json_encode($error);
			}
			break;
		case 'remove':
			// Get form parameters and escape
			$id = $db->real_escape_string($_POST['customer_id']);

			// Remove customer from database
			$cust = new Customer($db, $id);
			$success = $cust->remove();

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' =>  "Unable to remove customer_id $id"
				);
				echo json_encode($error);
			}
			break;
		case 'get':
			// Get form parameters and escape
			$id= $db->real_escape_string($_GET['customer_id']);

			// Get customer information
			$cust = new Customer($db, $id);

			// Return results
			if ($cust) {
				$response = array('status' => 'SUCCESS',
					'customer' => array(
						'f_name' => $cust->f_name,
						'm_name' => $cust->m_name,
						'l_name' => $cust->l_name,
						'accounts' => $cust->accounts
					)
				);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to retrieve customer_id $id"
				);
				echo json_encode($error);
			}
			break;
		case 'list':
			// Get list of customers
			$customers = Customer::listAll($db);

			// Return results
			if ($customers) {
				$response = array('status' => 'SUCCESS',
					"customers" => $customers	
				);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to retrieve customer list."
				);
				echo json_encode($error);
			}
			break;
		case 'add_account':
			// Get form parameters and escape
			$customer_id = $db->real_escape_string($_POST['customer_id']);
			$account_id = $db->real_escape_string($_POST['account_id']);
			$permission = $db->real_escape_string($_POST['permission']);

			// Associate the account with the customer
			$cust = new Customer($db, $customer_id);
			$success = $cust->addAccount($account_id, $permission);

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to link account_id $account_id."
				);
				echo json_encode($error);
			}
			break;
		case 'del_account':
			// Get form parameters and escape
			$customer_id = $db->real_escape_string($_POST['customer_id']);
			$account_id = $db->real_escape_string($_POST['account_id']);

			// Unlink the account
			$cust = new Customer($db, $customer_id);
			$success = $cust->removeAccount($account_id, $permission);

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to unlink account_id $account_id."
				);
				echo json_encode($error);
			}
			break;
		default: 
			echo "Error! No valid API specified.";
			exit();
	}

	$db->close();
?>