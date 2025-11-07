<?php
// api/user_actions.php - WITH CORRECT COLUMN NAMES
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

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
    // Get parameters
    $action = $_GET['action'] ?? '';
    $user_id = intval($_GET['id'] ?? 0);

    // Check session
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(false, 'Not authenticated. Please log in again.');
    }

    if (empty($action) || $action !== 'get_user') {
        jsonResponse(false, 'Invalid action. Use: get_user');
    }

    if ($user_id <= 0) {
        jsonResponse(false, 'Invalid user ID');
    }

    // Include files
    $base_dir = dirname(__DIR__, 2);
    require_once $base_dir . '/includes/auth.php';
    require_once $base_dir . '/includes/database.php';
    require_once $base_dir . '/includes/admin_functions.php';

    // Get user data - USING CORRECT COLUMN NAME: user_id
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, "User with ID $user_id not found");
    }

    // Format response - using correct column names
    $response_data = [
        'id' => $user['user_id'], // Map user_id to id for frontend
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
    jsonResponse(false, 'Server error: ' . $e->getMessage());
}
?>