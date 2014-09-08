<?php
	require 'header-footer.php';
	require 'db_func.php';

	$server_response = "<p>Waiting for form submit...</p>";

	setlocale(LC_MONETARY, 'en_US.utf8');

	// Start DB connections
	$db = db_connect();

	// Add account
	$acc_id = $_POST['acc_id'];
	$amount = str_replace(",", "", $_POST['amount']);
	$institution = "CIPHER BANK TERMINAL 001 - XX".substr($acc_id, 2);
	$overdraft_limit = -100.00;

	// Transaction code
	if ($acc_id && $amount) {
		// Get current account details
		$sql = "SELECT * FROM accounts WHERE account_id = '$acc_id';";

		if ($db !== 'CONNECT_FAIL')
			$result = db_query($db, $sql);
		else
			$server_response .= "<p>Database <span class='label label-danger'>connection failed</span></p>";

		if ($result && $result !== 'QUERY_FAIL') {
			$row = $result->fetch_assoc();
			$balance = $row['balance'];

			// Check for overdraft and fail or commit transaction
			if (($end_balance = $amount + $balance) > $overdraft_limit) {
				// Commit transaction
				$sql = "UPDATE accounts SET balance = $end_balance WHERE account_id = '$acc_id';";
				$result = db_query($db, $sql);
				if ($result && $result !== 'QUERY_FAIL') {
					$sql_trans = "INSERT INTO transactions VALUES(NULL, NULL, $balance, $amount, '$institution', 'commit', 'COMMITTED', $end_balance, '$acc_id');";
					$result_trans = db_query($db, $sql_trans);
					if ($result_trans && $result_trans !== 'QUERY_FAIL')
						$server_response .= "<p>Successfully completed transaction. New balance for account <strong>ID $acc_id</strong> is <span class='label label-success'>".money_format("%n", $end_balance)."</span></p>";
					else
						$server_response .= "<p><span class='label label-danger'>Unable</span> to complete transaction.</p>";
				} else {
					$server_response .= "<p><span class='label label-danger'>Unable</span> to start transaction.</p>";
				}
				
			} else {
				// Fail transaction
				$sql_trans = "INSERT INTO transactions VALUES(NULL, NULL, $balance, $amount, '$institution', 'fail', 'INSUFFICIENT FUNDS', $balance, '$acc_id');";
				$result_trans = db_query($db, $sql_trans);
				if ($result_trans && $result_trans !== 'QUERY_FAIL')
					$server_response .= "<p>Transaction returned status <strong>'INSUFFICIENT FUNDS'.</strong></p>";
				else
					$server_response .= "<p>Unable to complete <span class='label label-danger'>FAIL transaction</span></p>";
			}
		}
	}

	// Get list of accounts
	$sql = "SELECT * FROM accounts;";

	if ($db !== "CONNECT_FAIL")
		$acc_list = db_query($db, $sql);
	else
		$server_response .= "<p><span class='label label-danger'>Connection failed</span></p>";

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
				<h3 class="panel-title">Run Transaction</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="run_tr.php">
					<div class="form-group">
						<label for="acc_id">Select an account</label>
						<select name="acc_id" id="acc_id" class="form-control">
							<option disabled selected>Select an account</option>
							<?php
								if ($acc_list && $acc_list !== "QUERY_FAIL") {
									while ($row = $acc_list->fetch_assoc()) {
										$sql_link = "SELECT * FROM customer_account JOIN customers  ON customers.customer_id = customer_account.customer_id WHERE account_id = '".$row['account_id']."' LIMIT 1;";
										$result_link = db_query($db, $sql_link);
										$result_row = $result_link->fetch_assoc();
										if ($result_link && $result_link !== "QUERY_FAIL") {
											$selected = "";
											if ($row['account_id'] == $acc_id)
												$selected = "selected='selected'";
											echo "<option value='".$row['account_id']."' $selected>" .
												 "Account ID " . $row['account_id'] . ": " .
												 $result_row['f_name'] . ' ' .
												 $result_row['m_name'] . ' ' .
												 $result_row['l_name'] . "</option>";
										}
									}
								}
							?>
						</select>
					</div>

					<div class="form-group">
						<label for="amount">Enter amount of transaction ("-" to withdraw funds)</label>
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" name="amount" id="amount" class="form-control" placeholder="00.00">
						</div>
					</div>

					<div class="form-group">
						<label>
							Overdraft limit is <?php echo $overdraft_limit; ?>.
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
	db_close($db);
?>