<?php
// includes/security_functions.php
require_once 'security_enforcer.php';

function getSecurityEnforcer() {
    // Try to use MySQLi first (for login system)
    if (isset($GLOBALS['conn']) && $GLOBALS['conn'] instanceof mysqli) {
        return new SecurityEnforcer($GLOBALS['conn']);
    }
    // Try to use PDO second (for user management)
    elseif (isset($GLOBALS['db']) && $GLOBALS['db'] instanceof PDO) {
        return new SecurityEnforcer($GLOBALS['db']);
    }
    // Fallback - create new database connection
    else {
        require_once 'database.php';
        $database = new Database();
        return new SecurityEnforcer($database->getConnection());
    }
}

// Security policy functions that can be used globally
function validatePasswordAgainstPolicy($password) {
    $enforcer = getSecurityEnforcer();
    return $enforcer->validatePassword($password);
}

function getPasswordPolicyDescription() {
    $enforcer = getSecurityEnforcer();
    
    $min_length = intval($enforcer->getPolicy('password_min_length', '8'));
    $require_uppercase = $enforcer->getPolicy('password_require_uppercase', '1') === '1';
    $require_numbers = $enforcer->getPolicy('password_require_numbers', '1') === '1';
    $require_special = $enforcer->getPolicy('password_require_special', '1') === '1';
    
    $requirements = ["Minimum {$min_length} characters"];
    
    if ($require_uppercase) {
        $requirements[] = "At least one uppercase letter (A-Z)";
    }
    if ($require_numbers) {
        $requirements[] = "At least one number (0-9)";
    }
    if ($require_special) {
        $requirements[] = "At least one special character (!@#$%^&* etc.)";
    }
    
    return implode(", ", $requirements);
}

function getSecuritySetting($key, $default = '') {
    $enforcer = getSecurityEnforcer();
    return $enforcer->getPolicy($key, $default);
}

// Global security logging function - UPDATED
// Global security logging function - UPDATED
function logSecurityEvent($event_type, $description, $user_id = null) {
    $enforcer = getSecurityEnforcer();
    return $enforcer->logSecurityEvent($event_type, $description, $user_id);
}
?>