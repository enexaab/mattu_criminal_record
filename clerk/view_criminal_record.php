<?php
// view_criminal_record.php
// Updated: Simplified query like officers' version (no status filter for now).
// Added linked cases fetch (read-only). Enhanced error handling/logging.
// Accessible only to authenticated clerks. ID from GET param.

// Required includes
require '../includes/auth.php';      // Session and role checks
require '../includes/database.php';  // DB connection
require '../includes/clerk_functions.php'; // Read-only functions (for fallback)

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
        'system_name' => 'Mattu Criminal Records',
        'view_record' => 'View Record',
        'criminal_record_details' => 'Criminal Record Details',
        'error_no_id' => 'No record ID provided.',
        'error_not_found' => 'Record not found.',
        'error_db' => 'Database error:',
        'back_to_search' => 'Back to Search',
        'dashboard' => 'Dashboard',
        'search_view' => 'Search & View Records',
        'clerk_badge' => 'Clerk',
        'logout_confirm' => 'Are you sure you want to logout?',
        'logout' => 'Logout',
        'record_id' => 'Record ID',
        'personal_information' => 'Personal Information',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'height' => 'Height',
        'weight' => 'Weight',
        'not_specified' => 'Not specified',
        'identification' => 'Identification',
        'national_id' => 'National ID',
        'record_number' => 'Record Number',
        'eye_color' => 'Eye Color',
        'hair_color' => 'Hair Color',
        'n_a' => 'N/A',
        'distinguishing_marks' => 'Distinguishing Marks',
        'audit_information' => 'Audit Information',
        'created_by' => 'Created By',
        'system' => 'System',
        'created' => 'Created',
        'last_updated' => 'Last Updated',
        'linked_cases' => 'Linked Cases',
        'case_number' => 'Case Number',
        'case_type' => 'Case Type',
        'unknown_type' => 'Unknown Type',
        'status' => 'Status',
        'pending' => 'Pending',
        'date_reported' => 'Date Reported',
        'unknown_date' => 'Unknown date',
        'view_case' => 'View Case',
        'first_offender' => 'First Offender',
        'repeat' => 'Repeat',
        'wanted' => 'Wanted',
    ],
    'am' => [
        'system_name' => 'ማቱ የወንጀል መዝገቦች',
        'view_record' => 'መዝገብ ተመልከት',
        'criminal_record_details' => 'የወንጀል መዝገብ ዝርዝሮች',
        'error_no_id' => 'የመዝገብ መለያ አልተገኘም።',
        'error_not_found' => 'መዝገብ አልተገኘም።',
        'error_db' => 'የውሂብ ማያ ስህተት፡',
        'back_to_search' => 'ወደ ፍለጋ',
        'dashboard' => 'ዳሽቦርድ',
        'search_view' => 'ፍለጋ እና መዝገቦችን ተመልከት',
        'clerk_badge' => 'ጸሐፊ',
        'logout_confirm' => 'እውቅና ትወጣለህ?',
        'logout' => 'ውጣ',
        'record_id' => 'የመዝገብ መለያ',
        'personal_information' => 'የግል መረጃ',
        'date_of_birth' => 'የልደት ቀን',
        'gender' => 'ጾታ',
        'height' => 'ግድግዳ',
        'weight' => 'ክብደት',
        'not_specified' => 'አልተገለጸም',
        'identification' => 'መለያ',
        'national_id' => 'ብሔራዊ መለያ',
        'record_number' => 'የመዝገብ ቁጥር',
        'eye_color' => 'የዓይን ቀለም',
        'hair_color' => 'የፀጉር ቀለም',
        'n_a' => 'አይደለም',
        'distinguishing_marks' => 'የማወቅ ምልክቶች',
        'audit_information' => 'የቁጥጥር መረጃ',
        'created_by' => 'ተፈጠረ በ',
        'system' => 'ስርዓት',
        'created' => 'ተፈጠረ',
        'last_updated' => 'በመጨረሻ የተዘመነ',
        'linked_cases' => 'የተገናኙ ጉዳዮች',
        'case_number' => 'የጉዳይ ቁጥር',
        'case_type' => 'የጉዳይ አይነት',
        'unknown_type' => 'ያልታወቀ አይነት',
        'status' => 'ሁኔታ',
        'pending' => 'ተረጋጋ',
        'date_reported' => 'የተወሰነ ቀን',
        'unknown_date' => 'ያልታወቀ ቀን',
        'view_case' => 'ጉዳይ ተመልከት',
        'first_offender' => 'የመጀመሪያ ጥፋት',
        'repeat' => 'ድግግሞሽ',
        'wanted' => 'የሚፈለግ',
    ],
    'om' => [
        'system_name' => 'Sisteemi Mattu Diinagdee',
        'view_record' => 'Diinagdee Argisi',
        'criminal_record_details' => 'Qorannoo Diinagdee Ummataa',
        'error_no_id' => 'ID Diinagdee hin argamu.',
        'error_not_found' => 'Diinagdee hin argamu.',
        'error_db' => 'Saaqaa database:',
        'back_to_search' => 'Gadii Fufiisi',
        'dashboard' => 'Dashboardii',
        'search_view' => 'Gadisi Mattu Diinagdeewwan Argisi',
        'clerk_badge' => 'Karraa',
        'logout_confirm' => 'Fufiisi barbaadeti?',
        'logout' => 'Fufiisi',
        'record_id' => 'ID Diinagdee',
        'personal_information' => 'Qorannoo Gargaarsa',
        'date_of_birth' => 'Guyaa Lakkoofsa',
        'gender' => 'Awwaalummaa',
        'height' => 'Dhibbaa',
        'weight' => 'Miisa',
        'not_specified' => 'Hin Beekamu',
        'identification' => 'Meeraa',
        'national_id' => 'ID Qaama Oromiyaa',
        'record_number' => 'Naama Diinagdee',
        'eye_color' => 'Qilleensa Iyyaa',
        'hair_color' => 'Qilleensa Soogiddoo',
        'n_a' => 'N/A',
        'distinguishing_marks' => 'Mallattoolee Meeraa',
        'audit_information' => 'Qorannoo Qorannoo',
        'created_by' => 'Hojjetu',
        'system' => 'Sisteemi',
        'created' => 'Hojjetu',
        'last_updated' => 'Ijaarsa Kan Qabame',
        'linked_cases' => 'Caasoota Gaha',
        'case_number' => 'Naama Caasaa',
        'case_type' => 'Aangoo Caasaa',
        'unknown_type' => 'Aangoo Hin Beekamu',
        'status' => 'Haala',
        'pending' => 'Barumsa',
        'date_reported' => 'Guyaa Barame',
        'unknown_date' => 'Guyaa hin beekamu',
        'view_case' => 'Caasaa Argisi',
        'first_offender' => 'Ummata Jalqabaa',
        'repeat' => 'Dhiiga',
        'wanted' => 'Barbaachisu',
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
        'en' => ['first-offender' => 'First Offender', 'repeat' => 'Repeat', 'wanted' => 'Wanted'],
        'am' => ['first-offender' => 'የመጀመሪያ ጥፋት', 'repeat' => 'ድግግሞሽ', 'wanted' => 'የሚፈለግ'],
        'om' => ['first-offender' => 'Ummata Jalqabaa', 'repeat' => 'Dhiiga', 'wanted' => 'Barbaachisu'],
    ];
    global $current_lang;
    $clean_status = strtolower(str_replace(' ', '-', $status ?? 'first-offender'));
    return $statuses[$current_lang][$clean_status] ?? $status;
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

// Get record ID from URL
$record_id = intval($_GET['id'] ?? 0);  // Sanitize as int
$record = null;
$cases = [];
$error = '';

if ($record_id <= 0) {
    $error = t('error_no_id');
} else {
    // Fetch record (simplified like officers' version: no status filter)
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM criminal_records 
            WHERE id = ?
        ");
        $stmt->execute([$record_id]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$record) {
            error_log("Clerk view: No record found for ID $record_id");
            $error = t('error_not_found');
        } else {
            error_log("Clerk view: Loaded record ID $record_id successfully");
        }
    } catch (Exception $e) {
        error_log("Error fetching record: " . $e->getMessage());
        $error = t('error_db') . ' ' . $e->getMessage();
    }
    
    // Get cases linked to this criminal record (read-only, like officers' version)
    if ($record) {
        try {
            $casesTableExists = $pdo->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
            $caseFilesTableExists = $pdo->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
            
            if ($casesTableExists) {
                $stmt = $pdo->prepare("
                    SELECT c.* 
                    FROM cases c
                    INNER JOIN case_persons cp ON c.id = cp.case_id
                    WHERE cp.record_id = ?
                    ORDER BY c.date_reported DESC
                ");
                $stmt->execute([$record_id]);
                $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($caseFilesTableExists) {
                $stmt = $pdo->prepare("
                    SELECT cf.* 
                    FROM case_files cf
                    INNER JOIN case_persons cp ON cf.id = cp.case_id
                    WHERE cp.record_id = ?
                    ORDER BY cf.date_reported DESC
                ");
                $stmt->execute([$record_id]);
                $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error fetching cases: " . $e->getMessage());
            // No error shown to user (read-only); cases = []
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
    <!-- Cache control -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title><?php echo $record ? htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) : t('view_record'); ?> - <?php echo t('system_name'); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts for Amharic support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am'): ?>
    <style>
        body {
            font-family: 'Noto Sans Ethiopic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
    <?php endif; ?>
    
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.2);
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
        
        .view-container {
            padding: 30px 0;
        }
        
        .record-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            color: white;
            padding: 20px 25px;
            border: none;
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .card-body-custom {
            padding: 30px;
        }
        
        .record-photo {
            width: 200px;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid #e3f2fd;
        }
        
        .info-label {
            font-weight: 600;
            color: #1976d2;
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-first-offender {
            background: #d4edda;
            color: #155724;
        }
        
        .status-repeat {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-wanted {
            background: #f8d7da;
            color: #721c24;
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
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
            color: white;
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
        
        .case-item {
            border-left: 4px solid #1976d2;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .view-container {
                padding: 15px 0;
            }
            
            .card-body-custom {
                padding: 20px;
            }
            
            .record-photo {
                width: 150px;
                height: 180px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar: Clerk-specific -->
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
                        <a class="nav-link-custom active" href="search_records.php">
                            <i class="fas fa-search me-1"></i> <?php echo t('search_view'); ?>
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
                        <?php echo t('clerk_badge'); ?> <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('<?php echo t('logout_confirm'); ?>')">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main View Content -->
    <div class="view-container">
        <div class="container">
            <?php if ($error): ?>
                <!-- Error Display -->
                <div class="row">
                    <div class="col-12">
                        <div class="error-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h4><?php echo htmlspecialchars($error); ?></h4>
                            <a href="search_records.php" class="btn btn-primary mt-3">
                                <i class="fas fa-arrow-left me-2"></i><?php echo t('back_to_search'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php elseif ($record): ?>
                <!-- Record View -->
                <div class="row">
                    <div class="col-12">
                        <div class="record-card">
                            <div class="card-header-custom d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?> - <?php echo t('criminal_record_details'); ?>
                                </span>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? 'first-offender')); ?>">
                                    <?php echo translateStatus($record['status'] ?? 'first-offender'); ?>
                                </span>
                            </div>
                            <div class="card-body-custom">
                                <div class="row">
                                    <!-- Photo Section -->
                                    <div class="col-md-3 text-center">
                                        <?php if (!empty($record['photo'])): ?>
                                            <img src="../<?php echo htmlspecialchars($record['photo']); ?>" 
                                                 alt="Record Photo" class="record-photo">
                                        <?php else: ?>
                                            <div class="record-photo bg-light d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <p class="text-muted mt-2"><?php echo t('record_id'); ?>: <?php echo htmlspecialchars($record['id']); ?></p>
                                    </div>
                                    
                                    <!-- Basic Info -->
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="info-section">
                                                    <span class="info-label"><i class="fas fa-user me-2"></i><?php echo t('personal_information'); ?></span>
                                                    <div class="info-value"><?php echo htmlspecialchars($record['first_name'] ?? '') . ' ' . htmlspecialchars($record['last_name'] ?? ''); ?></div>
                                                    <div class="info-value">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        <?php echo !empty($record['date_of_birth']) ? htmlspecialchars($record['date_of_birth']) : t('not_specified'); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-venus-mars me-1"></i>
                                                        <?php echo htmlspecialchars($record['gender'] ?? t('not_specified')); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-ruler-vertical me-1"></i>
                                                        <?php echo t('height'); ?>: <?php echo htmlspecialchars($record['height'] ?? t('not_specified')); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-weight-hanging me-1"></i>
                                                        <?php echo t('weight'); ?>: <?php echo htmlspecialchars($record['weight'] ?? t('not_specified')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="info-section">
                                                    <span class="info-label"><i class="fas fa-id-card me-2"></i><?php echo t('identification'); ?></span>
                                                    <div class="info-value">
                                                        <i class="fas fa-id-card me-1"></i>
                                                        <?php echo t('national_id'); ?>: <?php echo htmlspecialchars($record['national_id'] ?? t('n_a')); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-hashtag me-1"></i>
                                                        <?php echo t('record_number'); ?>: <?php echo htmlspecialchars($record['record_number'] ?? t('n_a')); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-eye me-1"></i>
                                                        <?php echo t('eye_color'); ?>: <?php echo htmlspecialchars($record['eye_color'] ?? t('not_specified')); ?>
                                                    </div>
                                                    <div class="info-value">
                                                        <i class="fas fa-palette me-1"></i>
                                                        <?php echo t('hair_color'); ?>: <?php echo htmlspecialchars($record['hair_color'] ?? t('not_specified')); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Additional Details -->
                                        <?php if (!empty($record['distinguishing_marks'])): ?>
                                            <div class="info-section">
                                                <span class="info-label"><i class="fas fa-exclamation-triangle me-2"></i><?php echo t('distinguishing_marks'); ?></span>
                                                <div class="info-value"><?php echo htmlspecialchars($record['distinguishing_marks']); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Audit Info -->
                                        <div class="info-section">
                                            <span class="info-label"><i class="fas fa-info-circle me-2"></i><?php echo t('audit_information'); ?></span>
                                            <div class="info-value">
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo t('created_by'); ?>: <?php echo htmlspecialchars($record['created_by_username'] ?? t('system')); ?>
                                            </div>
                                            <div class="info-value">
                                                <i class="fas fa-calendar-check me-1"></i>
                                                <?php echo t('created'); ?>: <?php echo !empty($record['created_at']) ? date('F j, Y g:i A', strtotime($record['created_at'])) : t('n_a'); ?>
                                            </div>
                                            <?php if (!empty($record['updated_at'])): ?>
                                                <div class="info-value">
                                                    <i class="fas fa-sync me-1"></i>
                                                    <?php echo t('last_updated'); ?>: <?php echo date('F j, Y g:i A', strtotime($record['updated_at'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons: Read-only -->
                                <div class="text-center mt-4">
                                    <a href="search_records.php" class="btn btn-primary me-3">
                                        <i class="fas fa-arrow-left me-2"></i><?php echo t('back_to_search'); ?>
                                    </a>
                                    <a href="dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-home me-2"></i><?php echo t('dashboard'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Linked Cases (Read-only) -->
            <?php if (!empty($cases)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="record-card">
                            <div class="card-header-custom">
                                <i class="fas fa-folder me-2"></i>
                                <?php echo t('linked_cases'); ?> (<?php echo count($cases); ?>)
                            </div>
                            <div class="card-body-custom">
                                <?php foreach ($cases as $case): ?>
                                    <div class="case-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <strong><?php echo htmlspecialchars($case['case_number'] ?? t('n_a')); ?></strong>
                                            </div>
                                            <div class="col-md-3">
                                                <?php echo htmlspecialchars($case['case_type'] ?? t('unknown_type')); ?>
                                            </div>
                                            <div class="col-md-2">
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'pending')); ?>">
                                                    <?php echo htmlspecialchars($case['status'] ?? t('pending')); ?>
                                                </span>
                                            </div>
                                            <div class="col-md-2">
                                                <?php echo !empty($case['date_reported']) ? htmlspecialchars($case['date_reported']) : t('unknown_date'); ?>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <a href="view_case.php?id=<?php echo $case['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> <?php echo t('view_case'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>