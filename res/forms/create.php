<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page takes in the users data from new.php and tries to create
		a new account. If it can't, throws an error and redirects.
	*/

	# Starts the sesssion to pull the users' name
	session_start();
	if (isset($_SESSION["name"])) {
		header("Location: ../../?error=loggedin");
		die();
	}

	# Check to see if the username/password fields were filled in
	if ($_POST["username"] != "" && $_POST["pass1"] != "" && $_POST["pass2"] != "") {
		$username = $_POST["username"];
		# Check to see if the password and the check matched
		if (strcmp($_POST["pass1"], $_POST["pass2"])) {
			$error = "Passwords did not match. Please try again.";
			header("Location: ../../new.php?error=$error");
			die();
		}
		$pass = $_POST["pass1"];
	} else {
		$error = "Username and password cannot be blank";
		header("Location: ../../new.php?error=$error");
		die();
	}

	# Check to see if the user checked the terms and conditions
	if ($_POST["terms"] == false) {
		$error = "You must accept the terms and condition to create an account.";
		header("Location: ../../new.php?error=$error");
		die();
	}

	/* Usernames must start with a letter, consist of only lowercase letters and numbers,
	and be no more than 12 characters in length */
	$nameReg = "/^[a-z]([a-z\d]{1,10})[a-z\d]$/";
	/* Passwords must consist only of letters and numbers and be no more than 12
	characters in length */
	$passReg = "/^[a-zA-Z\d]([a-zA-Z\d]{1,10})[a-zA-Z\d]$/";
	if (!preg_match("$nameReg", trim($username))) {
		$error = "Usernames must start with a letter, consist of only lowercase letters and" . 
		" numbers and be no more than 12 characters in length";
		header("Location: ../../new.php?error=$error");
		die();
	} else if (!preg_match("$passReg", trim($pass))) {
		$error = "Passwords must consist only of letters and numbers and be no more than 12" .
		" characters in length.";
		header("Location: ../../new.php?error=$error");
		die();
	}
	
	# Creates the logins.txt file if there isn't one already
	if (!file_exists("../logins.txt")) {
		touch("../logins.txt");
	}

	# Adds the new user to the logins.txt file so they can login in the future
	$file = file("../logins.txt");
	foreach($file as $lines) {
		$accounts = explode("|", trim($lines));
		if ($username == $accounts[0]) {
			$error = "Sorry, that username has already been taken. Please try a different one.";
			header("Location: ../../new.php?error=$error");
			die();
		}
	}
	$newaccount = "$username|$pass\n";
	file_put_contents("../../res/logins.txt", $newaccount, FILE_APPEND);

	# Creates the new users' directory
	mkdir("../../users/$username/");

	# Gets the current date's day, month, and year
	$date = getdate();
	$day = $date["mday"];
	$month = $date["mon"];
	$year = $date["year"];
	# Creates 7 blank todo.txt files, one for every day of the week
	for ($i = 0; $i < 7; $i++) {
		$today = "$year/$month/$day";
		$todayEpoch = strtotime($today);
		/* strtotime returns null if not a valid date (i.e. 2016/01/32).
		Fixes month and day if invalid (end of month rollover) */
		if (!$todayEpoch) {
			$day = 1;
			$month++;
			$today = "$year/$month/$day";
			$todayEpoch = strtotime($today);
		}
		# Fixes month, day, year if STILL invalid (end of year rollover)
		if (!$todayEpoch) {
			$year++;
			$month = 1;
			$day = 1;
			$today = "$year/$month/$day";
			$todayEpoch = strtotime($today);
		}
		touch("../../users/$username/$todayEpoch.txt");
		# Currently broken. Works until the end of the month. Gotta fix
		$day++;
	}

	# Creates files in the users' directory
	touch("../../users/$username/index.php");
	touch("../../users/$username/settings.php");
	touch("../../users/$username/.htaccess");
	copy("../../res/temp/index.php", "../../users/$username/index.php");
	copy("../../res/temp/settings.php", "../../users/$username/settings.php");
	copy("../../res/temp/.htaccess", "../../users/$username/.htaccess");

	# Sets the settings to either user defined or defaults
	if (isset($_POST["units"]) && $_POST["units"] != "") {
		$units = $_POST["units"];
	} else {
		$units = "imperial";
	}
	if (isset($_POST["city"]) && $_POST["city"] != "") {
		$city = $_POST["city"];
	} else {
		$city = "Seattle";
	}
	if (isset($_POST["state"]) && $_POST["state"] != "") {
		$state = $_POST["state"];
	} else {
		$state = "Washington";
	}
	if (isset($_POST["country"]) && $_POST["country"] != "") {
		$country = $_POST["country"];
	} else {
		$country = "UnitedStates";
	}
	if (isset($_POST["zip"]) && $_POST["zip"] != "") {
		$zip = $_POST["zip"];
	} else {
		$zip = "98105";
	}

	$settings = "$units\n$city\n$state\n$country\n$zip";
	file_put_contents("../../users/$username/settings.txt", $settings);

	# Redirects the user to their homepage in their directory
	$_SESSION["name"] = $username;
	header("Location: ../../users/$username/");
	die();
?>