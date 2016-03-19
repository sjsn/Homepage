<?php
	session_start();
	if (!isset($_SESSION["name"])) {
		header("Location: ../../?error=login");
		die();
	} else {
		$name = $_SESSION["name"];
		$displayName = strtoupper($name) . "'s";
	}



?>

<!DOCTYPE html>
<html>
<head>
	<title><?=$displayName ?> Settings</title>
	<link rel="stylesheet" type="text/css" href="../../res/css/main.css">
</head>
<body>
	<header>
		<div id="login">
		<?php
		if (isset($_GET["error"])) {
		?>
			<p><?=$error ?></p>
		<?php
		} else {
		?>
			<a href="logout.php">Logout</a>
		<?php
		}
		?>
		</div>
		<a href="settings.php" id="webname">
			<h1 id="title"><?=$displayName ?> Settings</h1>
		</a>
	</header>
	<div id="back">
		<a href=".">
			<p>Back</p>
		</a>
	</div>
	<div id="settingsForm">
		<h2>Settings</h2>
		<form action="createsettings.php" method="post">
			Units:
			Imperial:
			<input type="radio" name="unit" value="imperial" checked />
			Metric:
			<input type="radio" name="unit" value="metric" /><br />
			City:
			<input type="text" name="city" placeholder="Seattle" /><br />
			Country:
			<input type="text" name="country" placeholder="United States" /><br />
			<br />
			<input type="submit" value="Submit">
		</form>
</body>
</html>
