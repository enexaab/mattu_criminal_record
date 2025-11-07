<?php
// api/user_actions_simple.php - Debug version
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Simple response function
function sendResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

try {
    // Check session
    if (!isset($_SESSION['user_id'])) {
        sendResponse(false, 'No user session found. Session data: ' . json_encode($_SESSION));
    }

    // Get action and ID
    $action = $_GET['action'] ?? '';
    $user_id = $_GET['id'] ?? 0;

    if ($action !== 'get_user') {
        sendResponse(false, 'Invalid action. Received: ' . $action);
    }

    if (!$user_id) {
        sendResponse(false, 'No user ID provided');
    }

    // Include required files
    $base_dir = dirname(__DIR__);
    $auth_file = $base_dir . '/includes/auth.php';
    $database_file = $base_dir . '/includes/database.php';
    $admin_functions_file = $base_dir . '/includes/admin_functions.php';

    if (!file_exists($auth_file)) {
        sendResponse(false, 'Auth file not found: ' . $auth_file);
    }
    require_once $auth_file;

    if (!file_exists($database_file)) {
        sendResponse(false, 'Database file not found: ' . $database_file);
    }
    require_once $database_file;

    if (!file_exists($admin_functions_file)) {
        sendResponse(false, 'Admin functions file not found: ' . $admin_functions_file);
    }
    require_once $admin_functions_file;

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        sendResponse(false, 'Failed to connect to database');
    }

    // Get user data directly (bypassing functions temporarily)
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        sendResponse(false, 'User not found with ID: ' . $user_id);
    }

    // Format response
    $user['role_name'] = ucfirst($user['role'] ?? 'unknown');
    $user['status'] = ($user['is_active'] ?? 0) ? 'active' : 'inactive';

    sendResponse(true, 'User data retrieved successfully', $user);

} catch (Exception $e) {
    sendResponse(false, 'Exception: ' . $e->getMessage());
}
?>