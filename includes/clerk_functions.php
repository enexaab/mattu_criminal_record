<?php
class ClerkFunctions {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getSystemStats() {
        try {
            $stats = [];
            
            // Total criminal records
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM criminal_records WHERE status != 'deleted'");
            $stmt->execute();
            $stats['total_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Active cases
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'active'");
            $stmt->execute();
            $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Pending cases
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'pending'");
            $stmt->execute();
            $stats['pending_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Closed cases
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'closed'");
            $stmt->execute();
            $stats['closed_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting system stats: " . $e->getMessage());
            return [
                'total_records' => 0,
                'active_cases' => 0,
                'pending_cases' => 0,
                'closed_cases' => 0
            ];
        }
    }
    
    public function getSystemNews() {
        try {
            $stmt = $this->conn->prepare("
                SELECT title, content, created_at
                FROM system_announcements 
                WHERE is_active = 1 
                AND target_role IN ('all', 'clerk')
                ORDER BY created_at DESC 
                LIMIT 5
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting system news: " . $e->getMessage());
            return [];
        }
    }
    
    public function searchRecords($query, $limit = 20, $offset = 0) {
        try {
            $searchTerm = "%$query%";
            
            $stmt = $this->conn->prepare("
                SELECT 
                    cr.*,
                    CONCAT(cr.first_name, ' ', cr.last_name) as full_name,
                    DATE_FORMAT(cr.created_at, '%M %d, %Y') as created_date
                FROM criminal_records cr
                WHERE (CONCAT(cr.first_name, ' ', cr.last_name) LIKE ? 
                       OR cr.national_id LIKE ? 
                       OR cr.record_number LIKE ?)
                AND cr.status != 'deleted'
                ORDER BY cr.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error searching records: " . $e->getMessage());
            return [];
        }
    }
    
    public function getRecordById($id) {
        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    cr.*,
                    CONCAT(cr.first_name, ' ', cr.last_name) as full_name,
                    u.username as created_by_username
                FROM criminal_records cr
                LEFT JOIN users u ON cr.created_by = u.id
                WHERE cr.id = ? AND cr.status != 'deleted'
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting record by ID: " . $e->getMessage());
            return null;
        }
    }
}

// Database schema additions for clerk functionality
/*
CREATE TABLE IF NOT EXISTS system_announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    target_role ENUM('all', 'administrator', 'officer', 'clerk') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_target_role (target_role),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Sample system announcements
INSERT INTO system_announcements (title, content, target_role, created_by) VALUES
('Data Entry Guidelines Updated', 'Please ensure all National ID numbers are entered in the correct 12-digit format without spaces or dashes.', 'clerk', 1),
('System Maintenance Notice', 'Scheduled maintenance will occur this Sunday from 2:00 AM to 4:00 AM. The system will be temporarily unavailable.', 'all', 1),
('New Search Features', 'You can now search using partial names and case numbers. The search is case-insensitive for better results.', 'clerk', 1);
*/
?>
