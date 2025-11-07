<?php
// api/debug_columns.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

echo "<pre>";
echo "=== DATABASE COLUMNS DEBUG ===\n";

try {
    $base_dir = dirname(__DIR__, 2);
    require_once $base_dir . '/includes/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Get columns from users table
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Columns in users table:\n";
    foreach ($columns as $column) {
        echo "  - " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\nFirst 3 users:\n";
    $stmt = $db->query("SELECT * FROM users LIMIT 3");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        print_r($user);
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
?>