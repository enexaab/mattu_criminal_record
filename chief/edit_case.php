<?php
// edit_case.php
require '../includes/database.php';
require '../includes/auth.php';

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
        'title' => 'Edit Case - {case_number} - Mattu City Criminal Management System',
        'header_title' => 'Edit Case',
        'header_subtitle' => 'Update case information for',
        'success_updated' => 'Case updated successfully!',
        'error_required' => 'Please fill in all required fields',
        'error_update_failed' => 'Failed to update case',
        'error_invalid_id' => 'Invalid case ID',
        'error_not_found' => 'Case not found',
        'error_permission' => 'You can only edit cases you created',
        'error_db' => 'Database error',
        'form_case_number' => 'Case Number',
        'form_case_type' => 'Case Type',
        'form_status' => 'Status',
        'form_priority' => 'Priority',
        'form_location' => 'Location',
        'form_date_reported' => 'Date Reported',
        'form_description' => 'Description',
        'form_select_type' => 'Select Case Type',
        'priority_low' => 'Low',
        'priority_medium' => 'Medium',
        'priority_high' => 'High',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_in_court' => 'In Court',
        'status_closed' => 'Closed',
        'status_suspended' => 'Suspended',
        'btn_update_case' => 'Update Case',
        'btn_cancel' => 'Cancel',
        'btn_back_cases' => 'Back to Cases',
        'btn_view_details' => 'View Case Details',
        'info_case_number' => 'Case Number:',
        'info_status' => 'Current Status:',
        'info_priority' => 'Priority:',
        'info_date_created' => 'Date Created:',
        'info_last_updated' => 'Last Updated:',
        'placeholder_location' => 'Where did the incident occur?',
        'placeholder_description' => 'Detailed description of the case...',
        'js_required_fields' => 'Please fill in all required fields marked with *',
        'js_updating' => 'Updating Case...',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'am' => [
        'title' => 'ጉዳይ አስተካክል - {case_number} - ማቱ ከተማ የወንጀል አስተዳደር ስርዓት',
        'header_title' => 'ጉዳይ አስተካክል',
        'header_subtitle' => 'ጉዳይ መረጃ አዘመን',
        'success_updated' => 'ጉዳይ በተሳካ ሁኔታ ተዘመነ!',
        'error_required' => 'ሁሉንም አስፈላጊ ክፍሎች ይሙሉ',
        'error_update_failed' => 'ጉዳይ ማዘመን አልተሳካም',
        'error_invalid_id' => 'የጉዳይ ID የተሳሳተ',
        'error_not_found' => 'ጉዳይ አልተገኘም',
        'error_permission' => 'የተፈጠሩትን ጉዳዮች ብቻ ማስተካከል ትችላለህ',
        'error_db' => 'የውሂብ ቤዝ ስህተት',
        'form_case_number' => 'የጉዳይ ቁጥር',
        'form_case_type' => 'የጉዳይ አይነት',
        'form_status' => 'ሁኔታ',
        'form_priority' => 'ቅደም ተደር',
        'form_location' => 'ቦታ',
        'form_date_reported' => 'የተመዘገበ ቀን',
        'form_description' => 'መግለጫ',
        'form_select_type' => 'የጉዳይ አይነት ይምረጡ',
        'priority_low' => 'ዝቅተኛ',
        'priority_medium' => 'መካከለኛ',
        'priority_high' => 'ከፍተኛ',
        'status_open' => 'ክፍት',
        'status_in_progress' => 'በተጀምረ ሁኔታ',
        'status_in_court' => 'በፍርድ ቤት',
        'status_closed' => 'ተዘግቧል',
        'status_suspended' => 'ተቋርጧል',
        'btn_update_case' => 'ጉዳይ አዘመን',
        'btn_cancel' => 'ተከስ',
        'btn_back_cases' => 'ወደ ጉዳዮች',
        'btn_view_details' => 'የጉዳይ ዝርዝር ይመልከቱ',
        'info_case_number' => 'የጉዳይ ቁጥር:',
        'info_status' => 'አሁኑ ሁኔታ:',
        'info_priority' => 'ቅደም ተደር:',
        'info_date_created' => 'የተፈጠረ ቀን:',
        'info_last_updated' => 'የመጨረሻ ዛሬ:',
        'placeholder_location' => 'ክስተቱ የተከሰተ በምን ላይ?',
        'placeholder_description' => 'የጉዳዩ ዝርዝር መግለጫ...',
        'js_required_fields' => 'ሁሉንም አስፈላጊ ክፍሎች * በተሰጡ መልክ ይሙሉ',
        'js_updating' => 'ጉዳይ ይነዳ...',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'om' => [
        'title' => 'Case Edit - {case_number} - Mattu City Criminal Management System',
        'header_title' => 'Case Edit',
        'header_subtitle' => 'Case information update',
        'success_updated' => 'Case updated successfully!',
        'error_required' => 'All required fields fill',
        'error_update_failed' => 'Case update miti',
        'error_invalid_id' => 'Invalid case ID',
        'error_not_found' => 'Case not found',
        'error_permission' => 'Created cases only edit',
        'error_db' => 'Database error',
        'form_case_number' => 'Case Number',
        'form_case_type' => 'Case Type',
        'form_status' => 'Status',
        'form_priority' => 'Priority',
        'form_location' => 'Location',
        'form_date_reported' => 'Date Reported',
        'form_description' => 'Description',
        'form_select_type' => 'Select Case Type',
        'priority_low' => 'Low',
        'priority_medium' => 'Medium',
        'priority_high' => 'High',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_in_court' => 'In Court',
        'status_closed' => 'Closed',
        'status_suspended' => 'Suspended',
        'btn_update_case' => 'Update Case',
        'btn_cancel' => 'Cancel',
        'btn_back_cases' => 'Back to Cases',
        'btn_view_details' => 'View Case Details',
        'info_case_number' => 'Case Number:',
        'info_status' => 'Current Status:',
        'info_priority' => 'Priority:',
        'info_date_created' => 'Date Created:',
        'info_last_updated' => 'Last Updated:',
        'placeholder_location' => 'Incident occurred where?',
        'placeholder_description' => 'Case detailed description...',
        'js_required_fields' => 'All required fields * marked fill',
        'js_updating' => 'Case updating...',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
];

function t($key, $replacements = []) {
    global $translations, $current_lang;
    $translation = $translations[$current_lang][$key] ?? $key;
    foreach ($replacements as $search => $replace) {
        $translation = str_replace('{' . $search . '}', $replace, $translation);
    }
    return $translation;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if case_id is provided
$case_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($case_id <= 0) {
    header("Location: manage_cases.php?error=" . urlencode(t('error_invalid_id')));
    exit();
}

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get case information and verify ownership
try {
    // Check which table exists
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    $tableName = $casesTableExists ? 'cases' : 'case_files';
    
    // Get case with creator information
    $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
    $stmt->execute([$case_id]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        header("Location: manage_cases.php?error=" . urlencode(t('error_not_found')));
        exit();
    }
    
    // Check if current user is the creator or has admin role
    $is_creator = isset($case['created_by']) && $case['created_by'] == $current_user['user_id'];
    $is_admin = $current_user['role'] === 'admin';
    
    if (!$is_creator && !$is_admin) {
        header("Location: manage_cases.php?error=" . urlencode(t('error_permission')));
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error fetching case: " . $e->getMessage());
    header("Location: manage_cases.php?error=" . urlencode(t('error_db')));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $case_number = $_POST['case_number'] ?? '';
        $case_type = $_POST['case_type'] ?? '';
        $status = $_POST['status'] ?? '';
        $description = $_POST['description'] ?? '';
        $location = $_POST['location'] ?? '';
        $date_reported = $_POST['date_reported'] ?? '';
        $priority = $_POST['priority'] ?? 'medium';
        
        // Validate required fields
        if (empty($case_number) || empty($case_type) || empty($description)) {
            throw new Exception(t('error_required'));
        }
        
        // Update case
        $updateQuery = "
            UPDATE $tableName 
            SET case_number = ?, case_type = ?, status = ?, description = ?, 
                location = ?, date_reported = ?, priority = ?, updated_at = NOW()
            WHERE id = ?
        ";
        
        $stmt = $db->prepare($updateQuery);
        $result = $stmt->execute([
            $case_number,
            $case_type,
            $status,
            $description,
            $location,
            $date_reported,
            $priority,
            $case_id
        ]);
        
        if ($result) {
            // Log activity
            if (function_exists('logOfficerActivity')) {
                logOfficerActivity(
                    $current_user['user_id'],
                    'case_updated',
                    "Updated case {$case_number} (Case ID: $case_id)"
                );
            }
            
            $success_message = t('success_updated');
            
            // Refresh case data
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$case_id]);
            $case = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception(t('error_update_failed'));
        }
        
    } catch (Exception $e) {
        $error_message = "Error updating case: " . $e->getMessage();
        error_log("Case update error: " . $e->getMessage());
    }
}

// Get available case types from database or use defaults
$case_types = [];
try {
    // Try to get case types from database if table exists
    $caseTypesTableExists = $db->query("SHOW TABLES LIKE 'case_types'")->rowCount() > 0;
    if ($caseTypesTableExists) {
        $stmt = $db->query("SELECT type_name FROM case_types ORDER BY type_name");
        $case_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (Exception $e) {
    // Use default case types if table doesn't exist
    error_log("Case types table not found, using defaults");
}

if (empty($case_types)) {
    $case_types = [
        'Theft', 'Assault', 'Burglary', 'Robbery', 'Fraud', 'Drug Offense',
        'Traffic Violation', 'Domestic Violence', 'Cyber Crime', 'Homicide',
        'Sexual Assault', 'Kidnapping', 'Arson', 'Vandalism', 'Other'
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title', ['case_number' => htmlspecialchars($case['case_number'])]); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .edit-case-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            max-width: 1000px;
            position: relative;
            overflow: hidden;
        }
        
        .edit-case-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .edit-case-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .edit-case-header h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-label-custom {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
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
        
        .case-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
                    padding: 20px;
                    margin-bottom: 20px;
                }
                
                .info-item {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    padding-bottom: 10px;
                    border-bottom: 1px solid #dee2e6;
                }
                
                .info-label {
                    font-weight: 600;
                    color: #495057;
                    min-width: 150px;
                }
                
                .info-value {
                    color: #6c757d;
                }
                
                .priority-badge {
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                
                .priority-high { background: #dc3545; color: white; }
                .priority-medium { background: #ffc107; color: #212529; }
                .priority-low { background: #28a745; color: white; }
                
                .status-badge {
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    text-transform: uppercase;
                }
                
                .status-open { background: #28a745; color: white; }
                .status-in-progress { background: #ffc107; color: #212529; }
                .status-in-court { background: #17a2b8; color: white; }
                .status-closed { background: #6c757d; color: white; }
                .status-suspended { background: #dc3545; color: white; }
                
                /* Language Selector */
                .lang-selector {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                
                .lang-selector form {
                    display: inline;
                }
                
                .lang-selector select {
                    padding: 5px 10px;
                    border: none;
                    border-radius: 5px;
                    background: rgba(255,255,255,0.2);
                    color: white;
                    font-size: 14px;
                }
                
                .lang-selector select option {
                    background: #667eea;
                    color: white;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="edit-case-container">
                    <!-- Language Selector -->
                    <div class="lang-selector" style="position: absolute; top: 20px; right: 20px;">
                        <form method="post">
                            <select name="lang" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="en" <?php echo $current_lang=='en'?'selected':''; ?>><?php echo t('lang_english'); ?></option>
                                <option value="am" <?php echo $current_lang=='am'?'selected':''; ?>><?php echo t('lang_amharic'); ?></option>
                                <option value="om" <?php echo $current_lang=='om'?'selected':''; ?>><?php echo t('lang_oromo'); ?></option>
                            </select>
                        </form>
                    </div>
                    
                    <!-- Header -->
                    <div class="edit-case-header">
                        <h3><i class="fas fa-edit me-3"></i><?php echo t('header_title'); ?></h3>
                        <p class="lead"><?php echo t('header_subtitle'); ?> <strong><?php echo htmlspecialchars($case['case_number']); ?></strong></p>
                    </div>
                    
                    <!-- Status Messages -->
                    <?php if (isset($success_message)): ?>
                        <div class="alert-custom alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert-custom alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- Case Information -->
                        <div class="col-md-4">
                            <div class="form-section">
                                <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i><?php echo t('info_case_number'); ?></h5>
                                
                                <div class="case-info-card">
                                    <div class="info-item">
                                        <span class="info-label"><?php echo t('info_case_number'); ?></span>
                                        <span class="info-value"><?php echo htmlspecialchars($case['case_number']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><?php echo t('info_status'); ?></span>
                                        <span class="info-value">
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'Open')); ?>">
                                                <?php echo htmlspecialchars($case['status'] ?? t('status_open')); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><?php echo t('info_priority'); ?></span>
                                        <span class="info-value">
                                            <span class="priority-badge priority-<?php echo strtolower($case['priority'] ?? 'medium'); ?>">
                                                <?php echo htmlspecialchars($case['priority'] ?? t('priority_medium')); ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><?php echo t('info_date_created'); ?></span>
                                        <span class="info-value">
                                            <?php echo date('M j, Y g:i A', strtotime($case['created_at'])); ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><?php echo t('info_last_updated'); ?></span>
                                        <span class="info-value">
                                            <?php echo isset($case['updated_at']) ? date('M j, Y g:i A', strtotime($case['updated_at'])) : t('never'); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="manage_cases.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i><?php echo t('btn_back_cases'); ?>
                                    </a>
                                    <a href="view_criminal_record.php?id=<?php echo $case_id; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i><?php echo t('btn_view_details'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Edit Form -->
                        <div class="col-md-8">
                            <div class="form-section">
                                <h5 class="mb-4"><i class="fas fa-edit me-2"></i><?php echo t('form_case_number'); ?></h5>
                                
                                <form method="POST" id="editCaseForm">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label-custom"><?php echo t('form_case_number'); ?> *</label>
                                            <input type="text" class="form-control form-control-custom" name="case_number" 
                                                   value="<?php echo htmlspecialchars($case['case_number']); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label-custom"><?php echo t('form_case_type'); ?> *</label>
                                            <select class="form-control form-control-custom" name="case_type" required>
                                                <option value=""><?php echo t('form_select_type'); ?></option>
                                                <?php foreach ($case_types as $type): ?>
                                                    <option value="<?php echo htmlspecialchars($type); ?>" 
                                                        <?php echo ($case['case_type'] ?? '') === $type ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($type); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label-custom"><?php echo t('form_status'); ?> *</label>
                                            <select class="form-control form-control-custom" name="status" required>
                                                <option value="Open" <?php echo ($case['status'] ?? '') === t('status_open') ? 'selected' : ''; ?>><?php echo t('status_open'); ?></option>
                                                <option value="In Progress" <?php echo ($case['status'] ?? '') === t('status_in_progress') ? 'selected' : ''; ?>><?php echo t('status_in_progress'); ?></option>
                                                <option value="In Court" <?php echo ($case['status'] ?? '') === t('status_in_court') ? 'selected' : ''; ?>><?php echo t('status_in_court'); ?></option>
                                                <option value="Closed" <?php echo ($case['status'] ?? '') === t('status_closed') ? 'selected' : ''; ?>><?php echo t('status_closed'); ?></option>
                                                <option value="Suspended" <?php echo ($case['status'] ?? '') === t('status_suspended') ? 'selected' : ''; ?>><?php echo t('status_suspended'); ?></option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label-custom"><?php echo t('form_priority'); ?></label>
                                            <select class="form-control form-control-custom" name="priority">
                                                <option value="low" <?php echo ($case['priority'] ?? '') === t('priority_low') ? 'selected' : ''; ?>><?php echo t('priority_low'); ?></option>
                                                <option value="medium" <?php echo ($case['priority'] ?? '') === t('priority_medium') ? 'selected' : ''; ?>><?php echo t('priority_medium'); ?></option>
                                                <option value="high" <?php echo ($case['priority'] ?? '') === t('priority_high') ? 'selected' : ''; ?>><?php echo t('priority_high'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label-custom"><?php echo t('form_location'); ?></label>
                                        <input type="text" class="form-control form-control-custom" name="location" 
                                               value="<?php echo htmlspecialchars($case['location'] ?? ''); ?>" 
                                               placeholder="<?php echo t('placeholder_location'); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label-custom"><?php echo t('form_date_reported'); ?></label>
                                        <input type="date" class="form-control form-control-custom" name="date_reported" 
                                               value="<?php echo htmlspecialchars($case['date_reported'] ?? date('Y-m-d')); ?>">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label-custom"><?php echo t('form_description'); ?> *</label>
                                        <textarea class="form-control form-control-custom" name="description" rows="5" required 
                                                  placeholder="<?php echo t('placeholder_description'); ?>"><?php echo htmlspecialchars($case['description'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary-custom flex-fill">
                                            <i class="fas fa-save me-2"></i><?php echo t('btn_update_case'); ?>
                                        </button>
                                        <a href="manage_cases.php" class="btn btn-secondary flex-fill">
                                            <i class="fas fa-times me-2"></i><?php echo t('btn_cancel'); ?>
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Form validation
                document.getElementById('editCaseForm').addEventListener('submit', function(e) {
                    const caseNumber = document.querySelector('input[name="case_number"]').value.trim();
                    const caseType = document.querySelector('select[name="case_type"]').value;
                    const description = document.querySelector('textarea[name="description"]').value.trim();
                    
                    if (!caseNumber || !caseType || !description) {
                        e.preventDefault();
                        alert('<?php echo addslashes(t("js_required_fields")); ?>');
                        return;
                    }
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?php echo t("js_updating"); ?>';
                });
            });
        </script>
    </body>
</html>