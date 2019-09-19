<?php 

	session_start();
	// Set session variables
	$_SESSION["favcolor"] = "green";
	$_SESSION["favanimal"] = "cat";
	echo "Session variables are set.";
	
	$username = $_SESSION['username'];
	$color = $_SESSION["favanimal"];

	echo '<div id="username">'.$username.'</div>';
	echo '<div id="username">'.$color.'</div>';
?>