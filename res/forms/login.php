<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page takes in the users login information and checks if
		it matches any existing accounts.
	*/

	# Starts the sesssion to store a username
	session_start();
	# Check to see if the user is already logged in
	if ($_SESSION["name"] != "") {
		header("Location: ../../index.php");
		die();
	}

	# Check to see if the login info is blank
	if ($_POST["name"] != "" && $_POST["pass"] != "") {
		$name = $_POST["name"];
		$pass = $_POST["pass"];
	} else {
		header("Location: ../../?error=blank");
		die();
	}

	# Creates a login file if none exists
	if (!file_exists("../logins.txt")) {
		touch("../logins.txt");
	}

	# Searches through login file for matches
	$file = file("../logins.txt");
	foreach($file as $lines) {
		$account = explode("|", trim($lines));
		if ($account[0] == $name && $account[1] == $pass) {
			$_SESSION["name"] = $name;
			header("Location: ../../");
			die();
		}
	}

	# If no matches found, redirects to login page with error
	header("Location: ../../?error=invalid");
	die();
?>