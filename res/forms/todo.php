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

	if (isset($_POST["action"])) {
		$action = $_POST["action"];
		if ($action == "del") {
			$item = $_POST["item"];
			$file = file("../../users/$name/todo.txt");
			$newFile = "";
			foreach($file as $line) {
				$items = explode("|", $line);
				if (strcmp($item, $items[0])) {
					$newFile = $newFile . "$line";
				}
			}
			file_put_contents("../../users/$name/todo.txt", $newFile);
			header("Location: ../../users/$name/");
			die();
		} else if ($action == "check") {
			if (isset($_POST["checked"])) {
				$checked = $_POST["checked"];
				$file = file("../../users/$name/todo.txt");
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
				file_put_contents("../../users/$name/todo.txt", $newFile);
				header("Location: ../../users/$name/");
				die();
			} else {
				header("Location: ../../users/$name/?error=check");
				die();
			}
		}
	} else {
		$item = $item . "|false\n";
		file_put_contents("../../users/$name/todo.txt", $item, FILE_APPEND);
		header("Location: ../../users/$name/");
		die();
	}
?>