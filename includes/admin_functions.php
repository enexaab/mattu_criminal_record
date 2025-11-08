<?php
// includes/admin_functions.php
// includes/admin_functions.php
require_once 'security_enforcer.php'; // ADD THIS LINE if missing
require_once 'security_functions.php'; // ADD THIS if it exists

class AdminFunctions {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getUserStats() {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    role,
                    COUNT(*) as count,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count
                FROM users 
                GROUP BY role
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting user stats: " . $e->getMessage());
            return [];
        }
    }
    
    public function getAllUsers() {
        try {
            $stmt = $this->conn->prepare("
                SELECT u.* 
                FROM users u 
                ORDER BY u.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllUsers Error: " . $e->getMessage());
            return [];
        }
    }

    public function getRoles() {
        try {
            $stmt = $this->conn->prepare("SELECT DISTINCT role FROM users");
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $formattedRoles = [];
            foreach ($roles as $index => $role) {
                $formattedRoles[] = [
                    'id' => $index + 1,
                    'role_name' => $role['role']
                ];
            }
            return $formattedRoles;
        } catch (PDOException $e) {
            error_log("getRoles Error: " . $e->getMessage());
            return [
                ['id' => 1, 'role_name' => 'administrator'],
                ['id' => 2, 'role_name' => 'chief'],
                ['id' => 3, 'role_name' => 'officer'],
                ['id' => 4, 'role_name' => 'clerk']
            ];
        }
    }
    public function createUser($userData) {
    try {
        // Force reload security settings to get latest changes
        $enforcer = getSecurityEnforcer();
        
        // ADD THIS: Force reload settings before validation
        if (method_exists($enforcer, 'reloadSettings')) {
            $enforcer->reloadSettings();
        }
        
        $password_validation = $enforcer->validatePassword($userData['password']);
        
        // Debug logging
        error_log("Create User - Password Validation: " . (is_string($password_validation) ? $password_validation : 'VALID'));
        error_log("Create User - Current Min Length: " . $enforcer->getPolicy('password_min_length', '8'));
        
        // FIX: Check if validation returned errors (string) or success (boolean true)
        if ($password_validation !== true) {
            throw new Exception("Password does not meet security requirements: " . $password_validation);
        }
        
        $stmt = $this->conn->prepare("
            INSERT INTO users (username, password, role, first_name, last_name, email, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
        ");
        
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        $result = $stmt->execute([
            $userData['username'],
            $hashedPassword,
            $userData['role_id'],
            $userData['first_name'],
            $userData['last_name'],
            $userData['email']
        ]);
        
        if ($result) {
            logSecurityEvent('user_created', "User {$userData['username']} created successfully", $_SESSION['user_id'] ?? null);
        }
        
        return $result;
    } catch (PDOException $e) {
        error_log("createUser Error: " . $e->getMessage());
        logSecurityEvent('user_creation_failed', "Failed to create user {$userData['username']}: " . $e->getMessage(), $_SESSION['user_id'] ?? null);
        return false;
    } catch (Exception $e) {
        error_log("createUser Policy Error: " . $e->getMessage());
        logSecurityEvent('user_creation_failed', "Policy violation for user {$userData['username']}: " . $e->getMessage(), $_SESSION['user_id'] ?? null);
        return false;
    }
}
    public function updateUser($userData) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE users 
                SET username = ?, first_name = ?, last_name = ?, email = ?, role = ?, is_active = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            
            $is_active = $userData['status'] === 'active' ? 1 : 0;
            
            $result = $stmt->execute([
                $userData['username'],
                $userData['first_name'],
                $userData['last_name'],
                $userData['email'],
                $userData['role_id'],
                $is_active,
                $userData['user_id']
            ]);
            
            if ($result) {
                logSecurityEvent('user_updated', "User {$userData['username']} updated successfully", $_SESSION['user_id'] ?? null);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            logSecurityEvent('user_update_failed', "Failed to update user {$userData['username']}: " . $e->getMessage(), $_SESSION['user_id'] ?? null);
            return false;
        }
    }

    public function resetUserPassword($user_id, $new_password) {
        try {
            // Use security enforcer for password validation
            $enforcer = getSecurityEnforcer();
            $password_errors = $enforcer->validatePassword($new_password);
            
            if (!empty($password_errors)) {
                throw new Exception("Password does not meet security requirements: " . implode(", ", $password_errors));
            }
            
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE users SET password = ?, force_password_change = 1, updated_at = NOW() WHERE user_id = ?");
            
            $result = $stmt->execute([$hashed_password, $user_id]);
            
            if ($result) {
                logSecurityEvent('password_reset', "Password reset for user ID: {$user_id}", $_SESSION['user_id'] ?? null);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            logSecurityEvent('password_reset_failed', "Failed to reset password for user ID: {$user_id}", $_SESSION['user_id'] ?? null);
            return false;
        } catch (Exception $e) {
            error_log("Reset password policy error: " . $e->getMessage());
            logSecurityEvent('password_reset_failed', "Policy violation for password reset user ID: {$user_id}", $_SESSION['user_id'] ?? null);
            return false;
        }
    }

    public function toggleUserStatus($user_id, $new_status) {
        try {
            $is_active = $new_status === 'active' ? 1 : 0;
            $stmt = $this->conn->prepare("UPDATE users SET is_active = ?, updated_at = NOW() WHERE user_id = ?");
            
            $result = $stmt->execute([$is_active, $user_id]);
            
            if ($result) {
                $action = $new_status === 'active' ? 'activated' : 'deactivated';
                logSecurityEvent('user_status_changed', "User ID: {$user_id} {$action}", $_SESSION['user_id'] ?? null);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Toggle status error: " . $e->getMessage());
            logSecurityEvent('user_status_change_failed', "Failed to change status for user ID: {$user_id}", $_SESSION['user_id'] ?? null);
            return false;
        }
    }

    public function getUserById($user_id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $user['role_name'] = ucfirst($user['role']);
                $user['status'] = $user['is_active'] ? 'active' : 'inactive';
            }
            
            return $user;
        } catch (PDOException $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return false;
        }
    }

    public function getSystemMetrics() {
        try {
            $metrics = [];
            
            // Database size
            $stmt = $this->conn->prepare("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS db_size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
            ");
            $stmt->execute();
            $metrics['database_size'] = $stmt->fetch(PDO::FETCH_ASSOC)['db_size_mb'];
            
            // Record counts
            $tables = ['users', 'criminal_records', 'cases', 'security_logs'];
            foreach ($tables as $table) {
                try {
                    $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM $table");
                    $stmt->execute();
                    $metrics[$table . '_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                } catch (Exception $e) {
                    $metrics[$table . '_count'] = 0;
                }
            }
            
            return $metrics;
        } catch (Exception $e) {
            error_log("Error getting system metrics: " . $e->getMessage());
            return [];
        }
    }
    
    public function logActivity($userId, $actionType, $description, $targetId = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO activity_logs (user_id, action_type, description, target_id, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$userId, $actionType, $description, $targetId]);
        } catch (Exception $e) {
            error_log("Error logging activity: " . $e->getMessage());
            return false;
        }
    }

    // Get security statistics for dashboard
    public function getSecurityStatistics() {
        try {
            $stats = [];
            
            // Failed logins today
            $query = "SELECT COUNT(*) as count FROM security_logs 
                      WHERE event_type = 'failed_login' 
                      AND DATE(created_at) = CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['failed_logins_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            
            // Locked accounts
            $stats['locked_accounts'] = 0;
            try {
                $max_attempts = getSecurityEnforcer()->getPolicy('login_max_attempts', '5');
                $query = "SELECT COUNT(*) as count FROM users WHERE login_attempts >= ? AND is_active = 1";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$max_attempts]);
                $stats['locked_accounts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            } catch (Exception $e) {
                $stats['locked_accounts'] = 0;
            }
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting security statistics: " . $e->getMessage());
            return ['failed_logins_today' => 0, 'locked_accounts' => 0];
        }
    }
}

// Create global functions for backward compatibility
function getAllUsers() {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->getAllUsers();
}

function getRoles() {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->getRoles();
}

function getUserById($userId) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->getUserById($userId);
}

function createUser($userData) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->createUser($userData);
}

function updateUser($userData) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->updateUser($userData);
}

function resetUserPassword($user_id, $new_password) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->resetUserPassword($user_id, $new_password);
}

function toggleUserStatus($user_id, $new_status) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->toggleUserStatus($user_id, $new_status);
}

function logActivity($userId, $actionType, $description, $targetId = null) {
    global $db;
    $adminFunctions = new AdminFunctions($db);
    return $adminFunctions->logActivity($userId, $actionType, $description, $targetId);
}
// REMOVE THESE FROM admin_functions.php - They are now in security_functions.php

// Security policy functions (now using security enforcer)
// function validatePasswordAgainstPolicy($password) {
//     $enforcer = getSecurityEnforcer();
//     return $enforcer->validatePassword($password);
// }

// function getPasswordPolicyDescription() {
//     $enforcer = getSecurityEnforcer();
    
//     $min_length = intval($enforcer->getPolicy('password_min_length', '8'));
//     $require_uppercase = $enforcer->getPolicy('password_require_uppercase', '1') === '1';
//     $require_numbers = $enforcer->getPolicy('password_require_numbers', '1') === '1';
//     $require_special = $enforcer->getPolicy('password_require_special', '1') === '1';
    
//     $requirements = ["Minimum {$min_length} characters"];
    
//     if ($require_uppercase) {
//         $requirements[] = "At least one uppercase letter (A-Z)";
//     }
//     if ($require_numbers) {
//         $requirements[] = "At least one number (0-9)";
//     }
//     if ($require_special) {
//         $requirements[] = "At least one special character (!@#$%^&* etc.)";
//     }
    
//     return implode(", ", $requirements);
// }

// function getSecuritySetting($key, $default = '') {
//     $enforcer = getSecurityEnforcer();
//     return $enforcer->getPolicy($key, $default);
// }
?>