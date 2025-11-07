<?php
// api/dashboard_data.php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Enforce officer access
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';
$officerId = $_GET['officer_id'] ?? $_SESSION['user_id'];

// Security check: Ensure officer can only access their own data
if ($officerId != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    switch($action) {
        case 'statistics':
            echo json_encode(getDashboardStatistics($db, $officerId));
            break;
        case 'assigned_cases':
            echo json_encode(getAssignedCases($db, $officerId));
            break;
        case 'recent_activity':
            echo json_encode(getRecentActivity($db, $officerId));
            break;
        case 'notifications':
            echo json_encode(getNotifications($db, $officerId));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Dashboard API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function getDashboardStatistics($db, $officerId) {
    try {
        // Total cases assigned to officer
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_cases 
            FROM cases 
            WHERE assigned_officer_id = ? OR lead_officer_id = ?
        ");
        $stmt->execute([$officerId, $officerId]);
        $totalCases = $stmt->fetch(PDO::FETCH_ASSOC)['total_cases'];
        
        // Active cases
        $stmt = $db->prepare("
            SELECT COUNT(*) as active_cases 
            FROM cases 
            WHERE (assigned_officer_id = ? OR lead_officer_id = ?) 
            AND status IN ('Open', 'Active', 'Under Investigation')
        ");
        $stmt->execute([$officerId, $officerId]);
        $activeCases = $stmt->fetch(PDO::FETCH_ASSOC)['active_cases'];
        
        // Records added this week by this officer
        $stmt = $db->prepare("
            SELECT COUNT(*) as weekly_records 
            FROM criminal_records 
            WHERE created_by = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$officerId]);
        $weeklyRecords = $stmt->fetch(PDO::FETCH_ASSOC)['weekly_records'];
        
        // Pending tasks (cases that need attention)
        $stmt = $db->prepare("
            SELECT COUNT(*) as pending_tasks 
            FROM cases 
            WHERE (assigned_officer_id = ? OR lead_officer_id = ?) 
            AND status IN ('Open', 'Pending', 'Under Investigation')
            AND date_reported >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->execute([$officerId, $officerId]);
        $pendingTasks = $stmt->fetch(PDO::FETCH_ASSOC)['pending_tasks'];
        
        return [
            'success' => true,
            'data' => [
                'total_cases' => $totalCases,
                'active_cases' => $activeCases,
                'weekly_records' => $weeklyRecords,
                'pending_tasks' => $pendingTasks
            ]
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getAssignedCases($db, $officerId) {
    try {
        // Check which table exists
        $tableCheck = $db->query("SHOW TABLES LIKE 'cases'");
        $casesTableExists = $tableCheck->rowCount() > 0;
        
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    case_number,
                    case_type,
                    status,
                    DATE_FORMAT(date_reported, '%Y-%m-%d') as date_reported
                FROM cases 
                WHERE assigned_officer_id = ? OR lead_officer_id = ?
                ORDER BY date_reported DESC 
                LIMIT 5
            ");
        } else {
            // Fallback to case_files table
            $stmt = $db->prepare("
                SELECT 
                    id,
                    case_number,
                    case_type,
                    status,
                    DATE_FORMAT(date_reported, '%Y-%m-%d') as date_reported
                FROM case_files 
                WHERE lead_officer_id = ?
                ORDER BY date_reported DESC 
                LIMIT 5
            ");
        }
        
        $stmt->execute([$officerId, $officerId]);
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $cases
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getRecentActivity($db, $officerId) {
    try {
        $activities = [];
        
        // Get recent criminal records added by this officer
        $stmt = $db->prepare("
            SELECT 
                id,
                CONCAT('Record: ', first_name, ' ', last_name) as title,
                'Added new criminal record' as description,
                'record' as type,
                DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') as timestamp
            FROM criminal_records 
            WHERE created_by = ?
            ORDER BY created_at DESC 
            LIMIT 3
        ");
        $stmt->execute([$officerId]);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $activities = array_merge($activities, $records);
        
        // Get recent case files created by this officer
        $tableCheck = $db->query("SHOW TABLES LIKE 'cases'");
        $casesTableExists = $tableCheck->rowCount() > 0;
        $tableName = $casesTableExists ? 'cases' : 'case_files';
        
        $stmt = $db->prepare("
            SELECT 
                id,
                CONCAT('Case: ', case_number) as title,
                CONCAT('Created ', case_type, ' case') as description,
                'case' as type,
                DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') as timestamp
            FROM $tableName 
            WHERE created_by = ?
            ORDER BY created_at DESC 
            LIMIT 2
        ");
        $stmt->execute([$officerId]);
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $activities = array_merge($activities, $cases);
        
        // Sort by timestamp and limit to 5
        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return [
            'success' => true,
            'data' => array_slice($activities, 0, 5)
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getNotifications($db, $officerId) {
    try {
        $notifications = [];
        
        // Check for cases needing attention (older than 7 days without update)
        $tableCheck = $db->query("SHOW TABLES LIKE 'cases'");
        $casesTableExists = $tableCheck->rowCount() > 0;
        $tableName = $casesTableExists ? 'cases' : 'case_files';
        
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM $tableName 
            WHERE (assigned_officer_id = ? OR lead_officer_id = ?)
            AND status IN ('Open', 'Active', 'Under Investigation')
            AND updated_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$officerId, $officerId]);
        $staleCases = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($staleCases > 0) {
            $notifications[] = [
                'title' => 'Cases Need Attention',
                'message' => "You have $staleCases case(s) that haven't been updated in over 7 days",
                'priority' => 'high'
            ];
        }
        
        // Check for new case assignments in last 24 hours
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM $tableName 
            WHERE (assigned_officer_id = ? OR lead_officer_id = ?)
            AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute([$officerId, $officerId]);
        $newCases = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($newCases > 0) {
            $notifications[] = [
                'title' => 'New Assignments',
                'message' => "You have $newCases new case(s) assigned in the last 24 hours",
                'priority' => 'medium'
            ];
        }
        
        // System notification
        $notifications[] = [
            'title' => 'System Update',
            'message' => 'Dashboard is now auto-updating every 60 seconds',
            'priority' => 'low'
        ];
        
        return [
            'success' => true,
            'data' => $notifications
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>