<?php
// db_connect.php: Establishes a connection to the MySQL database

$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "computer_store"; 

// Create 
$conn = new mysqli($servername, $username, $password, $dbname);

// Check 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 for broader character support
$conn->set_charset("utf8mb4");

// Start session here so it's available in all scripts that include this
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>