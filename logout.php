<?php
// logout.php - UPDATED WITH SESSION TRACKING
session_start();
require 'db_connect.php';

// ✅ ADD SESSION LOGOUT RECORDING
if (isset($_SESSION['user_id'])) {
    try {
        require 'includes/session_manager.php';
        $sessionManager = new SessionManager($conn);
        $sessionManager->recordLogout();
    } catch (Exception $e) {
        error_log("Logout recording error: " . $e->getMessage());
        // Continue with logout even if recording fails
    }
}

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page with logout message
header("Location: index.php?logout=1");
exit();
?>