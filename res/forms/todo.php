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
	if (isset($_POST["item"]) || $_POST["item"] = "") {
		$item = $_POST["item"];
	} else {
		header("Location: ../../?error=invalid");
		die();
	}

	# If no date or action parameter is set, throws an error
	if (isset($_POST["date"]) && isset($_POST["action"])) {
		# The date of the currently open ToDo List
		$date = $_POST["date"];
		$action = $_POST["action"];
		# Handles requests to delete ToDo items
		if ($action == "del") {
			$item = $_POST["item"];
			$file = file("../../users/$name/$date.txt");
			$newFile = "";
			foreach($file as $line) {
				$items = explode("|", $line);
				if (strcmp($item, $items[0])) {
					$newFile = $newFile . "$line";
				}
			}
			file_put_contents("../../users/$name/$date.txt", $newFile);
			header("Location: ../../users/$name/");
			die();
		# Handles requests to check (strikethrogugh) ToDo items
		} else if ($action == "check") {
			if (isset($_POST["checked"])) {
				$checked = $_POST["checked"];
				$file = file("../../users/$name/$date.txt");
				$item = $_POST["item"];
				$newFile= "";
				foreach($file as $line) {
					$items = explode("|", $line);
					if (!strcmp($item, $items[0])) {
						$item = "$item|$checked\n";
						$newFile = "$newFile" . "$item";
					} else {
						$newFile = "$newFile" . "$line";
					}
				}
				file_put_contents("../../users/$name/$date.txt", $newFile);
				header("Location: ../../users/$name/");
				die();
			} else {
				$error = "There was an error. Please try again.";
				header("Location: ../../users/$name/?error=$error");
				die();
			}
		# Handles requests to add new ToDo items
		} else if ($action == "add") {
			# Makes ToDo string standard characters only
			$item = htmlspecialchars($item);
			# Gets rid of any '|' chars to eliminate any future problems
			$item = explode("|", $item);
			# Replaces '|' items with spaces
			$item = implode(" ", $item);
			# Appends a default checked value of 'false' to each ToDo item
			$item = $item . "|false\n";
			# Appends that complete item to the end of the ToDo List file
			file_put_contents("../../users/$name/$date.txt", $item, FILE_APPEND);
			header("Location: ../../users/$name/");
			die();
		# If an action parameter was set to an invalid value, throws an error
		} else {
			header("HTTP/1.1 Invalid Parameters.");
			die("There was a problem with the action parameter. Please try again.");
		}
	} else {
		header("HTTP/1.1 Invalid Parameters");
		die("There was a problem with your parameters. Please try again.");
	}
?>