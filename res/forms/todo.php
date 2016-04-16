<?php

	/*
		Created by Samuel San Nicolas - 3/19/2016
		This page handles all of the ToDo requests that involve user inputs.
	*/

	# Starts the sesssion to pull the users' name
	session_start();
	# If no name is set, user isn't logged in so redirects to login page
	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
	} else {
		header("Location: ../../?error=login");
		die();
	}

	# Handles requests for invalid ToDo items
	if (isset($_POST["item"]) && $_POST["item"] != "") {
		$item = $_POST["item"];
	} else {
		header("Location: ../../?error=invalid");
		die();
	}

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
	$item = $db->quote($item);

	# If no date or action parameter is set, throws an error
	if ($_POST["date"] != "" && $_POST["action"] != "") {
		# The date of the currently open ToDo List
		$date = $_POST["date"];
		$action = $_POST["action"];
		$max = intval(getTotal($cleanName, $date, $db)) + 1;
		# Handles requests to delete ToDo items
		if ($action == "del") {
			$num = $_POST["num"];
			$delTodo = "DELETE FROM todos 
						WHERE username = {$cleanName}
						AND item = {$item}
						AND `date` = '$date'";
			try {
				$db->exec($delTodo);
				die();
			}
			catch (PDOException $e)
			{
				#header("Location: ../../users/$name/?error=del");
				die($e);
			}
		# Handles requests to check (strikethrogugh) ToDo items
		} else if ($action == "check") {
			if ($_POST["checked"] != "") {
				$checked = $_POST["checked"];
				$num = $_POST["num"];
				$changeChecked = "UPDATE todos 
								SET checked = '$checked'
								WHERE `date` = '$date'
								AND item = {$item}";
				try{
					$db->exec($changeChecked);
					die($item);
				} 
				catch (PDOException $e)
				{
					#header("Location: ../../users/$name/?error=check");
					die($e);
				}
			} else {
				$error = "There was an error. Please try again.";
				header("Location: ../../users/$name/?error=$error");
				die();
			}
		# Handles requests to add new ToDo items
		} else if ($action == "add") {
			$addItem = filter_input(INPUT_POST, "item", FILTER_SANITIZE_SPECIAL_CHARS);
			$addItem = htmlspecialchars($addItem);
			$addItem = $db->quote($addItem);
			$addItem = "INSERT INTO todos (username, `date`, item, checked, num) 
						VALUES ({$cleanName}, '$date', {$addItem}, 'false', {$max})";
			try {
				$db->exec($addItem);
				die();
			}
			catch (PDOException $e) 
			{
				#header("Location: ../../users/$name/?error=add");
				die($e);
			}
		# If an action parameter was set to an invalid value, throws an error
		} else {
			header("HTTP/1.1 Invalid Parameters.");
			die("There was a problem with the action parameter. Please try again.");
		}
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("There was a problem with your parameters. Please try again.");
	}

	function getTotal($cleanName, $date, $db) {
		$getTotal = "SELECT MAX(num)
					FROM todos
					WHERE `date` = '$date'
					AND username = {$cleanName}";
		try {
			$max = $db->query($getTotal);
			$max = $max->fetch();
			return $max["MAX(num)"];
		}
		catch (PDOException $e)
		{
			die($e);
		}
	}
?>