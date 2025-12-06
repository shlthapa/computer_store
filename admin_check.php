<?php
// admin_check.php: Used at the top of all admin scripts for security
session_start();

//  Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    $_SESSION['error'] = "Access denied. Please log in.";
    header("Location: ../login.php");
    exit();
}

//  Check if the logged-in user is an administrator (is_admin = 1)
if ($_SESSION['is_admin'] != 1) {
    // If logged in but not an admin, redirect to homepage
    $_SESSION['error'] = "Access denied. You do not have administrator privileges.";
    header("Location: ../index.php");
    exit();
}
// If both checks pass, the script continues (user is a logged-in admin)
?>