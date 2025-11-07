<?php
// CRITICAL: Force all PHP errors to display immediately
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure session is started for consistency, even if not strictly needed for this test
session_start();

// 1. Database Connection
// Ensure the path is correct relative to this file's location
require_once 'db_connect.php'; 

$message = '';
$error = '';

// --- CRUD: CREATE (Add New User) ---
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);
    $password = 'testpass123'; // Default password for testing

    if (!empty($username) && !empty($role)) {
        // Hash the test password
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the INSERT statement
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, is_active) VALUES (?, ?, ?, 1)");
        
        if ($stmt) {
            $stmt->bind_param("sss", $username, $password_hashed, $role);
            
            if ($stmt->execute()) {
                $message = "SUCCESS! Test user **'{$username}'** created in the database.";
            } else {
                $error = "ERROR! Database execution failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "FATAL ERROR! Database statement preparation failed: " . $conn->error;
        }
    } else {
        $error = "Please fill in the Username and Role fields.";
    }
}

// --- CRUD: READ (Fetch Existing Users) ---
$users = [];
$result = $conn->query("SELECT user_id, username, role, first_name, last_name, is_active FROM users ORDER BY user_id DESC LIMIT 10");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
} else {
    $error .= " | ERROR! Failed to read data: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database CRUD Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin-bottom: 20px; border-radius: 4px; }
        form input[type="text"], form select { padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-right: 10px; }
        form button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Mattu CRM Database Connection Test (CRUD)</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error">
                <strong>Connection/Query Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2>1. Add a Test User (CREATE)</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <input type="text" name="username" placeholder="Enter Username" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="administrator">administrator</option>
                <option value="officer">officer</option>
                <option value="clerk">clerk</option>
                <option value="chief">chief</option>
                <option value="test">test</option>
            </select>
            <button type="submit">Add User</button>
        </form>

        <h2>2. Last 10 Users in the 'users' Table (READ)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="4">No users found. Check your table name and database connection.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p style="margin-top: 30px; font-size: 12px; color: #666;">
            **Database Connection Details:** Host: 127.0.0.1:3306, Database: mattu_crm_db
        </p>
    </div>
</body>
</html>