<?php
	session_start();
	if (isset($_SESSION["name"])) {
		header("Location: ../../?error=loggedin");
		die();
	}

	if ($_POST["username"] != "" && $_POST["pass1"] != "" && $_POST["pass2"] != "") {
		$username = $_POST["username"];
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
	
	if (!file_exists("../logins.txt")) {
		touch("../logins.txt");
	}

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

	$date = getdate();
	$day = $date["mday"];
	$month = $date["mon"];
	$year = $date["year"];
	$today = "$year/$month/$day";

	mkdir("../../users/$username/");

	# Creates 7 blank todo.txt files, one for every day of the week
	for ($i = 0; $i < 7; $i++) {
		$todayEpoch = strtotime($today);
		touch("../../users/$username/$todayEpoch.txt");
		# Currently broken. Works until the end of the month. Gotta fix
		$day++;
		$today = "$year/$month/$day";
	}

	touch("../../users/$username/index.php");
	touch("../../users/$username/settings.php");
	touch("../../users/$username/.htaccess");
	copy("../../res/temp/index.php", "../../users/$username/index.php");
	copy("../../res/temp/settings.php", "../../users/$username/settings.php");
	copy("../../res/temp/.htaccess", "../../users/$username/.htaccess");

	if (isset($_POST["units"])) {
		$units = $_POST["units"];
	} else {
		$units = "imperial";
	}
	if (isset($_POST["city"])) {
		$city = $_POST["city"];
	} else {
		$city = "Seattle";
	}
	if (isset($_POST["state"])) {
		$state = $_POST["state"];
	} else {
		$state = "Washington";
	}
	if (isset($_POST["country"])) {
		$country = $_POST["country"];
	} else {
		$country = "UnitedStates";
	}
	if (isset($_POST["zip"])) {
		$zip = $_POST["zip"];
	} else {
		$zip = "98105";
	}

	$settings = "$units\n$city\n$state\n$country\n$zip";
	file_put_contents("../../users/$username/settings.txt", $settings);

	$_SESSION["name"] = $username;
	header("Location: ../../users/$username/");
	die();
?>