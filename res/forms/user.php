<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page genereates the users' settings and Todo information
		into JSON to be used by the front-end.
	*/

	# Starts the sesssion to pull the users' name
	session_start();
	if(!isset($_SESSION["name"])) {
		header("Location: ../../");
		die();
	}

	if (isset($_GET["mode"]) && isset($_GET["username"])) {
		$u = $_GET["username"];
		$n = $_SESSION["name"];
		$mode = $_GET["mode"];
		/* Checks to see if the entered username matches the username of
		the person logged in for added security. */
		if (strcmp($u, $n)) {
			header("HTTP/1.1 Invalid Login");
			die("You must be logged in to the account to view that information.");
		}
	} else {
		header("HTTP/1.1 Invalid Request");
		die("Invalid request, please check your parameters and try again.");
	}

	# If mode is account, makes json for account settings
	if ($mode == "account") {
		$settingsFile = file("../../users/$n/settings.txt");
		list($units, $city, $state, $country, $zip) = $settingsFile;
		$settingsData = array(
			"units" => trim($units),
			"city" => trim($city),
			"state" => trim($state),
			"country" => trim($country),
			"zip" => trim($zip),
		);

		$data = array(
			"account" => $u, 
			"settings" => $settingsData
		);
		makeJSON($data);
	# If mode is todo, makes json for ToDo List data
	} else if ($mode == "todo") {
		// Makes JSON for a single date, used for displaying data in main.js
		if (isset($_GET["date"])) {
			$today = $_GET["date"];
			$today = str_split($today);
			if ($today[count($today) - 1] == 0 && count($today) == 10) {
				$today = implode("", $today);
				if (!file_exists("../../users/$n/$today.txt")) {
					# Creates the new ToDo file if none exists
					touch("../../users/$n/$today.txt");
					# 86400 is one day in epoch time
					$sevenDaysAgo = $today - (86400 * 8);
					/* If there's a ToDo List from 8 days ago, delete it.
					Saves 7 ToDo Lists since weather API updates funny. */
					if (file_exists("../../users/$n/$sevenDaysAgo.txt")) {
						unlink("../../users/$n/$sevenDaysAgo.txt");
					}
				}
			} else {
				header("HTTP/1.1 Invalid Parameters");
				die("There was an error with the date parameter. Please try again.");
			}
			$todoFile = file("../../users/$n/$today.txt");
			$data = array();
			$todoData = array();
			$todoDataHolder = array();
			$todoComplete = array();
			foreach($todoFile as $items) {
				$value = explode("|", $items);
				$input = trim($value[0]);
				# Turns the true/false string into a boolean
				$checked = filter_var(trim($value[1]), FILTER_VALIDATE_BOOLEAN);
				$holderArray = array();
				$todoData["item"] = $input;
				$todoData["checked"] = $checked;
				$todoDataHolder[] = $todoData;
			}
			$todoComplete["items"] = $todoDataHolder;
			$data["todo"] = $todoComplete;
			makeJSON($data);
		// Makes JSON for every date, used for debugging
		} else {
			$date = getdate();
			$day = $date["mday"];
			$month = $date["mon"];
			$year = $date["year"];
			$today = "$year/$month/$day";
			$today = strtotime($today);
			$data = array();
			for ($i = 0; $i < 7; $i++) {
				$todoFile = file("../../users/$n/$today.txt");
				$todoComplete = array();
				$todoDataHolder = array();
				$todoData = array();
				foreach($todoFile as $items) {
					$value = explode("|", $items);
					$input = trim($value[0]);
					# Turns the true/false string into a boolean
					$checked = filter_var(trim($value[1]), FILTER_VALIDATE_BOOLEAN);
					$holderArray = array();
					$todoData["item"] = $input;
					$todoData["checked"] = $checked;
					$todoDataHolder[] = $todoData;
				}
				$todoComplete["items"] = $todoDataHolder;
				$data["$today"] = $todoComplete;
				// 86400 seconds in a day for Unix time
				$plusone = 86400;
				$today = $today + $plusone;
			}
			makeJSON($data);
		}
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("Invalid request, please check your parameters and try again.");
	}

	function makeJSON($data) {
		header("Content-type: application/json");
		print json_encode($data);
	}
?>
