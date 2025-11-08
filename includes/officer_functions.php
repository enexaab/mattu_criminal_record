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

// Force reload security settings after changes
function reloadSecuritySettings() {
    $enforcer = getSecurityEnforcer();
    return $enforcer->reloadSettings();
}

// Security policy functions
function validatePasswordAgainstPolicy($password) {
    $enforcer = getSecurityEnforcer();
    return $enforcer->validatePassword($password);
}

function getPasswordPolicyDescription() {
    $enforcer = getSecurityEnforcer();
    return $enforcer->getPasswordPolicyDescription();
}

function getSecuritySetting($key, $default = '') {
    $enforcer = getSecurityEnforcer();
    return $enforcer->getPolicy($key, $default);
}

// Global security logging function
function logSecurityEvent($event_type, $description, $user_id = null) {
    $enforcer = getSecurityEnforcer();
    return $enforcer->logSecurityEvent($event_type, $description, $user_id);
}

// Debug function to check current settings
function debugSecuritySettings() {
    $enforcer = getSecurityEnforcer();
    return $enforcer->getAllSettings();
}
?>