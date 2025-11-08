<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function requireClerkAccess() {
    if (!isLoggedIn() || getUserRole() !== 'clerk') {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
}


// Prevent any mutation operations for clerks
function preventMutationOperations() {
    $method = $_SERVER['REQUEST_METHOD'];
    if (in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
        http_response_code(405);
        echo json_encode(['error' => 'Mutation operations not allowed for clerk role']);
        exit;
    }
}

// New function to match dashboard_data.php requirement
function requireRole($requiredRoles) {
    if (!isLoggedIn() || !in_array(getUserRole(), $requiredRoles)) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
}
?>