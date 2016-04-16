<?php
	/*
		Created by Samuel San Nicolas - 3/19/2016
		This file deletes the users account and redirects them to the login page
	*/

	# Starts the sesssion to pull the users' name
	session_start();

	# Redirects the user if the required parameters aren't set
	if (!isset($_SESSION["name"])) {
		header("Location: ../../");
		die();
	} else if (!isset($_POST["confirm"])) {
		$name = $_SESSION["name"];
		header("Location: ../../users/$name/settings.php?error=confirmDel");
		die();
	}
	$name = $_SESSION["name"];

	$server = file("serversettings.txt");

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

	# Deletes the users data from the database
	$delUser = "DELETE FROM users 
				WHERE username = {$cleanName}";
	$delTodos = "DELETE FROM todos 
				WHERE username = {$cleanName}";
	$delSettings = "DELETE FROM settings 
					WHERE username = {$cleanName}";

	try {
		$db->exec($delUser);
		$db->exec($delTodos);
		$db->exec($delSettings);
		# Deletes the users account directory
		if (is_dir("../../users/$name")) {
			$files = glob("../../users/$name/*");
			foreach ($files as $file) {
				unlink($file);
			}
			unlink("../../users/$name/.htaccess");
			if (rmdir("../../users/$name")) {
				header("Location: ../../res/forms/logout.php?del=true");
				die();
			} else {
				header("Location: ../../users/$name/settings.php?error=delError");
				die();
			}
		} else {
			header("Location: ../../users/$name/settings.php?error=delError");
			die();
		}
	}
	catch (PDOException $e)
	{
		header("Location: ../../users/$name/settings.php?error=delError");
		die();
	}

?>