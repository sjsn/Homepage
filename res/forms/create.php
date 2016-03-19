<?php

	/*
		TODO: 
		-Create standards for username/pass
		-Implement errors into new.php
		-Implement a redundant password check (2 inputs, match or no?)
	*/
	session_start();
	if (isset($_SESSION["name"])) {
		header("Location: ../../?error=loggedin");
		die();
	}

	if ($_POST["username"] != "" && $_POST["pass"] !="") {
		$username = $_POST["username"];
		$pass = $_POST["pass"];
	} else {
		header("Location: ../../new.php?error=blank");
		die();
	}

	if ($_POST["terms"] == false) {
		header("Location: ../../new.php?error=terms");
		die();
	}

	if (!file_exists("../../res/logins.txt")) {
		touch("../../res/logins.txt");
	}

	$file = file("../../res/logins.txt");
	foreach($file as $lines) {
		$accounts = explode("|", trim($lines));
		if ($username == $accounts[0]) {
			header("Location: ../../new.php?error=used");
			die();
		}
	}
	$newaccount = "$username|$pass\n";
	file_put_contents("../../res/logins.txt", $newaccount);

	mkdir("../../users/$username/");
	touch("../../users/$username/index.php");
	touch("../../users/$username/settings.txt");
	touch("../../users/$username/todo.txt");
	touch("../../users/$username/settings.php");
	touch("../../users/$username/stop.htaccess");
	copy("../../res/temp/index.php", "../../users/$username/index.php");
	copy("../../res/temp/settings.txt", "../../users/$username/settings.txt");
	copy("../../res/temp/settings.php", "../../users/$username/settings.php");
	copy("../../res/temp/stop.htaccess", "../../users/$username/stop.htaccess");

	$_SESSION["name"] = $username;
	header("Location: ../../users/$username/");
	die();
?>