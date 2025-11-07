<?php
// includes/security_enforcer.php - COMPLETE UNIVERSAL VERSION WITH ALL FIXES
class SecurityEnforcer {
    private $db;
    private $db_type;
    private $settings = [];
    private $settings_loaded = false;
    
    public function __construct($db_connection) {
        $this->db = $db_connection;
        
        // Detect database connection type
        if ($db_connection instanceof mysqli) {
            $this->db_type = 'mysqli';
        } elseif ($db_connection instanceof PDO) {
            $this->db_type = 'pdo';
        } else {
            throw new Exception("Unsupported database connection type");
        }
        
        $this->loadSecuritySettings();
    }
    
    /**
     * Reload security settings from database (call this after settings change)
     */
    public function reloadSettings() {
        $this->settings = [];
        $this->settings_loaded = false;
        $this->loadSecuritySettings();
        return $this->settings_loaded;
    }
    
    /**
     * Get all current security settings (for debugging)
     */
    public function getAllSettings() {
        if (!$this->settings_loaded) {
            $this->loadSecuritySettings();
        }
        return $this->settings;
    }
    
    private function loadSecuritySettings() {
        try {
            $query = "SELECT setting_key, setting_value FROM security_settings";
            
            if ($this->db_type === 'mysqli') {
                // MySQLi version
                $stmt = $this->db->prepare($query);
                if ($stmt) {
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    while ($row = $result->fetch_assoc()) {
                        $this->settings[$row['setting_key']] = $row['setting_value'];
                    }
                    $stmt->close();
                    $this->settings_loaded = true;
                }
            } else {
                // PDO version
                $stmt = $this->db->query($query);
                if ($stmt) {
                    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach ($settings as $row) {
                        $this->settings[$row['setting_key']] = $row['setting_value'];
                    }
                    $this->settings_loaded = true;
                }
            }
            
            // If no settings were loaded, use defaults
            if (empty($this->settings)) {
                $this->setDefaultSettings();
            }
            
        } catch (Exception $e) {
            error_log("Security settings load error: " . $e->getMessage());
            // Set default settings if table doesn't exist
            $this->setDefaultSettings();
        }
    }
    
    private function setDefaultSettings() {
        $this->settings = [
            'password_min_length' => '8',
            'password_require_uppercase' => '1',
            'password_require_numbers' => '1',
            'password_require_special' => '1',
            'login_max_attempts' => '5',
            'session_timeout' => '60'
        ];
        $this->settings_loaded = true;
    }
    
    /**
     * Validate password against current security policy
     */
    public function validatePassword($password) {
        if (!$this->settings_loaded) {
            $this->loadSecuritySettings();
        }
        
        $min_length = intval($this->getSetting('password_min_length', 8));
        $require_upper = $this->getSetting('password_require_uppercase', '1') === '1';
        $require_numbers = $this->getSetting('password_require_numbers', '1') === '1';
        $require_special = $this->getSetting('password_require_special', '1') === '1';
        
        // Debug logging
        error_log("Password Validation Debug - min_length: $min_length, require_upper: $require_upper, require_numbers: $require_numbers, require_special: $require_special");
        
        if (strlen($password) < $min_length) {
            return "Password must be at least $min_length characters long";
        }
        
        if ($require_upper && !preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter";
        }
        
        if ($require_numbers && !preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number";
        }
        
        if ($require_special && !preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            return "Password must contain at least one special character";
        }
        
        return true;
    }
    
    /**
     * Get password policy description for display
     */
    public function getPasswordPolicyDescription() {
        if (!$this->settings_loaded) {
            $this->loadSecuritySettings();
        }
        
        $min_length = intval($this->getSetting('password_min_length', 8));
        $require_upper = $this->getSetting('password_require_uppercase', '1') === '1';
        $require_numbers = $this->getSetting('password_require_numbers', '1') === '1';
        $require_special = $this->getSetting('password_require_special', '1') === '1';
        
        $requirements = ["Minimum $min_length characters"];
        
        if ($require_upper) {
            $requirements[] = "one uppercase letter (A-Z)";
        }
        if ($require_numbers) {
            $requirements[] = "one number (0-9)";
        }
        if ($require_special) {
            $requirements[] = "one special character (!@#$%^&* etc.)";
        }
        
        return "Password must contain: " . implode(", ", $requirements);
    }
    
    // Check login attempts
    public function checkLoginAttempts($user_id) {
        $max_attempts = intval($this->getSetting('login_max_attempts', 5));
        
        try {
            $query = "SELECT login_attempts FROM users WHERE user_id = ?";
            
            if ($this->db_type === 'mysqli') {
                // MySQLi version
                $stmt = $this->db->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $stmt->close();
                    
                    if ($user && intval($user['login_attempts']) >= $max_attempts) {
                        return "Account locked due to too many failed login attempts";
                    }
                }
            } else {
                // PDO version
                $stmt = $this->db->prepare($query);
                $stmt->execute([$user_id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && intval($user['login_attempts']) >= $max_attempts) {
                    return "Account locked due to too many failed login attempts";
                }
            }
        } catch (Exception $e) {
            error_log("Login attempts check error: " . $e->getMessage());
        }
        
        return true;
    }
    
    // Handle failed login
    public function handleFailedLogin($user_id) {
        try {
            $query = "UPDATE users SET login_attempts = COALESCE(login_attempts, 0) + 1 WHERE user_id = ?";
            
            if ($this->db_type === 'mysqli') {
                $stmt = $this->db->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute([$user_id]);
            }
            
            // Log the failed login attempt
            $this->logSecurityEvent('failed_login', "Failed login attempt for user ID: $user_id", $user_id);
        } catch (Exception $e) {
            error_log("Failed login handling error: " . $e->getMessage());
        }
    }
    
    // Reset login attempts on successful login
    public function resetLoginAttempts($user_id) {
        try {
            $query = "UPDATE users SET login_attempts = 0 WHERE user_id = ?";
            
            if ($this->db_type === 'mysqli') {
                $stmt = $this->db->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute([$user_id]);
            }
        } catch (Exception $e) {
            error_log("Reset login attempts error: " . $e->getMessage());
        }
    }
    
    // Log security event
    public function logSecurityEvent($event_type, $description, $user_id = null) {
        try {
            $query = "INSERT INTO security_logs (user_id, event_type, description, ip_address, user_agent, created_at) 
                      VALUES (?, ?, ?, ?, ?, NOW())";
            
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            if ($this->db_type === 'mysqli') {
                $stmt = $this->db->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("issss", $user_id, $event_type, $description, $ip_address, $user_agent);
                    $result = $stmt->execute();
                    $stmt->close();
                    return $result;
                }
            } else {
                $stmt = $this->db->prepare($query);
                return $stmt->execute([$user_id, $event_type, $description, $ip_address, $user_agent]);
            }
        } catch (Exception $e) {
            error_log("Security log error: " . $e->getMessage());
            return false;
        }
        return false;
    }
    
    /**
     * Get security policy value with fallback
     */
    public function getPolicy($key, $default = '') {
        if (!$this->settings_loaded) {
            $this->loadSecuritySettings();
        }
        return $this->settings[$key] ?? $default;
    }
    
    /**
     * Internal method to get settings with proper type conversion
     */
    private function getSetting($key, $default = '') {
        if (!$this->settings_loaded) {
            $this->loadSecuritySettings();
        }
        return $this->settings[$key] ?? $default;
    }
    
    /**
     * Check if security settings table exists
     */
    public function checkSettingsTable() {
        try {
            if ($this->db_type === 'mysqli') {
                $result = $this->db->query("SHOW TABLES LIKE 'security_settings'");
                return $result && $result->num_rows > 0;
            } else {
                $stmt = $this->db->query("SHOW TABLES LIKE 'security_settings'");
                return $stmt && $stmt->rowCount() > 0;
            }
        } catch (Exception $e) {
            error_log("Check settings table error: " . $e->getMessage());
            return false;
        }
    }
}
?>