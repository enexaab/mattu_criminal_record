<?php
// check_database.php
require 'includes/database.php';

$database = new Database();
$pdo = $database->getConnection();

echo "<h2>Database Structure Check</h2>";

// Check what tables exist
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "<h3>Existing Tables:</h3>";
echo "<ul>";
foreach ($tables as $table) {
    echo "<li>$table</li>";
    
    // Show columns for each table
    $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>{$column['Field']} ({$column['Type']})</li>";
    }
    echo "</ul>";
}
echo "</ul>";
?>