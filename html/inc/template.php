<?php
class Template {
	public static function head($url) {
		$home = ""; $add_cust = ""; $rem_cust = ""; $add_acc = ""; $rem_acc = ""; $run_tr = ""; $view_tr = "";
		$subheading = "";
		
		switch($url) {
			case "home.php":
				$home = "active";
				$subheading = "Control Panel";
				break;
			case "add_cust.php":
				$add_cust = "active";
				$subheading = "Add Customer Form";
				break;
			case "rem_cust.php":
				$rem_cust = "active";
				$subheading = "Remove Customer Form";
				break;
			case "add_acc.php":
				$add_acc = "active";
				$subheading = "Add Account Form";
				break;
			case "rem_acc.php":
				$rem_acc = "active";
				$subheading = "Remove Account Form";
				break;
			case "run_tr.php":
				$run_tr = "active";
				$subheading = "Run Transaction";
				break;
			case "view_tr.php":
				$view_tr = "active";
				$subheading = "View Transactions";
				break;
			default:
				$home = "active";
				$subheading = "Control Panel";
				break;
		}

		echo <<<HEADER
			<!DOCTYPE html>
			<html>
			<head>
				<title>Cipher Bank</title>
				<meta name="viewport" content="width=device-width, initial-scale=1">

				<!-- CSS -->
				<link rel="stylesheet" href="css/bootstrap.min.css">
				<link rele="stylesheet" href="css/boostrap-theme.min.css">

				<!-- JavaScript -->
				<script type="text/javascript" src="js/jquery-1.11.0.min.js"></script>
				<script type="text/javascript" src="js/bootstrap.min.js"></script>
			</head>
			<body>
				<div class="container">
					<br>
					<nav class="navbar navbar-inverse">
						<div class="container-fluid">
							<div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar1">
									<span class="sr-only">Toggle navigation</span>
									<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
								</button>
								<a class="navbar-brand $home" href="index.php">Cipher Bank</a>
							</div>

							<div class="collapse navbar-collapse" id="navbar1">
								<ul class="nav navbar-nav">
									<li><a href="add_cust.php" class="$add_cust"><span class="glyphicon glyphicon-plus-sign"></span> Customer</a></li>
									<li><a href="rem_cust.php" class="$rem_cust"><span class="glyphicon glyphicon-minus-sign"></span> Customer</a></li>
									<li><a href="add_acc.php" class="$add_acc"><span class="glyphicon glyphicon-plus-sign"></span> Account</a></li>
									<li><a href="rem_acc.php" class="$rem_acc"><span class="glyphicon glyphicon-minus-sign"></span> Account</a></li>
									<li><a href="run_tr.php" class="$run_tr"><span class="glyphicon glyphicon-flash"></span> Trans.</a></li>
									<li><a href="view_tr.php" class="$view_tr"><span class="glyphicon glyphicon-list-alt"></span> Trans.</a></li>
								</ul>
							</div>
						</div>
					</nav>
				</div>

				<div class="container">
					<div class="page-header" style="margin-top: 0px;">
						<h1>Cipher Bank <small>$subheading</small></h1>
					</div>
				</div>
				<br>
HEADER;
// Heredoc end
	}

	public static function foot() {
		echo "</body>";
		echo "</html>";
	}
}

?>
