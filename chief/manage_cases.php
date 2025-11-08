<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Language support
$languages = ['en', 'am', 'om'];
$current_lang = $_SESSION['lang'] ?? 'en';
if (isset($_POST['lang']) && in_array($_POST['lang'], $languages)) {
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$translations = [
    'en' => [
        'page_title' => 'Manage Cases - Mattu City Criminal Management System',
        'header_title' => 'Manage Cases',
        'header_subtitle' => 'Central hub for viewing, filtering, and managing ongoing investigations',
        'chief_view_badge' => 'Chief View - All Cases',
        'officer_view_badge' => 'Officer View - My Cases Only',
        'btn_create_case' => 'Create New Case',
        'btn_dashboard' => 'Dashboard',
        'filters_title' => 'Filters & Search',
        'btn_reset' => 'Reset',
        'label_status' => 'Status',
        'option_all_statuses' => 'All Statuses',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_in_court' => 'In Court',
        'status_closed' => 'Closed',
        'status_suspended' => 'Suspended',
        'label_lead_officer' => 'Lead Officer',
        'option_all_officers' => 'All Officers',
        'label_search' => 'Search',
        'placeholder_search' => 'Case number, suspect name, or case type...',
        'table_title' => 'Case Files',
        'table_header_case_number' => 'Case Number',
        'table_header_status' => 'Status',
        'table_header_case_type' => 'Case Type',
        'table_header_date_reported' => 'Date Reported',
        'table_header_created_by' => 'Created By',
        'table_header_lead_officer' => 'Lead Officer',
        'table_header_primary_suspect' => 'Primary Suspect',
        'table_header_actions' => 'Actions',
        'text_other_officer' => '(Other Officer)',
        'text_unassigned' => 'Unassigned',
        'text_no_suspect' => 'No suspect linked',
        'action_view_details' => 'View Details',
        'action_edit_case' => 'Edit Case',
        'action_add_evidence' => 'Add Evidence',
        'action_update_status' => 'Update Status',
        'action_reassign_case' => 'Reassign Case',
        'pagination_previous' => 'Previous',
        'pagination_next' => 'Next',
        'empty_state_title' => 'No Cases Found',
        'empty_state_subtitle' => 'No cases match your current filters. Try adjusting your search criteria.',
        'empty_state_btn_create' => 'Create New Case',
        'modal_title_update_status' => 'Update Case Status',
        'modal_text_update_for' => 'Update status for',
        'modal_text_current_status' => 'Current status:',
        'status_current' => '(Current)',
        'msg_status_updated' => 'Case status updated to',
        'msg_failed_load_cases' => 'Failed to load cases. Please try again.',
        'msg_failed_update_status' => 'Failed to update case status. Please try again.',
        'results_loading' => 'Loading...',
        'results_count' => 'case',
        'results_plural' => 'cases',
        'pagination_showing' => 'Showing',
        'pagination_of' => 'of',
        'pagination_results' => 'results',
    ],
    'am' => [
        'page_title' => 'ጉዳዮችን ቆጣጠር - ማቱ ከተማ የወንጀል አስተዳደር ስርዓት',
        'header_title' => 'ጉዳዮችን ቆጣጠር',
        'header_subtitle' => 'በመታየት፣ በመጠቀም እና በመቆጣጠር ያለፉ ምርምሮችን መቆጣጠር ማእከል',
        'chief_view_badge' => 'የጂኔራል አመለካከት - ሁሉም ጉዳዮች',
        'officer_view_badge' => 'የባለሙያ አመለካከት - የኔ ጉዳዮች ብቻ',
        'btn_create_case' => 'አዲስ ጉዳይ ይፍጠሩ',
        'btn_dashboard' => 'ዳሽቦርድ',
        'filters_title' => 'አዝራሮች እና ፍለጋ',
        'btn_reset' => 'ዳግም ይጀምሩ',
        'label_status' => 'ሁኔታ',
        'option_all_statuses' => 'ሁሉም ሁኔታዎች',
        'status_open' => 'ክፍት',
        'status_in_progress' => 'በመሥራት ላይ',
        'status_in_court' => 'በፍርድ ቤት',
        'status_closed' => 'ተዘግቧል',
        'status_suspended' => 'ተቋርጧል',
        'label_lead_officer' => 'መሪ ባለሙያ',
        'option_all_officers' => 'ሁሉም ባለሙያዎች',
        'label_search' => 'ፍለጋ',
        'placeholder_search' => 'የጉዳይ ቁጥር፣ የተጠርጣሪ ስም ወይም የጉዳይ አይነት...',
        'table_title' => 'የጉዳይ ፋይሎች',
        'table_header_case_number' => 'የጉዳይ ቁጥር',
        'table_header_status' => 'ሁኔታ',
        'table_header_case_type' => 'የጉዳይ አይነት',
        'table_header_date_reported' => 'የተመዘገበ ቀን',
        'table_header_created_by' => 'የተፈጠረ በ',
        'table_header_lead_officer' => 'መሪ ባለሙያ',
        'table_header_primary_suspect' => 'ዋና ተጠርጣሪ',
        'table_header_actions' => 'ተግባራት',
        'text_other_officer' => '(ሌላ ባለሙያ)',
        'text_unassigned' => 'ያልተሰጠ',
        'text_no_suspect' => 'ተጠርጣሪ የለም',
        'action_view_details' => 'ዝርዝሮችን ይመልከቱ',
        'action_edit_case' => 'ጉዳይ ይማር',
        'action_add_evidence' => 'የተጠቃሚ ማስረጃ ይጨምሩ',
        'action_update_status' => 'ሁኔታ ይዘግቡ',
        'action_reassign_case' => 'ጉዳይ ይቀይሩ',
        'pagination_previous' => 'ቀዳሚ',
        'pagination_next' => 'ቀጣይ',
        'empty_state_title' => 'ጉዳይ አልተገኘም',
        'empty_state_subtitle' => 'ለተጠቀሙት አዝራሮች የሚጻፍ ጉዳይ የለም። የፍለጋዎን መርህ ይለውጡ።',
        'empty_state_btn_create' => 'አዲስ ጉዳይ ይፍጠሩ',
        'modal_title_update_status' => 'የጉዳይ ሁኔታ ይዘግቡ',
        'modal_text_update_for' => 'ለ ... ሁኔታ ይዘግቡ',
        'modal_text_current_status' => 'ወቅታዊ ሁኔታ:',
        'status_current' => '(ወቅታዊ)',
        'msg_status_updated' => 'የጉዳይ ሁኔታ ወደ ... ተዘግቧል',
        'msg_failed_load_cases' => 'ጉዳዮችን ማግኘት አልተሳካም። እባክዎ እንደገና ይሞክሩ።',
        'msg_failed_update_status' => 'የጉዳይ ሁኔታ ማዘጋጀት አልተሳካም። እባክዎ እንደገና ይሞክሩ።',
        'results_loading' => 'በመጫን ላይ...',
        'results_count' => 'ጉዳይ',
        'results_plural' => 'ጉዳዮች',
        'pagination_showing' => 'እየታዩ ሲሄዱ',
        'pagination_of' => 'ከ',
        'pagination_results' => 'ውጤቶች',
    ],
    'om' => [
        'page_title' => 'Caasoota Imaammisi - Mattu City Criminal Management System',
        'header_title' => 'Caasoota Imaammisi',
        'header_subtitle' => 'Gammachuuf, filtrii fi imaammii hojjetoota argamsa',
        'chief_view_badge' => 'Garaa View - All Caasoota',
        'officer_view_badge' => 'Officer View - Kannee Caasoota',
        'btn_create_case' => 'Caasaa Afuufi',
        'btn_dashboard' => 'Dashboardii',
        'filters_title' => 'Filters & Search',
        'btn_reset' => 'Dagaa',
        'label_status' => 'Status',
        'option_all_statuses' => 'All Statuses',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_in_court' => 'In Court',
        'status_closed' => 'Closed',
        'status_suspended' => 'Suspended',
        'label_lead_officer' => 'Lead Officer',
        'option_all_officers' => 'All Officers',
        'label_search' => 'Search',
        'placeholder_search' => 'Case number, suspect name, or case type...',
        'table_title' => 'Case Files',
        'table_header_case_number' => 'Case Number',
        'table_header_status' => 'Status',
        'table_header_case_type' => 'Case Type',
        'table_header_date_reported' => 'Date Reported',
        'table_header_created_by' => 'Created By',
        'table_header_lead_officer' => 'Lead Officer',
        'table_header_primary_suspect' => 'Primary Suspect',
        'table_header_actions' => 'Actions',
        'text_other_officer' => '(Other Officer)',
        'text_unassigned' => 'Unassigned',
        'text_no_suspect' => 'No suspect linked',
        'action_view_details' => 'View Details',
        'action_edit_case' => 'Edit Case',
        'action_add_evidence' => 'Add Evidence',
        'action_update_status' => 'Update Status',
        'action_reassign_case' => 'Reassign Case',
        'pagination_previous' => 'Previous',
        'pagination_next' => 'Next',
        'empty_state_title' => 'No Cases Found',
        'empty_state_subtitle' => 'No cases match your current filters. Try adjusting your search criteria.',
        'empty_state_btn_create' => 'Create New Case',
        'modal_title_update_status' => 'Update Case Status',
        'modal_text_update_for' => 'Update status for',
        'modal_text_current_status' => 'Current status:',
        'status_current' => '(Current)',
        'msg_status_updated' => 'Case status updated to',
        'msg_failed_load_cases' => 'Failed to load cases. Please try again.',
        'msg_failed_update_status' => 'Failed to update case status. Please try again.',
        'results_loading' => 'Loading...',
        'results_count' => 'case',
        'results_plural' => 'cases',
        'pagination_showing' => 'Showing',
        'pagination_of' => 'of',
        'pagination_results' => 'results',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Check if required files exist before including them
$required_files = [
    '../includes/auth.php',
    '../includes/database.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: Required file $file not found. Please ensure all include files are properly set up.");
    }
}

try {
    require_once '../includes/auth.php';
    require_once '../includes/database.php';
} catch (Exception $e) {
    die("Error loading required files: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role check - Must be Investigator, Officer, Admin, or Chief
if (!function_exists('requireRole')) {
    // Basic session-based role check
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['investigator', 'officer', 'admin', 'chief'])) {
        die("Access denied. Investigator, Officer, Admin, or Chief role required.");
    }
} else {
    // Use the requireRole function if it exists
    requireRole(['investigator', 'officer', 'admin', 'chief']);
}

// Check if user is chief
$is_chief = ($_SESSION['role'] === 'chief' || $_SESSION['role'] === 'admin');
$current_user_id = $_SESSION['user_id'];

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        switch ($_GET['action']) {
            case 'fetch':
                // Get filter parameters
                $status = isset($_GET['status']) ? trim($_GET['status']) : '';
                $lead_officer_id = isset($_GET['lead_officer_id']) ? intval($_GET['lead_officer_id']) : 0;
                $search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $sort_by = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'date_reported';
                $sort_order = isset($_GET['sort_order']) ? trim($_GET['sort_order']) : 'DESC';
                $per_page = 10;
                
                // Check which table exists
                $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
                $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
                
                if (!$casesTableExists && !$caseFilesTableExists) {
                    // If no cases table exists, return empty results
                    echo json_encode([
                        'success' => true,
                        'data' => [],
                        'total_records' => 0,
                        'total_pages' => 0,
                        'current_page' => $page,
                        'per_page' => $per_page
                    ]);
                    break;
                }
                
                $tableName = $casesTableExists ? 'cases' : 'case_files';
                
                // Build base query - Different for chiefs vs officers
                if ($is_chief) {
                    // Chief can see all cases
                    $query = "SELECT * FROM $tableName WHERE 1=1";
                    $params = [];
                } else {
                    // Officer can only see their own cases
                    $query = "SELECT * FROM $tableName WHERE created_by = ?";
                    $params = [$current_user_id];
                }
                
                // Apply filters
                if (!empty($status)) {
                    $query .= " AND status = ?";
                    $params[] = $status;
                }
                
                if ($lead_officer_id > 0) {
                    $query .= " AND (lead_officer_id = ? OR assigned_officer_id = ? OR created_by = ?)";
                    $params[] = $lead_officer_id;
                    $params[] = $lead_officer_id;
                    $params[] = $lead_officer_id;
                }
                
                if (!empty($search_query)) {
                    $query .= " AND (
                        case_number LIKE ? OR 
                        case_type LIKE ? OR 
                        description LIKE ? OR
                        location LIKE ?
                    )";
                    $searchParam = "%$search_query%";
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                    $params[] = $searchParam;
                }
                
                // Get total count
                $countQuery = "SELECT COUNT(*) as total FROM ($query) as filtered_cases";
                $countStmt = $db->prepare($countQuery);
                $countStmt->execute($params);
                $totalResult = $countStmt->fetch(PDO::FETCH_ASSOC);
                $total_records = $totalResult['total'];
                
                // Add sorting and pagination
                $validSortColumns = ['case_number', 'status', 'case_type', 'date_reported', 'created_at'];
                $sort_by = in_array($sort_by, $validSortColumns) ? $sort_by : 'date_reported';
                $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
                
                $query .= " ORDER BY $sort_by $sort_order LIMIT ? OFFSET ?";
                $params[] = $per_page;
                $params[] = ($page - 1) * $per_page;
                
                // Execute main query
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Format the data
                $formatted_cases = [];
                foreach ($cases as $case) {
                    $formatted_cases[] = [
                        'case_id' => $case['id'] ?? $case['case_id'] ?? 0,
                        'case_number' => $case['case_number'] ?? 'CASE-' . ($case['id'] ?? 'UNKNOWN'),
                        'status' => $case['status'] ?? 'Open',
                        'case_type' => $case['case_type'] ?? 'General',
                        'date_reported' => $case['date_reported'] ?? $case['created_at'] ?? date('Y-m-d'),
                        'location' => $case['location'] ?? 'Not specified',
                        'description' => $case['description'] ?? 'No description available',
                        'lead_officer_name' => 'Officer',
                        'lead_officer_badge' => '',
                        'suspect_name' => 'Not linked',
                        'suspect_national_id' => null,
                        'created_by' => $case['created_by'] ?? null,
                        'is_owner' => ($case['created_by'] ?? null) == $current_user_id
                    ];
                }
                
                $total_pages = ceil($total_records / $per_page);
                
                echo json_encode([
                    'success' => true,
                    'data' => $formatted_cases,
                    'total_records' => $total_records,
                    'total_pages' => $total_pages,
                    'current_page' => $page,
                    'per_page' => $per_page,
                    'is_chief' => $is_chief
                ]);
                break;
                
            case 'get_officers':
                // Get real officers list from database for chiefs
                if ($is_chief) {
                    try {
                        $usersTableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
                        if ($usersTableExists) {
                            $stmt = $db->query("SELECT id, first_name, last_name, badge_number FROM users WHERE role IN ('officer', 'investigator') ORDER BY first_name, last_name");
                            $officers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } else {
                            $officers = [];
                        }
                    } catch (Exception $e) {
                        $officers = [];
                    }
                } else {
                    // For officers, only show themselves
                    $officers = [
                        ['id' => $current_user_id, 'first_name' => $_SESSION['first_name'], 'last_name' => $_SESSION['last_name'], 'badge_number' => 'B001']
                    ];
                }
                
                // Format officers data
                $formatted_officers = [];
                foreach ($officers as $officer) {
                    $formatted_officers[] = [
                        'id' => $officer['id'],
                        'full_name' => $officer['first_name'] . ' ' . $officer['last_name'],
                        'badge_number' => $officer['badge_number'] ?? 'N/A'
                    ];
                }
                
                echo json_encode([
                    'success' => true,
                    'officers' => $formatted_officers,
                    'is_chief' => $is_chief
                ]);
                break;
                
            case 'update_status':
                // Handle status update
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception("Invalid request method");
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                $case_id = isset($input['case_id']) ? intval($input['case_id']) : 0;
                $new_status = isset($input['status']) ? trim($input['status']) : '';
                
                if ($case_id <= 0) {
                    throw new Exception("Invalid case ID");
                }
                
                $allowed_statuses = ['Open', 'In Progress', 'In Court', 'Closed', 'Suspended'];
                if (!in_array($new_status, $allowed_statuses)) {
                    throw new Exception("Invalid status value");
                }
                
                // Check which table exists
                $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
                $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
                $tableName = $casesTableExists ? 'cases' : 'case_files';
                
                // Check if user has permission to update this case
                if (!$is_chief) {
                    $checkStmt = $db->prepare("SELECT created_by FROM $tableName WHERE id = ?");
                    $checkStmt->execute([$case_id]);
                    $case = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$case || $case['created_by'] != $current_user_id) {
                        throw new Exception("You can only update cases you created");
                    }
                }
                
                // Update case status
                $updateQuery = "UPDATE $tableName SET status = ? WHERE id = ?";
                
                $stmt = $db->prepare($updateQuery);
                $result = $stmt->execute([$new_status, $case_id]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Case status updated successfully'
                    ]);
                } else {
                    throw new Exception("Failed to update case status");
                }
                break;
                
            default:
                throw new Exception("Invalid action");
        }
        
    } catch (Exception $e) {
        error_log("Manage Cases API Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit();
}

// Get current user info for display
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id'],
    'is_chief' => $is_chief
];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('page_title'); ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Google Fonts for Amharic support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am'): ?>
    <style>
        body {
            font-family: 'Noto Sans Ethiopic', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
    </style>
    <?php endif; ?>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            line-height: 1.6;
        }
        
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .dashboard-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .dashboard-title {
            flex: 1;
            min-width: 300px;
        }
        
        .dashboard-title h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .dashboard-title p {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .dashboard-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            cursor: pointer;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .filters-section {
            background: rgba(248, 249, 250, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
        }
        
        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filters-header h5 {
            color: #495057;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: #f8f9ff;
        }
        
        .search-group {
            position: relative;
        }
        
        .search-input {
            padding-right: 45px;
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e9ecef;
        }
        
        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px 25px;
            border-bottom: 2px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            font-weight: 700;
            color: #495057;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .results-count {
            color: #6c757d;
            font-weight: 500;
            font-size: 0.95rem;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .cases-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        
        .cases-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            cursor: pointer;
            user-select: none;
            transition: all 0.3s ease;
        }
        
        .cases-table th:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .cases-table th.sortable::after {
            content: '\f0dc';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.5;
            font-size: 0.8rem;
        }
        
        .cases-table th.sort-asc::after {
            content: '\f0de';
            opacity: 1;
        }
        
        .cases-table th.sort-desc::after {
            content: '\f0dd';
            opacity: 1;
        }
        
        .cases-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        
        .cases-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .cases-table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .case-number-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .case-number-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .status-open {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .status-in-progress {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #212529;
        }
        
        .status-in-court {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }
        
        .status-closed {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
        }
        
        .status-suspended {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .status-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .actions-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .actions-btn {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .actions-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }
        
        .actions-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1000;
            min-width: 180px;
            display: none;
        }
        
        .actions-menu.show {
            display: block;
        }
        
        .actions-menu a {
            display: block;
            padding: 10px 15px;
            color: #495057;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .actions-menu a:last-child {
            border-bottom: none;
        }
        
        .actions-menu a:hover {
            background: #f8f9fa;
            color: #667eea;
            padding-left: 20px;
        }
        
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        
        .pagination-info {
            color: #6c757d;
            font-weight: 500;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        
        .page-btn {
            background: white;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .page-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
            color: #495057;
            text-decoration: none;
        }
        
        .page-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            border-radius: 15px;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e9ecef;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h4 {
            margin-bottom: 10px;
            color: #495057;
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            position: relative;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .btn-close {
            position: absolute;
            top: 10px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .dashboard-container {
                margin: 10px;
                padding: 25px;
            }
            
            .dashboard-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .dashboard-title {
                min-width: auto;
            }
            
            .dashboard-title h3 {
                font-size: 2rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .cases-table {
                font-size: 0.85rem;
            }
            
            .cases-table th,
            .cases-table td {
                padding: 10px 8px;
            }
            
            .pagination-container {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .dashboard-container {
                padding: 20px;
            }
            
            .table-responsive {
                font-size: 0.8rem;
            }
            
            .cases-table th,
            .cases-table td {
                padding: 8px 6px;
            }
            
            .status-badge {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
            
            .actions-btn {
                padding: 6px 8px;
                font-size: 0.8rem;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            padding: 20px;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .modal-title {
            font-weight: 700;
            color: #495057;
            font-size: 1.3rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }

        .modal-close:hover {
            color: #495057;
        }

        .status-option {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin: 5px 0;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
        }

        .status-option:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .status-option.current {
            border-color: #28a745;
            background: #d4edda;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <div class="dashboard-container">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="dashboard-title">
                    <h3><i class="fas fa-tasks me-3"></i><?php echo t('header_title'); ?></h3>
                    <p><?php echo t('header_subtitle'); ?>
                    <?php if ($is_chief): ?>
                        <br><span class="badge bg-warning text-dark mt-1"><i class="fas fa-shield-alt me-1"></i><?php echo t('chief_view_badge'); ?></span>
                    <?php else: ?>
                        <br><span class="badge bg-info mt-1"><i class="fas fa-user me-1"></i><?php echo t('officer_view_badge'); ?></span>
                    <?php endif; ?>
                    </p>
                </div>
                <div class="dashboard-actions">
                    <a href="create_case.php" class="btn-primary-custom">
                        <i class="fas fa-plus me-2"></i><?php echo t('btn_create_case'); ?>
                    </a>
                    <a href="dashboard.php" class="btn-primary-custom" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                        <i class="fas fa-tachometer-alt me-2"></i><?php echo t('btn_dashboard'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <h5><i class="fas fa-filter"></i><?php echo t('filters_title'); ?></h5>
                    <button type="button" class="btn-reset" id="resetFiltersBtn">
                        <i class="fas fa-undo me-1"></i><?php echo t('btn_reset'); ?>
                    </button>
                </div>
                
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('label_status'); ?></label>
                        <select class="form-control-custom" id="statusFilter">
                            <option value=""><?php echo t('option_all_statuses'); ?></option>
                            <option value="Open"><?php echo t('status_open'); ?></option>
                            <option value="In Progress"><?php echo t('status_in_progress'); ?></option>
                            <option value="In Court"><?php echo t('status_in_court'); ?></option>
                            <option value="Closed"><?php echo t('status_closed'); ?></option>
                            <option value="Suspended"><?php echo t('status_suspended'); ?></option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('label_lead_officer'); ?></label>
                        <select class="form-control-custom" id="officerFilter">
                            <option value=""><?php echo t('option_all_officers'); ?></option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('label_search'); ?></label>
                        <div class="search-group">
                            <input type="text" class="form-control-custom search-input" id="searchInput" placeholder="<?php echo t('placeholder_search'); ?>">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cases Table Container -->
            <div class="table-container" id="tableContainer">
                <div class="table-header">
                    <div class="table-title">
                        <i class="fas fa-list"></i>
                        <?php echo t('table_title'); ?>
                    </div>
                    <div class="results-count" id="resultsCount">
                        <?php echo t('results_loading'); ?>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <div id="casesTableContainer">
                        <!-- Table will be populated via AJAX -->
                        <div class="loading-overlay">
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </div>
                
                <div class="pagination-container" id="paginationContainer">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link pagination-prev" href="#" aria-label="Previous">
                    <i class="fas fa-chevron-left"></i>
                    <span><?php echo t('pagination_previous'); ?></span>
                </a>
            </li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item active"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link pagination-next" href="#" aria-label="Next">
                    <span><?php echo t('pagination_next'); ?></span>
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.pagination-container {
    margin: 2rem 0;
}

.pagination {
    gap: 8px;
}

.page-link {
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    color: #495057;
    background: #f8f9fa;
}

.page-link:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.pagination-prev, .pagination-next {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
}

.pagination-prev:hover, .pagination-next:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
}
</style>
            </div>
            
            <!-- Status Message Container -->
            <div id="statusMessage"></div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo t('modal_title_update_status'); ?></h3>
                <button type="button" class="modal-close" onclick="closeStatusModal()">&times;</button>
            </div>
            <div id="statusModalContent">
                <!-- Modal content will be populated dynamically -->
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global variables
            let currentFilters = {
                status: '',
                lead_officer_id: '',
                search_query: '',
                page: 1,
                sort_by: 'date_reported',
                sort_order: 'DESC'
            };
            
            let currentCaseForStatusUpdate = null;
            let isChief = false; // Will be set from server response
            
            // DOM elements
            const statusFilter = document.getElementById('statusFilter');
            const officerFilter = document.getElementById('officerFilter');
            const searchInput = document.getElementById('searchInput');
            const resetFiltersBtn = document.getElementById('resetFiltersBtn');
            const casesTableContainer = document.getElementById('casesTableContainer');
            const paginationContainer = document.getElementById('paginationContainer');
            const resultsCount = document.getElementById('resultsCount');
            const statusMessage = document.getElementById('statusMessage');
            const statusModal = document.getElementById('statusModal');
            const statusModalContent = document.getElementById('statusModalContent');
            
            // Initialize dashboard
            init();
            
            function init() {
                loadOfficers();
                loadCases();
                setupEventListeners();
            }
            
            // Setup event listeners
            function setupEventListeners() {
                // Filter change events
                statusFilter.addEventListener('change', function() {
                    currentFilters.status = this.value;
                    currentFilters.page = 1;
                    loadCases();
                });
                
                officerFilter.addEventListener('change', function() {
                    currentFilters.lead_officer_id = this.value;
                    currentFilters.page = 1;
                    loadCases();
                });
                
                // Search input with debounce
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        currentFilters.search_query = this.value.trim();
                        currentFilters.page = 1;
                        loadCases();
                    }, 500);
                });
                
                // Reset filters
                resetFiltersBtn.addEventListener('click', function() {
                    statusFilter.value = '';
                    officerFilter.value = '';
                    searchInput.value = '';
                    
                    currentFilters = {
                        status: '',
                        lead_officer_id: '',
                        search_query: '',
                        page: 1,
                        sort_by: 'date_reported',
                        sort_order: 'DESC'
                    };
                    
                    loadCases();
                });
                
                // Close dropdowns when clicking outside
                document.addEventListener('click', function(e) {
                    const dropdowns = document.querySelectorAll('.actions-menu');
                    dropdowns.forEach(dropdown => {
                        if (!dropdown.parentElement.contains(e.target)) {
                            dropdown.classList.remove('show');
                        }
                    });
                });

                // Close modal when clicking outside
                statusModal.addEventListener('click', function(e) {
                    if (e.target === statusModal) {
                        closeStatusModal();
                    }
                });
            }
            
            // Load officers for filter dropdown
            function loadOfficers() {
                fetch('?action=get_officers')
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            officerFilter.innerHTML = '<option value=""><?php echo t('option_all_officers'); ?></option>';
                            
                            // If chief, show all officers; if officer, show only themselves
                            if (result.is_chief && result.officers.length > 0) {
                                result.officers.forEach(officer => {
                                    const option = document.createElement('option');
                                    option.value = officer.id;
                                    option.textContent = `${officer.full_name} (${officer.badge_number})`;
                                    officerFilter.appendChild(option);
                                });
                            } else if (!result.is_chief && result.officers.length > 0) {
                                // Officer can only filter by themselves
                                const officer = result.officers[0];
                                const option = document.createElement('option');
                                option.value = officer.id;
                                option.textContent = `${officer.full_name} (${officer.badge_number})`;
                                officerFilter.appendChild(option);
                            }
                            
                            isChief = result.is_chief || false;
                            updateUIForRole();
                        }
                    })
                    .catch(error => {
                        console.error('Error loading officers:', error);
                    });
            }
            
            // Load cases function
            function loadCases() {
                showLoading(true);
                
                const params = new URLSearchParams({
                    action: 'fetch',
                    ...currentFilters
                });
                
                fetch(`?${params.toString()}`)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            isChief = result.is_chief || false;
                            renderTable(result.data);
                            renderPagination(result);
                            updateResultsCount(result);
                            
                            // Update UI based on role
                            updateUIForRole();
                        } else {
                            showMessage(result.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading cases:', error);
                        showMessage('<?php echo addslashes(t("msg_failed_load_cases")); ?>', 'danger');
                    })
                    .finally(() => {
                        showLoading(false);
                    });
            }
            
            // Update UI based on user role
            function updateUIForRole() {
                const officerFilterGroup = document.querySelector('.filter-group:nth-child(2)');
                
                if (isChief) {
                    // Chief can see and use officer filter
                    officerFilterGroup.style.display = 'block';
                    officerFilter.disabled = false;
                } else {
                    // Officer cannot filter by other officers
                    officerFilterGroup.style.display = 'block';
                    officerFilter.disabled = true;
                }
            }
            
            // Update the table rendering to show ownership
            function renderTable(cases) {
                if (cases.length === 0) {
                    casesTableContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h4><?php echo t('empty_state_title'); ?></h4>
                            <p><?php echo t('empty_state_subtitle'); ?></p>
                            <div class="mt-3">
                                <a href="create_case.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i><?php echo t('empty_state_btn_create'); ?>
                                </a>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                const tableHTML = `
                    <table class="cases-table">
                        <thead>
                            <tr>
                                <th class="sortable" data-sort="case_number">
                                    <?php echo t('table_header_case_number'); ?>
                                </th>
                                <th class="sortable" data-sort="status">
                                    <?php echo t('table_header_status'); ?>
                                </th>
                                <th class="sortable" data-sort="case_type">
                                    <?php echo t('table_header_case_type'); ?>
                                </th>
                                <th class="sortable" data-sort="date_reported">
                                    <?php echo t('table_header_date_reported'); ?>
                                </th>
                                ${isChief ? '<th><?php echo t('table_header_created_by'); ?></th>' : ''}
                                <th class="sortable" data-sort="lead_officer_name">
                                    <?php echo t('table_header_lead_officer'); ?>
                                </th>
                                <th class="sortable" data-sort="suspect_name">
                                    <?php echo t('table_header_primary_suspect'); ?>
                                </th>
                                <th><?php echo t('table_header_actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cases.map(caseItem => `
                                <tr>
                                    <td>
                                        <a href="view_criminal_record.php?id=${caseItem.case_id}" class="case-number-link">
                                            ${caseItem.case_number}
                                            ${!caseItem.is_owner && isChief ? '<br><small class="text-muted"><?php echo t('text_other_officer'); ?></small>' : ''}
                                        </a>
                                    </td>
                                    <td>
                                        <button class="status-badge status-${caseItem.status.toLowerCase().replace(' ', '-')}" 
                                                onclick="showStatusModal(${caseItem.case_id}, '${caseItem.status}', '${caseItem.case_number}')">
                                            ${caseItem.status}
                                        </button>
                                    </td>
                                    <td>${caseItem.case_type}</td>
                                    <td>${formatDate(caseItem.date_reported)}</td>
                                    ${isChief ? `<td>${caseItem.is_owner ? 'You' : '<?php echo t('text_other_officer'); ?>'}</td>` : ''}
                                    <td>
                                        ${caseItem.lead_officer_name || '<?php echo t('text_unassigned'); ?>'}
                                        ${caseItem.lead_officer_badge ? `<br><small class="text-muted">${caseItem.lead_officer_badge}</small>` : ''}
                                    </td>
                                    <td>
                                        ${caseItem.suspect_name || '<?php echo t('text_no_suspect'); ?>'}
                                        ${caseItem.suspect_national_id ? `<br><small class="text-muted">${caseItem.suspect_national_id}</small>` : ''}
                                    </td>
                                    <td>
                                        <div class="actions-dropdown">
                                            <button class="actions-btn" onclick="toggleActionsMenu(this)">
                                                <i class="fas fa-ellipsis-v"></i>
                                                Actions
                                            </button>
                                            <div class="actions-menu">
                                                <a href="view_criminal_record.php?id=${caseItem.case_id}">
                                                    <i class="fas fa-eye me-2"></i><?php echo t('action_view_details'); ?>
                                                </a>
                                                ${caseItem.is_owner || isChief ? `
                                                    <a href="edit_case.php?id=${caseItem.case_id}">
                                                        <i class="fas fa-edit me-2"></i><?php echo t('action_edit_case'); ?>
                                                    </a>
                                                ` : ''}
                                                <a href="add_evidence.php?case_id=${caseItem.case_id}">
                                                    <i class="fas fa-plus-circle me-2"></i><?php echo t('action_add_evidence'); ?>
                                                </a>
                                                ${caseItem.is_owner || isChief ? `
                                                    <a href="#" onclick="showStatusModal(${caseItem.case_id}, '${caseItem.status}', '${caseItem.case_number}'); return false;">
                                                        <i class="fas fa-sync me-2"></i><?php echo t('action_update_status'); ?>
                                                    </a>
                                                ` : ''}
                                                ${isChief && !caseItem.is_owner ? `
                                                    <a href="reassign_case.php?id=${caseItem.case_id}">
                                                        <i class="fas fa-exchange-alt me-2"></i><?php echo t('action_reassign_case'); ?>
                                                    </a>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
                
                casesTableContainer.innerHTML = tableHTML;
                
                // Add sort event listeners
                const sortableHeaders = casesTableContainer.querySelectorAll('.sortable');
                sortableHeaders.forEach(header => {
                    header.addEventListener('click', function() {
                        const sortField = this.dataset.sort;
                        
                        // Toggle sort order if same field
                        if (currentFilters.sort_by === sortField) {
                            currentFilters.sort_order = currentFilters.sort_order === 'ASC' ? 'DESC' : 'ASC';
                        } else {
                            currentFilters.sort_by = sortField;
                            currentFilters.sort_order = 'ASC';
                        }
                        
                        currentFilters.page = 1;
                        loadCases();
                    });
                    
                    // Update header appearance
                    if (header.dataset.sort === currentFilters.sort_by) {
                        header.classList.add(currentFilters.sort_order === 'ASC' ? 'sort-asc' : 'sort-desc');
                    }
                });
            }
            
            // Render pagination
            function renderPagination(result) {
                const { current_page, total_pages, total_records, per_page } = result;
                
                if (total_pages <= 1) {
                    paginationContainer.innerHTML = `
                        <div class="pagination-info">
                            <?php echo t('pagination_showing'); ?> ${total_records} <?php echo t('results_count'); ?>${total_records !== 1 ? '<?php echo t('results_plural'); ?>' : ''}
                        </div>
                    `;
                    return;
                }
                
                const startRecord = ((current_page - 1) * per_page) + 1;
                const endRecord = Math.min(current_page * per_page, total_records);
                
                let paginationHTML = `
                    <div class="pagination-info">
                        <?php echo t('pagination_showing'); ?> ${startRecord}-${endRecord} <?php echo t('pagination_of'); ?> ${total_records} <?php echo t('pagination_results'); ?>
                    </div>
                    <div class="pagination">
                `;
                
                // Previous button
                if (current_page > 1) {
                    paginationHTML += `
                        <button class="page-btn" onclick="changePage(${current_page - 1})">
                            <i class="fas fa-chevron-left"></i>
                            <?php echo t('pagination_previous'); ?>
                        </button>
                    `;
                }
                
                // Page numbers
                const startPage = Math.max(1, current_page - 2);
                const endPage = Math.min(total_pages, current_page + 2);
                
                if (startPage > 1) {
                    paginationHTML += `<button class="page-btn" onclick="changePage(1)">1</button>`;
                    if (startPage > 2) {
                        paginationHTML += `<span class="page-btn" style="cursor: default;">...</span>`;
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    paginationHTML += `
                        <button class="page-btn ${i === current_page ? 'active' : ''}" onclick="changePage(${i})">
                            ${i}
                        </button>
                    `;
                }
                
                if (endPage < total_pages) {
                    if (endPage < total_pages - 1) {
                        paginationHTML += `<span class="page-btn" style="cursor: default;">...</span>`;
                    }
                    paginationHTML += `<button class="page-btn" onclick="changePage(${total_pages})">${total_pages}</button>`;
                }
                
                // Next button
                if (current_page < total_pages) {
                    paginationHTML += `
                        <button class="page-btn" onclick="changePage(${current_page + 1})">
                            <?php echo t('pagination_next'); ?>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    `;
                }
                
                paginationHTML += `</div>`;
                paginationContainer.innerHTML = paginationHTML;
            }
            
            // Update results count
            function updateResultsCount(result) {
                const { total_records } = result;
                resultsCount.textContent = `${total_records} <?php echo t('results_count'); ?>${total_records !== 1 ? '<?php echo t('results_plural'); ?>' : ''} <?php echo t('results_count'); ?>`;
            }
            
            // Change page function
            window.changePage = function(page) {
                currentFilters.page = page;
                loadCases();
            };
            
            // Toggle actions menu
            window.toggleActionsMenu = function(button) {
                const menu = button.nextElementSibling;
                const isVisible = menu.classList.contains('show');
                
                // Close all other menus
                document.querySelectorAll('.actions-menu').forEach(m => m.classList.remove('show'));
                
                // Toggle current menu
                if (!isVisible) {
                    menu.classList.add('show');
                }
            };
            
            // Show status update modal
            window.showStatusModal = function(caseId, currentStatus, caseNumber) {
                currentCaseForStatusUpdate = { caseId, currentStatus, caseNumber };
                
                const statuses = ['Open', 'In Progress', 'In Court', 'Closed', 'Suspended'];
                
                let modalHTML = `
                    <p><?php echo t('modal_text_update_for'); ?> <strong>${caseNumber}</strong></p>
                    <p><?php echo t('modal_text_current_status'); ?> <span class="status-badge status-${currentStatus.toLowerCase().replace(' ', '-')}">${currentStatus}</span></p>
                    <div class="mt-3">
                `;
                
                statuses.forEach(status => {
                    const isCurrent = status === currentStatus;
                    modalHTML += `
                        <button type="button" class="status-option ${isCurrent ? 'current' : ''}" 
                                onclick="updateCaseStatus('${status}')" ${isCurrent ? 'disabled' : ''}>
                            <i class="fas fa-${isCurrent ? 'check' : 'circle'} me-2"></i>
                            ${status} ${isCurrent ? '<?php echo t('status_current'); ?>' : ''}
                        </button>
                    `;
                });
                
                modalHTML += `</div>`;
                
                statusModalContent.innerHTML = modalHTML;
                statusModal.style.display = 'flex';
            };
            
            // Close status modal
            window.closeStatusModal = function() {
                statusModal.style.display = 'none';
                currentCaseForStatusUpdate = null;
            };
            
            // Update case status
            window.updateCaseStatus = function(newStatus) {
                if (!currentCaseForStatusUpdate) return;
                
                const { caseId, caseNumber } = currentCaseForStatusUpdate;
                
                fetch('?action=update_status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        case_id: caseId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showMessage(`<?php echo t('msg_status_updated'); ?> ${caseNumber} <?php echo t('msg_status_updated'); ?> ${newStatus}`, 'success');
                        closeStatusModal();
                        loadCases(); // Reload to show updated status
                    } else {
                        showMessage(result.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    showMessage('<?php echo addslashes(t("msg_failed_update_status")); ?>', 'danger');
                });
            };
            
            // Show loading state
            function showLoading(show) {
                if (show) {
                    casesTableContainer.innerHTML = `
                        <div class="loading-overlay">
                            <div class="loading-spinner"></div>
                        </div>
                    `;
                }
            }
            
            // Show status message
            function showMessage(message, type) {
                statusMessage.innerHTML = `
                    <div class="alert-custom alert-${type}" role="alert">
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                `;
                
                // Auto-hide success messages
                if (type === 'success') {
                    setTimeout(() => {
                        const alert = statusMessage.querySelector('.alert-custom');
                        if (alert) {
                            alert.remove();
                        }
                    }, 5000);
                }
                
                // Scroll to message
                statusMessage.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Format date helper
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            }
        });
    </script>
</body>
</html>