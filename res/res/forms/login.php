<?php
	session_start();
	if ($_SESSION["name"] != "") {
		$error = "You are already logged in.";
		header("Location: ../../index.php?error=$error");
		die();
	}

	if ($_POST["name"] != "" && $_POST["pass"] != "") {
		$name = $_POST["name"];
		$pass = $_POST["pass"];
	} else {
		$error = "Username and password cannot be blank.";
		header("Location: ../../index.php?error=$error");
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

	$error = "Invalid username or password. Please try again.";
	header("Location: ../../?error=");
	die();
?>