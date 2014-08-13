<?php
	require 'header-footer.php';
	require 'inc/db_func.php';

	head('view_tr.php', 'View Transactions');

	$server_response = "<p>Waiting for form submit...</p>";

	setlocale(LC_MONETARY, 'en_US.utf8');

	// Start DB connections
	$db = db_connect();

	// Add account
	$acc_id = $_POST['acc_id'];

	if ($acc_id) {
		$sql_trans = "SELECT * FROM transactions WHERE account_id = '$acc_id';";

		if ($db !== 'CONNECT_FAIL')
			$result_trans = db_query($db, $sql_trans);
		else
			$server_response .= "<p><span class='label label-danger'>Unable</span> to connect to database.</p>";

		if ($result_trans && $result_trans !== 'QUERY_FAIL')
			$server_response .= "<p>Transactions for account <span class='label label-info'>ID $acc_id</span> displayed below.</p>";
		else
			$server_response .= "<p><span class='label label-danger'>Unable</span> to retrieve transactions.</span>";
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
				<h3 class="panel-title">View Account Transactions</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="view_tr.php">
					<div class="form-group">
						<label for="acc_id">Select an account to view</label>
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
						<label>
							Results displayed in table below.
						</label>
					</div>

					<button type="submit" class="btn btn-primary">Submit Form</button>
				</form>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Transactions for Account ID <?php echo $acc_id; ?>.</h3>
			</div>
			<div class="panel-body">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th>Date &amp; Time</th>
							<th>Merchant</th>
							<th>Trans. Details</th>
							<th>Amount</th>
							<th>Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if ($result_trans && $result_trans !== "QUERY_FAIL") {
								while ($row = $result_trans->fetch_assoc()) {
									echo "<tr>";
										echo "<td>".$row['datetime']."</td>";
										echo "<td>".$row['institution']."</td>";
										echo "<td>".$row['status_info']."</td>";
										echo "<td>".money_format("%n", $row['amount'])."</td>";
										echo "<td>".money_format("%n", $row['balance_end'])."</td>";
									echo "</tr>";
								}
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4"><em>Current Balance:</em></td>
							<td>
								<?php
									$sql = "SELECT * FROM accounts WHERE account_id = '$acc_id';";
									if ($db !== "CONNECT_FAIL")
										$result = db_query($db, $sql);
									else
										$result = "QUERY_FAIL";

									if ($result && $result !== "QUERY_FAIL") {
										$row = $result->fetch_assoc();
										$balance_end = $row['balance'];
										echo money_format("%n", $balance_end);
									} else {
										echo "<span class='label label-warning'>UNKN</span>";
									}
								?>
							</td>
						</tr>						
					</tfoot>
				</table>
				
			</div>
		</div>
	</div>
</div>

<?php
	foot();
	db_close($db);
?>