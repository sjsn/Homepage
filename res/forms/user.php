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
		if (strcmp($u, $n) != 0) {
			header("Location: ../../?error=invalid");
			die();
		}
	} else {
		header("HTTP/1.1 Invalid Request");
		die("Invalid request, please check your parameters and try again.");
	}

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
	} else if ($mode == "todo") {
		$todoFile = file("../../users/$n/todo.txt");
		$date = getdate();
		$day = $date["mday"];
		$month = $date["mon"];
		$year = $date["year"];
		$today = "$month$day$year";
		$todoData = array();
		$todoArray = array();
		foreach($todoFile as $items) {
			$value = explode("|", $items);
			$input = trim($value[0]);
			$checked = filter_var(trim($value[1]), FILTER_VALIDATE_BOOLEAN);
			$holderArray = array();
			$todoData["item"] = $input;
			$todoData["checked"] = $checked;
			$todoArray[] = $todoData;
		}
		$data = array(
			"date" => $today,
			"todo" => $todoArray
			);
		makeJSON($data);
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("Invalid request, please check your parameters and try again.");
	}

	function makeJSON($data) {
		header("Content-type: application/json");
		print json_encode($data);
	}
?>
