<?php

	# Starts the sesssion to pull the users' name
	session_start();
	if (!isset($_SESSION["name"])) {
		$error = "Please log in to change any settings.";
		header("Location: ../../?error=$error");
		die();
	} else {
		$name = $_SESSION["name"];
		$displayName = strtoupper($name) . "'s";
	}

	$file = file("settings.txt");
	list($units, $city, $state, $country, $zip) = $file;
?>

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Settings</title>
	<link rel="icon" type="image/ico" href="../../res/img/favicon.ico" sizes="32x32">
	<link rel="stylesheet" type="text/css" href="../../res/css/main.css">
	<!-- Google Font: Lato -->
	<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="container">
		<header>
			<div id="nav">
				<div>
					<a href="../../res/forms/logout.php">Logout</a>
				</div>
			</div>
			<a href="../../" id="webname">
				<img src="../../res/img/logo.png" id="logo" alt="logo" />
				<h1 id="title"><?=$displayName ?> Homepage</h1>
			</a>
		</header>
		<div id="content">
			<div id="back">
				<a href="../../">
					Back
				</a>
			</div>
			<div id="cursetContainer">
				<div id="currentSettings">
					<h2>Current Settings</h2>
					<ul>
						<li>Units: <?=$units ?></li>
						<li>City: <?=$city ?></li>
						<li>State: <?=$state ?></li>
						<li>Country: <?=$country ?></li>
						<li>Zip: <?=$zip ?></li>
					</ul>
				</div>
			</div>
			<div id="settingsForm">
				<h2>Change Settings</h2>
				<form action="../../res/forms/createsettings.php" method="post">
					Units:
					Imperial:
					<input type="radio" name="unit" value="imperial" checked />
					Metric:
					<input type="radio" name="unit" value="metric" /><br />
					City:
					<input type="text" name="city" placeholder="Seattle" /><br />
					State:
					<input type="text" name="state" placeholder="Washington" /><br />
					Country:
					<input type="text" name="country" placeholder="United States" /><br />
					Zip Code:
					<input type="number" name="zip" placeholder="98105" maxlength="5" /><br />
					<br />
					<input type="submit" value="Submit" />
				</form>
			</div>
		</div>
		<footer>
			<p>Created by <span id="signature">Samuel San Nicolas</span></p>
			<div id="links">
				<a href="https://github.com/sjsn">github</a> 
				<span id="bar"> | </span> 
				<a href="http://www.samuelsannicolas.com">online portfolio</a>
			</div>
		</footer>
	</div>
</body>
</html>
