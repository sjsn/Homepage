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
		if (isset($_GET["date"])) {
			$date = $_GET["date"];
			$todoFile = file("../../users/$n/$date.txt");
			$date = getdate();
			$day = $date["mday"];
			$month = $date["mon"];
			$year = $date["year"];
			$today = "$month$day$year";
			$todoArray = array();
			# For every todo.txt file, add the contents to the ToDo json
			for ($i = 0; $i < 7; $i++) {
				$todoData = array();
				foreach($todoFile as $items) {
					$value = explode("|", $items);
					$input = trim($value[0]);
					# Turns the true/false string into a boolean
					$checked = filter_var(trim($value[1]), FILTER_VALIDATE_BOOLEAN);
					$holderArray = array();
					$todoData["item"] = $input;
					$todoData["checked"] = $checked;
					$todoArray["$today"] = $todoData;
					$day++;
					$today = "$month$day$year";
				}
			}
			$data = array(
					"today" => $today,
					"todo" => $todoArray
			);
			makeJSON($data);
		} else {
			header("HTTP/1.1 Invalid Parameters");
			die("Invalid request. No date parameter set. Please try again.");
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
