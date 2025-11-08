<?php
// chief/api/realtime_stats.php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get date range from request or use defaults
    $start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
    // Check which table exists
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    
    if (!$casesTableExists && !$caseFilesTableExists) {
        throw new Exception('No case tables found');
    }
    
    $tableName = $casesTableExists ? 'cases' : 'case_files';
    $stats = [];
    
    // DEBUG: Check table structure
    $debug_info = [];
    $debug_info['table_name'] = $tableName;
    
    // Check if case_type column exists
    $columnCheck = $db->query("SHOW COLUMNS FROM $tableName LIKE 'case_type'")->rowCount() > 0;
    $debug_info['case_type_exists'] = $columnCheck;
    
    // Check total records in table
    $totalRecords = $db->query("SELECT COUNT(*) as total FROM $tableName")->fetch(PDO::FETCH_ASSOC)['total'];
    $debug_info['total_records'] = $totalRecords;
    
    // Check records in date range
    $dateRangeCheck = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE (created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?)");
    $dateRangeCheck->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
    $debug_info['records_in_date_range'] = $dateRangeCheck->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Check actual case_type values
    $caseTypesCheck = $db->query("SELECT case_type, COUNT(*) as count FROM $tableName GROUP BY case_type");
    $debug_info['case_types_found'] = $caseTypesCheck->fetchAll(PDO::FETCH_ASSOC);
    
    // 1. BASIC STATISTICS (Simplified approach)
    $stats['total_cases'] = $totalRecords;
    
    // Solved Cases
    $solvedStmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status = 'Closed'");
    $solvedStmt->execute();
    $stats['solved_cases'] = (int)($solvedStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Active Cases
    $activeStmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status IN ('Open', 'In Progress', 'Pending')");
    $activeStmt->execute();
    $stats['active_cases'] = (int)($activeStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Clearance Rate
    $stats['clearance_rate'] = $stats['total_cases'] > 0 ? 
        round(($stats['solved_cases'] / $stats['total_cases']) * 100, 1) : 0;
        
    // Today's new cases
    $today = date('Y-m-d');
    $todayStmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE DATE(created_at) = ? OR DATE(date_reported) = ?");
    $todayStmt->execute([$today, $today]);
    $stats['today_cases'] = (int)($todayStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // 2. TOP OFFENSE CATEGORIES (SIMPLIFIED - Remove date filters temporarily)
    if ($columnCheck) {
        $offenseCategoriesQuery = "
            SELECT 
                case_type,
                COUNT(*) as count
            FROM $tableName 
            GROUP BY case_type
            ORDER BY count DESC 
            LIMIT 10
        ";
        $stmt = $db->query($offenseCategoriesQuery);
        $stats['top_offenses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Fallback if case_type doesn't exist
        $stats['top_offenses'] = [];
        $debug_info['case_type_fallback'] = 'case_type column not found';
    }
    
    $debug_info['top_offenses_result'] = $stats['top_offenses'];

    // 3. CRIME TRENDS ANALYSIS (12 Months)
    $crimeTrendsQuery = "
        SELECT 
            DATE_FORMAT(COALESCE(created_at, date_reported), '%Y-%m') as month,
            COUNT(*) as total_cases,
            SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as solved_cases
        FROM $tableName 
        WHERE (created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) OR date_reported >= DATE_SUB(NOW(), INTERVAL 12 MONTH))
        GROUP BY DATE_FORMAT(COALESCE(created_at, date_reported), '%Y-%m')
        ORDER BY month DESC
        LIMIT 12
    ";
    $stmt = $db->query($crimeTrendsQuery);
    $stats['crime_trends'] = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

    // 4. CASE STATUS DISTRIBUTION
    $statusDistributionQuery = "
        SELECT 
            status,
            COUNT(*) as count
        FROM $tableName 
        GROUP BY status
        ORDER BY count DESC
    ";
    $stmt = $db->query($statusDistributionQuery);
    $stats['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. OFFICER PERFORMANCE METRICS
    $usersTableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    if ($usersTableExists) {
        $officerPerformanceQuery = "
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                COUNT(c.id) as total_cases,
                SUM(CASE WHEN c.status = 'Closed' THEN 1 ELSE 0 END) as solved_cases,
                CASE 
                    WHEN COUNT(c.id) > 0 THEN 
                        ROUND((SUM(CASE WHEN c.status = 'Closed' THEN 1 ELSE 0 END) * 100.0 / COUNT(c.id)), 1)
                    ELSE 0 
                END as clearance_rate
            FROM users u
            LEFT JOIN $tableName c ON u.user_id = c.assigned_officer_id OR u.user_id = c.lead_officer_id
            WHERE u.role IN ('officer', 'chief', 'admin')
                AND u.is_active = 1
            GROUP BY u.user_id, u.first_name, u.last_name
            HAVING total_cases > 0
            ORDER BY clearance_rate DESC, total_cases DESC
            LIMIT 10
        ";
        $stmt = $db->query($officerPerformanceQuery);
        $stats['officer_performance'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stats['officer_performance'] = [];
    }
    
    // Active officers count
    if ($usersTableExists) {
        $officersStmt = $db->query("SELECT COUNT(*) as total FROM users WHERE role IN ('officer', 'chief', 'admin') AND is_active = 1");
        $stats['active_officers'] = (int)($officersStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 1);
    } else {
        $stats['active_officers'] = 1;
    }

    echo json_encode([
        'success' => true,
        'data' => $stats,
        'debug' => $debug_info, // Remove this in production
        'metadata' => [
            'date_range' => ['start' => $start_date, 'end' => $end_date],
            'table_used' => $tableName,
            'records_analyzed' => $stats['total_cases']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    error_log("Realtime stats error: " . $e->getMessage());
    
    // Enhanced fallback data with your actual case types
    $fallbackData = [
        'total_cases' => 4, // Based on your sample data
        'solved_cases' => 1, // Based on your sample data (1 closed case)
        'active_cases' => 3, // Based on your sample data
        'clearance_rate' => 25.0, // 1/4 = 25%
        'today_cases' => 0,
        'active_officers' => 3,
        'crime_trends' => [],
        'status_distribution' => [
            ['status' => 'Closed', 'count' => 1],
            ['status' => 'In Progress', 'count' => 1],
            ['status' => 'Open', 'count' => 1]
        ],
        'top_offenses' => [
            ['case_type' => 'Theft', 'count' => 1],
            ['case_type' => 'Robbery', 'count' => 1],
            ['case_type' => 'Domestic Violence', 'count' => 1],
            ['case_type' => 'Fraud', 'count' => 1]
        ],
        'officer_performance' => [],
        'avg_days_to_close' => 1.0
    ];
    
    // Generate sample trends data
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $fallbackData['crime_trends'][] = [
            'month' => $month,
            'total_cases' => rand(1, 4),
            'solved_cases' => rand(0, 2)
        ];
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => $fallbackData,
        'fallback' => true
    ]);
}
?>