<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page takes the setting information entered in the users' Settings
		page. Saves all information into the users' settings.txt file.
	*/

	# Starts the sesssion to pull the users' name
	session_start();
	if (!isset($_SESSION["name"])) {
		header("Location: ../../?error=login");
		die();
	} else {
		$name = $_SESSION["name"];
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

	$cleanName = $db->quote($name);

	if ($_POST["city"] == "") {
		$city = "Seattle";
	} else {
		$city = filter_input(INPUT_POST, "city", FILTER_SANITIZE_SPECIAL_CHARS);
		$cityNoSpaces = explode(" ", $city);
		$city = implode("", $cityNoSpaces);
	}

	if ($_POST["country"] == "") {
		$country = "UnitedStates";
	} else {
		$country = filter_input(INPUT_POST, "country", FILTER_SANITIZE_SPECIAL_CHARS);
		$countryNoSpaces = explode(" ", $country);
		$country = implode("", $countryNoSpaces);
	}

	if ($_POST["state"] == "") {
		$state = "Washington";
	} else {
		$state = filter_input(INPUT_POST, "state", FILTER_SANITIZE_SPECIAL_CHARS);
		$stateNoSpaces = explode(" ", $state);
		$state = implode("", $stateNoSpaces);
	}

	if ($_POST["zip"] == "") {
		$zip = "98105";
	} else {
		$zip = filter_input(INPUT_POST, "zip", FILTER_SANITIZE_SPECIAL_CHARS);
	}

	if ($_POST["unit"] == "") {
		$unit = "imperial";
	} else {
		$unit = filter_input(INPUT_POST, "unit", FILTER_SANITIZE_SPECIAL_CHARS);
	}
	
	$city = $db->quote($city);
	$country = $db->quote($country);
	$state = $db->quote($state);
	$zip = $db->quote($zip);
	$unit = $db->quote($unit);

	$newUnit = "UPDATE settings 
				SET units = {$unit}
				WHERE username = {$cleanName}";
	$newCity = "UPDATE settings
				SET city = {$city}
				WHERE username = {$cleanName}";
	$newState = "UPDATE settings
				SET state = {$state}
				WHERE username = {$cleanName}";
	$newCountry = "UPDATE settings
				SET country = {$country}
				WHERE username = {$cleanName}";
	$newZip = "UPDATE settings
				SET zip = {$zip}
				WHERE username = {$cleanName}";

	try {
		$db->exec($newUnit);
		$db->exec($newCity);
		$db->exec($newState);
		$db->exec($newCountry);
		$db->exec($newZip);
		header("Location: ../../users/$name/settings.php?update=true");
		die();
	} 
	catch (PDOException $e) 
	{
		#header("Location: ../../users/$name/settings.php?error=update");
		print "$newState";
		die($e);
	}
?>