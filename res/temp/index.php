<?php
	session_start();

	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		$displayName = strtoupper($name) . "'s";
	} else {
		header("Location: ../");
		die();
	}

	if (isset($_GET["error"])) {
		if ($_GET["error"] == "loggedin") {
			$error = "You are already logged in. Please logout to login in as a different user.";
		} else if ($_GET["error"] == "invalid") {
			$error = "Invalid username or password. Please try again.";
		} else if ($_GET["error"] == "blank") {
			$error = "Please input a usrname or password";
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Homepage</title>
	<link rel="stylesheet" type="text/css" href="../../res/css/main.css">
	<script type="text/javascript" src="../../res/js/main.js"></script>
</head>
<body>
	<header>
		<div id="nav">
			<a href="../../res/forms/settings.php">Settings</a>
			<a href="../../res/forms/logout.php">Logout</a>
		</div>
		<a href="." id="webname">
			<h1 id="title"><?=$displayName ?> Homepage</h1>
		</a>
	</header>
	<div id="weather">
		<div id="current">
			<h2>Current Weather:</h2>
			<img src="../../res/img/loading.gif" id="currentloading" />
			<div id="currenterror">
			</div>
		</div>
		<div id="forecast">
			<h2>7 Day Forecast:</h2>
			<img src="../../res/img/loading.gif" id="forecastloading" />
			<div id="forecasterror">
			</div>
		</div>
	</div>
	<div id="todo">
		<h2>ToDo List</h2>
		<img src="../../res/img/loading.gif" id="todoloading" />
		<div id="listContainer">
			<p id="notodo">
			</p>
			<ul id="list">
			</ul>
		</div>
		<div id="addItem">
			<input type="text" name="newItem" id="newItem" placeholder="e.g. Buy Milk" /><br />
			<br />
			<div id="add">
				Add Item
			</div>
		</div>
		<div id="addError">
		</div>
	</div>
</body>
</html>