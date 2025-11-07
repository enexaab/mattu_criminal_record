<?php
// test_simple.php - Basic connection test
echo "<h1>Basic PHP Test</h1>";
echo "PHP is working!<br>";

try {
    echo "Loading database connection...<br>";
    
    // Check if the database file exists
    $db_file = './includes/database.php';
    if (file_exists($db_file)) {
        echo "✓ database.php file exists<br>";
        require_once $db_file;
        echo "✓ database.php loaded successfully<br>";
    } else {
        echo "✗ database.php file NOT FOUND at: " . $db_file . "<br>";
        exit;
    }
    
    // Check if $db variable exists
    if (isset($db)) {
        echo "✓ \$db variable is set<br>";
        echo "✓ Database connection type: " . get_class($db) . "<br>";
    } else {
        echo "✗ \$db variable is NOT set<br>";
        exit;
    }
    
    // Test basic query
    echo "Testing database query...<br>";
    $stmt = $db->query("SELECT 1 as test");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✓ Database query successful!<br>";
        echo "✓ Connection to database is working!<br>";
    }
    
    // Test criminal_records table
    echo "Testing criminal_records table...<br>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM criminal_records");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Total criminal records: " . $count['count'] . "<br>";
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "✗ General Error: " . $e->getMessage() . "<br>";
}
?>