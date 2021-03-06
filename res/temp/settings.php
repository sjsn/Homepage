<?php
	# Starts the sesssion to pull the users' name
	session_start();
	if (!isset($_SESSION["name"])) {
		header("Location: ../../");
		die();
	} else {
		$name = $_SESSION["name"];
		$displayName = strtoupper($name) . "'s";
	}

	if (isset($_GET["error"])) {
		$error = $_GET["error"];
		if ($error == "confirmDel") {
			$errorMsg = "Please confirm that you wish to delete your account.";
		}
	}

	$server = file("../../res/forms/serversettings.txt");

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

	$getSettings = "SELECT units, city, state, country, zip
					FROM settings
					WHERE username = {$cleanName}";
	$rows = $db->query($getSettings);
	$row = $rows->fetch();
	$units = $row["units"];
	$city = $row["city"];
	$state = $row["state"];
	$country = $row["country"];
	$zip = $row["zip"];
?>

<!--
	Created by Samuel San Nicolas - 3/19/2016
	The page the user interacts with to change their settings or delete their 
	account.
-->

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
			<div id="deleteContainer">

				<?php
					if ($error == "confirmDel") {
				?>

				<div id="special">
					<div id="delError">
							<p><?=$errorMsg ?></p>
					</div>
				</div>

				<?php
				}
				?>

				<div id="deleteForm">
					<h2>Delete Account</h2>
					<form action="../../res/forms/delete.php" method="post">
						Are you sure?<br />
						<input type="checkbox" name="confirm" unchecked />Yes, I am sure<br />
						<br />
						<input type="submit" value="delete" />
					</form>
				</div>
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
