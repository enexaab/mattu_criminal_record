<?php
// admin/api/debug_test.php - SIMPLE DEBUG
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== PHP Debug Test ===<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Script: " . __FILE__ . "<br><br>";

// Test 1: Basic PHP
echo "Test 1: Basic PHP - ";
echo "OK<br>";

// Test 2: Session
echo "Test 2: Session - ";
session_start();
echo "OK<br>";

// Test 3: Database connection
echo "Test 3: Database Connection - ";
try {
    require_once '../includes/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "SUCCESS - Connected to database<br>";
        
        // Test 4: Check users table
        echo "Test 4: Users Table - ";
        $stmt = $db->query("SHOW TABLES LIKE 'users'");
        if ($stmt->rowCount() > 0) {
            echo "EXISTS<br>";
            
            // Test 5: Count users
            $stmt = $db->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Users count: " . ($result['count'] ?? 0) . "<br>";
        } else {
            echo "NOT FOUND<br>";
        }
    } else {
        echo "FAILED - No connection<br>";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

echo "<br>=== Debug Complete ===";
?>