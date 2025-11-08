<?php
require '../includes/auth.php';
require '../includes/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$database = new Database();
$db = $database->getConnection();

// Get real-time statistics
function getRealtimeStats($db) {
    $stats = [];
    
    try {
        // Check which table exists
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
        $tableName = $casesTableExists ? 'cases' : 'case_files';
        
        // Total Cases (last 30 days)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE (created_at >= ?) OR (date_reported >= ?)");
        $stmt->execute([$thirtyDaysAgo, $thirtyDaysAgo]);
        $stats['total_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Solved Cases (last 30 days)
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status = 'Closed' AND ((created_at >= ?) OR (date_reported >= ?))");
        $stmt->execute([$thirtyDaysAgo, $thirtyDaysAgo]);
        $stats['solved_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Active Cases
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status IN ('Open', 'In Progress', 'Pending')");
        $stmt->execute();
        $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Clearance Rate
        $stats['clearance_rate'] = $stats['total_cases'] > 0 ? 
            round(($stats['solved_cases'] / $stats['total_cases']) * 100, 1) : 0;
            
        // Today's new cases
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE DATE(created_at) = ? OR DATE(date_reported) = ?");
        $stmt->execute([$today, $today]);
        $stats['today_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // Active officers (users logged in today)
        $stmt = $db->query("SELECT COUNT(DISTINCT user_id) as total FROM audit_log WHERE action = 'login' AND DATE(timestamp) = CURDATE()");
        $stats['active_officers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 1;
        
        return [
            'success' => true,
            'data' => $stats,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}

// Get the data
$response = getRealtimeStats($db);
echo json_encode($response);
?>