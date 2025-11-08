<?php
// db_connect.php - CONNECTS TO DB ONLY
$dbHost = '127.0.0.1'; 
$dbUser = 'root'; 
$dbPass = '';     
$dbName = 'mattu_crm_db';
$dbPort = 3306; // Assuming MySQL/MariaDB is on the default port

// Create the connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort); 

// Check for connection errors
if ($conn->connect_error) {
    // If this line appears in the browser, your connection details/service are wrong.
    die("Database Connection failed: " . $conn->connect_error);
}
?>