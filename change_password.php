<?php
// change_password.php - FORCED PASSWORD RESET PAGE
session_start();
require 'db_connect.php';
require 'includes/security_enforcer.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if this is a forced password change
$forced = isset($_GET['forced']) && $_GET['forced'] == 1;
if (!$forced) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';
$securityEnforcer = new SecurityEnforcer($conn);

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        // Verify current password
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if ($user && password_verify($current_password, $user['password'])) {
            // Validate new password against security policy
            $passwordValidation = $securityEnforcer->validatePassword($new_password);
            if ($passwordValidation === true) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ?, force_password_change = 0 WHERE user_id = ?");
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $success = "Password changed successfully!";
                    $securityEnforcer->logSecurityEvent('password_change', 'User changed password successfully', $user_id);
                    
                    // Redirect after 2 seconds
                    header("Refresh: 2; URL=dashboard.php");
                } else {
                    $error = "Failed to update password. Please try again.";
                }
                $update_stmt->close();
            } else {
                $error = $passwordValidation;
            }
        } else {
            $error = "Current password is incorrect.";
        }
    }
}

// Get security settings for display
$securitySettings = [
    'min_length' => 6,
    'require_upper' => true,
    'require_numbers' => true,
    'require_special' => true
];

try {
    $settingsStmt = $conn->prepare("SELECT setting_key, setting_value FROM security_settings WHERE setting_key IN ('password_min_length', 'password_require_uppercase', 'password_require_numbers', 'password_require_special')");
    if ($settingsStmt) {
        $settingsStmt->execute();
        $settingsResult = $settingsStmt->get_result();
        while ($setting = $settingsResult->fetch_assoc()) {
            if ($setting['setting_key'] == 'password_min_length') {
                $securitySettings['min_length'] = $setting['setting_value'];
            } else {
                $securitySettings[str_replace('password_require_', '', $setting['setting_key'])] = $setting['setting_value'] == '1';
            }
        }
        $settingsStmt->close();
    }
} catch (Exception $e) {
    error_log("Security settings load error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Mattu Criminal Record System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .password-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 500px;
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
        
        .btn-primary {
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
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .password-strength {
            margin-top: 10px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }
        
        .strength-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            border-radius: 4px;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 14px;
            color: #6c757d;
        }
        
        .requirement.met {
            color: #28a745;
        }
        
        .requirement.unmet {
            color: #dc3545;
        }
        
        .requirement-icon {
            margin-right: 8px;
            width: 16px;
            height: 16px;
        }
        
        .security-alert {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 2px solid #ffc107;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="password-card">
        <div class="p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="bg-yellow-500 p-4 rounded-full">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <?php echo $forced ? 'Password Reset Required' : 'Change Password'; ?>
                </h1>
                <p class="text-gray-600">
                    <?php echo $forced ? 
                        'For security reasons, you must change your password before continuing.' : 
                        'Update your password to maintain account security.'; ?>
                </p>
            </div>
            
            <!-- Security Alert for Forced Reset -->
            <?php if ($forced): ?>
            <div class="security-alert">
                <div class="flex items-center justify-center mb-2">
                    <svg class="h-6 w-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <strong class="text-yellow-800">Security Requirement</strong>
                </div>
                <p class="text-yellow-700 text-sm">
                    You cannot access the system until you change your password. This is a mandatory security measure.
                </p>
            </div>
            <?php endif; ?>
            
            <!-- Error/Success Messages -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                    <?php if ($forced): ?>
                        <span class="ml-2">Redirecting to dashboard...</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Password Change Form -->
            <form method="post" action="change_password.php?forced=1" id="passwordForm">
                <!-- Current Password -->
                <div class="mb-6">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Current Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="form-input pl-12 pr-12" 
                            placeholder="Enter your current password"
                            required
                            autocomplete="current-password"
                        >
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button type="button" class="password-toggle absolute right-4 top-1/2 transform -translate-y-1/2" onclick="togglePassword('current_password', 'current_eye')">
                            <svg id="current_eye" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- New Password -->
                <div class="mb-6">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        New Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            class="form-input pl-12 pr-12" 
                            placeholder="Enter your new password"
                            required
                            autocomplete="new-password"
                            minlength="<?php echo $securitySettings['min_length']; ?>"
                        >
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button type="button" class="password-toggle absolute right-4 top-1/2 transform -translate-y-1/2" onclick="togglePassword('new_password', 'new_eye')">
                            <svg id="new_eye" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            class="form-input pl-12 pr-12" 
                            placeholder="Confirm your new password"
                            required
                            autocomplete="new-password"
                            minlength="<?php echo $securitySettings['min_length']; ?>"
                        >
                        <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <button type="button" class="password-toggle absolute right-4 top-1/2 transform -translate-y-1/2" onclick="togglePassword('confirm_password', 'confirm_eye')">
                            <svg id="confirm_eye" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Password Strength Meter -->
                <div class="password-strength">
                    <h4 class="font-medium text-gray-700 mb-3">Password Requirements</h4>
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="requirements-list">
                        <div class="requirement" id="reqLength">
                            <svg class="requirement-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            At least <?php echo $securitySettings['min_length']; ?> characters
                        </div>
                        <?php if ($securitySettings['require_upper']): ?>
                        <div class="requirement" id="reqUpper">
                            <svg class="requirement-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            One uppercase letter
                        </div>
                        <?php endif; ?>
                        <?php if ($securitySettings['require_numbers']): ?>
                        <div class="requirement" id="reqNumber">
                            <svg class="requirement-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            One number
                        </div>
                        <?php endif; ?>
                        <?php if ($securitySettings['require_special']): ?>
                        <div class="requirement" id="reqSpecial">
                            <svg class="requirement-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            One special character
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn-primary mt-6">
                    Change Password
                </button>
                
                <?php if (!$forced): ?>
                <div class="text-center mt-4">
                    <a href="dashboard.php" class="text-blue-600 hover:text-blue-500 text-sm">
                        ← Back to Dashboard
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, eyeId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(eyeId);
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                `;
            } else {
                field.type = 'password';
                eye.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }

        // Password strength checker
        document.getElementById('new_password').addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });

        function checkPasswordStrength(password) {
            const minLength = <?php echo $securitySettings['min_length']; ?>;
            const requireUpper = <?php echo $securitySettings['require_upper'] ? 'true' : 'false'; ?>;
            const requireNumbers = <?php echo $securitySettings['require_numbers'] ? 'true' : 'false'; ?>;
            const requireSpecial = <?php echo $securitySettings['require_special'] ? 'true' : 'false'; ?>;
            
            let strength = 0;
            let totalRequirements = 1; // Minimum length is always required
            if (requireUpper) totalRequirements++;
            if (requireNumbers) totalRequirements++;
            if (requireSpecial) totalRequirements++;
            
            // Check requirements
            const hasMinLength = password.length >= minLength;
            const hasUpper = /[A-Z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            const hasSpecial = /[!@#$%^&*()\-_=+{};:,<.>]/.test(password);
            
            // Update requirement indicators
            updateRequirement('reqLength', hasMinLength);
            if (requireUpper) updateRequirement('reqUpper', hasUpper);
            if (requireNumbers) updateRequirement('reqNumber', hasNumber);
            if (requireSpecial) updateRequirement('reqSpecial', hasSpecial);
            
            // Calculate strength
            if (hasMinLength) strength++;
            if (requireUpper && hasUpper) strength++;
            if (requireNumbers && hasNumber) strength++;
            if (requireSpecial && hasSpecial) strength++;
            
            // Update strength bar
            const percentage = (strength / totalRequirements) * 100;
            const strengthFill = document.getElementById('strengthFill');
            strengthFill.style.width = percentage + '%';
            
            if (percentage < 50) {
                strengthFill.style.background = '#dc3545';
            } else if (percentage < 100) {
                strengthFill.style.background = '#ffc107';
            } else {
                strengthFill.style.background = '#28a745';
            }
        }

        function updateRequirement(elementId, met) {
            const element = document.getElementById(elementId);
            if (element) {
                element.className = met ? 'requirement met' : 'requirement unmet';
            }
        }

        // Form validation
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match!');
                return false;
            }
            
            // Additional validation can be added here
            return true;
        });

        // Auto-focus on current password field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('current_password').focus();
        });
    </script>
</body>
</html>