<?php
// simple_debug.php - No authentication required
echo "<h2>Simple Database Debug</h2>";

try {
    require '../includes/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        echo "<p style='color: red;'>Database connection failed</p>";
        exit;
    }
    
    echo "<p style='color: green;'>Database connection successful!</p>";
    
    // Test if users table exists
    $stmt = $db->prepare("SHOW TABLES LIKE 'users'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p style='color: red;'>Users table does not exist!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>Users table exists!</p>";
    
    // Get all users
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total users in database: " . count($users) . "</p>";
    
    if (count($users) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Role</th><th>Active</th><th>Created</th></tr>";
        
        foreach ($users as $user) {
            $activeStatus = $user['is_active'] ? 'Yes' : 'No';
            $rowColor = $user['is_active'] ? '' : 'style="background: #ffe6e6;"';
            
            echo "<tr $rowColor>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['first_name']}</td>";
            echo "<td>{$user['last_name']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$activeStatus}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No users found in the database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Full error details:</strong><br>";
    echo "<pre>" . print_r($e, true) . "</pre></p>";
}
?>