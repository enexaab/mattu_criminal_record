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
        'title' => 'Case File #{case_number} - Mattu City Criminal Management System',
        'case_file' => 'Case File',
        'back_to_cases' => 'Back to Cases',
        'add_evidence' => 'Add Evidence',
        'update_status' => 'Update Status',
        'case_details' => 'Case Details',
        'linked_suspects' => 'Linked Suspects',
        'evidence' => 'Evidence',
        'case_notes_updates' => 'Case Notes & Updates',
        'case_number' => 'Case Number',
        'case_type' => 'Case Type',
        'date_reported' => 'Date Reported',
        'location' => 'Location',
        'severity' => 'Severity',
        'priority' => 'Priority',
        'lead_officer' => 'Lead Officer',
        'created_by' => 'Created By',
        'case_description' => 'Case Description',
        'view_record' => 'View Record',
        'no_suspects_linked' => 'No suspects linked to this case',
        'link_suspect' => 'Link Suspect',
        'dob' => 'DOB',
        'not_specified' => 'Not specified',
        'items' => 'items',
        'found' => 'Found',
        'collected_by' => 'Collected by',
        'date' => 'Date',
        'no_evidence_collected' => 'No evidence collected yet',
        'add_first_evidence' => 'Add First Evidence',
        'add_new_note' => 'Add New Note',
        'enter_case_notes' => 'Enter case notes, updates, or observations...',
        'note_type' => 'Note Type',
        'general' => 'General',
        'investigation' => 'Investigation',
        'court' => 'Court',
        'follow_up' => 'Follow-up',
        'mark_as_important' => 'Mark as Important',
        'add_note' => 'Add Note',
        'important' => 'Important',
        'no_notes_added' => 'No notes added yet',
        'update_case_status' => 'Update Case Status',
        'update_status_for' => 'Update status for',
        'current_status' => 'Current status',
        'evidence_media' => 'Evidence Media',
        'download' => 'Download',
        'note_added_successfully' => 'Note added successfully',
        'case_status_updated' => 'Case status updated to {status}',
        'please_enter_note_content' => 'Please enter note content.',
        'failed_to_add_note' => 'Failed to add note. Please try again.',
        'failed_to_update_status' => 'Failed to update case status. Please try again.',
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'in_court' => 'In Court',
        'closed' => 'Closed',
        'suspended' => 'Suspended',
        'suspect_photo' => 'Suspect Photo',
        'suspect' => 'Suspect',
        'relationship' => 'Relationship to Case',
    ],
    'am' => [
        'title' => 'የጉዳይ ፋይል #{case_number} - ማቱ ከተማ የወደንጀል አስተዳደር ስርዓት',
        'case_file' => 'የጉዳይ ፋይል',
        'back_to_cases' => 'ወደ ጉዳዮች',
        'add_evidence' => 'የውህደት ጨምር',
        'update_status' => 'ሁኔታ ዝውውር',
        'case_details' => 'የጉዳይ ዝርዝሮች',
        'linked_suspects' => 'የተገናኙ ተጠያቂዎች',
        'evidence' => 'የውህደት',
        'case_notes_updates' => 'የጉዳይ ማስታወቂያዎች እና ዝውውሮች',
        'case_number' => 'የጉዳይ ቁጥር',
        'case_type' => 'የጉዳይ አይነት',
        'date_reported' => 'የተአስረው ቀን',
        'location' => 'ቦታ',
        'severity' => 'ክብረት',
        'priority' => 'ቅደም ተቀዳሚነት',
        'lead_officer' => 'መሪ መኮንን',
        'created_by' => 'የተፈጠረ በ',
        'case_description' => 'የጉዳይ መግለጫ',
        'view_record' => 'መዝገብ ይመልከቱ',
        'no_suspects_linked' => 'ለዚህ ጉዳይ ተጠያቂ የሉም',
        'link_suspect' => 'ተጠያቂ ያገናኙ',
        'dob' => 'የልደት ቀን',
        'not_specified' => 'የሉም',
        'items' => 'ንጥሎች',
        'found' => 'ተገኝቷል',
        'collected_by' => 'ተገባ በ',
        'date' => 'ቀን',
        'no_evidence_collected' => 'የውህደት አልተገባም',
        'add_first_evidence' => 'የመጀመሪያ የውህደት ጨምር',
        'add_new_note' => 'አዲስ ማስታወቂያ ጨምር',
        'enter_case_notes' => 'የጉዳይ ማስታወቂያዎች፣ ዝውውሮች ወይም ምልከታዎችን ያስገቡ...',
        'note_type' => 'የማስታወቂያ አይነት',
        'general' => 'አጠቃላይ',
        'investigation' => 'መረጃ መሰብሰብ',
        'court' => 'ፍርድ ቤት',
        'follow_up' => 'መከታተያ',
        'mark_as_important' => 'እንደ አስፈላጊ አስቀምጥ',
        'add_note' => 'ማስታወቂያ ጨምር',
        'important' => 'አስፈላጊ',
        'no_notes_added' => 'ማስታወቂያ አልተጨምረም',
        'update_case_status' => 'የጉዳይ ሁኔታ ዝውውር',
        'update_status_for' => 'ለ... ሁኔታ ዝውውር',
        'current_status' => 'ወቅታዊ ሁኔታ',
        'evidence_media' => 'የውህደት ሚዲያ',
        'download' => 'ይገኙ',
        'note_added_successfully' => 'ማስታወቂያ በተሳካ ተጨምሯል',
        'case_status_updated' => 'የጉዳይ ሁኔታ ወደ {status} ተዘዋወረ',
        'please_enter_note_content' => 'ማስታወቂያ ይይዛቸው አስቀምጠው።',
        'failed_to_add_note' => 'ማስታወቂያ መጨመር አልተሳካም። እንደገና ይሞክሩ።',
        'failed_to_update_status' => 'የጉዳይ ሁኔታ ዝውውር አልተሳካም። እንደገና ይሞክሩ።',
        'open' => 'ክፍት',
        'in_progress' => 'በመስመር ላይ',
        'in_court' => 'በፍርድ ቤት',
        'closed' => 'ተዘግቧል',
        'suspended' => 'ተቋርጧል',
        'suspect_photo' => 'የተጠያቂ ፎቶ',
        'suspect' => 'ተጠያቂ',
        'relationship' => 'ከጉዳይ ጋር ያለው ግንኙነት',
    ],
    'om' => [
        'title' => 'Qoricha Fayila #{case_number} - Sisteemi Diinagdee Mattu Kuta',
        'case_file' => 'Qoricha Fayila',
        'back_to_cases' => 'Deebii Caasaa',
        'add_evidence' => 'Wojjii Qabuu',
        'update_status' => 'Hakkina Ijji',
        'case_details' => 'Qoricha Zariya',
        'linked_suspects' => 'Qoricha Gammachu',
        'evidence' => 'Wojjii',
        'case_notes_updates' => 'Qoricha Mikkirroota & Ijji',
        'case_number' => 'Naama Caasaa',
        'case_type' => 'Aangoo Caasaa',
        'date_reported' => 'Guyyaa Guyyaa',
        'location' => 'Lakkii',
        'severity' => 'Hakkina',
        'priority' => 'Qarqara',
        'lead_officer' => 'Meekoonnin Qabe',
        'created_by' => 'Qabe Deebii',
        'case_description' => 'Qoricha Miya',
        'view_record' => 'Qoricha Argisi',
        'no_suspects_linked' => 'Qoricha hin qabne',
        'link_suspect' => 'Qoricha Gammachuu',
        'dob' => 'Guyyaa Guyyaa',
        'not_specified' => 'Hin Taane',
        'items' => 'Qoricha',
        'found' => 'Argame',
        'collected_by' => 'Qabde Deebii',
        'date' => 'Guyyaa',
        'no_evidence_collected' => 'Wojjii hin qabne',
        'add_first_evidence' => 'Qoricha Qabuu Argisi',
        'add_new_note' => 'Qoricha Qabuu Argisi',
        'enter_case_notes' => 'Qoricha mikkirroota argisi...',
        'note_type' => 'Aangoo Mikkirroota',
        'general' => 'Aate',
        'investigation' => 'Maree',
        'court' => 'Kitaaba',
        'follow_up' => 'Maree Deebii',
        'mark_as_important' => 'Qarqara As Gammachuu',
        'add_note' => 'Mikkirroota Qabuu',
        'important' => 'Qarqara',
        'no_notes_added' => 'Mikkirroota hin qabne',
        'update_case_status' => 'Qoricha Hakkina Ijji',
        'update_status_for' => 'Hakkina Ijji',
        'current_status' => 'Hakkina Guyyaa',
        'evidence_media' => 'Wojjii Midiyaa',
        'download' => 'Gammachuu',
        'note_added_successfully' => 'Mikkirroota qabde',
        'case_status_updated' => 'Qoricha hakkina {status} ijje',
        'please_enter_note_content' => 'Mikkirroota argisi.',
        'failed_to_add_note' => 'Mikkirroota qabuu hin taane.',
        'failed_to_update_status' => 'Hakkina ijji hin taane.',
        'open' => 'Fufaa',
        'in_progress' => 'Maree',
        'in_court' => 'Kitaaba',
        'closed' => 'Dhabame',
        'suspended' => 'Dhiibame',
        'suspect_photo' => 'Qoricha Foto',
        'suspect' => 'Qoricha',
        'relationship' => 'Qoricha Gammachuu',
    ],
];
function t($key, $params = []) {
    global $translations, $current_lang;
    $trans = $translations[$current_lang][$key] ?? $key;
    // Replace placeholders if any
    if (strpos($trans, '{') !== false) {
        foreach ($params as $placeholder => $value) {
            $trans = str_replace('{' . $placeholder . '}', $value, $trans);
        }
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

// Role check - Must be Investigator, Officer, or Admin
if (!function_exists('requireRole')) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['investigator', 'officer', 'admin'])) {
        die("Access denied. Investigator, Officer, or Admin role required.");
    }
} else {
    requireRole(['investigator', 'officer', 'admin']);
}

// Get case ID from URL
$case_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($case_id <= 0) {
    die("Invalid case ID provided.");
}

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Fetch case details
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get case details from cases table - FIXED JOIN
    $case_stmt = $db->prepare("
        SELECT c.*, 
               u1.first_name as lead_first_name, u1.last_name as lead_last_name,
               u2.first_name as creator_first_name, u2.last_name as creator_last_name
        FROM cases c
        LEFT JOIN users u1 ON c.lead_officer_id = u1.user_id
        LEFT JOIN users u2 ON c.created_by = u2.user_id
        WHERE c.id = ?
    ");
    $case_stmt->execute([$case_id]);
    $case = $case_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        die("Case not found.");
    }
    
    // Get suspects linked to this case
    $suspects_stmt = $db->prepare("
        SELECT cr.*, cp.role, cp.relationship_to_case 
        FROM case_persons cp
        INNER JOIN criminal_records cr ON cp.record_id = cr.id
        WHERE cp.case_id = ? AND cp.role = 'Suspect'
    ");
    $suspects_stmt->execute([$case_id]);
    $suspects = $suspects_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get evidence for this case - FIXED JOIN
    $evidence_stmt = $db->prepare("
        SELECT e.*, u.first_name, u.last_name 
        FROM evidence e 
        LEFT JOIN users u ON e.collected_by = u.user_id 
        WHERE e.case_id = ? 
        ORDER BY e.created_at DESC
    ");
    $evidence_stmt->execute([$case_id]);
    $evidence = $evidence_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get case notes/updates - FIXED JOIN
    $notes_stmt = $db->prepare("
        SELECT cn.*, u.first_name, u.last_name 
        FROM case_notes cn
        LEFT JOIN users u ON cn.user_id = u.user_id
        WHERE cn.case_id = ?
        ORDER BY cn.created_at DESC
    ");
    $notes_stmt->execute([$case_id]);
    $case_notes = $notes_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die("Error loading case data: " . $e->getMessage());
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'add_note':
                $note_content = trim($input['note_content'] ?? '');
                $note_type = $input['note_type'] ?? 'general';
                $is_important = isset($input['is_important']) ? 1 : 0;
                
                if (empty($note_content)) {
                    throw new Exception(t('please_enter_note_content'));
                }
                
                // Insert case note
                $note_stmt = $db->prepare("
                    INSERT INTO case_notes (case_id, user_id, note_text, note_type, is_important, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $note_stmt->execute([$case_id, $current_user['user_id'], $note_content, $note_type, $is_important]);
                
                // Get the newly created note with user info
                $new_note_stmt = $db->prepare("
                    SELECT cn.*, u.first_name, u.last_name 
                    FROM case_notes cn
                    LEFT JOIN users u ON cn.user_id = u.user_id
                    WHERE cn.id = ?
                ");
                $new_note_stmt->execute([$db->lastInsertId()]);
                $new_note = $new_note_stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'message' => t('note_added_successfully'),
                    'note' => $new_note
                ]);
                break;
                
            case 'update_status':
                $new_status = trim($input['status'] ?? '');
                $allowed_statuses = [t('open'), t('in_progress'), t('in_court'), t('closed'), t('suspended')];
                
                if (!in_array($new_status, $allowed_statuses)) {
                    throw new Exception("Invalid status value.");
                }
                
                $status_stmt = $db->prepare("UPDATE cases SET status = ? WHERE id = ?");
                $status_stmt->execute([$new_status, $case_id]);
                
                echo json_encode([
                    'success' => true,
                    'message' => t('case_status_updated', ['status' => $new_status])
                ]);
                break;
                
            default:
                throw new Exception("Invalid action");
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title', ['case_number' => htmlspecialchars($case['case_number'])]); ?></title>
    
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
        
        .case-container {
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
        
        .case-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .case-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .case-title {
            flex: 1;
            min-width: 300px;
        }
        
        .case-title h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .case-title p {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .case-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
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
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .case-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .case-section {
            background: rgba(248, 249, 250, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            border: 2px solid #e9ecef;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .section-title {
            font-weight: 700;
            color: #495057;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            color: #212529;
            font-weight: 500;
            font-size: 1rem;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
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
        
        .suspect-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .suspect-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .suspect-photo {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
        
        .evidence-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #28a745;
        }
        
        .evidence-type-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-photo { background: #4facfe; color: white; }
        .badge-video { background: #f093fb; color: white; }
        .badge-document { background: #43e97b; color: white; }
        .badge-audio { background: #fa709a; color: white; }
        .badge-physical { background: #ff9a9e; color: white; }
        .badge-digital { background: #a8edea; color: #333; }
        
        .file-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        
        .note-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
        }
        
        .note-item.important {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        
        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .note-author {
            font-weight: 600;
            color: #495057;
        }
        
        .note-date {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .note-type-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-general { background: #6c757d; color: white; }
        .badge-investigation { background: #17a2b8; color: white; }
        .badge-court { background: #6f42c1; color: white; }
        .badge-followup { background: #fd7e14; color: white; }
        
        .note-content {
            color: #212529;
            line-height: 1.5;
        }
        
        .add-note-form {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            resize: vertical;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
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
            justify-content: space-between;
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
        
        /* Media Modal */
        .media-modal-content {
            max-width: 90%;
            max-height: 90%;
            width: auto;
        }
        
        .media-preview {
            max-width: 100%;
            max-height: 70vh;
            border-radius: 10px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .case-container {
                margin: 10px;
                padding: 25px;
            }
            
            .case-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .case-title h3 {
                font-size: 2rem;
            }
            
            .case-grid {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .case-actions {
                justify-content: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .case-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <div class="case-container">
            <!-- Case Header -->
            <div class="case-header">
                <div class="case-title">
                    <h3><i class="fas fa-folder me-3"></i><?php echo t('case_file'); ?>: <?php echo htmlspecialchars($case['case_number']); ?></h3>
                    <p><?php echo htmlspecialchars($case['case_type']); ?> - <?php echo htmlspecialchars($case['location']); ?></p>
                </div>
                <div class="case-actions">
                    <a href="manage_cases.php" class="btn-secondary-custom">
                        <i class="fas fa-arrow-left me-2"></i><?php echo t('back_to_cases'); ?>
                    </a>
                    <a href="add_evidence.php?case_id=<?php echo $case_id; ?>" class="btn-primary-custom">
                        <i class="fas fa-plus-circle me-2"></i><?php echo t('add_evidence'); ?>
                    </a>
                    <button class="btn-primary-custom" onclick="showStatusModal()" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                        <i class="fas fa-sync me-2"></i><?php echo t('update_status'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Status Message Container -->
            <div id="statusMessage"></div>
            
            <!-- Main Case Grid -->
            <div class="case-grid">
                <!-- Case Details -->
                <div class="case-section">
                    <div class="section-header">
                        <h4 class="section-title"><i class="fas fa-info-circle me-2"></i><?php echo t('case_details'); ?></h4>
                        <button class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'])); ?>" onclick="showStatusModal()">
                            <?php echo htmlspecialchars($case['status']); ?>
                        </button>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label"><?php echo t('case_number'); ?></div>
                            <div class="info-value"><?php echo htmlspecialchars($case['case_number']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('case_type'); ?></div>
                            <div class="info-value"><?php echo htmlspecialchars($case['case_type']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('date_reported'); ?></div>
                            <div class="info-value"><?php echo date('F j, Y', strtotime($case['date_reported'])); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('location'); ?></div>
                            <div class="info-value"><?php echo htmlspecialchars($case['location']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('severity'); ?></div>
                            <div class="info-value"><?php echo htmlspecialchars($case['severity']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('priority'); ?></div>
                            <div class="info-value"><?php echo htmlspecialchars($case['priority']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('lead_officer'); ?></div>
                            <div class="info-value">
                                <?php 
                                if ($case['lead_first_name']) {
                                    echo htmlspecialchars($case['lead_first_name'] . ' ' . $case['lead_last_name']);
                                } else {
                                    echo t('not_specified');
                                }
                                ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label"><?php echo t('created_by'); ?></div>
                            <div class="info-value">
                                <?php 
                                if ($case['creator_first_name']) {
                                    echo htmlspecialchars($case['creator_first_name'] . ' ' . $case['creator_last_name']);
                                } else {
                                    echo 'Unknown';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($case['description'])): ?>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label"><?php echo t('case_description'); ?></div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($case['description'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Suspects Information -->
                <div class="case-section">
                    <div class="section-header">
                        <h4 class="section-title"><i class="fas fa-user-tie me-2"></i><?php echo t('linked_suspects'); ?></h4>
                        <span class="badge bg-primary"><?php echo count($suspects); ?></span>
                    </div>
                    
                    <?php if (!empty($suspects)): ?>
                        <?php foreach ($suspects as $suspect): ?>
                            <div class="suspect-card">
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($suspect['photo'])): ?>
                                        <img src="../<?php echo htmlspecialchars($suspect['photo']); ?>" 
                                             alt="<?php echo t('suspect_photo'); ?>" class="suspect-photo me-3">
                                    <?php else: ?>
                                        <div class="suspect-photo bg-light d-flex align-items-center justify-content-center me-3">
                                            <i class="fas fa-user text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($suspect['first_name'] . ' ' . $suspect['last_name']); ?></h6>
                                        <p class="text-muted mb-1">
                                            <i class="fas fa-id-card me-1"></i>
                                            <?php echo htmlspecialchars($suspect['national_id']); ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo t('dob'); ?>: <?php echo !empty($suspect['date_of_birth']) ? htmlspecialchars($suspect['date_of_birth']) : t('not_specified'); ?>
                                        </p>
                                        <?php if (!empty($suspect['relationship_to_case'])): ?>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-link me-1"></i>
                                                <?php echo htmlspecialchars($suspect['relationship_to_case']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-end">
                                        <a href="view_criminal_record.php?id=<?php echo $suspect['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> <?php echo t('view_record'); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p><?php echo t('no_suspects_linked'); ?></p>
                            <a href="create_case.php?record_id=NEW" class="btn btn-primary btn-sm">
                                <i class="fas fa-link me-2"></i><?php echo t('link_suspect'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Evidence Section -->
            <div class="case-section">
                <div class="section-header">
                    <h4 class="section-title"><i class="fas fa-fingerprint me-2"></i><?php echo t('evidence'); ?></h4>
                    <span class="badge bg-success"><?php echo count($evidence); ?> <?php echo t('items'); ?></span>
                </div>
                
                <?php if (!empty($evidence)): ?>
                    <div class="row">
                        <?php foreach ($evidence as $item): ?>
                            <div class="col-md-6 mb-3">
                                <div class="evidence-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="evidence-type-badge badge-<?php echo $item['evidence_type']; ?>">
                                                <?php echo ucfirst($item['evidence_type']); ?>
                                            </span>
                                            <small class="text-muted ms-2">#<?php echo $item['id']; ?></small>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($item['created_at'])); ?>
                                        </small>
                                    </div>
                                    
                                    <p class="mb-2"><strong><?php echo htmlspecialchars($item['description']); ?></strong></p>
                                    
                                    <?php if ($item['file_path']): ?>
                                        <div class="mb-2">
                                            <?php
                                            $file_ext = pathinfo($item['file_path'], PATHINFO_EXTENSION);
                                            $is_image = in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif']);
                                            $is_video = in_array(strtolower($file_ext), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
                                            ?>
                                            
                                            <?php if ($is_image): ?>
                                                <img src="../<?php echo htmlspecialchars($item['file_path']); ?>" 
                                                     class="file-preview" alt="Evidence Photo"
                                                     onclick="openMediaModal('<?php echo htmlspecialchars($item['file_path']); ?>', 'image')">
                                            <?php elseif ($is_video): ?>
                                                <div class="file-preview bg-light d-flex align-items-center justify-content-center"
                                                     onclick="openMediaModal('<?php echo htmlspecialchars($item['file_path']); ?>', 'video')">
                                                    <i class="fas fa-video text-muted fa-2x"></i>
                                                </div>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="fas fa-play-circle me-1"></i><?php echo t('click_to_play_video'); ?>
                                                </small>
                                            <?php else: ?>
                                                <div class="file-preview bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-file text-muted fa-2x"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="small text-muted">
                                        <div><strong><?php echo t('found'); ?>:</strong> <?php echo $item['location_found'] ? htmlspecialchars($item['location_found']) : t('not_specified'); ?></div>
                                        <div><strong><?php echo t('collected_by'); ?>:</strong> 
                                            <?php 
                                            if (isset($item['first_name']) && isset($item['last_name'])) {
                                                echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']);
                                            } else {
                                                echo htmlspecialchars($item['collected_by']);
                                            }
                                            ?>
                                        </div>
                                        <div><strong><?php echo t('date'); ?>:</strong> <?php echo $item['date_found'] ? date('M j, Y', strtotime($item['date_found'])) : t('not_specified'); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p><?php echo t('no_evidence_collected'); ?></p>
                        <a href="add_evidence.php?case_id=<?php echo $case_id; ?>" class="btn btn-success">
                            <i class="fas fa-plus-circle me-2"></i><?php echo t('add_first_evidence'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Case Notes Section -->
            <div class="case-section">
                <div class="section-header">
                    <h4 class="section-title"><i class="fas fa-sticky-note me-2"></i><?php echo t('case_notes_updates'); ?></h4>
                    <span class="badge bg-warning text-dark"><?php echo count($case_notes); ?> <?php echo t('notes'); ?></span>
                </div>
                
                <!-- Add Note Form -->
                <div class="add-note-form">
                    <form id="addNoteForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><?php echo t('add_new_note'); ?></label>
                            <textarea class="form-control-custom" id="noteContent" rows="3" 
                                      placeholder="<?php echo t('enter_case_notes'); ?>" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold"><?php echo t('note_type'); ?></label>
                                    <select class="form-control-custom" id="noteType">
                                        <option value="general"><?php echo t('general'); ?></option>
                                        <option value="investigation"><?php echo t('investigation'); ?></option>
                                        <option value="court"><?php echo t('court'); ?></option>
                                        <option value="followup"><?php echo t('follow_up'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" id="isImportant">
                                        <label class="form-check-label fw-bold" for="isImportant">
                                            <?php echo t('mark_as_important'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i><?php echo t('add_note'); ?>
                        </button>
                    </form>
                </div>
                
                <!-- Notes List -->
                <div id="notesContainer">
                    <?php if (!empty($case_notes)): ?>
                        <?php foreach ($case_notes as $note): ?>
                            <div class="note-item <?php echo $note['is_important'] ? 'important' : ''; ?>">
                                <div class="note-header">
                                    <div>
                                        <div class="note-author">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($note['first_name'] . ' ' . $note['last_name']); ?>
                                        </div>
                                        <span class="note-type-badge badge-<?php echo $note['note_type']; ?>">
                                            <?php echo ucfirst($note['note_type']); ?>
                                        </span>
                                        <?php if ($note['is_important']): ?>
                                            <span class="badge bg-danger ms-1"><?php echo t('important'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="note-date">
                                        <?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="note-content">
                                    <?php echo nl2br(htmlspecialchars($note['note_text'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <p><?php echo t('no_notes_added'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
                <p><?php echo t('update_status_for'); ?> <strong><?php echo htmlspecialchars($case['case_number']); ?></strong></p>
                <p><?php echo t('current_status'); ?>: <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'])); ?>"><?php echo htmlspecialchars($case['status']); ?></span></p>
                <div class="mt-3">
                    <?php
                    $statuses = ['Open', 'In Progress', 'In Court', 'Closed', 'Suspended'];
                    foreach ($statuses as $status):
                        $isCurrent = $status === $case['status'];
                    ?>
                        <button type="button" class="status-option <?php echo $isCurrent ? 'current' : ''; ?>" 
                                onclick="updateCaseStatus('<?php echo $status; ?>')" <?php echo $isCurrent ? 'disabled' : ''; ?>>
                            <i class="fas fa-<?php echo $isCurrent ? 'check' : 'circle'; ?> me-2"></i>
                            <?php echo $status; ?> <?php echo $isCurrent ? '(Current)' : ''; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Preview Modal -->
    <div id="mediaModal" class="modal-overlay" style="display: none;">
        <div class="modal-content media-modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?php echo t('evidence_media'); ?></h3>
                <button type="button" class="modal-close" onclick="closeMediaModal()">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Evidence" class="media-preview" style="display: none;">
                <video id="modalVideo" controls class="media-preview" style="display: none;">
                    Your browser does not support the video tag.
                </video>
                <a id="downloadMedia" href="#" class="btn btn-primary mt-3" download style="display: none;">
                    <i class="fas fa-download me-2"></i><?php echo t('download'); ?>
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Status Modal Functions
        function showStatusModal() {
            document.getElementById('statusModal').style.display = 'flex';
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        // Media Modal Functions
        function openMediaModal(filePath, mediaType) {
            const fullPath = '../' + filePath;
            const modalImage = document.getElementById('modalImage');
            const modalVideo = document.getElementById('modalVideo');
            const downloadLink = document.getElementById('downloadMedia');
            
            // Hide all media elements first
            modalImage.style.display = 'none';
            modalVideo.style.display = 'none';
            downloadLink.style.display = 'none';
            
            // Reset sources
            modalImage.src = '';
            modalVideo.src = '';
            
            // Set download link
            downloadLink.href = fullPath;
            downloadLink.style.display = 'inline-block';
            
            // Show appropriate media element
            if (mediaType === 'image') {
                modalImage.src = fullPath;
                modalImage.style.display = 'block';
            } else if (mediaType === 'video') {
                modalVideo.src = fullPath;
                modalVideo.style.display = 'block';
            }
            
            // Show modal
            document.getElementById('mediaModal').style.display = 'flex';
        }
        
        function closeMediaModal() {
            document.getElementById('mediaModal').style.display = 'none';
            const modalVideo = document.getElementById('modalVideo');
            if (modalVideo) {
                modalVideo.pause();
                modalVideo.currentTime = 0;
            }
        }
        
        // Update Case Status
        function updateCaseStatus(newStatus) {
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'update_status',
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showMessage(`<?php echo t('case_status_updated', ['status' => '{status}']); ?>`.replace('{status}', newStatus), 'success');
                    closeStatusModal();
                    // Reload page to reflect changes
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(result.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('<?php echo t('failed_to_update_status'); ?>', 'danger');
            });
        }
        
        // Add Note Functionality
        document.getElementById('addNoteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const noteContent = document.getElementById('noteContent').value.trim();
            const noteType = document.getElementById('noteType').value;
            const isImportant = document.getElementById('isImportant').checked;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (!noteContent) {
                showMessage('<?php echo t('please_enter_note_content'); ?>', 'danger');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner me-2"></span><?php echo t('adding'); ?>...';
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'add_note',
                    note_content: noteContent,
                    note_type: noteType,
                    is_important: isImportant
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Add new note to the list
                    const notesContainer = document.getElementById('notesContainer');
                    const newNoteHTML = `
                        <div class="note-item ${isImportant ? 'important' : ''}">
                            <div class="note-header">
                                <div>
                                    <div class="note-author">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($current_user['full_name']); ?>
                                    </div>
                                    <span class="note-type-badge badge-${noteType}">
                                        ${noteType.charAt(0).toUpperCase() + noteType.slice(1)}
                                    </span>
                                    ${isImportant ? '<span class="badge bg-danger ms-1"><?php echo t('important'); ?></span>' : ''}
                                </div>
                                <div class="note-date">
                                    ${new Date().toLocaleString()}
                                </div>
                            </div>
                            <div class="note-content">
                                ${noteContent.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    `;
                    
                    // If no notes exist, replace the empty state
                    if (notesContainer.querySelector('.text-center')) {
                        notesContainer.innerHTML = newNoteHTML;
                    } else {
                        notesContainer.insertAdjacentHTML('afterbegin', newNoteHTML);
                    }
                    
                    // Clear form
                    document.getElementById('noteContent').value = '';
                    document.getElementById('isImportant').checked = false;
                    showMessage('<?php echo t('note_added_successfully'); ?>', 'success');
                } else {
                    showMessage(result.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('<?php echo t('failed_to_add_note'); ?>', 'danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-2"></i><?php echo t('add_note'); ?>';
            });
        });
        
        // Show status message
        function showMessage(message, type) {
            const statusMessage = document.getElementById('statusMessage');
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
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.id === 'statusModal') {
                closeStatusModal();
            }
            if (e.target.id === 'mediaModal') {
                closeMediaModal();
            }
        });
    </script>
</body>
</html>