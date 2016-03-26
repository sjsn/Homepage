<?php
	# Starts the sesssion to pull the users' name
	session_start();

	/* Check to see if a user is logged in. If they are, redirects them to their
	own homepage in their directory */
	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		header("Location: ./users/$name/");
		die();
	}

	# Cehck to see if there any error was thrown
	if (isset($_GET["error"])) {
		$error = $_GET["error"];
	}
?>

<!--
	Created by Samuel San Nicolas - 3/19/2016
	This page allows the user to enter information to create a new account.
-->

<!DOCTYPE html>
<html>
<head>
	<title>Create an Account</title>
	<link rel="icon" type="image/ico" href="./res/img/favicon.ico" sizes="32x32">
	<link rel="stylesheet" type="text/css" href="./res/css/main.css">
</head>
<body>
	<div id="container">
		<header>
			<div id="login">
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
				<h1 id="title">Homepage</h1>
			</a>
		</header>
		<div id="content">
			<div id="back">
				<a href=".">
					Back
				</a>
			</div>
			<div id="instructions">
				<h2>Create A New Account</h2>
				<p>
					New usernames must start with a letter, be between 3-12 characters in length, 
					and consist only of lowercase letters and numbers. New passwords must be 
					between 3-12 characters in length and consist of only numbers and letters of 
					any case.
				</p>
			</div>
			<div id="special">
				<?php
				if ($error) {
				?>
				<div id="error">
					<?=$error ?>
				</div>
				<?php
				}
				?>
			</div>
			<p id="formInstr"><span id="required">*</span> = required</p>
			<div id="createForm">
				<form action="./res/forms/create.php" method="post">
					<h3>Account Information:</h3>
					Username:
					<input type="text" name="username" placeholder="e.g. billybob123" maxlength="12" />
					<span id="required">*</span><br />
					Password:
					<input type="password" name="pass1" placeholder="e.g. Password123" maxlength="12" />
					<span id="required">*</span><br />
					Confirm Password:
					<input type="password" name="pass2" placeholder="e.g. Password123" maxlength="12"/>
					<span id="required">*</span><br />
					<h3>Account Settings:</h3>
					<p>Default settings are imperial units and Seattle, Washington.</p>
					Units: 
					Imperial: 
					<input type="radio" name="units" value="imperial" />
					Celcius: 
					<input type="radio" name="units" value="metric" /><br />
					City:
					<input type="text" name="city" placeholder="e.g. Seattle" /><br />
					State: 
					<input type="text" name="state" placeholder="e.g. Washington" /><br />
					Country: 
					<input type="text" name="country" placeholder="e.g. United Staes" /><br />
					Zip Code: 
					<input type="number" name="zip" placeholder="e.g. 98105" maxlength="5"/><br />
					<br />
					<input type="checkbox" name="terms" unchecked />
					<a href="terms.html" id="termsCheckBox">I have read and agree to the terms of service.</a>
					<span id="required">*</span><br />
					<br />
					<input type="submit" value="Create Account">
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
