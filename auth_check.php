<?php
// auth_check.php
// 💡 CRITICAL: Must be the first thing called on a protected page.
session_start();

// Check if the user is NOT logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['role'])) {
    // If not logged in, redirect them to the index/login page
    // NOTE: Use the correct path based on your folder structure
    header("Location: ../index.php"); 
    exit();
}

// User is logged in. These variables are ready for use in the dashboard.
$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];
$user_first_name = $_SESSION['first_name'];
$user_last_name = $_SESSION['last_name'];
?>