<?php
// view_case.php
// This file displays a read-only view of a single case for clerks.
// Fetches data from the database (case details, suspects, evidence, notes).
// No edit/add options—view-only. Accessible only to authenticated clerks.
// ID from GET param.

// Required includes
require '../includes/auth.php';      // Session and role checks
require '../includes/database.php';  // DB connection
require '../includes/clerk_functions.php'; // Read-only functions (optional fallback)

// Language support
$languages = ['en', 'am', 'om'];
$current_lang = $_SESSION['lang'] ?? 'en';
if (isset($_POST['lang']) && in_array($_POST['lang'], $languages)) {
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
    exit();
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . $_SERVER['PHP_SELF'] . (isset($_GET['id']) ? '?id=' . $_GET['id'] : ''));
    exit();
}

$translations = [
    'en' => [
        'system_name' => 'Mattu Criminal Record System',
        'view_case' => 'View Case',
        'case_file' => 'Case File',
        'error_no_id' => 'No case ID provided.',
        'error_not_found' => 'Case not found.',
        'error_db' => 'Database error:',
        'back_to_search' => 'Back to Search',
        'dashboard' => 'Dashboard',
        'case_type_location' => '%s - %s',
        'case_details' => 'Case Details',
        'case_number' => 'Case Number',
        'case_type' => 'Case Type',
        'date_reported' => 'Date Reported',
        'location' => 'Location',
        'severity' => 'Severity',
        'priority' => 'Priority',
        'lead_officer' => 'Lead Officer',
        'created_by' => 'Created By',
        'case_description' => 'Case Description',
        'not_assigned' => 'Not assigned',
        'unknown' => 'Unknown',
        'not_specified' => 'Not specified',
        'linked_suspects' => 'Linked Suspects',
        'no_suspects' => 'No suspects linked to this case',
        'dob' => 'DOB',
        'relationship' => 'Relationship to Case',
        'view_record' => 'View Record',
        'evidence' => 'Evidence',
        'evidence_items' => 'items',
        'no_evidence' => 'No evidence collected yet',
        'evidence_type' => '%s', // ucfirst
        'evidence_id' => '#%s',
        'evidence_date' => 'M j, Y g:i A',
        'description' => 'No description',
        'found' => 'Found',
        'collected_by' => 'Collected by',
        'date' => 'Date',
        'video_file' => 'Video file',
        'document' => 'Document',
        'case_notes' => 'Case Notes & Updates',
        'notes' => 'notes',
        'no_notes' => 'No notes added yet',
        'note_author' => 'Author',
        'note_type' => 'General', // ucfirst
        'important' => 'Important',
        'note_date' => 'M j, Y g:i A',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_in_court' => 'In Court',
        'status_closed' => 'Closed',
        'status_suspended' => 'Suspended',
        'logout' => 'Logout',
        'clerk' => 'Clerk',
    ],
    'am' => [
        'system_name' => 'ማቱ የወንጀል መዝገብ ስርዓት',
        'view_case' => 'ጉዳይ ተመልከት',
        'case_file' => 'የጉዳይ ፋይል',
        'error_no_id' => 'የጉዳይ መለያ አልተገኘም።',
        'error_not_found' => 'ጉዳይ አልተገኘም።',
        'error_db' => 'የውሂብ ማያ ስህተት፡',
        'back_to_search' => 'ወደ ፍለጋ',
        'dashboard' => 'ዳሽቦርድ',
        'case_type_location' => '%s - %s',
        'case_details' => 'የጉዳይ ዝርዝሮች',
        'case_number' => 'የጉዳይ ቁጥር',
        'case_type' => 'የጉዳይ አይነት',
        'date_reported' => 'የተወሰነ ቀን',
        'location' => 'ቦታ',
        'severity' => 'ክብረት',
        'priority' => 'ቅድሚያ',
        'lead_officer' => 'መሪ መኮንን',
        'created_by' => 'በተፈጠረው',
        'case_description' => 'የጉዳይ መግለጫ',
        'not_assigned' => 'አልተሰጠም',
        'unknown' => 'ያልታወቀ',
        'not_specified' => 'አልተገለጸም',
        'linked_suspects' => 'የተገናኙ ጥፋቶች',
        'no_suspects' => 'ከዚህ ጉዳይ ጋር የተገናኙ ጥፋቶች የሉም',
        'dob' => 'የልደት ቀን',
        'relationship' => 'ከጉዳይ ጋር ግንኙነት',
        'view_record' => 'መዝገብ ተመልከት',
        'evidence' => 'የተገኘ ማስረጃ',
        'evidence_items' => 'ንጥሎች',
        'no_evidence' => 'የተገኘ ማስረጃ የለም',
        'evidence_type' => '%s',
        'evidence_id' => '#%s',
        'evidence_date' => 'M j, Y g:i A',
        'description' => 'መግለጫ የለም',
        'found' => 'ተገኘ',
        'collected_by' => 'ተገኘ በ',
        'date' => 'ቀን',
        'video_file' => 'ቪዲዮ ፋይል',
        'document' => 'መዛግብ',
        'case_notes' => 'የጉዳይ ማስታወሻዎች እና ዝማኔዎች',
        'notes' => 'ማስታወሻዎች',
        'no_notes' => 'ማስታወሻ አልተጨምረም',
        'note_author' => 'ጸሐፊ',
        'note_type' => 'አጠቃላይ',
        'important' => 'አስፈላጊ',
        'note_date' => 'M j, Y g:i A',
        'status_open' => 'ክፍት',
        'status_in_progress' => 'በመረጃ',
        'status_in_court' => 'በፍርድ ቤት',
        'status_closed' => 'ዝጋ',
        'status_suspended' => 'ተቋርጧል',
        'logout' => 'ውጣ',
        'clerk' => 'ጸሐፊ',
    ],
    'om' => [
        'system_name' => 'Sisteemi Mattu Diinagdee',
        'view_case' => 'Caasaa Argisi',
        'case_file' => 'Fayila Caasaa',
        'error_no_id' => 'ID Caasaa hin argamu.',
        'error_not_found' => 'Caasaa hin argamu.',
        'error_db' => 'Saaqaa database:',
        'back_to_search' => 'Gadii Fufiisi',
        'dashboard' => 'Dashboardii',
        'case_type_location' => '%s - %s',
        'case_details' => 'Qorannoo Caasaa',
        'case_number' => 'Naama Caasaa',
        'case_type' => 'Aangoo Caasaa',
        'date_reported' => 'Guyaa Barame',
        'location' => 'Aanoo',
        'severity' => 'Haala',
        'priority' => 'Qadammii',
        'lead_officer' => 'Afraa Hojiin',
        'created_by' => 'Hojjetu',
        'case_description' => 'Qorannoo Caasaa',
        'not_assigned' => 'Hin Taqamu',
        'unknown' => 'Hin Beekamu',
        'not_specified' => 'Hin Beekamu',
        'linked_suspects' => 'Ummataa Diinagdeewwan',
        'no_suspects' => 'Ummataa diinagdeewwan kan caasaa kanaa hin jiru',
        'dob' => 'Guyaa Lakkoofsa',
        'relationship' => 'Gaha Caasaa',
        'view_record' => 'Diinagdee Argisi',
        'evidence' => 'Ijaarsa',
        'evidence_items' => 'Ijaarsota',
        'no_evidence' => 'Ijaarsa hin jiru',
        'evidence_type' => '%s',
        'evidence_id' => '#%s',
        'evidence_date' => 'M j, Y g:i A',
        'description' => 'Qorannoo hin jiru',
        'found' => 'Argame',
        'collected_by' => 'Hojjetu',
        'date' => 'Guyaa',
        'video_file' => 'Fayila Videoo',
        'document' => 'Qorannoo',
        'case_notes' => 'Mataa Caasaa Mattu Ijaarsa',
        'notes' => 'Mataa',
        'no_notes' => 'Mataa hin jiru',
        'note_author' => 'Qoricha',
        'note_type' => 'Aangoo',
        'important' => 'Barbaachisa',
        'note_date' => 'M j, Y g:i A',
        'status_open' => 'Mittii',
        'status_in_progress' => 'Hojii',
        'status_in_court' => 'Fardi',
        'status_closed' => 'Dhabame',
        'status_suspended' => 'Tufi',
        'logout' => 'Fufiisi',
        'clerk' => 'Karraa',
    ],
];

function t($key, $params = []) {
    global $translations, $current_lang;
    $text = $translations[$current_lang][$key] ?? $key;
    if (!empty($params)) {
        foreach ($params as $k => $v) {
            $text = str_replace(":$k", $v, $text);
        }
    }
    return $text;
}

function translateStatus($status) {
    $statuses = [
        'en' => ['open' => 'Open', 'in-progress' => 'In Progress', 'in-court' => 'In Court', 'closed' => 'Closed', 'suspended' => 'Suspended'],
        'am' => ['open' => 'ክፍት', 'in-progress' => 'በመረጃ', 'in-court' => 'በፍርድ ቤት', 'closed' => 'ዝጋ', 'suspended' => 'ተቋርጧል'],
        'om' => ['open' => 'Mittii', 'in-progress' => 'Hojii', 'in-court' => 'Fardi', 'closed' => 'Dhabame', 'suspended' => 'Tufi'],
    ];
    global $current_lang;
    $clean_status = strtolower(str_replace(' ', '-', $status ?? 'open'));
    return $statuses[$current_lang][$clean_status] ?? $status;
}

function translateEvidenceType($type) {
    $types = [
        'en' => ['photo' => 'Photo', 'video' => 'Video', 'document' => 'Document', 'audio' => 'Audio', 'physical' => 'Physical', 'digital' => 'Digital'],
        'am' => ['photo' => 'ፎቶ', 'video' => 'ቪዲዮ', 'document' => 'መዛግብ', 'audio' => 'ድምጽ', 'physical' => 'አካላዊ', 'digital' => 'ዲጂታል'],
        'om' => ['photo' => 'Fotoo', 'video' => 'Videoo', 'document' => 'Qorannoo', 'audio' => 'Dhimsa', 'physical' => 'Akaakayyaa', 'digital' => 'Digitaala'],
    ];
    global $current_lang;
    $clean_type = strtolower($type ?? 'document');
    return ucfirst($types[$current_lang][$clean_type] ?? $type);
}

function translateNoteType($type) {
    $types = [
        'en' => ['general' => 'General', 'investigation' => 'Investigation', 'court' => 'Court', 'followup' => 'Followup'],
        'am' => ['general' => 'አጠቃላይ', 'investigation' => 'መረጃ', 'court' => 'ፍርድ ቤት', 'followup' => 'መከታተል'],
        'om' => ['general' => 'Aangoo', 'investigation' => 'Ijaarsa', 'court' => 'Fardi', 'followup' => 'Ijaarsa'],
    ];
    global $current_lang;
    $clean_type = strtolower($type ?? 'general');
    return ucfirst($types[$current_lang][$clean_type] ?? $type);
}

// Initialize database
$database = new Database();
$pdo = $database->getConnection();

// Auth check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Enforce clerk role
requireRole(['clerk']);

// Get case ID from URL
$case_id = intval($_GET['id'] ?? 0);
$case = null;
$suspects = [];
$evidence = [];
$case_notes = [];
$error = '';

if ($case_id <= 0) {
    $error = t('error_no_id');
} else {
    // Fetch case details (like officers' version, with JOINs)
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   u1.first_name as lead_first_name, u1.last_name as lead_last_name,
                   u2.first_name as creator_first_name, u2.last_name as creator_last_name
            FROM cases c
            LEFT JOIN users u1 ON c.lead_officer_id = u1.user_id
            LEFT JOIN users u2 ON c.created_by = u2.user_id
            WHERE c.id = ?
        ");
        $stmt->execute([$case_id]);
        $case = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$case) {
            error_log("Clerk view case: No case found for ID $case_id");
            $error = t('error_not_found');
        } else {
            error_log("Clerk view case: Loaded case ID $case_id successfully");
        }
    } catch (Exception $e) {
        error_log("Error fetching case: " . $e->getMessage());
        $error = t('error_db') . ' ' . $e->getMessage();
    }
    
    // Fetch suspects (read-only)
    if ($case) {
        try {
            $stmt = $pdo->prepare("
                SELECT cr.*, cp.role, cp.relationship_to_case 
                FROM case_persons cp
                INNER JOIN criminal_records cr ON cp.record_id = cr.id
                WHERE cp.case_id = ? AND cp.role = 'Suspect'
            ");
            $stmt->execute([$case_id]);
            $suspects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching suspects: " . $e->getMessage());
        }
    }
    
    // Fetch evidence (read-only)
    if ($case) {
        try {
            $stmt = $pdo->prepare("
                SELECT e.*, u.first_name, u.last_name 
                FROM evidence e 
                LEFT JOIN users u ON e.collected_by = u.user_id 
                WHERE e.case_id = ? 
                ORDER BY e.created_at DESC
            ");
            $stmt->execute([$case_id]);
            $evidence = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching evidence: " . $e->getMessage());
        }
    }
    
    // Fetch case notes (read-only)
    if ($case) {
        try {
            $stmt = $pdo->prepare("
                SELECT cn.*, u.first_name, u.last_name 
                FROM case_notes cn
                LEFT JOIN users u ON cn.user_id = u.user_id
                WHERE cn.case_id = ?
                ORDER BY cn.created_at DESC
            ");
            $stmt->execute([$case_id]);
            $case_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching case notes: " . $e->getMessage());
        }
    }
}

// User info for header
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'clerk'
];
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $case ? t('case_file') . ' #' . htmlspecialchars($case['case_number']) : t('view_case'); ?> - <?php echo t('system_name'); ?></title>
    
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            line-height: 1.6;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: #1565c0 !important;
        }
        
        .clerk-badge {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
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
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
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
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
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
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
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
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
            cursor: pointer;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(25, 118, 210, 0.4);
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
            border-left: 4px solid #1976d2;
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
        
        .error-alert {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .error-alert i {
            font-size: 2rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .nav-link-custom {
            background: rgba(255, 255, 255, 0.9);
            color: #1565c0;
            border-radius: 10px;
            padding: 12px 20px;
            margin: 0 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .nav-link-custom:hover {
            background: #1976d2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
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
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-search me-2"></i>
                <?php echo t('system_name'); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> <?php echo t('dashboard'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="search_records.php">
                            <i class="fas fa-search me-1"></i> <?php echo t('back_to_search'); ?>
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Language Selector -->
                    <form method="post" class="me-3">
                        <select name="lang" onchange="this.form.submit()" class="form-select form-select-sm">
                            <option value="en" <?php echo $current_lang=='en'?'selected':''; ?>>English</option>
                            <option value="am" <?php echo $current_lang=='am'?'selected':''; ?>>አማርኛ</option>
                            <option value="om" <?php echo $current_lang=='om'?'selected':''; ?>>Afaan Oromoo</option>
                        </select>
                    </form>
                    
                    <span class="clerk-badge me-3">
                        <i class="fas fa-user-tie me-1"></i>
                        <?php echo t('clerk'); ?> <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('<?php echo t('logout'); ?>?')">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <?php if ($error): ?>
            <!-- Error Display -->
            <div class="row">
                <div class="col-12">
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4><?php echo htmlspecialchars($error); ?></h4>
                        <a href="search_records.php" class="btn btn-primary-custom mt-3">
                            <i class="fas fa-arrow-left me-2"></i><?php echo t('back_to_search'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php elseif ($case): ?>
            <div class="case-container">
                <!-- Case Header -->
                <div class="case-header">
                    <div class="case-title">
                        <h3><i class="fas fa-folder me-3"></i><?php echo t('case_file'); ?>: <?php echo htmlspecialchars($case['case_number']); ?></h3>
                        <p><?php echo sprintf(t('case_type_location'), htmlspecialchars($case['case_type'] ?? t('unknown')), htmlspecialchars($case['location'] ?? t('not_specified'))); ?></p>
                    </div>
                    <div class="case-actions">
                        <a href="search_records.php" class="btn-secondary-custom">
                            <i class="fas fa-arrow-left me-2"></i><?php echo t('back_to_search'); ?>
                        </a>
                        <a href="dashboard.php" class="btn-primary-custom">
                            <i class="fas fa-home me-2"></i><?php echo t('dashboard'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Main Case Grid -->
                <div class="case-grid">
                    <!-- Case Details -->
                    <div class="case-section">
                        <div class="section-header">
                            <h4 class="section-title"><i class="fas fa-info-circle me-2"></i><?php echo t('case_details'); ?></h4>
                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'open')); ?>">
                                <?php echo translateStatus($case['status'] ?? 'open'); ?>
                            </span>
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><?php echo t('case_number'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($case['case_number'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('case_type'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($case['case_type'] ?? t('unknown')); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('date_reported'); ?></div>
                                <div class="info-value"><?php echo !empty($case['date_reported']) ? date('F j, Y', strtotime($case['date_reported'])) : t('not_specified'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('location'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($case['location'] ?? t('not_specified')); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('severity'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($case['severity'] ?? t('not_specified')); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('priority'); ?></div>
                                <div class="info-value"><?php echo htmlspecialchars($case['priority'] ?? t('not_specified')); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('lead_officer'); ?></div>
                                <div class="info-value">
                                    <?php 
                                    if (($case['lead_first_name'] ?? '') && ($case['lead_last_name'] ?? '')) {
                                        echo htmlspecialchars($case['lead_first_name'] . ' ' . $case['lead_last_name']);
                                    } else {
                                        echo t('not_assigned');
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><?php echo t('created_by'); ?></div>
                                <div class="info-value">
                                    <?php 
                                    if (($case['creator_first_name'] ?? '') && ($case['creator_last_name'] ?? '')) {
                                        echo htmlspecialchars($case['creator_first_name'] . ' ' . $case['creator_last_name']);
                                    } else {
                                        echo t('unknown');
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
                                                 alt="Suspect Photo" class="suspect-photo me-3">
                                        <?php else: ?>
                                            <div class="suspect-photo bg-light d-flex align-items-center justify-content-center me-3">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($suspect['first_name'] ?? '') . ' ' . htmlspecialchars($suspect['last_name'] ?? ''); ?></h6>
                                            <p class="text-muted mb-1">
                                                <i class="fas fa-id-card me-1"></i>
                                                <?php echo htmlspecialchars($suspect['national_id'] ?? 'N/A'); ?>
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
                                            <a href="view_criminal_record.php?id=<?php echo $suspect['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> <?php echo t('view_record'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                <p><?php echo t('no_suspects'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Evidence Section -->
                <div class="case-section">
                    <div class="section-header">
                        <h4 class="section-title"><i class="fas fa-fingerprint me-2"></i><?php echo t('evidence'); ?></h4>
                        <span class="badge bg-success"><?php echo count($evidence); ?> <?php echo t('evidence_items'); ?></span>
                    </div>
                    
                    <?php if (!empty($evidence)): ?>
                        <div class="row">
                            <?php foreach ($evidence as $item): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="evidence-item">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="evidence-type-badge badge-<?php echo htmlspecialchars($item['evidence_type'] ?? 'document'); ?>">
                                                    <?php echo translateEvidenceType($item['evidence_type'] ?? 'document'); ?>
                                                </span>
                                                <small class="text-muted ms-2"><?php echo sprintf(t('evidence_id'), htmlspecialchars($item['id'] ?? 'N/A')); ?></small>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo !empty($item['created_at']) ? date(t('evidence_date'), strtotime($item['created_at'])) : t('unknown'); ?>
                                            </small>
                                        </div>
                                        
                                        <p class="mb-2"><strong><?php echo htmlspecialchars($item['description'] ?? t('description')); ?></strong></p>
                                        
                                        <?php if (!empty($item['file_path'])): ?>
                                            <div class="mb-2">
                                                <?php
                                                $file_ext = pathinfo($item['file_path'], PATHINFO_EXTENSION);
                                                $is_image = in_array(strtolower($file_ext ?? ''), ['jpg', 'jpeg', 'png', 'gif']);
                                                $is_video = in_array(strtolower($file_ext ?? ''), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
                                                ?>
                                                
                                                <?php if ($is_image): ?>
                                                    <img src="../<?php echo htmlspecialchars($item['file_path']); ?>" 
                                                         class="file-preview" alt="Evidence Photo">
                                                <?php elseif ($is_video): ?>
                                                    <div class="file-preview bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-video text-muted fa-2x"></i>
                                                    </div>
                                                    <small class="text-muted d-block mt-1"><?php echo t('video_file'); ?></small>
                                                <?php else: ?>
                                                    <div class="file-preview bg-light d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-file text-muted fa-2x"></i>
                                                    </div>
                                                    <small class="text-muted d-block mt-1"><?php echo t('document'); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="small text-muted">
                                            <div><strong><?php echo t('found'); ?>:</strong> <?php echo htmlspecialchars($item['location_found'] ?? t('not_specified')); ?></div>
                                            <div><strong><?php echo t('collected_by'); ?>:</strong> 
                                                <?php 
                                                if (($item['first_name'] ?? '') && ($item['last_name'] ?? '')) {
                                                    echo htmlspecialchars($item['first_name'] . ' ' . $item['last_name']);
                                                } else {
                                                    echo htmlspecialchars($item['collected_by'] ?? t('unknown'));
                                                }
                                                ?>
                                            </div>
                                            <div><strong><?php echo t('date'); ?>:</strong> <?php echo !empty($item['date_found']) ? date('M j, Y', strtotime($item['date_found'])) : t('not_specified'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p><?php echo t('no_evidence'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Case Notes Section -->
                <div class="case-section">
                    <div class="section-header">
                        <h4 class="section-title"><i class="fas fa-sticky-note me-2"></i><?php echo t('case_notes'); ?></h4>
                        <span class="badge bg-warning text-dark"><?php echo count($case_notes); ?> <?php echo t('notes'); ?></span>
                    </div>
                    
                    <?php if (!empty($case_notes)): ?>
                        <?php foreach ($case_notes as $note): ?>
                            <div class="note-item <?php echo ($note['is_important'] ?? 0) ? 'important' : ''; ?>">
                                <div class="note-header">
                                    <div>
                                        <div class="note-author">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars(($note['first_name'] ?? '') . ' ' . ($note['last_name'] ?? '')); ?>
                                        </div>
                                        <span class="note-type-badge badge-<?php echo htmlspecialchars($note['note_type'] ?? 'general'); ?>">
                                            <?php echo translateNoteType($note['note_type'] ?? 'general'); ?>
                                        </span>
                                        <?php if (($note['is_important'] ?? 0) == 1): ?>
                                            <span class="badge bg-danger ms-1"><?php echo t('important'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="note-date">
                                        <?php echo !empty($note['created_at']) ? date(t('note_date'), strtotime($note['created_at'])) : t('unknown'); ?>
                                    </div>
                                </div>
                                <div class="note-content">
                                    <?php echo nl2br(htmlspecialchars($note['note_text'] ?? '')); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <p><?php echo t('no_notes'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Close modals when clicking outside (if any)
        document.addEventListener('click', function(e) {
            // No modals for clerk, but ready for future
        });
    </script>
</body>
</html>