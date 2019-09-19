<?php
$servername = "localhost";
$username = "tkpohtke_mumnew";
$password = "!#Skylaraj24!";

try {
    $conn = new PDO("mysql:host=$servername;dbname=tkpohtke_mumblit_mumnew", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
    UPDATE Wo_Posts SET newviews = 0;	
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>

