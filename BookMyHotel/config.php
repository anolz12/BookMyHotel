<?php
// config.php - Main database configuration (place this in your root folder)
$host = "localhost";
$dbname = "bookmyhotel";
$username = "root"; 
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>