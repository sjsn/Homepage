<?php
	session_start();

	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		if (isset($_GET["error"])) {
			$error = $_GET["error"];
			$url = "./$name/?error=$error";
		} else {
			$url = "./users/$name/";
		}
		header("Location: $url");
		die();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Homepage</title>
	<link rel="icon" type="image/ico" href="./res/img/favicon.ico" sizes="32x32">
	<link rel="stylesheet" type="text/css" href="./res/css/main.css">
	<!-- Google Font: Lato -->
	<link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
</head>
<body>
	<div id="container">
		<header>
			<div id="login">
				<?php
				if (isset($_GET["error"])) {
					$error = $_GET["error"];
				?>
				<div class="error">
					<?=$error ?>
				</div>
				<?php
				}
				?>
				<form action="./res/forms/login.php" method="post">
					<input type="text" name="name" placeholder="username" />
					<input type="password" name="pass" placeholder="password" />
					<br />
					<div id="loginOptions">
						<a href="new.php" id="create">Create an account</a>
						or 
						<input type="submit" value="Login" id="loginButton" />
					</div>
				</form>
			</div>
			<a href="." id="webname">
				<img src="./res/img/logo.png" id="logo" alt="logo" />
				<h1 id="title">Your Personal Homepage</h1>
			</a>
		</header>
		<div id="content">
			<div id="pageDesc">
				<h2 id="dev">In Development</h2>
				<p>
					This website is currently in developement! Feel free to make an account and poke 
					around, but be aware that everything you see is subject to change! If you find 
					any major bugs, don't hesitate to let me know. Otherwise, enjoy what I have done 
					so far! Just be aware that I may be wiping all current accounts at any time. Thank 
					you!
				</p>
				<h2>What is this page?</h2>
				<p>
					This page provides you with weather data based on your location, the ability to
					create your own To-Do list, and the ability to add a customizable calendar.
				</p>
				<h2>How to access</h2>
				<p>
					If you're a returning user, just log in to access your account! If
					you're a returning user, simply <a href="new.php">create a new account</a> 
					to access all of the features! It's that easy!
				</p>
			</div>
		</div>
		<footer>
			<p>Created by <span id="signature">Samuel San Nicolas</span></p>
			<div id="links">
				<a href="https://github.com/sjsn">github</a> 
				<div id="bar"> | </div> 
				<a href="http://www.samuelsannicolas.com">online portfolio</a>
			</div>
		</footer>
	</div>
</body>
</html>