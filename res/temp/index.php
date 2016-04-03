<?php
	# Starts the sesssion to pull the users' name
	session_start();

	/* Redirects the user if they attempt to log into a page that belongs
	to a different account than the one they're logged in to. */
	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		$dir = getcwd();
		$dir = explode("/", $dir);
		$dir = $dir[9];
		if (strcmp($name, $dir)) {
			header("Location: ../");
			die();
		}
		$displayName = strtoupper($name) . "'s";
	} else {
		header("Location: ../");
		die();
	}
?>

<!--
	Created by Samuel San Nicolas - 3/19/2016
	This page is the HTML for the users' personal homepage. The rest of the information
	is genereated in main.js.
-->

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Homepage</title>
	<link rel="icon" type="image/ico" href="../../res/img/favicon.ico" sizes="32x32">
	<link rel="stylesheet" type="text/css" href="../../res/css/main.css">
	<!-- Google Font: Lato -->
	<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<script type="text/javascript" src="../../res/js/main.js"></script>
</head>
<body>
	<div id="container">
		<header>
			<div id="nav">
				<div>
					<a href="./settings.php">Settings</a>
				</div>
				<div>
					<a href="../../res/forms/logout.php">Logout</a>
				</div>
			</div>
			<a href="." id="webname">
				<img src="../../res/img/logo.png" id="logo" alt="logo" />
				<h1 id="title"><?=$displayName ?> Homepage</h1>
			</a>
		</header>
		<div id="content">
			<div id="present">
				<h2 id="selected"></h2>
				<h3 id="presentDate"></h3>
				<div id="currentContent">
					<div id="current">
						<img src="../../res/img/loading.gif" id="currentloading" alt="loading" />
						<div id="currenterror">
						</div>
						<div id="currentWeather">	
						</div>
					</div>
					<div id="todo">
						<h3>ToDo List</h3>
						<img src="../../res/img/loading.gif" id="todoloading" alt="loading" />
						<div id="listContainer">
							<p id="todoerror">
							</p>
							<table id="list">
							</table>
							<div id="addItem">
								<br />
								<input type="text" name="newItem" id="newItem" size="30" maxlength="30" 
								placeholder="e.g. Finish homework (30 char limit)" />
								<div id="add">
									Add
								</div>
								<div id="addError">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="week">
				<h3>This Week:</h3>
				<div id="forecast">
					<img src="../../res/img/loading.gif" id="forecastloading" alt="loading" />
					<div id="forecasterror">
					</div>
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