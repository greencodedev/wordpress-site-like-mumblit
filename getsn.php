<?php 
	session_start();
	$username = $_SESSION['mode'];

	echo '<div id="username">'.$username.'</div>';
?>