<?php
// api/officer_data.php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Enforce officer access - use basic role check if function doesn't exist
if (!function_exists('requireRole')) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['officer', 'investigator', 'admin'])) {
        echo json_encode(['success' => false, 'message' => 'Access denied. Officer role required.']);
        exit;
    }
} else {
    requireRole(['officer', 'investigator', 'admin']);
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
        case 'assigned_cases':
            echo json_encode(getAssignedCases($db, $officerId));
            break;
        case 'weekly_stats':
            echo json_encode(getWeeklyStats($db, $officerId));
            break;
        case 'recent_searches':
            echo json_encode(getRecentSearches($db, $officerId));
            break;
        case 'notifications':
            echo json_encode(getNotifications($db, $officerId));
            break;
        case 'statistics':
            echo json_encode(getDashboardStatistics($db, $officerId));
            break;
        case 'recent_activity':
            echo json_encode(getRecentActivity($db, $officerId));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Officer Dashboard API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
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
            // Try to join with criminal_records if possible
            if ($criminalRecordsExists) {
                $stmt = $db->prepare("
                    SELECT 
                        c.id,
                        c.case_number,
                        COALESCE(CONCAT(cr.first_name, ' ', cr.last_name), 'Unknown') as criminal_name,
                        c.status,
                        c.updated_at,
                        DATE_FORMAT(c.updated_at, '%M %d, %Y') as last_updated
                    FROM cases c
                    LEFT JOIN criminal_records cr ON c.criminal_record_id = cr.id
                    WHERE c.assigned_officer_id = ? OR c.lead_officer_id = ?
                    ORDER BY c.updated_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$officerId, $officerId]);
            } else {
                $stmt = $db->prepare("
                    SELECT 
                        id,
                        case_number,
                        'Not specified' as criminal_name,
                        status,
                        updated_at,
                        DATE_FORMAT(updated_at, '%M %d, %Y') as last_updated
                    FROM cases 
                    WHERE assigned_officer_id = ? OR lead_officer_id = ?
                    ORDER BY updated_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$officerId, $officerId]);
            }
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
                    case_type as criminal_name,
                    status,
                    created_at as updated_at,
                    DATE_FORMAT(created_at, '%M %d, %Y') as last_updated
                FROM case_files 
                WHERE lead_officer_id = ?
                ORDER BY created_at DESC
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

function getWeeklyStats($db, $officerId) {
    try {
        $records_added = 0;
        
        // Check if criminal_records table exists
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;
        
        if ($criminalRecordsExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as records_added
                FROM criminal_records 
                WHERE created_by = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $records_added = $result ? $result['records_added'] : 0;
        }
        
        return [
            'success' => true,
            'data' => ['records_added' => $records_added]
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getRecentSearches($db, $officerId) {
    try {
        // Check if view_history table exists
        $viewHistoryExists = $db->query("SHOW TABLES LIKE 'view_history'")->rowCount() > 0;
        
        if ($viewHistoryExists) {
            $stmt = $db->prepare("
                SELECT 
                    target_type as type,
                    target_id as id,
                    target_title as title,
                    DATE_FORMAT(viewed_at, '%M %d at %h:%i %p') as viewed_at
                FROM view_history 
                WHERE user_id = ?
                ORDER BY viewed_at DESC
                LIMIT 5
            ");
            $stmt->execute([$officerId]);
            $searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Return empty array if table doesn't exist
            $searches = [];
        }
        
        return [
            'success' => true,
            'data' => $searches
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

        // Check for cases needing attention (older than 7 days without update)
        $staleCases = 0;
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM cases 
                WHERE (assigned_officer_id = ? OR lead_officer_id = ?)
                AND status IN ('Open', 'Active', 'Under Investigation')
                AND updated_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $staleCases = $result ? $result['count'] : 0;
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM case_files 
                WHERE lead_officer_id = ?
                AND status IN ('Open', 'Active', 'Under Investigation')
                AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $staleCases = $result ? $result['count'] : 0;
        }

        if ($staleCases > 0) {
            $notifications[] = [
                'type' => 'urgent',
                'title' => 'Cases Need Attention',
                'message' => "You have $staleCases case(s) that haven't been updated in over 7 days"
            ];
        }

        // Check for new case assignments in last 24 hours
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

        // Total cases
        if ($casesTableExists) {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM cases WHERE assigned_officer_id = ? OR lead_officer_id = ?");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalCases = $result ? $result['total'] : 0;
            
            // Active cases
            $stmt = $db->prepare("SELECT COUNT(*) as active FROM cases WHERE (assigned_officer_id = ? OR lead_officer_id = ?) AND status IN ('Open', 'Active', 'Under Investigation')");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $activeCases = $result ? $result['active'] : 0;
            
            // Pending tasks
            $stmt = $db->prepare("SELECT COUNT(*) as pending FROM cases WHERE (assigned_officer_id = ? OR lead_officer_id = ?) AND status IN ('Open', 'Pending', 'Under Investigation')");
            $stmt->execute([$officerId, $officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pendingTasks = $result ? $result['pending'] : 0;
            
        } elseif ($caseFilesTableExists) {
            $stmt = $db->prepare("SELECT COUNT(*) as total FROM case_files WHERE lead_officer_id = ?");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalCases = $result ? $result['total'] : 0;
            
            $stmt = $db->prepare("SELECT COUNT(*) as active FROM case_files WHERE lead_officer_id = ? AND status IN ('Open', 'Active', 'Under Investigation')");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $activeCases = $result ? $result['active'] : 0;
            
            $stmt = $db->prepare("SELECT COUNT(*) as pending FROM case_files WHERE lead_officer_id = ? AND status IN ('Open', 'Pending', 'Under Investigation')");
            $stmt->execute([$officerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pendingTasks = $result ? $result['pending'] : 0;
        }

        // Weekly records
        if ($criminalRecordsExists) {
            $stmt = $db->prepare("SELECT COUNT(*) as weekly FROM criminal_records WHERE created_by = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
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

function getRecentActivity($db, $officerId) {
    try {
        $activities = [];
        
        // Check which tables exist
        $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;
        $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
        $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;

        // Get recent criminal records
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

        // Get recent cases
        if ($casesTableExists) {
            $stmt = $db->prepare("
                SELECT 
                    id,
                    CONCAT('Case: ', case_number) as title,
                    CONCAT('Created case') as description,
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
?>