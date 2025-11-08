<?php
// api/clerk_stats.php
// API endpoint for real-time clerk statistics. Queries database counts for dashboard display.
// Returns JSON. Accessible only to clerks. No caching - direct DB hits for freshness.

// Start session and includes
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Role check
requireRole(['clerk']);

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

try {
    $stats = [];
    
    // Total criminal records (excludes deleted) - real-time count
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM criminal_records WHERE status != 'deleted'");
    $stmt->execute();
    $stats['total_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Case statuses (assumes 'cases' table with status column)
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'active'");
    $stmt->execute();
    $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'pending'");
    $stmt->execute();
    $stats['pending_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'closed'");
    $stmt->execute();
    $stats['closed_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Clerk stats error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Stats error']);
}
?>