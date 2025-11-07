<?php
// security_logs.php
require 'includes/auth.php';
require 'includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get all security logs
$query = "SELECT sl.*, u.username 
          FROM security_logs sl 
          LEFT JOIN users u ON sl.user_id = u.id 
          ORDER BY sl.created_at DESC 
          LIMIT 100";
$stmt = $db->prepare($query);
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Security Logs</title>
    <style>
        /* Add similar styles as security.php */
    </style>
</head>
<body>
    <div class="container">
        <h1>Security Logs</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Event Type</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                    <td><?php echo htmlspecialchars($log['event_type']); ?></td>
                    <td><?php echo htmlspecialchars($log['description']); ?></td>
                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>