<?php
// includes/clerk_functions.php
// Updated: Enhanced getSystemStats to handle table detection and common status values like chief dashboard.
// Now checks for 'cases' or 'case_files' table and uses statuses: 'Open', 'In Progress', 'Pending' for active.
// This fixes zero counts if statuses don't match 'active'/'pending'/'closed'.

class ClerkFunctions {
    private $conn; // PDO connection
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Updated: Get system statistics with table detection and flexible statuses
    public function getSystemStats() {
        try {
            $stats = [];
            
            // Total records (non-deleted) - unchanged
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM criminal_records WHERE status != 'deleted'");
            $stmt->execute();
            $stats['total_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Check which cases table exists (like chief dashboard)
            $casesTableExists = $this->conn->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
            $caseFilesTableExists = $this->conn->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
            $tableName = $casesTableExists ? 'cases' : ($caseFilesTableExists ? 'case_files' : null);
            
            if (!$tableName) {
                // No cases table: fallback to zeros
                $stats['active_cases'] = 0;
                $stats['pending_cases'] = 0;
                $stats['closed_cases'] = 0;
                return $stats;
            }
            
            // Active cases: status IN ('Open', 'In Progress', 'Pending')
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM $tableName WHERE status IN ('Open', 'In Progress', 'Pending')");
            $stmt->execute();
            $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Pending cases: status = 'Pending'
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM $tableName WHERE status = 'Pending'");
            $stmt->execute();
            $stats['pending_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Closed cases: status = 'Closed'
            $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM $tableName WHERE status = 'Closed'");
            $stmt->execute();
            $stats['closed_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            return $stats;
        } catch (Exception $e) {
            error_log("Error getting system stats: " . $e->getMessage());
            // Fallback zeros for error handling
            return [
                'total_records' => 0,
                'active_cases' => 0,
                'pending_cases' => 0,
                'closed_cases' => 0
            ];
        }
    }
    
    // Get system news: From system_announcements table, filtered for clerks
    public function getSystemNews() {
        try {
            $stmt = $this->conn->prepare("
                SELECT title, content, created_at
                FROM system_announcements 
                WHERE is_active = 1 
                AND target_role IN ('all', 'clerk')
                ORDER BY created_at DESC 
                LIMIT 5  -- Recent 5 only
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting system news: " . $e->getMessage());
            return []; // Empty on error
        }
    }
    
    // Search records: Fuzzy search on names, IDs, record numbers
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
            return []; // Empty on error
        }
    }
    
    // Get single record by ID: For viewing details
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
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("Query for ID $id returned: " . ($result ? 'Found' : 'Null'));  // Debug log
            return $result;
        } catch (Exception $e) {
            error_log("Error getting record by ID: " . $e->getMessage());
            return null; // Null on error
        }
    }
}

// Database Schema Notes (Run once to add tables if missing):
/*
-- System announcements table for news/updates
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

-- Sample data (insert if empty)
INSERT INTO system_announcements (title, content, target_role, created_by) VALUES
('Data Entry Guidelines Updated', 'Ensure National ID: 12-digit format, no spaces/dashes.', 'clerk', 1),
('System Maintenance', 'Maintenance Sunday 2-4 AM. System unavailable.', 'all', 1),
('New Search Features', 'Partial names/case numbers supported. Case-insensitive.', 'clerk', 1);

-- Ensure 'cases' or 'case_files' table has status column
-- ALTER TABLE cases ADD COLUMN IF NOT EXISTS status ENUM('Open', 'In Progress', 'Pending', 'Closed', 'deleted') DEFAULT 'Pending';
-- ALTER TABLE case_files ADD COLUMN IF NOT EXISTS status ENUM('Open', 'In Progress', 'Pending', 'Closed', 'deleted') DEFAULT 'Pending';
-- ALTER TABLE criminal_records ADD COLUMN IF NOT EXISTS status ENUM('first-offender', 'repeat', 'wanted', 'deleted') DEFAULT 'first-offender';
*/
?>