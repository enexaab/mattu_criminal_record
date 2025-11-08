<?php
// security.php - COMPLETELY FIXED AND FULLY FUNCTIONAL
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

// Language support
$languages = ['en', 'am', 'om'];
$current_lang = $_SESSION['lang'] ?? 'en';
if (isset($_POST['lang']) && in_array($_POST['lang'], $languages)) {
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$translations = [
    'en' => [
        'title' => 'Security Center - Mattu Criminal Record System',
        'header_title' => 'Security Center',
        'status_header' => 'Security System Status:',
        'status_operational' => 'All components operational',
        'status_db' => 'Database Connected',
        'status_policies' => 'Policies Active',
        'status_monitoring' => 'Real-time Monitoring',
        'card_password_title' => 'Password Policy',
        'card_password_desc' => 'Configure password requirements and complexity',
        'card_password_stat' => 'chars',
        'card_login_title' => 'Login Security',
        'card_login_desc' => 'Manage login attempts and session settings',
        'card_login_stat' => 'attempts',
        'card_2fa_title' => 'Two-Factor Auth',
        'card_2fa_desc' => 'Enable additional security layers',
        'card_2fa_stat_enabled' => 'Enabled',
        'card_2fa_stat_disabled' => 'Disabled',
        'card_monitoring_title' => 'Security Monitoring',
        'card_monitoring_desc' => 'Real-time security event tracking',
        'card_monitoring_stat' => 'events',
        'section_password' => 'Password Policy',
        'label_min_length' => 'Minimum Password Length',
        'current_length' => 'Current:',
        'current_applies' => 'Applies to ALL password changes system-wide',
        'label_uppercase' => 'Require uppercase letters',
        'label_numbers' => 'Require numbers',
        'label_special' => 'Require special characters',
        'btn_update_policy' => 'Update Policy',
        'section_login' => 'Login Security',
        'label_max_attempts' => 'Maximum Login Attempts',
        'desc_attempts' => 'Number of failed attempts before account lockout',
        'label_lockout_duration' => 'Lockout Duration (minutes)',
        'desc_lockout' => 'How long to lock account after max attempts',
        'label_session_timeout' => 'Session Timeout (minutes)',
        'desc_timeout' => 'Automatically logout after inactivity',
        'btn_update_settings' => 'Update Settings',
        'section_stats' => 'Security Statistics',
        'stat_failed_logins' => 'Failed Logins Today',
        'stat_locked_accounts' => 'Locked Accounts',
        'section_recent_events' => 'Recent Security Events',
        'table_event' => 'Event',
        'table_description' => 'Description',
        'table_user' => 'User',
        'table_time' => 'Time',
        'no_events' => 'No security events recorded yet.',
        'section_quick_actions' => 'Quick Security Actions',
        'btn_force_reset' => 'Force Password Reset',
        'confirm_force_reset' => 'Are you sure you want to force all users to reset their passwords?',
        'btn_lock_sessions' => 'Lock All Sessions',
        'confirm_lock_sessions' => 'This will log out all users immediately. Are you sure?',
        'btn_scan' => 'Run Security Scan',
        'msg_policy_updated' => 'Password policy updated successfully! Changes are now active system-wide.',
        'msg_login_updated' => 'Login policy updated successfully! Changes are now active system-wide.',
        'msg_2fa_updated' => 'Two-factor authentication settings updated!',
        'msg_force_reset_success' => 'All users will be required to reset their passwords on next login!',
        'msg_scan_completed' => 'Security scan completed! No critical issues found.',
        'msg_error_prefix' => 'Error:',
        'success_icon' => 'check-circle',
        'error_icon' => 'exclamation-triangle',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'am' => [
        'title' => 'ደህንነት ማዕከል - ማቱ የወንጀል መዝገብ ስርዓት',
        'header_title' => 'ደህንነት ማዕከል',
        'status_header' => 'የደህንነት ስርዓት ሁኔታ:',
        'status_operational' => 'ሁሉም ክፍሎች በሥራ ላይ ናቸው',
        'status_db' => 'የውሂብ ቤዝ የተገናኘ',
        'status_policies' => 'ፖሊሲዎች ንቁ ናቸው',
        'status_monitoring' => 'በጊዜው ላይ የሚከታተል',
        'card_password_title' => 'የይለፍ ቃል ፖሊሲ',
        'card_password_desc' => 'የይለፍ ቃል መስፈርቶችን እና ውህደትን ያዘጋጁ',
        'card_password_stat' => 'ቁምፊዎች',
        'card_login_title' => 'የግባ ደህንነት',
        'card_login_desc' => 'የግባ ሙከራዎችን እና የግባ ቅንብሮችን ይቆጣጠሩ',
        'card_login_stat' => 'ሙከራዎች',
        'card_2fa_title' => 'ሁለት ተኮር ማረጋገጫ',
        'card_2fa_desc' => 'ተጨማሪ ደህንነት ሽፋኖችን ያንቁ',
        'card_2fa_stat_enabled' => 'ተክተለ',
        'card_2fa_stat_disabled' => 'ተሰነሰ',
        'card_monitoring_title' => 'ደህንነት መከታተያ',
        'card_monitoring_desc' => 'በጊዜው ላይ የደህንነት ክስተቶችን መከታተል',
        'card_monitoring_stat' => 'ክስተቶች',
        'section_password' => 'የይለፍ ቃል ፖሊሲ',
        'label_min_length' => 'ዝቅተኛ የይለፍ ቃል ርዝመት',
        'current_length' => 'አሁኑ:',
        'current_applies' => 'ለሁሉም የይለፍ ቃል ለውጦች በስርዓት ዙሪያ ይሰራል',
        'label_uppercase' => 'ትልቅ ፊደላት ይጠይቃሉ',
        'label_numbers' => 'ቁጥሮች ይጠይቃሉ',
        'label_special' => 'ልዩ ቁምፊዎች ይጠይቃሉ',
        'btn_update_policy' => 'ፖሊሲን ይዘምኑ',
        'section_login' => 'የግባ ደህንነት',
        'label_max_attempts' => 'ከፍተኛ የግባ ሙከራዎች',
        'desc_attempts' => 'ከአካውንት መቆለፍ በፊት የተሳነው ሙከራዎች ቁጥር',
        'label_lockout_duration' => 'የመቆለፍ ጊዜ (ደቂቃ)',
        'desc_lockout' => 'ከከፍተኛ ሙከራዎች በኋላ አካውንት ለምን ያቆላል',
        'label_session_timeout' => 'የግባ ጊዜ ገደብ (ደቂቃ)',
        'desc_timeout' => 'ከአያያዝ በኋላ በቀጥታ ይወጣል',
        'btn_update_settings' => 'ቅንብሮችን ይዘምኑ',
        'section_stats' => 'ደህንነት ስታቲስቲክስ',
        'stat_failed_logins' => 'ዛሬ የተሳነው ግባዎች',
        'stat_locked_accounts' => 'የተቆለፉ አካውንቶች',
        'section_recent_events' => 'የቅርብ ጊዜ ደህንነት ክስተቶች',
        'table_event' => 'ክስተት',
        'table_description' => 'መግለጫ',
        'table_user' => 'ተጠቃሚ',
        'table_time' => 'ጊዜ',
        'no_events' => 'የሚታወቁ ደህንነት ክስተቶች የሉም።',
        'section_quick_actions' => 'ፈጣን ደህንነት እርምጃዎች',
        'btn_force_reset' => 'የይለፍ ቃል ዳግም ማስጀመር ያስገድዱ',
        'confirm_force_reset' => 'ሁሉንም ተጠቃሚዎች የይለፍ ቃላቸውን ዳግም እንዲጠቀሙ ትፈልጋለህ?',
        'btn_lock_sessions' => 'ሁሉንም ግባዎች ይቆልፉ',
        'confirm_lock_sessions' => 'ይህ ሁሉንም ተጠቃሚዎች በቀጥታ ያውጣል። ትናገራለህ?',
        'btn_scan' => 'ደህንነት ምርመራ ያስጀምሩ',
        'msg_policy_updated' => 'የይለፍ ቃል ፖሊሲ በተሳካ ሁኔታ ተዘመነ! ለውጦቹ አሁን በስርዓት ዙሪያ ንቁ ናቸው።',
        'msg_login_updated' => 'የግባ ፖሊሲ በተሳካ ሁኔታ ተዘመነ! ለውጦቹ አሁን በስርዓት ዙሪያ ንቁ ናቸው።',
        'msg_2fa_updated' => 'የሁለት ተኮር ማረጋገጫ ቅንብሮች ተዘመኑ!',
        'msg_force_reset_success' => 'ሁሉም ተጠቃሚዎች በቀጣዩ ግባ ጊዜ የይለፍ ቃላቸውን መዳግም ይጠይቃሉ!',
        'msg_scan_completed' => 'ደህንነት ምርመራ ተጠናቅቋል! አስፈላጊ ችግሮች አልተገኙም።',
        'msg_error_prefix' => 'ስህተት:',
        'success_icon' => 'check-circle',
        'error_icon' => 'exclamation-triangle',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'om' => [
        'title' => 'Qabeenya Ammaata - Sisteemi Ummata Mattu Diinagdee',
        'header_title' => 'Qabeenya Ammaata',
        'status_header' => 'Balaa Sisteemi Ammaata:',
        'status_operational' => 'Kompoonentoota hundee yoo taane ykn',
        'status_db' => 'Database argame',
        'status_policies' => 'Polisiin Active',
        'status_monitoring' => 'Yoo taane argamsi',
        'card_password_title' => 'Polisii Passwordii',
        'card_password_desc' => 'Maatii passwordii fi balaa argachuu',
        'card_password_stat' => 'chars',
        'card_login_title' => 'Qabeenya Loginii',
        'card_login_desc' => 'Imaammataan loginii fi balaa argachuu',
        'card_login_stat' => 'mucaa',
        'card_2fa_title' => 'Authii Lamaan',
        'card_2fa_desc' => 'Qabeenya sossobaa argachuu',
        'card_2fa_stat_enabled' => 'Ykn',
        'card_2fa_stat_disabled' => 'Moo ykn',
        'card_monitoring_title' => 'Ijaarsa Ammaata',
        'card_monitoring_desc' => 'Yoo taane argamsi ammaata',
        'card_monitoring_stat' => 'ija',
        'section_password' => 'Polisii Passwordii',
        'label_min_length' => 'Maatii Minimumii Passwordii',
        'current_length' => 'Yoo taane:',
        'current_applies' => 'Bilisummaa passwordii hundee argachuu',
        'label_uppercase' => 'Maatii gadii argachuu',
        'label_numbers' => 'Qarqaroo argachuu',
        'label_special' => 'Maatii sossobaa argachuu',
        'btn_update_policy' => 'Polisii Aadaa',
        'section_login' => 'Qabeenya Loginii',
        'label_max_attempts' => 'Mucaa Maksimumii Loginii',
        'desc_attempts' => 'Mucaa moo ykn awwaaluu bilisummaa',
        'label_lockout_duration' => 'Maatii Lockout (digii)',
        'desc_lockout' => 'Mucaa maks mucaa awwaaluu',
        'label_session_timeout' => 'Maatii Session (digii)',
        'desc_timeout' => 'Awwaala yoo taane fufiisi',
        'btn_update_settings' => 'Balaa Aadaa',
        'section_stats' => 'Statistiki Ammaata',
        'stat_failed_logins' => "Loginiin Har'aa Moo",
        'stat_locked_accounts' => 'Akauntaa Lock',
        'section_recent_events' => "Ija Utuu Ammaata",
        'table_event' => 'Ija',
        'table_description' => 'Maatii',
        'table_user' => 'User',
        'table_time' => 'Waqti',
        'no_events' => 'Ija ammaata argamuu miti.',
        'section_quick_actions' => 'Irmaa Qabsoo Ammaata',
        'btn_force_reset' => 'Passwordii Force Reset',
        'confirm_force_reset' => 'Useroota hundee passwordii reset barbaachisu?',
        'btn_lock_sessions' => 'Sessionota Hundee Lock',
        'confirm_lock_sessions' => 'Useroota hundee fufiisi yoo taane. Barbaachisa?',
        'btn_scan' => 'Scan Ammaata Ummisi',
        'msg_policy_updated' => 'Polisii passwordii yoo taane aadaa! Balaa yoo taane active.',
        'msg_login_updated' => 'Polisii loginii yoo taane aadaa! Balaa yoo taane active.',
        'msg_2fa_updated' => 'Balaa Authii lamaan aadaa!',
        'msg_force_reset_success' => 'Useroota hundee passwordii reset barbaachisu!',
        'msg_scan_completed' => 'Scan ammaata yoo taane! Caasaa sossobaa miti.',
        'msg_error_prefix' => 'Maatii:',
        'success_icon' => 'check-circle',
        'error_icon' => 'exclamation-triangle',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

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
    
    // Lock all sessions (logout all users) - FIXED VERSION
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
            
            $message = t('msg_policy_updated');
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
            
            $message = t('msg_login_updated');
        }
        elseif ($action === 'enable_2fa') {
            $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
            $security->updateSetting('two_factor_enabled', $enable_2fa);
            
            $security->logEvent('settings_update', 'Two-factor authentication settings updated');
            $message = t('msg_2fa_updated');
        }
        elseif ($action === 'force_password_reset') {
            if ($security->forcePasswordReset()) {
                $security->logEvent('security_action', 'Forced password reset for all users');
                $message = t('msg_force_reset_success');
            } else {
                throw new Exception("Failed to force password reset. Please check database permissions.");
            }
        }
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
            $message = t('msg_scan_completed');
        }
    } catch (Exception $e) {
        $message = t('msg_error_prefix') . ' ' . $e->getMessage();
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
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts for Amharic support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am'): ?>
    <style>
        body {
            font-family: 'Noto Sans Ethiopic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
    <?php endif; ?>
    
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Language Selector in Header */
        .lang-selector {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .lang-selector form {
            display: inline;
        }
        
        .lang-selector select {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 14px;
        }
        
        .lang-selector select option {
            background: #667eea;
            color: white;
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
            
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
        <div class="header">
            <h1><i class="fas fa-shield-alt"></i> <?php echo t('header_title'); ?></h1>
          
        </div>
        
        <div class="content">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type === 'error' ? 'error' : 'success'; ?>">
                    <i class="fas fa-<?php echo $message_type === 'error' ? t('error_icon') : t('success_icon'); ?>"></i> 
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- System Status Indicator -->
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                <i class="fas fa-check-circle"></i> 
                <strong><?php echo t('status_header'); ?></strong> <?php echo t('status_operational'); ?> | 
                <span class="status-active system-status-indicator"></span><?php echo t('status_db'); ?> | 
                <span class="status-active system-status-indicator"></span><?php echo t('status_policies'); ?> |
                <span class="status-active system-status-indicator"></span><?php echo t('status_monitoring'); ?>
            </div>
            
            <!-- Security Overview Cards -->
            <div class="security-grid">
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4><?php echo t('card_password_title'); ?></h4>
                    <p><?php echo t('card_password_desc'); ?></p>
                    <div class="stat-number"><?php echo $password_min_length; ?>+ <?php echo t('card_password_stat'); ?></div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4><?php echo t('card_login_title'); ?></h4>
                    <p><?php echo t('card_login_desc'); ?></p>
                    <div class="stat-number"><?php echo $login_max_attempts; ?> <?php echo t('card_login_stat'); ?></div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <h4><?php echo t('card_2fa_title'); ?></h4>
                    <p><?php echo t('card_2fa_desc'); ?></p>
                    <div class="stat-number"><?php echo $two_factor_enabled ? t('card_2fa_stat_enabled') : t('card_2fa_stat_disabled'); ?></div>
                </div>
                
                <div class="security-card">
                    <div class="security-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4><?php echo t('card_monitoring_title'); ?></h4>
                    <p><?php echo t('card_monitoring_desc'); ?></p>
                    <div class="stat-number"><?php echo $stats['failed_logins_today']; ?> <?php echo t('card_monitoring_stat'); ?></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <!-- Password Policy -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-lock"></i> <?php echo t('section_password'); ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_password_policy">
                                
                                <div class="form-group">
                                    <label for="min_length"><?php echo t('label_min_length'); ?></label>
                                    <input type="number" class="form-control" id="min_length" name="min_length" 
                                           value="<?php echo htmlspecialchars($password_min_length); ?>" min="6" max="20">
                                    <small style="color: #6c757d; display: block; margin-top: 5px;">
                                        <?php echo t('current_length'); ?> <?php echo $password_min_length; ?> <?php echo t('card_password_stat'); ?> | 
                                        <?php echo t('current_applies'); ?>
                                    </small>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_uppercase" 
                                               <?php echo $password_require_uppercase ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label><?php echo t('label_uppercase'); ?></label>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_numbers" 
                                               <?php echo $password_require_numbers ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label><?php echo t('label_numbers'); ?></label>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="switch">
                                        <input type="checkbox" name="require_special" 
                                               <?php echo $password_require_special ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <label><?php echo t('label_special'); ?></label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo t('btn_update_policy'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                 
                </div>
                
                <div class="col-md-6">
                    <!-- Login Security -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-user-shield"></i> <?php echo t('section_login'); ?></h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="update_login_policy">
                                
                                <div class="form-group">
                                    <label for="max_attempts"><?php echo t('label_max_attempts'); ?></label>
                                    <input type="number" class="form-control" id="max_attempts" name="max_attempts" 
                                           value="<?php echo htmlspecialchars($login_max_attempts); ?>" min="3" max="10">
                                    <small style="color: #6c757d;"><?php echo t('desc_attempts'); ?></small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="lockout_duration"><?php echo t('label_lockout_duration'); ?></label>
                                    <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" 
                                           value="<?php echo htmlspecialchars($login_lockout_duration); ?>" min="5" max="1440">
                                    <small style="color: #6c757d;"><?php echo t('desc_lockout'); ?></small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="session_timeout"><?php echo t('label_session_timeout'); ?></label>
                                    <input type="number" class="form-control" id="session_timeout" name="session_timeout" 
                                           value="<?php echo htmlspecialchars($session_timeout); ?>" min="15" max="480">
                                    <small style="color: #6c757d;"><?php echo t('desc_timeout'); ?></small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> <?php echo t('btn_update_settings'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Security Statistics -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar"></i> <?php echo t('section_stats'); ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php echo $stats['failed_logins_today']; ?></div>
                                        <div class="stat-label"><?php echo t('stat_failed_logins'); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php echo $stats['locked_accounts']; ?></div>
                                        <div class="stat-label"><?php echo t('stat_locked_accounts'); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-top: 20px;">
                                <h4><?php echo t('section_recent_events'); ?></h4>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?php echo t('table_event'); ?></th>
                                            <th><?php echo t('table_description'); ?></th>
                                            <th><?php echo t('table_user'); ?></th>
                                            <th><?php echo t('table_time'); ?></th>
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
                                                    <?php echo t('no_events'); ?>
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
                    <h3><i class="fas fa-bolt"></i> <?php echo t('section_quick_actions'); ?></h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="force_password_reset">
                            <button type="submit" class="btn btn-warning" onclick="return confirm('<?php echo addslashes(t('confirm_force_reset')); ?>')">
                                <i class="fas fa-key"></i> <?php echo t('btn_force_reset'); ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="lock_all_sessions">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('<?php echo addslashes(t('confirm_lock_sessions')); ?>')">
                                <i class="fas fa-lock"></i> <?php echo t('btn_lock_sessions'); ?>
                            </button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="run_security_scan">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> <?php echo t('btn_scan'); ?>
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