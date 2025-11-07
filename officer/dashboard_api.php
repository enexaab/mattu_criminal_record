<?php
// officer/dashboard_api.php
session_start();
require_once '../includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}


header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';
$officerId = $_SESSION['user_id'];

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
        $totalCases = 0;
        $activeCases = 0;
        $weeklyRecords = 0;
        $pendingTasks = 0;

        // Check which tables exist
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;

        // Total cases assigned to this officer
        if ($casesTableExists) {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM cases WHERE assigned_officer_id = ? OR lead_officer_id = ?");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalCases = $result ? $result['total'] : 0;
            
            // Active cases
            $stmt = $db->prepare("SELECT COUNT(*) as active FROM cases WHERE (assigned_officer_id = ? OR lead_officer_id = ?) AND status IN ('Open', 'Active', 'Under Investigation', 'Pending')");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $activeCases = $result ? $result['active'] : 0;
            
            // Pending tasks (all active cases)
            $pendingTasks = $activeCases;
            
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM case_files WHERE lead_officer_id = ?");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalCases = $result ? $result['total'] : 0;
            
            $stmt = $db->prepare("SELECT COUNT(*) as active FROM case_files WHERE lead_officer_id = ? AND status IN ('Open', 'Active', 'Under Investigation', 'Pending')");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $activeCases = $result ? $result['active'] : 0;
            
            $pendingTasks = $activeCases;
        }

        // Weekly records - REAL DATA from criminal_records
        if ($criminalRecordsExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as weekly 
                FROM criminal_records 
                WHERE created_by = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $weeklyRecords = $result ? $result['weekly'] : 0;
        }

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
        // Check which tables exist
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;

        $cases = [];
        $total = 0;

        if ($casesTableExists) {
            // Get cases with criminal record information if available
            if ($criminalRecordsExists) {
                $stmt = $db->prepare("
                    SELECT 
                        c.id,
                        c.case_number,
                        c.case_type,
                        c.status,
                        DATE_FORMAT(c.date_reported, '%Y-%m-%d') as date_reported,
                        COALESCE(CONCAT(cr.first_name, ' ', cr.last_name), 'Not Specified') as criminal_name
                    FROM cases c
                    LEFT JOIN case_persons cp ON c.id = cp.case_id
                    LEFT JOIN criminal_records cr ON cp.record_id = cr.id
                    WHERE c.assigned_officer_id = ? OR c.lead_officer_id = ?
                    GROUP BY c.id
                    ORDER BY c.date_reported DESC
                    LIMIT 5
                ");
            } else {
                $stmt = $db->prepare("
                    SELECT 
                        id,
                        case_number,
                        case_type,
                        status,
                        DATE_FORMAT(date_reported, '%Y-%m-%d') as date_reported,
                        'Not Available' as criminal_name
                    FROM cases 
                    WHERE assigned_officer_id = ? OR lead_officer_id = ?
                    ORDER BY date_reported DESC
                    LIMIT 5
                ");
            }
            $stmt->execute([$officerId, $officerId]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get total count
            $countStmt = $db->prepare("SELECT COUNT(*) as total FROM cases WHERE assigned_officer_id = ? OR lead_officer_id = ?");
            $countStmt->execute([$officerId, $officerId]);
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
            
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    case_number,
                    case_type,
                    status,
                    DATE_FORMAT(date_reported, '%Y-%m-%d') as date_reported,
                    'Case File' as criminal_name
                FROM case_files 
                WHERE lead_officer_id = ?
                ORDER BY date_reported DESC
                LIMIT 5
            ");
            $stmt->execute([$officerId]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $countStmt = $db->prepare("SELECT COUNT(*) as total FROM case_files WHERE lead_officer_id = ?");
            $countStmt->execute([$officerId]);
            $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        return [
            'success' => true,
            'data' => $cases,
            'total' => $total
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getRecentActivity($db, $officerId) {
    try {
        $activities = [];
        
        // Check which tables exist
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;

        // Get recent criminal records added by this officer - REAL DATA
        if ($criminalRecordsExists) {
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
        }

        // Get recent cases created by this officer - REAL DATA
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    CONCAT('Case: ', case_number) as title,
                    CONCAT('Created ', case_type, ' case') as description,
                    'case' as type,
                    DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') as timestamp
                FROM cases 
                WHERE created_by = ?
                ORDER BY created_at DESC 
                LIMIT 2
            ");
            $stmt->execute([$officerId]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $activities = array_merge($activities, $cases);
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    CONCAT('Case: ', case_number) as title,
                    CONCAT('Created ', case_type, ' case') as description,
                    'case' as type,
                    DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') as timestamp
                FROM case_files 
                WHERE created_by = ?
                ORDER BY created_at DESC 
                LIMIT 2
            ");
            $stmt->execute([$officerId]);
            $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $activities = array_merge($activities, $cases);
        }

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
        
        // Check which tables exist
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;

        // Check for cases needing attention (older than 3 days without update) - REAL DATA
        $staleCases = 0;
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM cases 
                WHERE (assigned_officer_id = ? OR lead_officer_id = ?)
                AND status IN ('Open', 'Active', 'Under Investigation', 'Pending')
                AND updated_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
            ");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $staleCases = $result ? $result['count'] : 0;
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM case_files 
                WHERE lead_officer_id = ?
                AND status IN ('Open', 'Active', 'Under Investigation', 'Pending')
                AND created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $staleCases = $result ? $result['count'] : 0;
        }

        if ($staleCases > 0) {
            $notifications[] = [
                'type' => 'urgent',
                'title' => 'Cases Need Attention',
                'message' => "You have $staleCases case(s) that haven't been updated in over 3 days"
            ];
        }

        // Check for new case assignments in last 24 hours - REAL DATA
        $newCases = 0;
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM cases 
                WHERE (assigned_officer_id = ? OR lead_officer_id = ?)
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCases = $result ? $result['count'] : 0;
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM case_files 
                WHERE lead_officer_id = ?
                AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCases = $result ? $result['count'] : 0;
        }

        if ($newCases > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'New Case Assignment',
                'message' => "$newCases new case(s) assigned to you in the last 24 hours"
            ];
        }

        // Get weekly records count for notification - REAL DATA
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;
        $weeklyRecords = 0;
        if ($criminalRecordsExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM criminal_records 
                WHERE created_by = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $weeklyRecords = $result ? $result['count'] : 0;
        }

        if ($weeklyRecords > 0) {
            $notifications[] = [
                'type' => 'success',
                'title' => 'Records Added',
                'message' => "You've added $weeklyRecords criminal record(s) this week"
            ];
        }

        // Add welcome notification if no other notifications
        if (empty($notifications)) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Welcome to Dashboard',
                'message' => 'Everything is up to date. No pending actions required.'
            ];
        }

        return [
            'success' => true,
            'data' => $notifications
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>