<?php
// search_records.php
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
        'title' => 'Search Criminal Records - Mattu City Criminal Management System',
        'mattu_criminal_records' => 'Mattu Criminal Records',
        'dashboard' => 'Dashboard',
        'search_records' => 'Search Records',
        'my_cases' => 'My Cases',
        'add_record' => 'Add Record',
        'logout' => 'Logout',
        'officer' => 'Officer',
        'search_criminal_records' => 'Search Criminal Records',
        'search_type' => 'Search Type',
        'name' => 'Name',
        'national_id' => 'National ID',
        'case_number' => 'Case Number',
        'search_query' => 'Search Query',
        'enter_search_query' => 'Enter search query...',
        'search' => 'Search',
        'search_results' => 'Search Results',
        'found_records' => 'Found {count} record(s) for "{query}"',
        'date_of_birth' => 'Date of Birth',
        'not_specified' => 'Not specified',
        'gender' => 'Gender',
        'status' => 'Status',
        'first_offender' => 'First Offender',
        'repeat' => 'Repeat Offender',
        'wanted' => 'Wanted',
        'view' => 'View',
        'case' => 'Case',
        'no_records_found' => 'No Records Found',
        'no_match_criteria' => 'No criminal records match your search criteria. Try adjusting your search terms.',
        'add_new_record' => 'Add New Record',
        'search_tips' => 'Search Tips',
        'search_by_name' => 'Search by Name',
        'search_name_desc' => 'Search using first name, last name, or both',
        'search_by_national_id' => 'Search by National ID',
        'search_national_id_desc' => 'Find records using national identification number',
        'search_by_case' => 'Search by Case',
        'search_case_desc' => 'Find criminals associated with specific case numbers',
        'records' => 'records',
    ],
    'am' => [
        'title' => 'የወደንጀላዊ መዝገቦች ፍለጋ - ማቱ ከተማ የወደንጀል አስተዳደር ስርዓት',
        'mattu_criminal_records' => 'ማቱ የወደንጀላዊ መዝገቦች',
        'dashboard' => 'ጃሽባር',
        'search_records' => 'መዝገቦች ይፈልጉ',
        'my_cases' => 'የኔ ጉዳዮች',
        'add_record' => 'መዝገብ ጨምር',
        'logout' => 'ውጣ',
        'officer' => 'መኮንን',
        'search_criminal_records' => 'የወደንጀላዊ መዝገቦች ይፈልጉ',
        'search_type' => 'የፍለጋ አይነት',
        'name' => 'ስም',
        'national_id' => 'ብሔራዊ መለያ',
        'case_number' => 'የጉዳይ ቁጥር',
        'search_query' => 'የፍለጋ ጥያቄ',
        'enter_search_query' => 'የፍለጋ ጥያቄ ያስገቡ...',
        'search' => 'ይፈልጉ',
        'search_results' => 'የፍለጋ ውጤቶች',
        'found_records' => '{count} መዝገብ(ዎች) ለ \"{query}\" ተገኝተው',
        'date_of_birth' => 'የልደት ቀን',
        'not_specified' => 'የሉም',
        'gender' => 'ጾታ',
        'status' => 'ሁኔታ',
        'first_offender' => 'የመጀመሪያ ጥፋተኝ',
        'repeat' => 'የተደጋግፈ',
        'wanted' => 'የሚፈለግ',
        'view' => 'ይመልከቱ',
        'case' => 'ጉዳይ',
        'no_records_found' => 'መዝገብ አልተገኘም',
        'no_match_criteria' => 'የወደንጀላዊ መዝገቦች የፍለጋዎ መስፈርት አይጋጥሙም። የፍለጋዎን ቃላት ይለውጡ።',
        'add_new_record' => 'አዲስ መዝገብ ጨምር',
        'search_tips' => 'የፍለጋ ምክሮች',
        'search_by_name' => 'በስም ይፈልጉ',
        'search_name_desc' => 'በመጀመሪያ ስም፣ የመጨረሻ ስም ወይም ሁለቱም ይፈልጉ',
        'search_by_national_id' => 'በብሔራዊ መለያ ይፈልጉ',
        'search_national_id_desc' => 'በብሔራዊ መለያ ቁጥር መዝገቦችን ይፈልጉ',
        'search_by_case' => 'በጉዳይ ይፈልጉ',
        'search_case_desc' => 'በተወሰኑ የጉዳይ ቁጥሮች ጋር የተገናኙ ወደንጀላዊ አባላትን ይፈልጉ',
        'records' => 'መዝገቦች',
    ],
    'om' => [
        'title' => 'Qoricha Diinagdee Gammachuu - Sisteemi Diinagdee Mattu Kuta',
        'mattu_criminal_records' => 'Qoricha Diinagdee Mattu',
        'dashboard' => 'Dashiboard',
        'search_records' => 'Qoricha Diinagdee Gammachuu',
        'my_cases' => 'Caasaa Kee',
        'add_record' => 'Qoricha Qabuu',
        'logout' => 'Deebii',
        'officer' => 'Meekoonnin',
        'search_criminal_records' => 'Qoricha Diinagdee Gammachuu',
        'search_type' => 'Aangoo Qoricha',
        'name' => 'Maatii',
        'national_id' => 'ID Naamaa',
        'case_number' => 'Naama Caasaa',
        'search_query' => 'Qoricha Gammachuu',
        'enter_search_query' => 'Qoricha gammachuu argisi...',
        'search' => 'Gammachuu',
        'search_results' => 'Qoricha Wojjii',
        'found_records' => '{count} qoricha(s) keessatti \"{query}\" argame',
        'date_of_birth' => 'Guyyaa Guyyaa',
        'not_specified' => 'Hin Taane',
        'gender' => 'Aangoo',
        'status' => 'Hakkina',
        'first_offender' => 'Qoricha Qabuu',
        'repeat' => 'Qoricha Deebii',
        'wanted' => 'Qoricha Qabuu',
        'view' => 'Argisi',
        'case' => 'Caasaa',
        'no_records_found' => 'Qoricha Hin Arganne',
        'no_match_criteria' => 'Qoricha diinagdee qoricha qoricha hin taane. Qoricha qoricha argisi.',
        'add_new_record' => 'Qoricha Qabuu Argisi',
        'search_tips' => 'Qoricha Mikkirroota',
        'search_by_name' => 'Maatii Qoricha',
        'search_name_desc' => 'Maatii qabuu, maatii deebii, ykn hunda qoricha',
        'search_by_national_id' => 'ID Naamaa Qoricha',
        'search_national_id_desc' => 'ID naamaa qoricha qoricha',
        'search_by_case' => 'Caasaa Qoricha',
        'search_case_desc' => 'Naama caasaa qoricha qoricha diinagdee',
        'records' => 'Qoricha',
    ],
];
function t($key) {
    global $translations, $current_lang, $search_results, $search_query;
    $trans = $translations[$current_lang][$key] ?? $key;
    // Replace placeholders if any
    if (strpos($trans, '{') !== false) {
        $trans = str_replace('{count}', count($search_results ?? []), $trans);
        $trans = str_replace('{query}', htmlspecialchars($search_query ?? ''), $trans);
    }
    return $trans;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Initialize variables
$search_results = [];
$search_performed = false;
$search_query = '';
$search_type = 'name';

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_query = trim($_POST['search_query'] ?? '');
    $search_type = $_POST['search_type'] ?? 'name';
    $search_performed = true;
    
    if (!empty($search_query)) {
        $database = new Database();
        $db = $database->getConnection();
        
        try {
            switch ($search_type) {
                case 'national_id':
                    $stmt = $db->prepare("
                        SELECT * FROM criminal_records 
                        WHERE national_id LIKE ? 
                        ORDER BY created_at DESC
                    ");
                    $stmt->execute(["%$search_query%"]);
                    break;
                    
                case 'name':
                    $stmt = $db->prepare("
                        SELECT * FROM criminal_records 
                        WHERE first_name LIKE ? OR last_name LIKE ? 
                        ORDER BY created_at DESC
                    ");
                    $stmt->execute(["%$search_query%", "%$search_query%"]);
                    break;
                    
                case 'case_number':
                    // Search cases and link to criminal records
                    $stmt = $db->prepare("
                        SELECT cr.*, c.case_number 
                        FROM criminal_records cr
                        INNER JOIN case_persons cp ON cr.id = cp.record_id
                        INNER JOIN cases c ON cp.case_id = c.id
                        WHERE c.case_number LIKE ?
                        ORDER BY cr.created_at DESC
                    ");
                    $stmt->execute(["%$search_query%"]);
                    break;
                    
                default:
                    $stmt = $db->prepare("
                        SELECT * FROM criminal_records 
                        WHERE first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ?
                        ORDER BY created_at DESC
                    ");
                    $stmt->execute(["%$search_query%", "%$search_query%", "%$search_query%"]);
            }
            
            $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $search_error = t('error_search') . ": " . $e->getMessage();
        }
    }
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
    <title><?php echo t('title'); ?></title>
    
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
        
        .navbar-brand {
            font-weight: 700;
            color: #2c3e50 !important;
        }
        
        .officer-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .search-container {
            padding: 30px 0;
        }
        
        .search-card {
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
        
        .search-form {
            background: rgba(248, 249, 250, 0.5);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
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
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .result-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .record-photo {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
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
        
        .search-stats {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 15px;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .no-results {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        @media (max-width: 768px) {
            .search-container {
                padding: 15px 0;
            }
            
            .card-body-custom {
                padding: 20px;
            }
            
            .record-photo {
                width: 60px;
                height: 60px;
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
                        <a class="nav-link-custom active" href="search_records.php">
                            <i class="fas fa-search me-1"></i> <?php echo t('search_records'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="manage_cases.php">
                            <i class="fas fa-folder-open me-1"></i> <?php echo t('my_cases'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="add_criminal_record.php">
                            <i class="fas fa-user-plus me-1"></i> <?php echo t('add_record'); ?>
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
    
    <!-- Main Search Content -->
    <div class="search-container">
        <div class="container">
            <!-- Search Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="search-card">
                        <div class="card-header-custom">
                            <i class="fas fa-search me-2"></i><?php echo t('search_criminal_records'); ?>
                        </div>
                        <div class="card-body-custom">
                            <!-- Search Form -->
                            <form method="POST" class="search-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold"><?php echo t('search_type'); ?></label>
                                        <select name="search_type" class="form-select form-control-custom">
                                            <option value="name" <?php echo $search_type === 'name' ? 'selected' : ''; ?>><?php echo t('name'); ?></option>
                                            <option value="national_id" <?php echo $search_type === 'national_id' ? 'selected' : ''; ?>><?php echo t('national_id'); ?></option>
                                            <option value="case_number" <?php echo $search_type === 'case_number' ? 'selected' : ''; ?>><?php echo t('case_number'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><?php echo t('search_query'); ?></label>
                                        <input type="text" name="search_query" class="form-control form-control-custom" 
                                               placeholder="<?php echo t('enter_search_query'); ?>" value="<?php echo htmlspecialchars($search_query); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="search" class="btn btn-primary-custom w-100">
                                            <i class="fas fa-search me-2"></i><?php echo t('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <?php if ($search_performed): ?>
                                <!-- Search Statistics -->
                                <div class="search-stats">
                                    <h5 class="mb-1">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        <?php echo t('search_results'); ?>
                                    </h5>
                                    <p class="mb-0">
                                        <?php echo t('found_records'); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search Results -->
            <?php if ($search_performed): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="search-card">
                            <div class="card-header-custom d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-list me-2"></i>
                                    <?php echo t('search_results'); ?>
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?php echo count($search_results); ?> <?php echo t('records'); ?>
                                </span>
                            </div>
                            <div class="card-body-custom">
                                <?php if (!empty($search_results)): ?>
                                    <?php foreach ($search_results as $record): ?>
                                        <div class="result-card">
                                            <div class="row align-items-center">
                                                <div class="col-md-1 text-center">
                                                    <?php if (!empty($record['photo'])): ?>
                                                        <img src="../<?php echo htmlspecialchars($record['photo']); ?>" 
                                                             alt="Photo" class="record-photo">
                                                    <?php else: ?>
                                                        <div class="record-photo bg-light d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-user text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-3">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-id-card me-1"></i>
                                                        <?php echo htmlspecialchars($record['national_id']); ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('date_of_birth'); ?></small>
                                                    <div><?php echo !empty($record['date_of_birth']) ? htmlspecialchars($record['date_of_birth']) : t('not_specified'); ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('gender'); ?></small>
                                                    <div><?php echo htmlspecialchars($record['gender'] ?? t('not_specified')); ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('status'); ?></small>
                                                    <div>
                                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? 'first-offender')); ?>">
                                                            <?php echo htmlspecialchars($record['status'] ?? t('first_offender')); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group">
                                                        <a href="view_criminal_record.php?id=<?php echo $record['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> <?php echo t('view'); ?>
                                                        </a>
                                                        <a href="create_case.php?record_id=<?php echo $record['id']; ?>" 
                                                           class="btn btn-sm btn-success">
                                                            <i class="fas fa-plus"></i> <?php echo t('case'); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-results">
                                        <i class="fas fa-search"></i>
                                        <h4><?php echo t('no_records_found'); ?></h4>
                                        <p><?php echo t('no_match_criteria'); ?></p>
                                        <div class="mt-3">
                                            <a href="add_criminal_record.php" class="btn btn-primary">
                                                <i class="fas fa-user-plus me-2"></i><?php echo t('add_new_record'); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Quick Search Tips -->
                <div class="row">
                    <div class="col-12">
                        <div class="search-card">
                            <div class="card-header-custom">
                                <i class="fas fa-lightbulb me-2"></i><?php echo t('search_tips'); ?>
                            </div>
                            <div class="card-body-custom">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-3 me-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <h6><?php echo t('search_by_name'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('search_name_desc'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle p-3 me-3">
                                                <i class="fas fa-id-card text-white"></i>
                                            </div>
                                            <div>
                                                <h6><?php echo t('search_by_national_id'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('search_national_id_desc'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle p-3 me-3">
                                                <i class="fas fa-folder text-white"></i>
                                            </div>
                                            <div>
                                                <h6><?php echo t('search_by_case'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('search_case_desc'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-focus on search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search_query"]');
            if (searchInput) {
                searchInput.focus();
            }
        });
        
        // Clear search when changing search type
        document.querySelector('select[name="search_type"]').addEventListener('change', function() {
            document.querySelector('input[name="search_query"]').value = '';
        });
    </script>
</body>
</html>