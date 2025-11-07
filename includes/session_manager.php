<?php
// includes/session_manager.php
class SessionManager {
    private $conn;
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
        $this->initializeSessionTable();
    }
    
    private function initializeSessionTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS user_sessions (
                session_id VARCHAR(128) PRIMARY KEY,
                user_id INT,
                username VARCHAR(100),
                ip_address VARCHAR(45),
                user_agent TEXT,
                login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                is_active TINYINT DEFAULT 1,
                logout_time TIMESTAMP NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_is_active (is_active),
                INDEX idx_last_activity (last_activity)
            )";
            
            if ($this->conn->query($query) === false) {
                throw new Exception("Failed to create session table: " . $this->conn->error);
            }
        } catch (Exception $e) {
            error_log("Session table initialization error: " . $e->getMessage());
        }
    }
    
    // Record user login
    public function recordLogin($user_id, $username) {
        try {
            $session_id = session_id();
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $query = "INSERT INTO user_sessions (session_id, user_id, username, ip_address, user_agent, login_time, is_active) 
                      VALUES (?, ?, ?, ?, ?, NOW(), 1)
                      ON DUPLICATE KEY UPDATE 
                      user_id = VALUES(user_id),
                      username = VALUES(username),
                      ip_address = VALUES(ip_address),
                      user_agent = VALUES(user_agent),
                      login_time = NOW(),
                      is_active = 1,
                      logout_time = NULL";
                      
            $stmt = $this->conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("sisss", $session_id, $user_id, $username, $ip_address, $user_agent);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
            return false;
        } catch (Exception $e) {
            error_log("Record login error: " . $e->getMessage());
            return false;
        }
    }
    
    // Record user logout
    public function recordLogout($session_id = null) {
        try {
            $session_id = $session_id ?: session_id();
            $query = "UPDATE user_sessions SET is_active = 0, logout_time = NOW() WHERE session_id = ? AND is_active = 1";
            $stmt = $this->conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param("s", $session_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
            return false;
        } catch (Exception $e) {
            error_log("Record logout error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get active sessions count
    public function getActiveSessionsCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM user_sessions WHERE is_active = 1";
            $result = $this->conn->query($query);
            if ($result) {
                $row = $result->fetch_assoc();
                return $row['count'] ?? 0;
            }
            return 0;
        } catch (Exception $e) {
            error_log("Get active sessions error: " . $e->getMessage());
            return 0;
        }
    }
}
?>