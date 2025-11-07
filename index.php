<?php
// index.php - SECURE LOGIN PAGE WITH SECURITY POLICY ENFORCEMENT
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); 
require 'db_connect.php';
require 'includes/security_enforcer.php'; // Add security enforcer

$error = '';
$success = '';

// Initialize security enforcer
$securityEnforcer = new SecurityEnforcer($conn);
// Add this function after your database connection
function updateLastLogin($user_id, $conn) {
    $sql = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Failed to prepare last_login update: " . $conn->error);
        return false;
    }
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
// Handle POST Request (Login Attempt)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, username, password, role, first_name, last_name, login_attempts, is_active FROM users WHERE username = ?");
        
        if ($stmt === false) {
             $error = "Database error during login preparation: " . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                
                // SECURITY CHECK 1: Check if account is active
                if (!$user['is_active']) {
                    $error = "Account is deactivated. Please contact administrator.";
                    $securityEnforcer->logSecurityEvent('login_attempt', "Attempted login to deactivated account: $username");
                }
                // SECURITY CHECK 2: Check login attempts
                elseif ($securityEnforcer->checkLoginAttempts($user['user_id']) !== true) {
                    $error = $securityEnforcer->checkLoginAttempts($user['user_id']);
                    $securityEnforcer->logSecurityEvent('login_blocked', "Login blocked for locked account: $username", $user['user_id']);
                }
                // SECURITY CHECK 3: Verify password
                // SECURITY CHECK 3: Verify password
elseif (password_verify($password, $user['password'])) {
    
    // SUCCESSFUL LOGIN - Reset attempts and log event
    $securityEnforcer->resetLoginAttempts($user['user_id']);
    $securityEnforcer->logSecurityEvent('successful_login', "User logged in successfully from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), $user['user_id']);
    
    // --- SUCCESSFUL LOGIN: SET SESSION & REDIRECT ---
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['first_name'] = $user['first_name'] ?? '';
    $_SESSION['last_name'] = $user['last_name'] ?? '';
    $_SESSION['login_time'] = time();
    $_SESSION['security_level'] = 'high';
    
    // ✅ UPDATE LAST LOGIN TIMESTAMP - ADD THIS LINE
    updateLastLogin($user['user_id'], $conn);
    
    // ✅ ADD SESSION RECORDING HERE - RIGHT BEFORE REDIRECT
    require 'includes/session_manager.php';
    $sessionManager = new SessionManager($conn);
    $sessionManager->recordLogin($user['user_id'], $user['username']);
    
    session_regenerate_id(true);
    
    // SECURITY: Check if password reset is forced
    $checkReset = $conn->prepare("SELECT force_password_change FROM users WHERE user_id = ?");
    $checkReset->bind_param("i", $user['user_id']);
    $checkReset->execute();
    $resetResult = $checkReset->get_result();
    $resetData = $resetResult->fetch_assoc();
    
    if ($resetData && $resetData['force_password_change']) {
        // Redirect to password change page
        header("Location: change_password.php?forced=1");
        exit();
    }
    
    // Determine redirect URL based on role
    $redirect_url = "dashboard.php";
    switch($user['role']) {
        case 'administrator':
            $redirect_url = "admin/dashboard.php";
            break;
        case 'officer':
            $redirect_url = "officer/dashboard.php";
            break;
        case 'clerk':
            $redirect_url = "clerk/dashboard.php";
            break;
        case 'chief':
            $redirect_url = "chief/dashboard.php";
            break;
    }

    // IMMEDIATE REDIRECTION
    header("Location: " . $redirect_url);
    exit(); 
    
}
              else {
                    // FAILED LOGIN - Increment attempts and log event
                    $securityEnforcer->handleFailedLogin($user['user_id']);
                    
                    // Get current attempt count for user message
                    $attemptStmt = $conn->prepare("SELECT login_attempts FROM users WHERE user_id = ?");
                    $attemptStmt->bind_param("i", $user['user_id']);
                    $attemptStmt->execute();
                    $attemptResult = $attemptStmt->get_result();
                    $attemptData = $attemptResult->fetch_assoc();
                    
                    $max_attempts = 5; // Default, should come from security settings
                    $remaining_attempts = $max_attempts - $attemptData['login_attempts'];
                    
                    $error = "Invalid credentials. " . ($remaining_attempts > 0 ? 
                        "$remaining_attempts attempts remaining." : 
                        "Account locked due to too many failed attempts.");
                        
                    $securityEnforcer->logSecurityEvent('failed_login', "Failed login attempt for user: $username", $user['user_id']);
                }
            } else {
                $error = "Invalid credentials. Please check your username and password.";
                $securityEnforcer->logSecurityEvent('failed_login', "Attempted login with non-existent username: $username");
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Get security settings for display
$securitySettings = [
    'min_password_length' => 6,
    'max_login_attempts' => 5,
    'session_timeout' => 60
];

try {
    $settingsStmt = $conn->prepare("SELECT setting_key, setting_value FROM security_settings WHERE setting_key IN ('password_min_length', 'login_max_attempts', 'session_timeout')");
    if ($settingsStmt) {
        $settingsStmt->execute();
        $settingsResult = $settingsStmt->get_result();
        while ($setting = $settingsResult->fetch_assoc()) {
            $securitySettings[$setting['setting_key']] = $setting['setting_value'];
        }
        $settingsStmt->close();
    }
} catch (Exception $e) {
    // Use default settings if there's an error
    error_log("Security settings load error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mattu Criminal Record System - Secure Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            box-sizing: border-box;
        }
        
        .login-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 450px;
            margin: 20px;
        }
        
        .form-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            background: white;
            outline: none;
        }
        
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            width: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .success-message {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s ease;
        }
        
        .password-toggle:hover {
            color: #374151;
        }
        
        .security-badge {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .security-features {
            background: rgba(59, 130, 246, 0.05);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 16px;
            margin-top: 20px;
        }
        
        .security-feature {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 12px;
            color: #4b5563;
        }
        
        .security-feature:last-child {
            margin-bottom: 0;
        }
        
        .security-feature svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            color: #059669;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }
        
        .floating-element {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-element:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .floating-element:nth-child(3) {
            width: 40px;
            height: 40px;
            bottom: 30%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .system-info {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-top: 24px;
            text-align: center;
        }
        
        .attempt-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 8px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Floating Background Elements -->
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        
        <!-- Login Card -->
        <div class="login-card">
            <div class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4">
                        <div class="bg-blue-600 p-4 rounded-full">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Mattu City Criminal Record System</h1>
                    <p class="text-gray-600">Secure Authentication Portal</p>
                    
                    <div class="security-badge">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Security Policy Enforced
                    </div>
                </div>
                
                <!-- Login Form -->
                <form method="post" action="index.php" id="loginForm">
                    <!-- Error/Success Messages -->
                    <?php if (!empty($error)): ?>
                        <div class="error-message">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success-message">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Username Field -->
                    <div class="mb-6">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username or User ID
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-input pl-12" 
                                placeholder="Enter your username or ID"
                                required
                                autocomplete="username"
                                value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                            >
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input pl-12 pr-12" 
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                                minlength="<?php echo $securitySettings['password_min_length']; ?>"
                            >
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="attempt-warning" id="attemptWarning">
                            <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span id="attemptMessage"></span>
                        </div>
                    </div>
                    
                    <!-- Security Features -->
                    <div class="security-features">
                        <div class="security-feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Minimum <?php echo $securitySettings['password_min_length']; ?> character password
                        </div>
                        <div class="security-feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Account lock after <?php echo $securitySettings['max_login_attempts']; ?> failed attempts
                        </div>
                        <div class="security-feature">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Auto-logout after <?php echo $securitySettings['session_timeout']; ?> minutes of inactivity
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6 mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="rememberMe" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-500 transition-colors" onclick="alert('Please contact your system administrator for password reset assistance.')">
                            Forgot password?
                        </a>
                    </div>
                    
                    <!-- Login Button -->
                    <button type="submit" class="btn-login">
                        Sign In Securely
                    </button>
                </form>
                
                <!-- System Information -->
                <div class="system-info">
                    <div class="text-white text-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span>System Status:</span>
                            <span class="flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                                Security Policy Active
                            </span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span>Failed Logins Today:</span>
                            <span><?php 
                                try {
                                    $failStmt = $conn->prepare("SELECT COUNT(*) as count FROM security_logs WHERE event_type = 'failed_login' AND DATE(created_at) = CURDATE()");
                                    if ($failStmt) {
                                        $failStmt->execute();
                                        $failResult = $failStmt->get_result();
                                        $failData = $failResult->fetch_assoc();
                                        echo $failData['count'] ?? 0;
                                        $failStmt->close();
                                    } else {
                                        echo '0';
                                    }
                                } catch (Exception $e) {
                                    echo '0';
                                }
                            ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Security Level:</span>
                            <span>HIGH</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // Real-time validation
        document.getElementById('username').addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length > 0 && value.length < 3) {
                this.classList.add('error');
            } else {
                this.classList.remove('error');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const value = this.value;
            const minLength = <?php echo $securitySettings['password_min_length']; ?>;
            
            if (value.length > 0 && value.length < minLength) {
                this.classList.add('error');
                document.getElementById('attemptWarning').style.display = 'block';
                document.getElementById('attemptMessage').textContent = `Password must be at least ${minLength} characters`;
            } else {
                this.classList.remove('error');
                document.getElementById('attemptWarning').style.display = 'none';
            }
        });

        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const errorMsg = document.querySelector('.error-message');
            const successMsg = document.querySelector('.success-message');
            
            if (errorMsg) errorMsg.style.display = 'none';
            if (successMsg) successMsg.style.display = 'none';
        }, 5000);

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>