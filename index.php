<?php
	session_start();

	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		if (isset($_GET["error"])) {
			$error = $_GET["error"];
			$url = "./$name/?error=$error";
		} else {
			$url = "./$name/";
		}
		header("Location: $url");
		die();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Homepage</title>
	<link rel="stylesheet" type="text/css" href="./res/css/main.css">
</head>
<body>
	<header>
		<div id="login">
			<form action="./res/forms/login.php" method="post">
				<input type="text" name="name" placeholder="username" />
				<input type="password" name="pass" placeholder="password" />
				<input type="submit" value="Login" />
			</form>
			<a href="new.php" id="create">Create an account</a>
		</div>
		<a href="." id="webname">
			<h1 id="title">Your Personal Homepage</h1>
		</a>
	</header>
	<div id="pageDesc">
		<h2>What is this page?</h2>
		<p>This page provides you with weather data based on your lcoation, the ability to
			create your own To-Do list, and the ability to add a customizable calendar.
		</p>
		<h2>How to access</h2>
		<p>
			If you're new, simply create a new account to accesss all of the features! If
			you're a returning user, simply <a href="new.php">create a new account</a>! 
			It's that easy!
		</p>
	</div>
</body>
</html>