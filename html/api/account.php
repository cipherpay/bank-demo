<?php
	// Load database configuration
	require '../config.php';
	require '../classes/Customer.php';
	require '../classes/Account.php';

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
	 * 'add' - adds an account to db
	 * 'remove' - removes account from db
	 * 'get' - returns account data
	 * 'list' - lists all accounts
	 * 'add_customer' - adds a customer to the account
	 * 'del_customer' - removes a customer
	 */
	$api = $_REQUEST['api'];

	switch ($api) {
		case 'add':
			// Get form parameters and escape
			$balance = $db->real_escape_string($_POST['balance']);
			$type = $db->real_escape_string($_POST['type']);
			$name = $db->real_escape_string($_POST['name']);
			$customer_id = $db->real_escape_string($_POST['customer_id']);
			$add_customer_status = false;

			// Add account to database
			$id = Account::add($db, $balance, $type, $name);

			if (is_numeric($customer_id)) {
				$acct = new Account($db, $id);
				$add_customer_status = $acct->addCustomer($customer_id);
			}

			// Return results
			if ($id !== "ERROR") {
				$response = array('status' => 'SUCCESS',
								  'account_id' => $id,
								  'added_customer' => $add_customer_status);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Failed to create account."
				);
				echo json_encode($error);
			}
			break;
		case 'remove':
			// Get form parameters and escape
			$id = $db->real_escape_string($_POST['account_id']);

			// Remove account and associated transactions
			$acct = new Account($db, $id);
			$success = $acct->remove();

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' =>  "Unable to remove account_id $id"
				);
				echo json_encode($error);
			}
			break;
		case 'get':
			// Get form parameters and escape
			$id = $db->real_escape_string($_GET['account_id']);
			$get_customer_details = $_GET['list_customers'];
			
			// Get account information
			$acct = new Account($db, $id);

			// Return results
			if ($acct) {
				// Prepare $customers list
				if ($get_customer_details) {
					$customers = array();
					for ($i = 0; $i < count($acct->customers); $i++) {
						$cust = new Customer($db, $acct->customers[$i]);
						$customer = array(
							'customer_id' => $cust->id,
							'f_name' => $cust->f_name,
							'm_name' => $cust->m_name,
							'l_name' => $cust->l_name
						);
						array_push($customers, $customer);
					}
				} else {
					$customers = $acct->customers;
				}
				// Generate response
				$response = array('status' => 'SUCCESS',
					'account' => array(
						'account_id' => $id,
						'balance' => $acct->balance,
						'type' => $acct->type,
						'min_balance' => $acct->min_balance,
						'customers' => $customers // defined above
					)
				);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to retrieve account_id $id"
				);
				echo json_encode($error);
			}
			break;
		case 'list':
			// Get parameters
			$get_customer_details = $_GET['list_customers'];

			// Get list of accounts
			$temp_accounts = Account::listAll($db);

			if ($get_customer_details) {
				$accounts = array();
				for ($i = 0; $i < count($temp_accounts); $i++) {
					$acct = new Account($db, $temp_accounts[$i]['account_id']);
					$customers = array();
					for ($j = 0; $j < count($acct->customers); $j++) {
						$cust = new Customer($db, $acct->customers[$j]);
						$customer = array(
							'customer_id' => $cust->id,
							'f_name' => $cust->f_name,
							'm_name' => $cust->m_name,
							'l_name' => $cust->l_name
						);
						array_push($customers, $customer);
					}
					array_push($accounts, array_merge($temp_accounts[$i], array('customers' => $customers)));
				}
			} else {
				$accounts = $temp_accounts;
			}

			// Return results
			if ($accounts) {
				$response = array('status' => 'SUCCESS',
					"accounts" => $accounts	
				);
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to retrieve account list."
				);
				echo json_encode($error);
			}
			break;
		case 'add_customer':
			// Get form parameters and escape
			$account_id = $db->real_escape_string($_POST['account_id']);
			$customer_id = $db->real_escape_string($_POST['customer_id']);
			$permission = $db->real_escape_string($_POST['permission']);

			// Associate the account with the customer
			$acct = new Account($db, $account_id);
			$success = $acct->addCustomer($customer_id, $permission);

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to link customer_id $customer_id."
				);
				echo json_encode($error);
			}
			break;
		case 'del_customer':
			// Get form parameters and escape
			$account_id = $db->real_escape_string($_POST['account_id']);
			$customer_id = $db->real_escape_string($_POST['customer_id']);

			// Unlink the customer
			$acct = new Account($db, $account_id);
			$success = $acct->removeCustomer($customer_id);

			// Return results
			if ($success) {
				$response = array('status' => 'SUCCESS');
				echo json_encode($response);
			} else {
				$error = array('status' => 'ERROR',
					'message' => "Unable to unlink customer_id $customer_id."
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