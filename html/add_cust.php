<?php
	require 'header-footer.php';
	require 'db_func.php';

	$server_response = "<p>Waiting for form submit...</p> ";

	if ($_POST['f_name'] && $_POST['l_name']) {
		$f_name = $_POST['f_name'];
		$m_name = $_POST['m_name'];
		$l_name = $_POST['l_name'];

		$db = db_connect();

		$sql = "INSERT INTO customers VALUES(NULL, '$f_name', '$m_name', '$l_name');";

		if ($db !== "CONNECT_FAIL")
			$result = db_query($db, $sql);
		else
			$server_response = "<p><span class='label label-danger'>Connection failed.</span></p>";

		if ($result && $result !== "QUERY_FAIL")
			$server_response = "Added <span class='label label-success'>$f_name $m_name $l_name</span> to the database. ";
		else
			$server_response .= "<p><span class='label label-danger'>There was an error in your SQL.</span></p>";

		db_close($db);

	}
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
				<h3 class="panel-title">Add New Customer</h3>
			</div>
			<div class="panel-body">
				<form class="" role="form" method="post" action="add_cust.php">
					<div class="form-group">
						<label for="f_name">First Name</label>
						<input type="text" name="f_name" id="f_name" class="form-control">
					</div>

					<div class="form-group">
						<label for="m_name">Middle Name (optional)</label>
						<input type="text" name="m_name" id="m_name" class="form-control">
					</div>

					<div class="form-group">
						<label for="l_name">Last Name</label>
						<input type="text" name="l_name" id="l_name" class="form-control">
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