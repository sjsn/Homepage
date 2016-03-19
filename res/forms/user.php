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
		$settingsFile = file("../../$n/settings.txt");
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
		$todoFile = file("../../$n/todo.txt");
		$count = count($todoFile);
		$todoData = array();
		for($i = 0; $i < $count; $i++) {
			array_push($todoData, strval("item") -> trim($todoFile[$i]));
		}
		$data = array("todo" => $todoData);
		makeJSON($name, $data);
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("Invalid request, please check your parameters and try again.");
	}

	function makeJSON($data) {
		header("Content-type: application/json");
		print json_encode($data);
	}
?>
