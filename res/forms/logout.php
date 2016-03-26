<?php
	# Starts the sesssion to pull the users' name
	session_start();
	# Destroys the current session
	session_unset();
	session_destroy();
	session_regenerate_id(TRUE);

	# Redirects the user back to the login page
	if (isset($_GET["del"]) && $_GET["del"] == true) {
		header("Location: ../../?del=true");
		die();
	} else {
		header("Location: ../../");
		die();
	}
?>