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
		$name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
		$pass = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_SPECIAL_CHARS);
	} else {
		header("Location: ../../?error=blank");
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
	$cleanName = htmlspecialchars($name);
	$cleanName = $db->quote($cleanName);
	$cleanPass = htmlspecialchars($pass);
	$cleanPass = $db->quote($cleanPass);

	# Searches through database for matches
	$loginCheck = "SELECT username 
					FROM users 
					WHERE username = {$cleanName} 
					AND password = {$cleanPass}";
	try {
		$rows = $db->query($loginCheck);
		$row = $rows->fetch();
		if ($row["username"] && $row["username"] != "") {
			$_SESSION["name"] = "$name";
			header("Location: ../../users/$name");
			die();
		} else {
			# If no matches found, redirects to login page with error
			header("Location: ../../?error=invalid");
			die();
		}
	}
	catch (PDOException $e)
	{
		#header("Location: ../../?error=invalid");
		die($e);
	}
?>