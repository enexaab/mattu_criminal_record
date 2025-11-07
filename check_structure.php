<?php
// check_structure.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Directory Structure Check</h2>";

echo "<p>Current file: " . __FILE__ . "</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";

echo "<h3>Checking includes directory:</h3>";
$includesPath = __DIR__ . '/includes';
if (is_dir($includesPath)) {
    echo "<p style='color: green;'>✓ Includes directory exists: $includesPath</p>";
    
    // List files in includes directory
    $files = scandir($includesPath);
    echo "<p>Files in includes directory:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $fullPath = $includesPath . '/' . $file;
            echo "<li>$file - " . (is_file($fullPath) ? 'File' : 'Directory') . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Includes directory NOT found: $includesPath</p>";
}

echo "<h3>Checking parent directory:</h3>";
$parentPath = dirname(__DIR__);
echo "<p>Parent directory: $parentPath</p>";

// Check if we can find the files using different paths
$possiblePaths = [
    __DIR__ . '/includes/database.php',
    __DIR__ . '/../includes/database.php', 
    dirname(__DIR__) . '/includes/database.php',
    realpath(__DIR__ . '/../includes/database.php')
];

echo "<h3>Testing different paths to database.php:</h3>";
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        echo "<p style='color: green;'>✓ FOUND: $path</p>";
    } else {
        echo "<p style='color: red;'>✗ NOT FOUND: $path</p>";
    }
}
?>