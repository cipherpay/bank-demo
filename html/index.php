<?php
	// Page requirments
	if (!defined("ROOT"))
		define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/");

	require ROOT."inc/template.php";

	// Set page content
	$page = $_GET["page"];

	if (empty($page))
		$page = "home.php";

	// Display page content
	Template::head($page);

	include ROOT.$page;

	Template::foot();
?>