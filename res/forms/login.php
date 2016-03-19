<?php
	session_start();
	if ($_SESSION["name"] != "") {
		header("Location: ../../index.php?error=loggedin");
		die();
	}

	if ($_POST["name"] != "" && $_POST["pass"] != "") {
		$name = $_POST["name"];
		$pass = $_POST["pass"];
	} else {
		header("Location: ../../index.php?error=blank");
		die();
	}

	if (!file_exists("../logins.txt")) {
		touch("../logins.txt");
	}

	$file = file("../logins.txt");
	foreach($file as $lines) {
		$account = explode("|", trim($lines));
		if ($account[0] == $name && $account[1] == $pass) {
			$_SESSION["name"] = $name;
			header("Location: ../../");
			die();
		}
	}

	header("Location: ../../?error=invalid");
	die();
?>