<?php
// admin/api/find_paths.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h3>File Structure Debug</h3>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Current file: " . __FILE__ . "<br><br>";

// Test different paths
$paths_to_test = [
    '../includes/database.php',
    '../../includes/database.php',
    '/opt/lampp/htdocs/mattupolice/includes/database.php',
    './../includes/database.php'
];

foreach ($paths_to_test as $path) {
    echo "Testing: $path - ";
    if (file_exists($path)) {
        echo "<span style='color: green;'>FOUND</span><br>";
    } else {
        echo "<span style='color: red;'>NOT FOUND</span><br>";
    }
}

echo "<br>=== Directory Contents ===";
echo "<pre>";
// List contents of parent directories
echo "Current dir: " . __DIR__ . "\n";
print_r(scandir(__DIR__));

echo "\nParent dir: " . dirname(__DIR__) . "\n";
print_r(scandir(dirname(__DIR__)));

echo "\nTwo levels up: " . dirname(dirname(__DIR__)) . "\n";
print_r(scandir(dirname(dirname(__DIR__))));
echo "</pre>";
?>