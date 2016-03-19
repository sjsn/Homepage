<?php
	session_start();

	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		header("Location: ./$name/");
		die();
	}

	if (isset($_GET["error"])) {
		if ($_GET["error"] == "loggedin") {
			$error = "You are already logged in. Please logout to login in as a different user.";
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Create an Account</title>
	<link rel="stylesheet" type="text/css" href="./res/css/main.css">
</head>
<body>
	<header>
		<div id="login">
		<?php
		if ($name == "" || !isset($_SESSION["name"])) {
		?>
			<form action="./res/forms/login.php" method="post">
				<input type="text" name="name" placeholder="username" />
				<input type="password" name="pass" placeholder="password" />
				<input type="submit" value="Login" />
			</form>
		<?php
		} else {
		?>
			<a href="./res/forms/logout.php">Logout</a>
		<?php
		}
		if (isset($_GET["error"])) {
		?>
			<p><?=$error ?></p>
		<?php
		}
		?>
		</div>
		<a href="new.php" id="webname">
			<h1 id="title">Crate an Account</h1>
		</a>
	</header>
	<div id="createForm">
		<form action="./res/forms/create.php" method="post">
			Username:
			<input type="text" name="username" placeholder="e.g. billybob123" /><br />
			Password:
			<input type="password" name="pass" placeholder="e.g. Password123" /><br />
			<br />
			<input type="checkbox" name="terms" unchecked/>
			<a href="terms.html">I have read and agree to the terms of service.</a>
			<br />
			<input type="submit" value="Create Account">
		</form>
	</div>
</body>
</html>
