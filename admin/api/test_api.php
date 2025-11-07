<?php
// api/test_api.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Start session and set user_id for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Temporary for testing
    $_SESSION['first_name'] = 'Test';
    $_SESSION['last_name'] = 'User';
}

header('Content-Type: application/json');

function jsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

try {
    // Test with hardcoded values first
    $action = 'get_user';
    $user_id = 1;
    
    echo "<!-- Testing with action: $action, user_id: $user_id -->\n";
    
    // Include files
    $base_dir = dirname(__DIR__);
    require_once $base_dir . '/includes/auth.php';
    require_once $base_dir . '/includes/database.php';
    require_once $base_dir . '/includes/admin_functions.php';

    // Get user data
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, "User with ID $user_id not found");
    }

    // Format response
    $response_data = [
        'id' => $user['id'],
        'username' => $user['username'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'role_name' => ucfirst($user['role']),
        'is_active' => $user['is_active'],
        'status' => $user['is_active'] ? 'active' : 'inactive'
    ];

    jsonResponse(true, 'User data retrieved successfully', $response_data);

} catch (Exception $e) {
    jsonResponse(false, 'Error: ' . $e->getMessage());
}
?>