<?php
	require 'header-footer.php';
	require 'db_func.php';

	head('rem_cust.php', 'Remove Customer Form');

	$server_response = "<p>Waiting for form submit...</p>";

	// Start DB connections
	$db = db_connect();

	// Remove customer (and accounts)
	$method = $_POST['method'];
	$cust_id = $_POST['cust_id'];
	$remove_acc = $_POST['remove_acc'];

	if ($method == "delete" && $cust_id) {
		// Check for Remove Accounts
		if ($remove_acc) {
			$sql_accounts = "SELECT * FROM customer_account WHERE customer_id = '$cust_id';";
			if ($db !== "CONNECT_FAIL")
				$result_accounts = db_query($db, $sql_accounts);

			if ($result_accounts && $result_accounts->num_rows > 0) {
				while ($row = $result_accounts->fetch_assoc()) {
					$sql_0 = "DELETE FROM customer_account WHERE account_id = '".$row['account_id']."';";
					$sql_1 = "DELETE FROM accounts WHERE account_id = '".$row['account_id']."';";
					$result_0 = db_query($db, $sql_0);
					$result_1 = db_query($db, $sql_1);
					if ($result_1 !== "QUERY_FAIL")
						$server_response .= "<p>Successfully removed account <span class='label label-info'>ID ".$row['account_id']."</span></p>";
					else
						$server_response .= "<p>Unable to remove account <span class='label label-warning'>ID ".$row['account_id']."</span></p> ";
				}
			}
		}

		// Delete customer from customers table
		$sql_0 = "DELETE FROM customer_account WHERE customer_id = '$cust_id';";
		$sql_1 = "DELETE FROM customers WHERE customer_id = '$cust_id';";

		if ($db !== "CONNECT_FAIL") {
			$result_0 = db_query($db, $sql_0);
			$result_1 = db_query($db, $sql_1);
		}
		

		if ($result_1 && $result_1 !== "QUERY_FAIL")
			$server_response .= "<p>Successfully deleted customer <span class='label label-info'>ID $cust_id</span></p>";
		else
			$server_response .= "<p>Unable to delete customer <span class='label label-warning'>ID $cust_id</span></p> ";
	}

	// Get list of customers
	$sql = "SELECT * FROM customers;";

	if ($db !== "CONNECT_FAIL")
		$cust_list = db_query($db, $sql);
	else
		$server_response = "<p><span class='label label-danger'>Connection failed</span></p>";

	if ($cust_list === "QUERY_FAIL")
		$server_response .= "<p>There was an <span class='label label-danger'>error</span> in your SQL.</p>";

	db_close($db);
?>

<div class="container">
	<div class="col-md-3">
		<div class="well">
			<h4>Server Response</h4>
			<?php echo $server_response; ?>
		</div>
	</div>
	<div class="col-md-9">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Remove Existing Customer</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="rem_cust.php">
					<input type="hidden" name="method" value="delete">
					<div class="form-group">
						<label for="cust_id">Select customer to remove</label>
						<select name="cust_id" id="cust_id" class="form-control">
							<option disabled selected>Select a customer</option>
							<?php
								if ($cust_list && $cust_list !== "QUERY_FAIL") {
									while ($row = $cust_list->fetch_assoc()) {
										echo '<option value="'.$row['customer_id'].'">' .
											'ID ' . $row['customer_id'] . ': ' .
											$row['f_name'] . ' ' .
											$row['m_name'] . ' ' .
											$row['l_name'] . '</option>';
									}
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<label>
							<input type="checkbox" name="remove_acc" id="remove_acc"> Remove all associated accounts?
						</label>
					</div>

					<button type="submit" class="btn btn-primary">Submit Form</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php
	foot();
?>