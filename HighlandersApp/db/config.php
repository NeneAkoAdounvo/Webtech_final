<?php
$servername = "localhost";  
$username = "victor.adounvo";         // Database username
$password = "160119";             // Database password
$dbname = "webtech_fall2024_victor_adounvo";  // Database name

// $servername = "localhost";  
// $username = "root";         // Database username
// $password = "";             // Database password
// $dbname = "highlandersfc";  // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
