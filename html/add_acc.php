<?php
	require 'header-footer.php';
	require 'db_func.php';

	head('add_acc.php', 'Add Account Form');

	$server_response = "<p>Waiting for form submit...</p>";

	setlocale(LC_MONETARY, 'en_US.utf8');

	// Start DB connections
	$db = db_connect();

	// Add account
	$cust_id = $_POST['cust_id'];
	$balance = str_replace(",", "", $_POST['balance']);

	if ($cust_id) {
		$sql = "INSERT INTO accounts VALUES(NULL, $balance, NULL, 1);";

		if ($db !== "CONNECT_FAIL")
			$result = db_query($db, $sql);

		if ($result && $result !== "QUERY_FAIL") {
			$acc_id = $db->insert_id;

			$sql_link = "INSERT INTO customer_account VALUES('$acc_id', '$cust_id', 'primary');";

			$result_link = db_query($db, $sql_link);

			if ($result_link && $result_link !== "QUERY_FAIL")
				$server_response .= "<p>Successfully added account <span class='label label-info'>ID $acc_id</span> with balance of <strong>".money_format("%n", $balance)."</strong></p>";
			else
				$server_response .= "<p><span class='label label-danger'>Unable</span> to associate account.</p>";
		} else {
			$server_response .= "<p><span class='label label-danger'>Unable</span> to add account.</p>";
		}
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
				<h3 class="panel-title">Add New Account</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="add_acc.php">
					<div class="form-group">
						<label for="cust_id">Select customer to add an account</label>
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
						<label for="balance">Enter beginning balance</label>
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" name="balance" id="balance" class="form-control" placeholder="00.00">
						</div>
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