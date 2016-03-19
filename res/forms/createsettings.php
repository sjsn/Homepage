<?php
	session_start();
	if (!isset($_SESSION["name"])) {
		header("Location: ../../?error=login");
		die();
	} else {
		$name = $_SESSION["name"];
	}

	if ($_POST["city"] == "") {
		$city = "Seattle";
	} else {
		$city = $_POST["city"];
		$cityNoSpaces = explode(" ", $city);
		$city = implode("", $cityNoSpaces);
	}

	if ($_POST["country"] == "") {
		$country = "UnitedStates";
	} else {
		$country = $_POST["country"];
		$countryNoSpaces = explode(" ", $country);
		$country = implode("", $countryNoSpaces);
	}

	if ($_POST["state"] == "") {
		$state = "Washington";
	} else {
		$state = $_POST["state"];
		$stateNoSpaces = explode(" ", $state);
		$state = implode("", $stateNoSpaces);
	}

	if ($_POST["zip"] == "") {
		$zip = "98105";
	} else {
		$zip = $_POST["zip"];
	}


	if (isset($_POST["unit"])) {
		$unit = $_POST["unit"];
	} else {
		$unit = "imperial";
	}

	$newSettings = "$unit\n" .
					"$city\n" .
					"$state\n" .
					"$country\n" .
					"$zip\n";
	file_put_contents("../../users/$name/settings.txt", $newSettings);
	header("Location: ../../users/$name/settings.php");
?>