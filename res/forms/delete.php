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

	# Deletes the users account directory and login info
	if (is_dir("../../users/$name")) {
		$files = glob("../../users/$name/*");
		foreach ($files as $file) {
			unlink($file);
		}
		unlink("../../users/$name/.htaccess");
		if (rmdir("../../users/$name")) {
			$file = file("../logins.txt");
			$logins = "";
			foreach($file as $accounts) {
				$user = explode("|", trim($accounts));
				if ($name != $user[0]) {
					$logins = "$logins$accounts";
				}
			}
			file_put_contents("../logins.txt", $logins);
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

?>