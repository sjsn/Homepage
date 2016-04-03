<?php
	# Starts the session to see if the user is logged in
	session_start();

	# If the user is logged in, redirects to their homepage
	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
		header("Location: ./users/$name/");
		die();
	}
?>

<!--
	Created by Samuel San Nicolas - 3/19/2016
	This is the initial page for the entire website. Here, the user can login,
	choose to create an account, and just see what the site is all about.
-->

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
				<form action="./res/forms/login.php" method="post" id="mainLogin">
					<input type="text" name="name" placeholder="username" />
					<input type="password" name="pass" placeholder="password" />
					<br />
					<div id="loginOptions">
						<a href="new.php" id="create">Create an account</a>
						or 
						<input type="submit" value="Login" id="loginButton" />
					</div>
				</form>

				<?php
				if (isset($_GET["error"])) {
					$error = $_GET["error"];
					if ($error == "blank") {
						$errorMsg = "The username and password cannot be blank. Please try again.";
					} else if ($error = "invalid") {
						$errorMsg = "That username and password is invalid. Please try again.";
					}
				?>

				<div id="mainError">
					<?=$errorMsg ?>
				</div>

				<?php
				}
				?>
				
			</div>
			<a href="." id="webname">
				<img src="./res/img/logo.png" id="logo" alt="logo" />
				<h1 id="title">Homepage</h1>
			</a>
		</header>
		<div id="homeContent">
			<div id="pageDesc">
				<h2 id="siteTitle">Everything you need to plan your week, all in one place.</h2>
				<div id="exampleContent">
					<h3 id="siteDesc">What does this homepage offer?</h3>
					<div class="section">
						<h3 class="sectionTitle">Weather</h3>
						<img src="" alt="Weather Example" />
						<p>
							Get live weather provided by the free Open Weather Map
							API. See the current temperature, a description of what
							the weather is going to be like for the day, and the 
							projected high and low temperature for the day!
						</p>
					</div>
					<div class="section">
						<h3 class="sectionTitle">ToDo Lists</h3>
						<img src="" alt="ToDo Lists Example" />
						<p>
							Create ToDo Lists for the current day! Click on the ToDo Item
							or the checkbox next to it to cross it off, or click the 'Delete'
							button next to the item to remove it from the list (irreversible).
							You may have up to 10 ToDo items for each day, so it's best to 
							use these ToDo lists as general day planning as opposed to 
							in-depth list creation.
						</p>
					</div>
					<div class="section">
						<h3 class="sectionTitle">Week Planning</h3>
						<img src="" alt="Week Planning Example" />
						<p>
							See the weather forecast for the week! Also, click on any day
							of the week to see that days ToDo items! This allows you to 
							plan ahead for your week and improve your productivity! 
						</p>
					</div>
					<div id="otherFeatures">
						<h3>Keyboard Shortcuts</h3>
						<table id="featureList">
							<tr><th>Feature</th></tr>
							<tr><td>Use the keys ' [ ' and ' ] ' to navigate between days of the week</td></tr>
							<tr><td>Press the ' - ' (dash) key to delete the last set ToDo item</td></tr>
							<tr><td>Press the ' enter ' key to add ToDo list items</td></tr>
							<tr><td>If nothing is selected, hit the 'enter' key to select the ToDo text field</td></tr>
						</table>
					</div>
				</div>
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