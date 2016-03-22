<?php
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
			$error = "You must be logged in to check that data.";
			header("Location: ../../?error=$error");
			die();
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
			"zip" => trim($zip)
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
			$today = $_GET["date"];
			$todoFile = file("../../users/$n/$today.txt");
			$data = array();
			$todoData = array();
			$todoDataHolder = array();
			$todoComplete = array();
			for ($i = 0; $i < 7; $i++) {
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
