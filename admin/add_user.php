<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if required files exist before including them
$required_files = [
    '../includes/auth.php',
    '../includes/database.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: Required file $file not found. Please ensure all include files are properly set up.");
    }
}

try {
    require_once '../includes/auth.php';
    require_once '../includes/database.php';
    require_once '../includes/admin_functions.php';
} catch (Exception $e) {
    die("Error loading required files: " . $e->getMessage());
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role check - Must be Administrator
if (!function_exists('requireRole')) {
    // Basic session-based role check
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
        die("Access denied. Administrator role required.");
    }
} else {
    // Use the requireRole function if it exists
    requireRole(['administrator']);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get the request URI to determine the action
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Handle username availability check
    if (strpos($request_uri, 'check_username') !== false) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['username']) || empty($input['username'])) {
                throw new Exception("Username is required");
            }
            
            $username = trim($input['username']);
            
            // Check database connection
            if (!isset($db) || !$db) {
                throw new Exception("Database connection not available");
            }
            
            // Check if username already exists
            $stmt = $db->prepare("SELECT user_id, username FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                echo json_encode([
                    'success' => false,
                    'available' => false,
                    'message' => "Username already exists in the system",
                    'existing_user' => [
                        'id' => $existing['user_id'],
                        'username' => $existing['username']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'available' => true,
                    'message' => "Username is available"
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }
    
    // Handle user creation
    try {
        // Get form data
        $username = trim($_POST['username']);
        $temporary_password = trim($_POST['temporary_password']);
        $role = trim($_POST['role']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $email = !empty($_POST['email']) ? trim($_POST['email']) : null;
        $phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
        $badge_number = !empty($_POST['badge_number']) ? trim($_POST['badge_number']) : null;
        $department = !empty($_POST['department']) ? trim($_POST['department']) : null;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate mandatory fields
        $required_fields = [
            'username' => $username,
            'temporary_password' => $temporary_password,
            'role' => $role,
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
        
        foreach ($required_fields as $field => $value) {
            if (empty($value)) {
                throw new Exception("Missing required field: " . ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        // Validate username format
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new Exception("Username can only contain letters, numbers, and underscores");
        }
        
        if (strlen($username) < 3) {
            throw new Exception("Username must be at least 3 characters long");
        }
        
        // Validate password strength
        if (strlen($temporary_password) < 8) {
            throw new Exception("Temporary password must be at least 8 characters long");
        }
        
        if (!preg_match('/[A-Z]/', $temporary_password) || 
            !preg_match('/[a-z]/', $temporary_password) || 
            !preg_match('/[0-9]/', $temporary_password)) {
            throw new Exception("Temporary password must contain at least one uppercase letter, one lowercase letter, and one number");
        }
        
        // Validate role
        $allowed_roles = ['administrator', 'chief', 'officer', 'clerk'];
        if (!in_array($role, $allowed_roles)) {
            throw new Exception("Invalid role selected");
        }
        
        // Check database connection
        if (!isset($db) || !$db) {
            throw new Exception("Database connection not available");
        }
        
        // Check username uniqueness
        $check_stmt = $db->prepare("SELECT user_id FROM users WHERE username = ?");
        $check_stmt->execute([$username]);
        if ($check_stmt->fetch()) {
            throw new Exception("Username already exists in the system. Please choose a different username.");
        }
        
        // Check badge number uniqueness if provided
        if ($badge_number) {
            $badge_check = $db->prepare("SELECT user_id FROM users WHERE badge_number = ?");
            $badge_check->execute([$badge_number]);
            if ($badge_check->fetch()) {
                throw new Exception("Badge number already exists in the system. Please use a unique badge number.");
            }
        }
        
        // Start database transaction
        $db->beginTransaction();
        
        // Hash the temporary password
        $hashed_password = password_hash($temporary_password, PASSWORD_DEFAULT);
        
        // INSERT into users table
        $stmt = $db->prepare("
            INSERT INTO users (
                username, password, role, first_name, last_name, email, 
                phone, badge_number, department, is_active, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $username,
            $hashed_password,
            $role,
            $first_name,
            $last_name,
            $email,
            $phone,
            $badge_number,
            $department,
            $is_active
        ]);
        
        $user_id = $db->lastInsertId();
        
        // Commit transaction
        $db->commit();
        
        // Log activity
        if (function_exists('logActivity')) {
            logActivity(
                $_SESSION['user_id'], 
                'user_created', 
                "Created new user: $first_name $last_name ($username) with role: $role",
                $user_id
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'User account created successfully',
            'user_id' => $user_id,
            'username' => $username,
            'full_name' => $first_name . ' ' . $last_name,
            'role' => $role,
            'temporary_password' => $temporary_password // Only returned for display, not stored
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($db && $db->inTransaction()) {
            $db->rollBack();
        }
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit();
}

// Get current user info for display
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'],
    'user_id' => $_SESSION['user_id']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Mattu City Criminal Management System</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            line-height: 1.6;
        }
        
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            max-width: 1000px;
            position: relative;
            overflow: hidden;
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .form-header h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .fieldset-custom {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            background: rgba(248, 249, 250, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .fieldset-custom:hover {
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
        }
        
        .fieldset-custom legend {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            border: none;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }
        
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 10px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 10px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 10px;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .form-label-custom {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            display: block;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: #f8f9ff;
        }
        
        .form-control-custom.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 2.94 2.94L8.5 6.4l-.94-.94L4.5 8.5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        
        .form-control-custom.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        
        .required-indicator {
            color: #dc3545;
            font-weight: bold;
        }
        
        .input-group {
            display: flex;
            align-items: stretch;
        }
        
        .input-group .form-control-custom {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
        }
        
        .btn-check {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            cursor: pointer;
            white-space: nowrap;
        }
        
        .btn-check:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
        }
        
        .btn-check:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn-generate {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
            color: #212529;
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            cursor: pointer;
            margin-left: 10px;
        }
        
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            cursor: pointer;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary-custom:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .password-strength {
            margin-top: 10px;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        
        .password-strength-bar {
            height: 8px;
            border-radius: 4px;
            background: #e9ecef;
            margin-bottom: 8px;
            overflow: hidden;
        }
        
        .password-strength-fill {
            height: 100%;
            border-radius: 4px;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .password-strength.weak .password-strength-fill {
            background: #dc3545;
            width: 33%;
        }
        
        .password-strength.medium .password-strength-fill {
            background: #ffc107;
            width: 66%;
        }
        
        .password-strength.strong .password-strength-fill {
            background: #28a745;
            width: 100%;
        }
        
        .password-requirements {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .password-requirements li {
            margin-bottom: 2px;
        }
        
        .password-requirements li.valid {
            color: #28a745;
        }
        
        .password-requirements li.valid::before {
            content: '✓ ';
            font-weight: bold;
        }
        
        .password-requirements li.invalid {
            color: #dc3545;
        }
        
        .password-requirements li.invalid::before {
            content: '✗ ';
            font-weight: bold;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 20px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            position: relative;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border: 2px solid #ffc107;
        }
        
        .btn-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        .form-text-custom {
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        .form-control-custom.is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        .success-actions {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            margin: 0 10px 10px 0;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .user-credentials {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196f3;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            display: none;
        }
        
        .credential-item {
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #2196f3;
        }
        
        .credential-item strong {
            color: #1976d2;
            display: inline-block;
            width: 150px;
        }
        
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .me-2 {
            margin-right: 0.5rem;
        }
        
        .me-3 {
            margin-right: 1rem;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .form-check-input {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }
        
        .form-check-label {
            font-weight: 600;
            color: #495057;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .form-container {
                margin: 10px;
                padding: 25px;
            }
            
            .form-header h3 {
                font-size: 2rem;
            }
            
            .fieldset-custom {
                padding: 20px;
            }
            
            .col-md-6,
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-secondary-custom,
            .btn-primary-custom {
                width: 100%;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            .input-group .form-control-custom {
                border-radius: 12px;
                border-right: 2px solid #e9ecef;
                margin-bottom: 10px;
            }
            
            .btn-check,
            .btn-generate {
                border-radius: 12px;
                width: 100%;
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <div class="form-container">
            <!-- Form Header -->
            <div class="form-header">
                <h3><i class="fas fa-user-plus me-3"></i>Add New User</h3>
                <p>Create a new user account with temporary password and assigned role</p>
            </div>
            
            <!-- Main Form -->
            <form id="addUserForm" novalidate>
                
                <!-- Account Information Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-user-circle me-2"></i>Account Information</legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label form-label-custom">
                                    Username <span class="required-indicator">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-custom" id="username" name="username" required placeholder="Enter unique username">
                                    <button type="button" class="btn-check" id="checkUsernameBtn">
                                        <i class="fas fa-search me-1"></i>Check
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please provide a valid and unique username.</div>
                                <div class="form-text-custom">Username must be unique and at least 3 characters long</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label form-label-custom">
                                    User Role <span class="required-indicator">*</span>
                                </label>
                                <select class="form-control form-control-custom" id="role" name="role" required>
                                    <option value="">Select User Role</option>
                                    <option value="administrator">Administrator</option>
                                    <option value="chief">Police Chief</option>
                                    <option value="officer">Police Officer</option>
                                    <option value="clerk">Records Clerk</option>
                                </select>
                                <div class="invalid-feedback">Please select a user role.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="temporary_password" class="form-label form-label-custom">
                                    Temporary Password <span class="required-indicator">*</span>
                                </label>
                                <div class="d-flex">
                                    <input type="password" class="form-control form-control-custom" id="temporary_password" name="temporary_password" required placeholder="Enter temporary password">
                                    <button type="button" class="btn-generate" id="generatePasswordBtn">
                                        <i class="fas fa-magic me-1"></i>Generate
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please provide a strong temporary password.</div>
                                
                                <!-- Password Strength Meter -->
                                <div class="password-strength" id="passwordStrength">
                                    <div class="password-strength-bar">
                                        <div class="password-strength-fill"></div>
                                    </div>
                                    <div class="password-requirements">
                                        <ul>
                                            <li class="invalid" id="reqLength">At least 8 characters</li>
                                            <li class="invalid" id="reqUppercase">One uppercase letter</li>
                                            <li class="invalid" id="reqLowercase">One lowercase letter</li>
                                            <li class="invalid" id="reqNumber">One number</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label form-label-custom">Email Address</label>
                                <input type="email" class="form-control form-control-custom" id="email" name="email" placeholder="user@example.com">
                                <div class="form-text-custom">Optional email address for notifications</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">
                            Account is active (user can login immediately)
                        </label>
                    </div>
                </fieldset>
                
                <!-- Personal Information Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-id-card me-2"></i>Personal Information</legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label form-label-custom">
                                    First Name <span class="required-indicator">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="first_name" name="first_name" required placeholder="Enter first name">
                                <div class="invalid-feedback">Please provide the first name.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label form-label-custom">
                                    Last Name <span class="required-indicator">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="last_name" name="last_name" required placeholder="Enter last name">
                                <div class="invalid-feedback">Please provide the last name.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label form-label-custom">Phone Number</label>
                                <input type="tel" class="form-control form-control-custom" id="phone" name="phone" placeholder="+1234567890">
                                <div class="form-text-custom">Optional contact number</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department" class="form-label form-label-custom">Department</label>
                                <input type="text" class="form-control form-control-custom" id="department" name="department" placeholder="Enter department">
                                <div class="form-text-custom">Optional department assignment</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="badge_number" class="form-label form-label-custom">Badge Number</label>
                                <input type="text" class="form-control form-control-custom" id="badge_number" name="badge_number" placeholder="Enter badge number">
                                <div class="form-text-custom">Optional badge number (must be unique)</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="user_management.php" class="btn-secondary-custom">
                        <i class="fas fa-arrow-left me-2"></i>Back to User Management
                    </a>
                    
                    <button type="submit" class="btn-primary-custom" id="submitBtn" disabled>
                        <i class="fas fa-user-plus me-2"></i>Create User Account
                    </button>
                </div>
                
                <!-- Status Message Container -->
                <div id="statusMessage" class="mt-4"></div>
                
                <!-- User Credentials Display (shown after successful creation) -->
                <div id="userCredentials" class="user-credentials">
                    <h5><i class="fas fa-key me-2"></i>User Credentials Created Successfully</h5>
                    <div class="credential-item">
                        <strong>Username:</strong> <span id="displayUsername"></span>
                    </div>
                    <div class="credential-item">
                        <strong>Temporary Password:</strong> <span id="displayPassword"></span>
                    </div>
                    <div class="credential-item">
                        <strong>Full Name:</strong> <span id="displayFullName"></span>
                    </div>
                    <div class="credential-item">
                        <strong>Role:</strong> <span id="displayRole"></span>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Please provide these credentials to the user securely. 
                        They will be required to change their password upon first login.
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addUserForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusMessage = document.getElementById('statusMessage');
            const checkUsernameBtn = document.getElementById('checkUsernameBtn');
            const generatePasswordBtn = document.getElementById('generatePasswordBtn');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('temporary_password');
            const userCredentials = document.getElementById('userCredentials');
            
            let usernameAvailable = false;
            let passwordStrong = false;
            
            // Set up password strength checking
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                checkFormValidity();
            });
            
            // Username availability check
            checkUsernameBtn.addEventListener('click', function() {
                checkUsernameAvailability();
            });
            
            usernameInput.addEventListener('blur', function() {
                if (this.value.trim()) {
                    checkUsernameAvailability();
                }
            });
            
            usernameInput.addEventListener('input', function() {
                usernameAvailable = false;
                submitBtn.disabled = true;
                this.classList.remove('is-valid', 'is-invalid');
            });
            
            // Generate strong password
            generatePasswordBtn.addEventListener('click', function() {
                const generatedPassword = generateStrongPassword();
                passwordInput.value = generatedPassword;
                checkPasswordStrength(generatedPassword);
                checkFormValidity();
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                    checkFormValidity();
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                    checkFormValidity();
                });
            });
            
            // Check overall form validity
            function checkFormValidity() {
                const requiredFields = form.querySelectorAll('[required]');
                let allValid = usernameAvailable && passwordStrong;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim() || field.classList.contains('is-invalid')) {
                        allValid = false;
                    }
                });
                
                submitBtn.disabled = !allValid;
            }
            
            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!usernameAvailable) {
                    showMessage('Please verify the username availability before submitting.', 'warning');
                    return;
                }
                
                if (!passwordStrong) {
                    showMessage('Please ensure the temporary password meets all strength requirements.', 'warning');
                    return;
                }
                
                // Validate all fields
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    showMessage('Please correct the errors in the form before submitting.', 'danger');
                    return;
                }
                
                // Show loading state
                showLoading(true);
                
                // Submit via AJAX
                const formData = new FormData(form);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Show success message and user credentials
                        showMessage(
                            `<i class="fas fa-check-circle me-2"></i><strong>Success!</strong> ${result.message}`, 
                            'success'
                        );
                        
                        // Display user credentials
                        document.getElementById('displayUsername').textContent = result.username;
                        document.getElementById('displayPassword').textContent = result.temporary_password;
                        document.getElementById('displayFullName').textContent = result.full_name;
                        document.getElementById('displayRole').textContent = result.role.charAt(0).toUpperCase() + result.role.slice(1);
                        
                        userCredentials.style.display = 'block';
                        
                        // Reset form
                        form.reset();
                        
                        // Reset states
                        usernameAvailable = false;
                        passwordStrong = false;
                        submitBtn.disabled = true;
                        
                        // Reset validation classes
                        inputs.forEach(input => {
                            input.classList.remove('is-valid', 'is-invalid');
                        });
                        
                        // Reset password strength meter
                        resetPasswordStrength();
                        
                        // Scroll to credentials
                        userCredentials.scrollIntoView({ behavior: 'smooth' });
                        
                    } else {
                        showMessage(
                            `<i class="fas fa-exclamation-triangle me-2"></i><strong>Error!</strong> ${result.message}`, 
                            'danger'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(
                        `<i class="fas fa-times-circle me-2"></i><strong>Network Error!</strong> Please check your connection and try again.`, 
                        'danger'
                    );
                })
                .finally(() => {
                    showLoading(false);
                });
            });
            
            // Check username availability
            function checkUsernameAvailability() {
                const username = usernameInput.value.trim();
                
                if (!username) {
                    showMessage('Please enter a username to check availability.', 'warning');
                    return;
                }
                
                if (username.length < 3) {
                    showMessage('Username must be at least 3 characters long.', 'warning');
                    return;
                }
                
                // Show loading state
                checkUsernameBtn.disabled = true;
                checkUsernameBtn.innerHTML = '<span class="loading-spinner me-1"></span>Checking...';
                
                // Make AJAX request to check username
                fetch(window.location.href + '?check_username=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        username: username
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.available) {
                        // Username is available
                        usernameAvailable = true;
                        usernameInput.classList.remove('is-invalid');
                        usernameInput.classList.add('is-valid');
                        
                        checkFormValidity();
                        
                        showMessage(
                            `<i class="fas fa-check-circle me-2"></i><strong>Available!</strong> Username is unique and can be used.`, 
                            'success'
                        );
                        
                    } else if (!result.available) {
                        // Username already exists
                        usernameAvailable = false;
                        usernameInput.classList.remove('is-valid');
                        usernameInput.classList.add('is-invalid');
                        submitBtn.disabled = true;
                        
                        showMessage(
                            `<i class="fas fa-exclamation-triangle me-2"></i><strong>Already Exists!</strong> This username is already registered.`, 
                            'warning'
                        );
                    } else {
                        // Error occurred
                        usernameAvailable = false;
                        usernameInput.classList.remove('is-valid');
                        usernameInput.classList.add('is-invalid');
                        submitBtn.disabled = true;
                        
                        showMessage(
                            `<i class="fas fa-times-circle me-2"></i><strong>Error!</strong> ${result.message}`, 
                            'danger'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(
                        `<i class="fas fa-times-circle me-2"></i><strong>Error!</strong> Failed to check username availability. Please try again.`, 
                        'danger'
                    );
                })
                .finally(() => {
                    // Reset button state
                    checkUsernameBtn.disabled = false;
                    checkUsernameBtn.innerHTML = '<i class="fas fa-search me-1"></i>Check';
                });
            }
            
            // Check password strength
            function checkPasswordStrength(password) {
                const strengthMeter = document.getElementById('passwordStrength');
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password)
                };
                
                // Update requirement indicators
                document.getElementById('reqLength').className = requirements.length ? 'valid' : 'invalid';
                document.getElementById('reqUppercase').className = requirements.uppercase ? 'valid' : 'invalid';
                document.getElementById('reqLowercase').className = requirements.lowercase ? 'valid' : 'invalid';
                document.getElementById('reqNumber').className = requirements.number ? 'valid' : 'invalid';
                
                // Calculate strength and update meter
                const metRequirements = Object.values(requirements).filter(Boolean).length;
                const totalRequirements = Object.keys(requirements).length;
                
                if (metRequirements === 0) {
                    strengthMeter.className = 'password-strength';
                    passwordStrong = false;
                } else if (metRequirements <= 2) {
                    strengthMeter.className = 'password-strength weak';
                    passwordStrong = false;
                } else if (metRequirements === 3) {
                    strengthMeter.className = 'password-strength medium';
                    passwordStrong = true;
                } else {
                    strengthMeter.className = 'password-strength strong';
                    passwordStrong = true;
                }
                
                // Update field validation
                if (passwordStrong && password.length > 0) {
                    passwordInput.classList.remove('is-invalid');
                    passwordInput.classList.add('is-valid');
                } else if (password.length > 0) {
                    passwordInput.classList.remove('is-valid');
                    passwordInput.classList.add('is-invalid');
                } else {
                    passwordInput.classList.remove('is-valid', 'is-invalid');
                }
            }
            
            // Reset password strength meter
            function resetPasswordStrength() {
                const strengthMeter = document.getElementById('passwordStrength');
                strengthMeter.className = 'password-strength';
                
                document.getElementById('reqLength').className = 'invalid';
                document.getElementById('reqUppercase').className = 'invalid';
                document.getElementById('reqLowercase').className = 'invalid';
                document.getElementById('reqNumber').className = 'invalid';
                
                passwordStrong = false;
            }
            
            // Generate strong password
            function generateStrongPassword() {
                const length = 12;
                const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
                let password = "";
                
                // Ensure at least one of each required character type
                password += "A"; // uppercase
                password += "b"; // lowercase
                password += "1"; // number
                password += "!"; // symbol
                
                // Fill the rest randomly
                for (let i = password.length; i < length; i++) {
                    password += charset.charAt(Math.floor(Math.random() * charset.length));
                }
                
                // Shuffle the password
                return password.split('').sort(() => 0.5 - Math.random()).join('');
            }
            
            // Field validation function
            function validateField(field) {
                const value = field.value.trim();
                let isValid = true;
                
                // Required field validation
                if (field.hasAttribute('required') && !value) {
                    isValid = false;
                }
                
                // Specific field validations
                switch (field.id) {
                    case 'username':
                        if (value && (value.length < 3 || !/^[a-zA-Z0-9_]+$/.test(value))) {
                            field.setCustomValidity('Username must be at least 3 characters and contain only letters, numbers, and underscores');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'first_name':
                    case 'last_name':
                        if (value && (value.length < 2 || !/^[a-zA-Z\s]+$/.test(value))) {
                            field.setCustomValidity('Name must be at least 2 characters and contain only letters');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'email':
                        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            field.setCustomValidity('Please enter a valid email address');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'phone':
                        if (value && !/^[\+]?[0-9\s\-\(\)]+$/.test(value)) {
                            field.setCustomValidity('Please enter a valid phone number');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                }
                
                // Update field appearance
                if (isValid && (field.hasAttribute('required') || value)) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else if (!isValid) {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-valid', 'is-invalid');
                }
                
                return isValid;
            }
            
            // Show loading state
            function showLoading(show) {
                if (show) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Creating User...';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Create User Account';
                    checkFormValidity(); // Re-enable based on form validity
                }
            }
            
            // Show status message
            function showMessage(message, type) {
                statusMessage.innerHTML = `
                    <div class="alert-custom alert-${type}" role="alert">
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                        ${message}
                    </div>
                `;
                
                // Auto-hide success messages after 8 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        const alert = statusMessage.querySelector('.alert-custom');
                        if (alert) {
                            alert.remove();
                        }
                    }, 8000);
                }
                
                // Scroll to message
                statusMessage.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>