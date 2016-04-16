<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page genereates the users' settings and Todo information
		into JSON to be used by the front-end.
	*/

	# Starts the sesssion to pull the users' name
	session_start();

	# Checks if someone is trying to access a different users file
	if(!isset($_SESSION["name"])) {
		header("Location: ../../");
		die();
	}

	# Gets the info for connecting to the database
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

	# Makes sure that the required parameters are set. Redirects if not
	if (isset($_GET["mode"]) && isset($_GET["username"])) {
		$username = $_GET["username"];
		$name = $_SESSION["name"];
		$mode = $_GET["mode"];
		/* Checks to see if the entered username matches the username of
		the person logged in for added security. */
		if (strcmp($username, $name)) {
			header("HTTP/1.1 Invalid Login");
			die("You must be logged in to the account to view that information.");
		}
	} else {
		header("HTTP/1.1 Invalid Request");
		die("Invalid request, please check your parameters and try again.");
	}

	$cleanName = $db->quote($name);

	# Makes json for account settings
	if ($mode == "account") {
		$getSettings = "SELECT units, city, state, country, zip 
						FROM settings 
						WHERE username = {$cleanName}";
		$rows = $db->query($getSettings);
		$row = $rows->fetch();
		$settingsData = array(
			"units" => trim($row["units"]),
			"city" => trim($row["city"]),
			"state" => trim($row["state"]),
			"country" => trim($row["country"]),
			"zip" => trim($row["zip"])
		);

		$data = array(
			"account" => $username, 
			"settings" => $settingsData
		);
		makeJSON($data);
	# Makes json for ToDo List data
	} else if ($mode == "todo") {
		// Makes JSON for a single date, used for displaying data in main.js
		if (isset($_GET["date"])) {
			$today = $_GET["date"];
			# Check to make sure date parameter is accurate
			if ($today % 2 == 0 && strlen($today) >= 10) {
				# 86400 is one day in epoch time
				$eightDaysAgo = $today - (86400 * 8);
				/* If there's a ToDo List from 8 days ago or before, delete it.
				Saves 7 ToDo Lists since weather API updates funny. */
				$delOld = "DELETE FROM todos
							WHERE `date` <= '$eightDaysAgo'
							AND username = {$cleanName}";
				try {
					$db->exec($delOld);
				}
				catch (PDOException $e)
				{
					header("HTTP/1.1 Invalid");
					die("There was a problem with the query: " . $e);
				}
			} else {
				header("HTTP/1.1 Invalid Parameters");
				die("There was an error with the date parameter. Please try again.");
			}
			$data = array();
			$todoData = array();
			$todoDataHolder = array();
			$todoComplete = array();
			$getTodos = "SELECT item, checked
						FROM todos
						WHERE `date` = {$today}
						AND username = {$cleanName}
						ORDER BY num ASC";
			$rows = $db->query($getTodos);
			foreach($rows as $row) {
				$todoData["item"] = $row["item"];
				$todoData["checked"] = $row["checked"];
				$todoDataHolder[] = $todoData;
			}
			$todoComplete["items"] = $todoDataHolder;
			$data["todo"] = $todoComplete;
			makeJSON($data);
		// Makes JSON for every date, used for debugging
		} else {
			header("Location: ../../users/$username/settings.php?error=date");
			die();
		}
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("Invalid request, please check your parameters and try again.");
	}

	# Helper function to turn the passed in data into JSON
	function makeJSON($data) {
		header("Content-type: application/json");
		print json_encode($data);
	}
?>
