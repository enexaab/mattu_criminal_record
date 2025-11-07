<?php
// api/path_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";
echo "=== PATH DEBUG ===\n";
echo "Current file: " . __FILE__ . "\n";
echo "Current directory: " . __DIR__ . "\n";

$base_dir = dirname(__DIR__);
echo "Base directory (dirname(__DIR__)): $base_dir\n";

// Check different possible paths
$paths_to_check = [
    'includes/auth.php' => $base_dir . '/includes/auth.php',
    '../includes/auth.php' => __DIR__ . '/../includes/auth.php',
    '../../includes/auth.php' => __DIR__ . '/../../includes/auth.php',
    'auth.php' => __DIR__ . '/auth.php'
];

foreach ($paths_to_check as $name => $path) {
    echo "$name: " . (file_exists($path) ? 'EXISTS' : 'MISSING') . "\n";
    if (file_exists($path)) {
        echo "  Full path: $path\n";
    }
}

// List files in parent directory
echo "\nFiles in parent directory:\n";
$parent_dir = dirname(__DIR__);
$files = scandir($parent_dir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $full_path = $parent_dir . '/' . $file;
        echo "  $file - " . (is_dir($full_path) ? 'DIR' : 'FILE') . "\n";
    }
}

echo "</pre>";
?>