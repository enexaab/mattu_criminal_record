<?php
// view_criminal_record.php
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
        'title' => '{name} - Criminal Record - Mattu City Criminal Management System',
        'mattu_criminal_records' => 'Mattu Criminal Records',
        'dashboard' => 'Dashboard',
        'search' => 'Search',
        'view_record' => 'View Record',
        'logout' => 'Logout',
        'criminal_record_details' => 'Criminal Record Details',
        'add_case' => 'Add Case',
        'back_to_search' => 'Back to Search',
        'record_id' => 'Record ID',
        'full_name' => 'Full Name',
        'national_id' => 'National ID',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'status' => 'Status',
        'height' => 'Height',
        'weight' => 'Weight',
        'eye_color' => 'Eye Color',
        'hair_color' => 'Hair Color',
        'distinguishing_marks' => 'Distinguishing Marks',
        'record_created' => 'Record Created',
        'last_updated' => 'Last Updated',
        'not_specified' => 'Not specified',
        'none_recorded' => 'None recorded',
        'unknown' => 'Unknown',
        'linked_cases' => 'Linked Cases ({count})',
        'case_number' => 'Case Number',
        'case_type' => 'Case Type',
        'date_reported' => 'Date Reported',
        'view_case' => 'View Case',
        'unknown_type' => 'Unknown Type',
        'unknown_date' => 'Unknown date',
    ],
    'am' => [
        'title' => '{name} - የወደንጀላዊ መዝገብ - ማቱ ከተማ የወደንጀል አስተዳደር ስርዓት',
        'mattu_criminal_records' => 'ማቱ የወደንጀላዊ መዝገቦች',
        'dashboard' => 'ጃሽባር',
        'search' => 'ፍለጋ',
        'view_record' => 'መዝገብ ይመልከቱ',
        'logout' => 'ውጣ',
        'criminal_record_details' => 'የወደንጀላዊ መዝገብ ዝርዝሮች',
        'add_case' => 'ጉዳይ ጨምር',
        'back_to_search' => 'ወደ ፍለጋ',
        'record_id' => 'የመዝገብ መለዤ',
        'full_name' => 'ሙሉ ስም',
        'national_id' => 'ብሔራዊ መለያ',
        'date_of_birth' => 'የልደት ቀን',
        'gender' => 'ጾታ',
        'status' => 'ሁኔታ',
        'height' => 'ጭነት',
        'weight' => 'ክብደት',
        'eye_color' => 'የዓይን ቀለም',
        'hair_color' => 'የሰውር ቀለም',
        'distinguishing_marks' => 'የማወቅ ምልክቶች',
        'record_created' => 'መዝገብ ተፈጠረ',
        'last_updated' => 'የመጨረሻ ዝውውር',
        'not_specified' => 'የሉም',
        'none_recorded' => 'የሉም የተመዘገበ',
        'unknown' => 'የለም የሚታወቅ',
        'linked_cases' => 'የተገናኙ ጉዳዮች ({count})',
        'case_number' => 'የጉዳይ ቁጥር',
        'case_type' => 'የጉዳይ አይነት',
        'date_reported' => 'የተአስረው ቀን',
        'view_case' => 'ጉዳይ ይመልከቱ',
        'unknown_type' => 'የለም አይነት',
        'unknown_date' => 'የለም ቀን',
    ],
    'om' => [
        'title' => '{name} - Qoricha Diinagdee - Sisteemi Diinagdee Mattu Kuta',
        'mattu_criminal_records' => 'Qoricha Diinagdee Mattu',
        'dashboard' => 'Dashiboard',
        'search' => 'Gammachuu',
        'view_record' => 'Qoricha Argisi',
        'logout' => 'Deebii',
        'criminal_record_details' => 'Qoricha Zariya',
        'add_case' => 'Caasaa Qabuu',
        'back_to_search' => 'Deebii Gammachuu',
        'record_id' => 'ID Qoricha',
        'full_name' => 'Maatii',
        'national_id' => 'ID Naamaa',
        'date_of_birth' => 'Guyyaa Guyyaa',
        'gender' => 'Aangoo',
        'status' => 'Hakkina',
        'height' => 'Guyyaa',
        'weight' => 'Guyyaa Guyyaa',
        'eye_color' => 'Ija Guyyaa',
        'hair_color' => 'Ija Guyyaa',
        'distinguishing_marks' => 'Guyyaa Guyyaa',
        'record_created' => 'Qoricha Qabuu',
        'last_updated' => 'Guyyaa Guyyaa',
        'not_specified' => 'Hin Taane',
        'none_recorded' => 'Hin Qabne',
        'unknown' => 'Hin Beekamu',
        'linked_cases' => 'Qoricha Gammachu ({count})',
        'case_number' => 'Naama Caasaa',
        'case_type' => 'Aangoo Caasaa',
        'date_reported' => 'Guyyaa Guyyaa',
        'view_case' => 'Caasaa Argisi',
        'unknown_type' => 'Aangoo Hin Beekamu',
        'unknown_date' => 'Guyyaa Hin Beekamu',
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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if record ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: search_records.php");
    exit();
}

$record_id = intval($_GET['id']);

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Fetch criminal record from database
$database = new Database();
$db = $database->getConnection();

try {
    $stmt = $db->prepare("
        SELECT * FROM criminal_records 
        WHERE id = ?
    ");
    $stmt->execute([$record_id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        header("Location: search_records.php?error=Record not found");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error fetching record: " . $e->getMessage());
    header("Location: search_records.php?error=Database error");
    exit();
}

// Get cases linked to this criminal record
$cases = [];
try {
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    
    if ($casesTableExists) {
        $stmt = $db->prepare("
            SELECT c.* 
            FROM cases c
            INNER JOIN case_persons cp ON c.id = cp.case_id
            WHERE cp.record_id = ?
            ORDER BY c.date_reported DESC
        ");
        $stmt->execute([$record_id]);
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif ($caseFilesTableExists) {
        $stmt = $db->prepare("
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
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <!-- Add these cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title', ['name' => htmlspecialchars($record['first_name'] . ' ' . $record['last_name'])]); ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .record-container {
            padding: 30px 0;
        }
        
        .record-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .card-body-custom {
            padding: 25px;
        }
        
        .record-photo {
            width: 200px;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid #e9ecef;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #212529;
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
        
        .nav-link-custom {
            background: rgba(255, 255, 255, 0.9);
            color: #495057;
            border-radius: 10px;
            padding: 12px 20px;
            margin: 0 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .nav-link-custom:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .case-item {
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .record-container {
                padding: 15px 0;
            }
            
            .record-photo {
                width: 150px;
                height: 180px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-shield-alt me-2"></i>
                <?php echo t('mattu_criminal_records'); ?>
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
                            <i class="fas fa-search me-1"></i> <?php echo t('search'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom active" href="#">
                            <i class="fas fa-user me-1"></i> <?php echo t('view_record'); ?>
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="officer-badge me-3">
                        <i class="fas fa-user-shield me-1"></i>
                        <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Record Content -->
    <div class="record-container">
        <div class="container">
            <!-- Criminal Record Details -->
            <div class="row">
                <div class="col-12">
                    <div class="record-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-user me-2"></i>
                                <?php echo t('criminal_record_details'); ?>
                            </span>
                            <div>
                                <a href="create_case.php?record_id=<?php echo $record['id']; ?>" class="btn btn-success btn-sm me-2">
                                    <i class="fas fa-plus me-1"></i> <?php echo t('add_case'); ?>
                                </a>
                                <a href="search_records.php" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> <?php echo t('back_to_search'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="card-body-custom">
                            <div class="row">
                                <!-- Photo Column -->
                                <div class="col-md-3 text-center">
                                    <?php if (!empty($record['photo'])): ?>
                                        <img src="../<?php echo htmlspecialchars($record['photo']); ?>" 
                                             alt="Photo" class="record-photo mb-3">
                                    <?php else: ?>
                                        <div class="record-photo bg-light d-flex align-items-center justify-content-center mb-3">
                                            <i class="fas fa-user text-muted fa-4x"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? 'first-offender')); ?>">
                                            <?php echo htmlspecialchars($record['status'] ?? t('status')); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="text-muted small">
                                        <?php echo t('record_id'); ?>: #<?php echo $record['id']; ?>
                                    </div>
                                </div>
                                
                                <!-- Personal Information -->
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-label"><?php echo t('full_name'); ?></div>
                                            <div class="info-value h5">
                                                <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label"><?php echo t('national_id'); ?></div>
                                            <div class="info-value h5 text-primary">
                                                <?php echo htmlspecialchars($record['national_id']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('date_of_birth'); ?></div>
                                            <div class="info-value">
                                                <?php echo !empty($record['date_of_birth']) ? htmlspecialchars($record['date_of_birth']) : t('not_specified'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('gender'); ?></div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($record['gender'] ?? t('not_specified')); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('status'); ?></div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($record['status'] ?? t('status')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('height'); ?></div>
                                            <div class="info-value">
                                                <?php echo !empty($record['height']) ? htmlspecialchars($record['height']) . ' cm' : t('not_specified'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('weight'); ?></div>
                                            <div class="info-value">
                                                <?php echo !empty($record['weight']) ? htmlspecialchars($record['weight']) . ' kg' : t('not_specified'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('eye_color'); ?></div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($record['eye_color'] ?? t('not_specified')); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <div class="info-label"><?php echo t('hair_color'); ?></div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($record['hair_color'] ?? t('not_specified')); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="info-label"><?php echo t('distinguishing_marks'); ?></div>
                                            <div class="info-value">
                                                <?php echo !empty($record['distinguishing_marks']) ? htmlspecialchars($record['distinguishing_marks']) : t('none_recorded'); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="info-label"><?php echo t('record_created'); ?></div>
                                            <div class="info-value text-muted">
                                                <?php echo !empty($record['created_at']) ? date('F j, Y g:i A', strtotime($record['created_at'])) : t('unknown'); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-label"><?php echo t('last_updated'); ?></div>
                                            <div class="info-value text-muted">
                                                <?php echo !empty($record['updated_at']) ? date('F j, Y g:i A', strtotime($record['updated_at'])) : t('unknown'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Linked Cases -->
            <?php if (!empty($cases)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="record-card">
                        <div class="card-header-custom">
                            <i class="fas fa-folder me-2"></i>
                            <?php echo t('linked_cases', ['count' => count($cases)]); ?>
                        </div>
                        <div class="card-body-custom">
                            <?php foreach ($cases as $case): ?>
                                <div class="case-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <strong><?php echo htmlspecialchars($case['case_number']); ?></strong>
                                        </div>
                                        <div class="col-md-3">
                                            <?php echo htmlspecialchars($case['case_type'] ?? t('unknown_type')); ?>
                                        </div>
                                        <div class="col-md-2">
                                            <span class="badge bg-<?php echo ($case['status'] === 'Closed') ? 'success' : 'warning'; ?>">
                                                <?php echo htmlspecialchars($case['status']); ?>
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
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>