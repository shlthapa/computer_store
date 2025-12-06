<?php
// logout.php: Destroys the current user session

session_start();

$_SESSION = array();
session_destroy(); // Destroy the session

// Redirect to the login page
header("Location: login.php");
exit();
?>