<?php
	session_start();
	if (isset($_SESSION["name"])) {
		$name = $_SESSION["name"];
	} else {
		header("Location: ../../?error=login");
		die();
	}

	if (isset($_POST["item"])) {
		$item = $_POST["item"];
	} else {
		header("Location: ../../?error=invalid");
		die();
	}

	/*
		Change all (todo.txt)'s with ($date.txt's). $date is date of current ToDo list.
		Also add $date parameter to add, del, check functions on main.js
	*/
	if (isset($_POST["date"])) {
		# The date of the currently open ToDo List
		$date = $_POST["date"];
		if (isset($_POST["action"])) {
			$action = $_POST["action"];
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
			} else {
				$error = "Incorrect parameters. Please check and try again.";
				header("Location: ../../users/$name/?error=$error");
				die();
			}
		} else {

		}
	} else {
		# Makes ToDo string standard characters only
		$item = htmlspecialchars($item);
		# Gets rid of any '|' chars to eliminate any future problems
		$item = explode("|", $item);
		# Replaces '|' items with spaces
		$item = implode(" ", $item);
		# Appends a default checked value of 'false' to each ToDo item
		$item = $item . "|false\n";
		file_put_contents("../../users/$name/$date.txt", $item, FILE_APPEND);
		header("Location: ../../users/$name/");
		die();
	}
?>