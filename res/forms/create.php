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
		$username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
		# Check to see if the password and the check matched
		if (strcmp($_POST["pass1"], $_POST["pass2"])) {
			$error = "Passwords did not match. Please try again.";
			header("Location: ../../new.php?error=$error");
			die();
		}
		$pass = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_SPECIAL_CHARS);
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
	
	$server = file("serversettings.txt");

	# The database login credientials;
	$servername = trim($server[0]);
	$serverport = trim($server[1]);
	$serveruser = trim($server[2]);
	$serverpass = trim($server[3]);
	$dbname = trim($server[4]);

	# Establishes connection with database via PDO object
	$db = new PDO("mysql:dbname=$dbname;port=$serverport;host=$servername;charset=utf8", "$serveruser", "$serverpass");
	# Generates SQL error messages
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$cleanName = $db->quote($username);
	$cleanPass = $db->quote($pass);

	# Checks to see if the username is already in use
	$dupCheck = "SELECT username 
				FROM users 
				WHERE username = {$cleanName}";
	$rows = $db->query($dupCheck);
	$row = $rows->fetch();
	if ($row["username"] && $row["username"] != "") {
		$error = "Sorry, that username has already been taken. Please try a different one.";
		header("Location: ../../new.php?error=$error");
		die();
	}

	try {
		# Adds the new user to the database so they can login in the future
		$addUser = "INSERT INTO users (username, password) 
					VALUES ({$cleanName}, {$cleanPass})";
		$db->exec($addUser);
	} 
	catch (PDOException $e) 
	{
		header("Location: ../../?error=add");
		die($e);
	}

	# Creates a "users" directory if none exists
	if (!file_exists("../../users")) {
		mkdir("../../users/");
	}
	# Creates the new users' directory
	mkdir("../../users/$username/");

	# Creates files in the users' directory
	touch("../../users/$username/index.php");
	touch("../../users/$username/settings.php");
	touch("../../users/$username/.htaccess");
	copy("../../res/temp/index.php", "../../users/$username/index.php");
	copy("../../res/temp/settings.php", "../../users/$username/settings.php");
	copy("../../res/temp/.htaccess", "../../users/$username/.htaccess");

	# Sets the settings to either user defined or defaults
	if (isset($_POST["units"]) && $_POST["units"] != "") {
		$units = filter_input(INPUT_POST, "units", FILTER_SANITIZE_SPECIAL_CHARS);
	} else {
		$units = "imperial";
	}
	if (isset($_POST["city"]) && $_POST["city"] != "") {
		$city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS);

	} else {
		$city = "Seattle";
	}
	if (isset($_POST["state"]) && $_POST["state"] != "") {
		$state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_SPECIAL_CHARS);
	} else {
		$state = "Washington";
	}
	if (isset($_POST["country"]) && $_POST["country"] != "") {
		$country = filter_input(INPUT_POST, "country", FILTER_SANITIZE_SPECIAL_CHARS);
	} else {
		$country = "UnitedStates";
	}
	if (isset($_POST["zip"]) && $_POST["zip"] != "") {
		$zip = filter_input(INPUT_POST, "zip", FILTER_SANITIZE_SPECIAL_CHARS);
	} else {
		$zip = "98105";
	}

	$units = $db->quote($units);
	$city = $db->quote($city);
	$state = $db->quote($state);
	$country = $db->quote($country);
	$zip = $db->quote($zip);


	$addSettings = "INSERT INTO settings (username, units, city, state, country, zip)
					VALUES ({$cleanName}, {$units}, {$city}, {$state}, {$country}, {$zip})";
	try {
		$db->exec($addSettings);
		# Redirects the user to their homepage in their directory
		$_SESSION["name"] = $username;
		header("Location: ../../users/$username/");
		die();
	}
	catch (PDOException $e) 
	{
		#header("Location: ../../new.php?error=dbadd");
		die($e);
	}
?>