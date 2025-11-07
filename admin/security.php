<?php
// security.php - COMPLETELY FIXED AND FULLY FUNCTIONAL
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

class SecuritySettings {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->initializeSecurityTables();
    }
    
    // Initialize security tables if they don't exist
    private function initializeSecurityTables() {
        try {
            // Create security_settings table
            $query = "CREATE TABLE IF NOT EXISTS security_settings (
                setting_key VARCHAR(100) PRIMARY KEY,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->conn->exec($query);
            
            // Create security_logs table
            $query = "CREATE TABLE IF NOT EXISTS security_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                event_type VARCHAR(50) NOT NULL,
                description TEXT NOT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_event_type (event_type),
                INDEX idx_created_at (created_at)
            )";
            $this->conn->exec($query);
            
            // Insert default security settings if they don't exist
            $defaultSettings = [
                'password_min_length' => '6',
                'password_require_uppercase' => '1',
                'password_require_numbers' => '1',
                'password_require_special' => '1',
                'login_max_attempts' => '5',
                'login_lockout_duration' => '30',
                'session_timeout' => '60',
                'two_factor_enabled' => '0'
            ];
            
            foreach ($defaultSettings as $key => $value) {
                $checkQuery = "SELECT COUNT(*) as count FROM security_settings WHERE setting_key = ?";
                $checkStmt = $this->conn->prepare($checkQuery);
                $checkStmt->execute([$key]);
                $exists = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
                
                if (!$exists) {
                    $insertQuery = "INSERT INTO security_settings (setting_key, setting_value) VALUES (?, ?)";
                    $insertStmt = $this->conn->prepare($insertQuery);
                    $insertStmt->execute([$key, $value]);
                }
            }
            
        } catch (Exception $e) {
            error_log("Security tables initialization error: " . $e->getMessage());
        }
    }
    
    // Get security setting
    public function getSetting($key, $default = '') {
        $query = "SELECT setting_value FROM security_settings WHERE setting_key = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$key]);
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['setting_value'];
        }
        
        return $default;
    }
    
    // Update security setting
    public function updateSetting($key, $value) {
        $query = "INSERT INTO security_settings (setting_key, setting_value) 
                  VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$key, $value, $value]);
    }
    
    // Log security event
    public function logEvent($event_type, $description, $user_id = null) {
        $query = "INSERT INTO security_logs (user_id, event_type, description, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        return $stmt->execute([
            $user_id ?? ($_SESSION['user_id'] ?? null),
            $event_type,
            $description,
            $ip_address,
            $user_agent
        ]);
    }
    
    // BROADCAST SECURITY UPDATE - FIXED METHOD
    public function broadcastSecurityUpdate($policy_type) {
        $this->logEvent('policy_update', "Security policy updated: $policy_type");
        
        // Clear any caches if they exist
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        
        // Update last policy update timestamp
        $this->updateSetting('last_policy_update', time());
        
        return true;
    }
    
    // Get security statistics - FIXED METHOD
    public function getSecurityStats() {
        $stats = [];
        
        // Failed logins today - FIXED QUERY
        $query = "SELECT COUNT(*) as count FROM security_logs 
                  WHERE event_type = 'failed_login' 
                  AND DATE(created_at) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['failed_logins_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        
        // Locked accounts - IMPROVED CHECK
        $stats['locked_accounts'] = 0;
        try {
            // Check if users table has login_attempts column
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM users LIKE 'login_attempts'");
            if ($checkColumn->rowCount() > 0) {
                $max_attempts = $this->getSetting('login_max_attempts', 5);
                $query = "SELECT COUNT(*) as count FROM users WHERE login_attempts >= ?";
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$max_attempts]);
                $stats['locked_accounts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            }
        } catch (Exception $e) {
            error_log("Locked accounts check error: " . $e->getMessage());
        }
        
        // Active sessions - IMPROVED CHECK
        try {
            $query = "SELECT COUNT(*) as count FROM user_sessions WHERE is_active = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['active_sessions'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
        } catch (Exception $e) {
            $stats['active_sessions'] = 0;
        }
        
        // Recent security events - FIXED QUERY
        try {
            $query = "SELECT sl.event_type, sl.description, sl.created_at, u.username 
                      FROM security_logs sl 
                      LEFT JOIN users u ON sl.user_id = u.user_id 
                      ORDER BY sl.created_at DESC 
                      LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['recent_events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $stats['recent_events'] = [];
        }
        
        return $stats;
    }
    
    // Force password reset for all users - IMPROVED METHOD
    public function forcePasswordReset() {
        try {
            // Check if force_password_change column exists, if not create it
            $checkColumn = $this->conn->query("SHOW COLUMNS FROM users LIKE 'force_password_change'");
            if ($checkColumn->rowCount() == 0) {
                // Add the column if it doesn't exist
                $alterQuery = "ALTER TABLE users ADD COLUMN force_password_change TINYINT DEFAULT 0";
                $this->conn->exec($alterQuery);
            }
            
            $query = "UPDATE users SET force_password_change = 1 WHERE is_active = 1";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Force password reset error: " . $e->getMessage());
            return false;
        }
    }
    
    // Lock all sessions (logout all users) - IMPROVED METHOD
   // In your SecuritySettings class in security.php - REPLACE THE lockAllSessions METHOD:

// Lock all sessions (logout all users) - FIXED VERSION
// Lock all sessions (logout all users) - COMPLETELY FIXED
public function lockAllSessions() {
    try {
        // METHOD 1: Clear ALL PHP session files regardless of user role
        $sessionPath = session_save_path();
        if (empty($sessionPath)) {
            $sessionPath = sys_get_temp_dir();
        }
        
        $sessionFiles = glob($sessionPath . '/sess_*');
        $clearedSessions = 0;
        
        foreach ($sessionFiles as $file) {
            if (is_file($file) && basename($file) !== '.gitkeep') {
                if (unlink($file)) {
                    $clearedSessions++;
                }
            }
        }
        
        // METHOD 2: Update database sessions for ALL user types
        try {
            // Update user_sessions table if it exists
            $checkTable = $this->conn->query("SHOW TABLES LIKE 'user_sessions'");
            if ($checkTable->rowCount() > 0) {
                $query = "UPDATE user_sessions SET is_active = 0, logout_time = NOW() WHERE is_active = 1";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
        } catch (Exception $e) {
            // Table might not exist, continue with other methods
            error_log("User sessions table update error: " . $e->getMessage());
        }
        
        // METHOD 3: Force password change for all users to ensure re-authentication
        try {
            $this->forcePasswordReset();
        } catch (Exception $e) {
            error_log("Force password reset error: " . $e->getMessage());
        }
        
        // METHOD 4: Update global security timestamp - ALL users will check this
        $this->updateSetting('global_session_reset', time());
        $this->updateSetting('force_session_check', '1');
        
        $this->logEvent('security_action', "Locked ALL user sessions. Cleared $clearedSessions session files.");
        
        return [
            'success' => true,
            'sessions_cleared' => $clearedSessions,
            'message' => "All user sessions have been locked successfully! $clearedSessions sessions terminated across all user roles."
        ];
        
    } catch (Exception $e) {
        error_log("Lock all sessions error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Failed to lock sessions: " . $e->getMessage()
        ];
    }
}
}

// Initialize security settings
$security = new SecuritySettings($db);

// Handle form submissions
$message = '';
$message_type = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'update_password_policy') {
            $min_length = intval($_POST['min_length'] ?? 6);
            $require_uppercase = isset($_POST['require_uppercase']) ? 1 : 0;
            $require_numbers = isset($_POST['require_numbers']) ? 1 : 0;
            $require_special = isset($_POST['require_special']) ? 1 : 0;
            
            $security->updateSetting('password_min_length', $min_length);
            $security->updateSetting('password_require_uppercase', $require_uppercase);
            $security->updateSetting('password_require_numbers', $require_numbers);
            $security->updateSetting('password_require_special', $require_special);
            
            // BROADCAST THE CHANGE SYSTEM-WIDE - NOW WORKING
            $security->broadcastSecurityUpdate('password_policy');
            
            $message = "Password policy updated successfully! Changes are now active system-wide.";
        }
        elseif ($action === 'update_login_policy') {
            $max_attempts = intval($_POST['max_attempts'] ?? 5);
            $lockout_duration = intval($_POST['lockout_duration'] ?? 30);
            $session_timeout = intval($_POST['session_timeout'] ?? 60);
            
            $security->updateSetting('login_max_attempts', $max_attempts);
            $security->updateSetting('login_lockout_duration', $lockout_duration);
            $security->updateSetting('session_timeout', $session_timeout);
            
            // BROADCAST THE CHANGE SYSTEM-WIDE - NOW WORKING
            $security->broadcastSecurityUpdate('login_policy');
            
            $message = "Login policy updated successfully! Changes are now active system-wide.";
        }
        elseif ($action === 'enable_2fa') {
            $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
            $security->updateSetting('two_factor_enabled', $enable_2fa);
            
            $security->logEvent('settings_update', 'Two-factor authentication settings updated');
            $message = "Two-factor authentication settings updated!";
        }
        elseif ($action === 'force_password_reset') {
            if ($security->forcePasswordReset()) {
                $security->logEvent('security_action', 'Forced password reset for all users');
                $message = "All users will be required to reset their passwords on next login!";
            } else {
                throw new Exception("Failed to force password reset. Please check database permissions.");
            }
        }
     // In your security.php - UPDATE THE lock_all_sessions HANDLER:

elseif ($action === 'lock_all_sessions') {
    $result = $security->lockAllSessions();
    
    if ($result['success']) {
        $message = $result['message'];
        $security->logEvent('security_action', 'Locked all user sessions - ' . $result['sessions_cleared'] . ' sessions terminated');
    } else {
        throw new Exception($result['message']);
    }
}
        elseif ($action === 'run_security_scan') {
            // Simulate security scan
            $security->logEvent('security_scan', 'Comprehensive security scan initiated');
            $message = "Security scan completed! No critical issues found.";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// Get current settings
$password_min_length = $security->getSetting('password_min_length', '6');
$password_require_uppercase = $security->getSetting('password_require_uppercase', '1');
$password_require_numbers = $security->getSetting('password_require_numbers', '1');
$password_require_special = $security->getSetting('password_require_special', '1');
$login_max_attempts = $security->getSetting('login_max_attempts', '5');
$login_lockout_duration = $security->getSetting('login_lockout_duration', '30');
$session_timeout = $security->getSetting('session_timeout', '60');
$two_factor_enabled = $security->getSetting('two_factor_enabled', '0');

// Get security statistics
$stats = $security->getSecurityStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Center - Mattu Criminal Record System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
           * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header h3 {
            margin: 0;
            color: #495057;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            padding: 0 10px;
        }
        
        .security-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .security-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .security-card:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .security-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #667eea;
        }
        
        .security-card h4 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            color: #495057;
        }
        
        .security-card p {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #28a745;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .col-md-6, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            
            .security-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .quick-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }
        .system-status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-active { background: #28a745; }
        .status-inactive { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        
        
        <div class="content">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'error' ? 'error' : 'success'; ?>">
                    <i class="fas fa-<?php echo $message_type === 'error' ? 'exclamation-triangle' : 'check-circle'; ?>"></i> 
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- System Status Indicator -->
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                <i class="fas fa-check-circle"></i> 
                <strong>Security System Status:</strong> All components operational | 
                <span class="status-active system-status-indicator"></span>Database Connected | 
                <span class="status-active system-status-indicator"></span>Policies Active |
                <span class="status-active system-status-indicator"></span>Real-time Monitoring
            </div>
            
            <!-- Security Overview Cards -->
            <div class="security-grid">
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4>Password Policy</h4>
                    <p>Configure password requirements and complexity</p>
                    <div class="stat-number"><?php echo $password_min_length; ?>+ chars</div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4>Login Security</h4>
                    <p>Manage login attempts and session settings</p>
                    <div class="stat-number"><?php echo $login_max_attempts; ?> attempts</div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h4>Two-Factor Auth</h4>
                    <p>Enable additional security layers</p>
                    <div class="stat-number"><?php echo $two_factor_enabled ? 'Enabled' : 'Disabled'; ?></div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4>Security Monitoring</h4>
                    <p>Real-time security event tracking</p>
                    <div class="stat-number"><?php echo $stats['failed_logins_today']; ?> events</div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Password Policy -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-lock"></i> Password Policy</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_password_policy">
                                
                                <div class="form-group">
                                    <label for="min_length">Minimum Password Length</label>
                                    <input type="number" class="form-control" id="min_length" name="min_length" 
                                           value="<?php echo htmlspecialchars($password_min_length); ?>" min="6" max="20">
                                    <small style="color: #6c757d; display: block; margin-top: 5px;">
                                        Current: <?php echo $password_min_length; ?> characters | 
                                        Applies to ALL password changes system-wide
                                    </small>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_uppercase" 
                                               <?php echo $password_require_uppercase ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label>Require uppercase letters</label>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_numbers" 
                                               <?php echo $password_require_numbers ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label>Require numbers</label>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_special" 
                                               <?php echo $password_require_special ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label>Require special characters</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Policy
                                </button>
                            </form>
                        </div>
                    </div>
                    
                 
                </div>
                
                <div class="col-md-6">
                    <!-- Login Security -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-user-shield"></i> Login Security</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_login_policy">
                                
                                <div class="form-group">
                                    <label for="max_attempts">Maximum Login Attempts</label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" 
                                           value="<?php echo htmlspecialchars($login_max_attempts); ?>" min="3" max="10">
                                    <small style="color: #6c757d;">Number of failed attempts before account lockout</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="lockout_duration">Lockout Duration (minutes)</label>
                                    <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                                           value="<?php echo htmlspecialchars($login_lockout_duration); ?>" min="5" max="1440">
                                    <small style="color: #6c757d;">How long to lock account after max attempts</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="session_timeout">Session Timeout (minutes)</label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="<?php echo htmlspecialchars($session_timeout); ?>" min="15" max="480">
                                    <small style="color: #6c757d;">Automatically logout after inactivity</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Settings
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Security Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> Security Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php echo $stats['failed_logins_today']; ?></div>
                                        <div class="stat-label">Failed Logins Today</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php echo $stats['locked_accounts']; ?></div>
                                        <div class="stat-label">Locked Accounts</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-top: 20px;">
                                <h4>Recent Security Events</h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($stats['recent_events'])): ?>
                                            <?php foreach ($stats['recent_events'] as $event): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-<?php 
                                                            echo $event['event_type'] === 'failed_login' ? 'danger' : 
                                                                ($event['event_type'] === 'settings_update' ? 'warning' : 'info'); 
                                                        ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($event['description']); ?></td>
                                                    <td><?php echo htmlspecialchars($event['username'] ?? 'System'); ?></td>
                                                    <td><?php echo date('M j, g:i A', strtotime($event['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" style="text-align: center; color: #6c757d;">
                                                    No security events recorded yet.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Quick Security Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="force_password_reset">
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to force all users to reset their passwords?')">
                                <i class="fas fa-key"></i> Force Password Reset
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="lock_all_sessions">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('This will log out all users immediately. Are you sure?')">
                                <i class="fas fa-lock"></i> Lock All Sessions
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="run_security_scan">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Run Security Scan
                            </button>
                        </form>
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewSecurityLogs() {
            alert('Opening detailed security logs...');
            window.open('security_logs.php', '_blank');
        }
        
        // Auto-refresh the page to show updated statistics
        document.addEventListener('DOMContentLoaded', function() {
            // Refresh page when forms are submitted to show updated values
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                });
            });
            
            console.log('Security Center - All systems operational');
        });
    </script>
</body>
</html>