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
        'title' => 'Manage Cases - Mattu City Criminal Management System',
        'manage_cases' => 'Manage Cases',
        'central_hub' => 'Central hub for viewing, filtering, and managing ongoing investigations',
        'create_new_case' => 'Create New Case',
        'dashboard' => 'Dashboard',
        'filters_search' => 'Filters & Search',
        'reset' => 'Reset',
        'status' => 'Status',
        'all_statuses' => 'All Statuses',
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'in_court' => 'In Court',
        'closed' => 'Closed',
        'suspended' => 'Suspended',
        'lead_officer' => 'Lead Officer',
        'all_officers' => 'All Officers',
        'search_placeholder' => 'Case number, suspect name, or case type...',
        'case_files' => 'Case Files',
        'loading' => 'Loading...',
        'no_cases_found' => 'No Cases Found',
        'no_cases_match' => 'No cases match your current filters. Try adjusting your search criteria.',
        'case_number' => 'Case Number',
        'status_header' => 'Status',
        'case_type' => 'Case Type',
        'date_reported' => 'Date Reported',
        'lead_officer_name' => 'Lead Officer',
        'primary_suspect' => 'Primary Suspect',
        'actions' => 'Actions',
        'view_details' => 'View Details',
        'edit_case' => 'Edit Case',
        'add_evidence' => 'Add Evidence',
        'update_status' => 'Update Status',
        'showing_results' => 'Showing',
        'of' => 'of',
        'results' => 'results',
        'previous' => 'Previous',
        'next' => 'Next',
        'update_case_status' => 'Update Case Status',
        'current_status' => 'Current status',
        'case_status_updated' => 'Case status updated successfully',
        'failed_update_status' => 'Failed to update case status',
        'failed_load_cases' => 'Failed to load cases. Please try again.',
        'access_denied' => 'Access denied. Investigator, Officer, or Admin role required.',
        'invalid_request_method' => 'Invalid request method',
        'invalid_case_id' => 'Invalid case ID',
        'invalid_status_value' => 'Invalid status value',
        'failed_update_case_status' => 'Failed to update case status',
        'invalid_action' => 'Invalid action',
    ],
    'am' => [
        'title' => 'ጉዳዮችን አስተዳደር - ማቱ ከተማ የወደንጀል አስተዳደር ስርዓት',
        'manage_cases' => 'ጉዳዮችን አስተዳደር',
        'central_hub' => 'የተጀምረውን ምርመራዎች ለመመልከት፣ ለመጥለቅ እና ለመቆጣጠር መሃል',
        'create_new_case' => 'አዲስ ጉዳይ ፍጠር',
        'dashboard' => 'ጃሽባር',
        'filters_search' => 'መጥለቂያዎች እና ፍለጋ',
        'reset' => 'ዳግም ጀምር',
        'status' => 'ሁኔታ',
        'all_statuses' => 'ሁሉም ሁኔታዎች',
        'open' => 'ክፍት',
        'in_progress' => 'በመረጃ',
        'in_court' => 'በፍርድ ቤት',
        'closed' => 'ተዘግቧል',
        'suspended' => 'ተቋርጧል',
        'lead_officer' => 'መሪ መኮንን',
        'all_officers' => 'ሁሉም መኮንኖች',
        'search_placeholder' => 'የጉዳይ ቁጥር፣ የተጠርጣሪ ስም ወይም የጉዳይ አይነት...',
        'case_files' => 'የጉዳይ ፋይሎች',
        'loading' => 'በመጫን...',
        'no_cases_found' => 'ጉዳይ አልተገኘም',
        'no_cases_match' => 'በአሁኑ መጥለቂያዎች ያላቸው ጉዳዮች የሉም። የፍለጋዎን መርዛማ ይሞክሩ።',
        'case_number' => 'የጉዳይ ቁጥር',
        'status_header' => 'ሁኔታ',
        'case_type' => 'የጉዳይ አይነት',
        'date_reported' => 'የተመረጠ ቀን',
        'lead_officer_name' => 'መሪ መኮንን',
        'primary_suspect' => 'ዋና ተጠርጣሪ',
        'actions' => 'እርምጃዎች',
        'view_details' => 'ዝርዝር ይመልከቱ',
        'edit_case' => 'ጉዳይ አርትዕ',
        'add_evidence' => 'ውህደት ጨምር',
        'update_status' => 'ሁኔታ ይዘውሉ',
        'showing_results' => 'በማሳየው',
        'of' => 'ከ',
        'results' => 'ውጤቶች',
        'previous' => 'ቀደምት',
        'next' => 'ቀጣይ',
        'update_case_status' => 'የጉዳይ ሁኔታ ይዘውሉ',
        'current_status' => 'ዛሬው ሁኔታ',
        'case_status_updated' => 'የጉዳይ ሁኔታ በተሳካ ሁኔታ ተዘዋወረ',
        'failed_update_status' => 'የጉዳይ ሁኔታ ለመዘወር ተሳካ አልተለመደም',
        'failed_load_cases' => 'ጉዳዮችን ለመጫን ተሳካ አልተለመደም። እንደገና ይሞክሩ።',
        'access_denied' => 'መግባት ተከላከለ። ምርመራዊ፣ መኮንን ወይም አስተዳዳሪ ሚና ያስፈልጋል።',
        'invalid_request_method' => 'የጥያቄ ዘዴ ተገፋ',
        'invalid_case_id' => 'የጉዳይ መለያ ተገፋ',
        'invalid_status_value' => 'የሁኔታ እሴት ተገፋ',
        'failed_update_case_status' => 'የጉዳይ ሁኔታ ለመዘወር ተሳካ አልተለመደም',
        'invalid_action' => 'የተገለጸ እርምጃ ተገፋ',
    ],
    'om' => [
        'title' => 'Caasoota Gammachuu - Sisteemi Diinagdee Mattu Kuta',
        'manage_cases' => 'Caasoota Gammachuu',
        'central_hub' => 'Qoricha argachuu, qoricha, fi diinagdee qoricha qoricha',
        'create_new_case' => 'Caasaa Qophaa Argisi',
        'dashboard' => 'Dashiboardii',
        'filters_search' => 'Qoricha fi Qoricha',
        'reset' => 'Deebii',
        'status' => 'Hakkina',
        'all_statuses' => 'Hakkina Hunda',
        'open' => 'Fufaa',
        'in_progress' => 'Deebii Kennuu',
        'in_court' => 'Deebii Biiroo',
        'closed' => 'Deebii',
        'suspended' => 'Deebii Kennuu',
        'lead_officer' => 'Meekoonnin Qoricha',
        'all_officers' => 'Meekoonni Hunda',
        'search_placeholder' => 'Naama caasaa, naama diinagdee, ykn aangoo caasaa...',
        'case_files' => 'Fayiloota Caasaa',
        'loading' => 'Kennuu...',
        'no_cases_found' => 'Caasaa Hin Arganne',
        'no_cases_match' => 'Caasaa qoricha qoricha hin taane. Qoricha qoricha argisi.',
        'case_number' => 'Naama Caasaa',
        'status_header' => 'Hakkina',
        'case_type' => 'Aangoo Caasaa',
        'date_reported' => 'Guyyaa Qoricha',
        'lead_officer_name' => 'Meekoonnin Qoricha',
        'primary_suspect' => 'Diinagdee Qoricha',
        'actions' => 'Qoricha',
        'view_details' => 'Qoricha Argisi',
        'edit_case' => 'Caasaa Editii',
        'add_evidence' => 'Ummata Diinagdee Qabuu',
        'update_status' => 'Hakkina Kennuu',
        'showing_results' => 'Argachuu',
        'of' => 'Keessatti',
        'results' => 'Wojjii',
        'previous' => 'Qoricha',
        'next' => 'Guyyaa',
        'update_case_status' => 'Hakkina Caasaa Kennuu',
        'current_status' => 'Hakkina Qoricha',
        'case_status_updated' => 'Hakkina caasaa kennuu argame',
        'failed_update_status' => 'Hakkina caasaa kennuu sagadduu',
        'failed_load_cases' => 'Caasoota kennuu sagadduu. Eenyummaa argisi.',
        'access_denied' => 'Gammachuu deebii. Meekoonnin, meekoonnin, ykn adminii qoricha.',
        'invalid_request_method' => 'Qoricha qoricha sagadduu',
        'invalid_case_id' => 'ID Caasaa sagadduu',
        'invalid_status_value' => 'Hakkina qoricha sagadduu',
        'failed_update_case_status' => 'Hakkina caasaa kennuu sagadduu',
        'invalid_action' => 'Qoricha sagadduu',
    ],
];
function t($key) {
    global $translations, $current_lang;
    $trans = $translations[$current_lang][$key] ?? $key;
    // Replace placeholders if any
    if (strpos($trans, '{') !== false) {
        $trans = str_replace('{case_number}', $case_number ?? '', $trans);
        $trans = str_replace('{error}', $error_message ?? '', $trans);
    }
    return $trans;
}

// Check if required files exist before including them
$required_files = [
    '../includes/auth.php',
    '../includes/database.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die(t('error_required_file') . " $file " . t('not_found'));
    }
}

try {
    require_once '../includes/auth.php';
    require_once '../includes/database.php';
} catch (Exception $e) {
    die(t('error_loading_files') . ": " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role check - Must be Investigator, Officer, or Admin
if (!function_exists('requireRole')) {
    // Basic session-based role check
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['investigator', 'officer', 'admin'])) {
        die(t('access_denied'));
    }
} else {
    // Use the requireRole function if it exists
    requireRole(['investigator', 'officer', 'admin']);
}

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
                
               // Build base query - FIXED: Removed duplicate created_by
$query = "SELECT * FROM $tableName WHERE 1=1";
$params = [];
                
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
                        'created_by' => $case['created_by'] ?? null
                    ];
                }
                
                $total_pages = ceil($total_records / $per_page);
                
                echo json_encode([
                    'success' => true,
                    'data' => $formatted_cases,
                    'total_records' => $total_records,
                    'total_pages' => $total_pages,
                    'current_page' => $page,
                    'per_page' => $per_page
                ]);
                break;
                
            case 'get_officers':
                // Simplified officers list
                $officers = [
                    ['id' => $_SESSION['user_id'], 'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'], 'badge_number' => 'B001']
                ];
                
                echo json_encode([
                    'success' => true,
                    'officers' => $officers
                ]);
                break;
                
            case 'update_status':
                // Handle status update
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    throw new Exception(t('invalid_request_method'));
                }
                
                $input = json_decode(file_get_contents('php://input'), true);
                $case_id = isset($input['case_id']) ? intval($input['case_id']) : 0;
                $new_status = isset($input['status']) ? trim($input['status']) : '';
                
                if ($case_id <= 0) {
                    throw new Exception(t('invalid_case_id'));
                }
                
                $allowed_statuses = [t('open'), t('in_progress'), t('in_court'), t('closed'), t('suspended')];
                if (!in_array($new_status, $allowed_statuses)) {
                    throw new Exception(t('invalid_status_value'));
                }
                
                // Check which table exists
                $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
                $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
                $tableName = $casesTableExists ? 'cases' : 'case_files';
                
                // Update case status
                $updateQuery = "UPDATE $tableName SET status = ? WHERE id = ?";
                
                $stmt = $db->prepare($updateQuery);
                $result = $stmt->execute([$new_status, $case_id]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => t('case_status_updated')
                    ]);
                } else {
                    throw new Exception(t('failed_update_case_status'));
                }
                break;
                
            default:
                throw new Exception(t('invalid_action'));
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
    'user_id' => $_SESSION['user_id']
];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Google Fonts for Amharic and Oromo support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am' || $current_lang == 'om'): ?>
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
                    <h3><i class="fas fa-tasks me-3"></i><?php echo t('manage_cases'); ?></h3>
                    <p><?php echo t('central_hub'); ?></p>
                </div>
                <div class="dashboard-actions">
                    <a href="create_case.php" class="btn-primary-custom">
                        <i class="fas fa-plus me-2"></i><?php echo t('create_new_case'); ?>
                    </a>
                    <a href="dashboard.php" class="btn-primary-custom" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                        <i class="fas fa-tachometer-alt me-2"></i><?php echo t('dashboard'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filters-header">
                    <h5><i class="fas fa-filter"></i><?php echo t('filters_search'); ?></h5>
                    <button type="button" class="btn-reset" id="resetFiltersBtn">
                        <i class="fas fa-undo me-1"></i><?php echo t('reset'); ?>
                    </button>
                </div>
                
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('status'); ?></label>
                        <select class="form-control-custom" id="statusFilter">
                            <option value=""><?php echo t('all_statuses'); ?></option>
                            <option value="Open"><?php echo t('open'); ?></option>
                            <option value="In Progress"><?php echo t('in_progress'); ?></option>
                            <option value="In Court"><?php echo t('in_court'); ?></option>
                            <option value="Closed"><?php echo t('closed'); ?></option>
                            <option value="Suspended"><?php echo t('suspended'); ?></option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('lead_officer'); ?></label>
                        <select class="form-control-custom" id="officerFilter">
                            <option value=""><?php echo t('all_officers'); ?></option>
                            <!-- Options will be populated via AJAX -->
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label"><?php echo t('search_placeholder'); ?></label>
                        <div class="search-group">
                            <input type="text" class="form-control-custom search-input" id="searchInput" placeholder="<?php echo t('search_placeholder'); ?>">
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
                        <?php echo t('case_files'); ?>
                    </div>
                    <div class="results-count" id="resultsCount">
                        <?php echo t('loading'); ?>
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
                    <!-- Pagination will be populated via AJAX -->
                </div>
            </div>
            
            <!-- Status Message Container -->
            <div id="statusMessage"></div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo t('update_case_status'); ?></h3>
                <button type="button" class="modal-close" onclick="closeStatusModal()">&times;</button>
            </div>
            <div id="statusModalContent">
                <!-- Modal content will be populated dynamically -->
            </div>
        </div>
    </div>
    
    <script>
        // Translations for JS
        const TRANSLATIONS = <?php echo json_encode($translations[$current_lang]); ?>;
    </script>
    
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
                            officerFilter.innerHTML = '<option value="">' + TRANSLATIONS.all_officers + '</option>';
                            result.officers.forEach(officer => {
                                const option = document.createElement('option');
                                option.value = officer.id;
                                option.textContent = `${officer.full_name} (${officer.badge_number})`;
                                officerFilter.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading officers:', error);
                    });
            }
            
            // Main function to load cases
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
                            renderTable(result.data);
                            renderPagination(result);
                            updateResultsCount(result);
                        } else {
                            showMessage(result.message, 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading cases:', error);
                        showMessage(TRANSLATIONS.failed_load_cases, 'danger');
                    })
                    .finally(() => {
                        showLoading(false);
                    });
            }
            
            // Render cases table - FIXED: Complete function
            function renderTable(cases) {
                if (cases.length === 0) {
                    casesTableContainer.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h4>${TRANSLATIONS.no_cases_found}</h4>
                            <p>${TRANSLATIONS.no_cases_match}</p>
                            <div class="mt-3">
                                <a href="create_case.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>${TRANSLATIONS.create_new_case}
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
                                    ${TRANSLATIONS.case_number}
                                </th>
                                <th class="sortable" data-sort="status">
                                    ${TRANSLATIONS.status_header}
                                </th>
                                <th class="sortable" data-sort="case_type">
                                    ${TRANSLATIONS.case_type}
                                </th>
                                <th class="sortable" data-sort="date_reported">
                                    ${TRANSLATIONS.date_reported}
                                </th>
                                <th class="sortable" data-sort="lead_officer_name">
                                    ${TRANSLATIONS.lead_officer_name}
                                </th>
                                <th class="sortable" data-sort="suspect_name">
                                    ${TRANSLATIONS.primary_suspect}
                                </th>
                                <th>${TRANSLATIONS.actions}</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${cases.map(caseItem => `
                                <tr>
                                    <td>
                                        <a href="view_criminal_record.php?id=${caseItem.case_id}" class="case-number-link">
                                            ${caseItem.case_number}
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
                                    <td>
                                        ${caseItem.lead_officer_name || TRANSLATIONS.unassigned}
                                        ${caseItem.lead_officer_badge ? `<br><small class="text-muted">${caseItem.lead_officer_badge}</small>` : ''}
                                    </td>
                                    <td>
                                        ${caseItem.suspect_name || TRANSLATIONS.no_suspect_linked}
                                        ${caseItem.suspect_national_id ? `<br><small class="text-muted">${caseItem.suspect_national_id}</small>` : ''}
                                    </td>
                                    <td>
                                        <div class="actions-dropdown">
                                            <button class="actions-btn" onclick="toggleActionsMenu(this)">
                                                <i class="fas fa-ellipsis-v"></i>
                                                ${TRANSLATIONS.actions}
                                            </button>
                                            <div class="actions-menu">
                                                <a href="view_criminal_record.php?id=${caseItem.case_id}">
                                                    <i class="fas fa-eye me-2"></i>${TRANSLATIONS.view_details}
                                                </a>
                                                ${caseItem.created_by == <?php echo $_SESSION['user_id']; ?> ? `
                                                    <a href="edit_case.php?id=${caseItem.case_id}">
                                                        <i class="fas fa-edit me-2"></i>${TRANSLATIONS.edit_case}
                                                    </a>
                                                ` : ''}
                                                <a href="add_evidence.php?case_id=${caseItem.case_id}">
                                                    <i class="fas fa-plus-circle me-2"></i>${TRANSLATIONS.add_evidence}
                                                </a>
                                                <a href="#" onclick="showStatusModal(${caseItem.case_id}, '${caseItem.status}', '${caseItem.case_number}'); return false;">
                                                    <i class="fas fa-sync me-2"></i>${TRANSLATIONS.update_status}
                                                </a>
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
                            ${TRANSLATIONS.showing_results} ${total_records} ${TRANSLATIONS.results}
                        </div>
                    `;
                    return;
                }
                
                const startRecord = ((current_page - 1) * per_page) + 1;
                const endRecord = Math.min(current_page * per_page, total_records);
                
                let paginationHTML = `
                    <div class="pagination-info">
                        ${TRANSLATIONS.showing_results} ${startRecord}-${endRecord} ${TRANSLATIONS.of} ${total_records} ${TRANSLATIONS.results}
                    </div>
                    <div class="pagination">
                `;
                
                // Previous button
                if (current_page > 1) {
                    paginationHTML += `
                        <button class="page-btn" onclick="changePage(${current_page - 1})">
                            <i class="fas fa-chevron-left"></i>
                            ${TRANSLATIONS.previous}
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
                            ${TRANSLATIONS.next}
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
                resultsCount.textContent = `${total_records} ${TRANSLATIONS.results}`;
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
                
                const statuses = [TRANSLATIONS.open, TRANSLATIONS.in_progress, TRANSLATIONS.in_court, TRANSLATIONS.closed, TRANSLATIONS.suspended];
                
                let modalHTML = `
                    <p>${TRANSLATIONS.update_case_status} <strong>${caseNumber}</strong></p>
                    <p>${TRANSLATIONS.current_status}: <span class="status-badge status-${currentStatus.toLowerCase().replace(' ', '-')}">${currentStatus}</span></p>
                    <div class="mt-3">
                `;
                
                statuses.forEach(status => {
                    const isCurrent = status === currentStatus;
                    modalHTML += `
                        <button type="button" class="status-option ${isCurrent ? 'current' : ''}" 
                                onclick="updateCaseStatus('${status}')" ${isCurrent ? 'disabled' : ''}>
                            <i class="fas fa-${isCurrent ? 'check' : 'circle'} me-2"></i>
                            ${status} ${isCurrent ? '(Current)' : ''}
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
                        showMessage(`${TRANSLATIONS.case_status_updated} ${caseNumber} ${newStatus}`, 'success');
                        closeStatusModal();
                        loadCases(); // Reload to show updated status
                    } else {
                        showMessage(result.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error updating status:', error);
                    showMessage(TRANSLATIONS.failed_update_status, 'danger');
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