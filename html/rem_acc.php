<?php
	require 'header-footer.php';
	require 'db_func.php';

	$server_response = "<p>Waiting for form submit...</p>";

	// Start DB connections
	$db = db_connect();

	// Remove account
	$acc_id = $_POST['acc_id'];
	$method = $_POST['method'];

	if ($acc_id && $method == 'delete' && $db !== 'CONNECT_FAIL') {
		// Get customer information
		$sql_customer = "SELECT * FROM customer_account JOIN customers ON customer_account.customer_id = customers.customer_id WHERE account_id = '$acc_id' LIMIT 1;";
		$result_customer = db_query($db, $sql_customer);
		$result_customer_row = $result_customer->fetch_assoc();

		// Remove links in customer_account table
		$sql_unlink = "DELETE FROM customer_account WHERE account_id = '$acc_id';";
		$result_unlink = db_query($db, $sql_unlink);

		// Remove transactions history
		$sql_rem_trans = "DELETE FROM transactions WHERE account_id = '$acc_id';";
		$result_rem_trans = db_query($db, $sql_rem_trans);

		$sql_rem_acc = "DELETE FROM accounts WHERE account_id = '$acc_id';";
		$result_rem_acc = db_query($db, $sql_rem_acc);
		if ($result_rem_acc !== "QUERY_FAIL") {
			if ($result_customer_row !== "QUERY_FAIL") {
				$server_response .= "<p>Successfully removed account <span class='label label-info'>ID $acc_id</span> belonging to <strong>" .
				$result_customer_row['f_name'] . ' ' .
				$result_customer_row['m_name'] . ' ' .
				$result_customer_row['l_name'] . '.</strong></p>';
			} else {
				$server_response .= "<p>Successfully removed account <span class='label label-info'>ID $acc_id</span></p>";	
			}
		} else {
			$server_response .= "<p><span class='label label-danger'>Unable</span> to remove account ID $acc_id.</p>";
		}
	}

	// Get list of accounts
	$sql = "SELECT * FROM accounts;";

	if ($db !== "CONNECT_FAIL")
		$acc_list = db_query($db, $sql);
	else
		$server_response = "<p><span class='label label-danger'>Connection failed</span></p>";

	if ($acc_list === "QUERY_FAIL")
		$server_response .= "<p>There was an <span class='label label-danger'>error</span> in your SQL.</p>";

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
				<h3 class="panel-title">Remove Existing Account</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="rem_acc.php">
					<input type="hidden" name="method" value="delete">
					<div class="form-group">
						<label for="acc_id">Select an account to remove</label>
						<select name="acc_id" id="acc_id" class="form-control">
							<option disabled selected>Select an account</option>
							<?php
								if ($acc_list && $acc_list !== "QUERY_FAIL") {
									while ($row = $acc_list->fetch_assoc()) {
										$sql_link = "SELECT * FROM customer_account JOIN customers  ON customers.customer_id = customer_account.customer_id WHERE account_id = '".$row['account_id']."' LIMIT 1;";
										$result_link = db_query($db, $sql_link);
										$result_row = $result_link->fetch_assoc();
										if ($result_link && $result_link !== "QUERY_FAIL") {
											echo '<option value="'.$row['account_id'].'">' .
												 'Account ID ' . $row['account_id'] . ': ' .
												 $result_row['f_name'] . ' ' .
												 $result_row['m_name'] . ' ' .
												 $result_row['l_name'] . '</option>';
										}
									}
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<label for="balance">Note: Deleting an account is not reversible!</label>
					</div>

					<button type="submit" class="btn btn-primary">Submit Form</button>
				</form>
		</div>
	</div>
</div>

<?php
	db_close($db);
	foot();
?>