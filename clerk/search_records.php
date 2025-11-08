<?php
// search_records.php
// This file provides a read-only search interface for clerks in the Mattu Criminal Record System.
// Clerks can search and view criminal records/cases but cannot create, edit, or add new records.
// All searches query the real-time database via PDO. Results link to view pages only.

// Required includes for authentication, database connection, and clerk-specific functions
require '../includes/auth.php';      // Handles session validation and login checks
require '../includes/database.php';  // Provides PDO database connection
require '../includes/clerk_functions.php'; // Clerk-specific methods (e.g., search)

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
        'title' => 'Search Criminal Records - Mattu Criminal Record System',
        'navbar_brand' => 'Mattu Criminal Records',
        'dashboard' => 'Dashboard',
        'search_view' => 'Search & View Records',
        'clerk_badge' => 'Clerk',
        'logout' => 'Are you sure you want to logout?',
        'search_header' => 'Search Criminal Records',
        'search_type_label' => 'Search Type',
        'search_query_label' => 'Search Query',
        'search_placeholder' => 'Enter search term...',
        'search_btn' => 'Search',
        'search_results' => 'Search Results',
        'found_records' => 'Found',
        'record(s)' => 'record(s)',
        'for' => 'for',
        'date_of_birth' => 'Date of Birth',
        'gender' => 'Gender',
        'status' => 'Status',
        'view' => 'View',
        'no_records_found' => 'No Records Found',
        'no_match_criteria' => 'No criminal records match your search criteria. Try adjusting your search terms.',
        'search_tips' => 'Search Tips',
        'tip_name' => 'Search by Name',
        'tip_name_desc' => 'Search using first name, last name, or both',
        'tip_id' => 'Search by National ID',
        'tip_id_desc' => 'Find records using national identification number',
        'tip_case' => 'Search by Case',
        'tip_case_desc' => 'Find criminals associated with specific case numbers',
        'error_search' => 'Error performing search:',
        'logout_confirm' => 'Logout',
    ],
    'am' => [
        'title' => 'የወንጀል መዝገቦች ፍለጋ - ማቱ የወንጀል መዝገብ ስርዓት',
        'navbar_brand' => 'ማቱ የወንጀል መዝገቦች',
        'dashboard' => 'ዳሽቦርድ',
        'search_view' => 'ፍለጋ እና መዝገቦችን ተመልከት',
        'clerk_badge' => 'ጸሐፊ',
        'logout' => 'እውቅና ትወጣለህ?',
        'search_header' => 'የወንጀል መዝገቦች ፍለጋ',
        'search_type_label' => 'የፍለጋ አይነት',
        'search_query_label' => 'የፍለጋ ጥያቄ',
        'search_placeholder' => 'የፍለጋ ቃል ያስገቡ...',
        'search_btn' => 'ፍለጋ',
        'search_results' => 'የፍለጋ ውጤቶች',
        'found_records' => 'ተገኝቷል',
        'record(s)' => 'መዝገብ',
        'for' => 'ለ',
        'date_of_birth' => 'የልደት ቀን',
        'gender' => 'ጾታ',
        'status' => 'ሁኔታ',
        'view' => 'ተመልከት',
        'no_records_found' => 'መዝገብ አልተገኘም',
        'no_match_criteria' => 'የወንጀል መዝገቦች ያለውን የፍለጋ መስፈርት አይጋጥሙም። የፍለጋ ቃሎቹን ይቀይሩ።',
        'search_tips' => 'የፍለጋ ምክሮች',
        'tip_name' => 'በስም ፍለጋ',
        'tip_name_desc' => 'በመጀመሪያ ስም፣ የመጨረሻ ስም ወይም ሁለቱ ፍለጋ ያድርጉ',
        'tip_id' => 'በብሔራዊ መለያ ፍለጋ',
        'tip_id_desc' => 'በብሔራዊ መለያ ቁጥር መዝገቦችን ይገኙ',
        'tip_case' => 'በጉዳይ ፍለጋ',
        'tip_case_desc' => 'በተወሰኑ ጉዳይ ቁጥሮች የተገናኙ ወንጀሮችን ይገኙ',
        'error_search' => 'በፍለጋ ላይ ስህተት፡',
        'logout_confirm' => 'ውጣ',
    ],
    'om' => [
        'title' => 'Gadii Diinagdeewwan - Sisteemi Mattu Diinagdee',
        'navbar_brand' => 'Sisteemi Mattu Diinagdee',
        'dashboard' => 'Dashboardii',
        'search_view' => 'Gadisi Mattu Diinagdeewwan Argisi',
        'clerk_badge' => 'Karraa',
        'logout' => 'Fufiisi barbaadeti?',
        'search_header' => 'Gadii Diinagdee',
        'search_type_label' => 'Aangoo Gadisi',
        'search_query_label' => 'Qorannoo Gadisi',
        'search_placeholder' => 'Termii gadisi fidu...',
        'search_btn' => 'Gadisi',
        'search_results' => 'Waliin Gadii',
        'found_records' => 'Argame',
        'record(s)' => 'diinagdee',
        'for' => 'kan',
        'date_of_birth' => 'Guyaa Lakkoofsa',
        'gender' => 'Awwaalummaa',
        'status' => 'Haala',
        'view' => 'Argisi',
        'no_records_found' => 'Diinagdee Hin Argamu',
        'no_match_criteria' => 'Diinagdeewwan ummataa waliin gadii meeshaa hin taane. Termiwwan gadii dhabu.',
        'search_tips' => 'Malkaa Gadisi',
        'tip_name' => 'Gadii Isa',
        'tip_name_desc' => 'Isa jalqabaa, isa jalqabaa ykn hunda gadisi',
        'tip_id' => 'Gadii ID Qaama Oromiyaa',
        'tip_id_desc' => 'Diinagdeewwan ID qamamee argisi',
        'tip_case' => 'Gadii Caasaa',
        'tip_case_desc' => 'Caasoota qamamee ummataa diinagdeewwan argisi',
        'error_search' => 'Saaqaa gadisi:',
        'logout_confirm' => 'Fufiisi',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Enforce clerk role
requireRole(['clerk']);

// Get current user info (read-only from session)
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'clerk',
    'user_id' => $_SESSION['user_id']
];

// Initialize variables
$search_results = [];
$search_performed = false;
$search_query = '';
$search_type = 'name';
$search_error = '';

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $search_query = trim($_POST['search_query'] ?? '');
    $search_type = $_POST['search_type'] ?? 'name';
    $search_performed = true;
    
    if (!empty($search_query)) {
        $clerk = new ClerkFunctions($pdo); // Use clerk functions for consistent read-only access
        
        try {
            // Use general search method (adapts to type via query param simulation)
            // For simplicity, map search_type to ClerkFunctions::searchRecords (which handles name/ID)
            // Case number requires custom join query (read-only)
            if ($search_type === 'case_number') {
                // Custom read-only query for case-linked records
                $stmt = $pdo->prepare("
                    SELECT cr.*, c.case_number 
                    FROM criminal_records cr
                    INNER JOIN case_persons cp ON cr.id = cp.record_id
                    INNER JOIN cases c ON cp.case_id = c.id
                    WHERE c.case_number LIKE ?
                    AND cr.status != 'deleted'
                    ORDER BY cr.created_at DESC
                    LIMIT 20
                ");
                $stmt->execute(["%$search_query%"]);
                $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Use ClerkFunctions for name/national_id (fuzzy search)
                $search_results = $clerk->searchRecords($search_query, 20, 0);
            }
            
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $search_error = t('error_search') . " " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <!-- Cache control to prevent stale data -->
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
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
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
            border: 2px solid #e3f2fd;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
        .result-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #1976d2;
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
            border: 3px solid #e3f2fd;
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
        
        .search-stats {
            background: linear-gradient(135deg, #42a5f5 0%, #1976d2 100%);
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
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
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
    <!-- Navigation Bar: Clerk-specific links (read-only) -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-search me-2"></i>
                <?php echo t('navbar_brand'); ?>
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
                    
                    <!-- Displays current clerk's name (read-only) -->
                    <span class="clerk-badge me-3">
                        <i class="fas fa-user-tie me-1"></i>
                        <?php echo t('clerk_badge'); ?> <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <!-- Logout with confirmation -->
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('<?php echo t('logout'); ?>')">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout_confirm'); ?>
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
                            <i class="fas fa-search me-2"></i><?php echo t('search_header'); ?>
                        </div>
                        <div class="card-body-custom">
                            <!-- Search Form: Read-only submission -->
                            <form method="POST" class="search-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold"><?php echo t('search_type_label'); ?></label>
                                        <select name="search_type" class="form-select form-control-custom">
                                            <option value="name" <?php echo $search_type === 'name' ? 'selected' : ''; ?>><?php echo t('name'); ?></option>
                                            <option value="national_id" <?php echo $search_type === 'national_id' ? 'selected' : ''; ?>><?php echo t('national_id'); ?></option>
                                            <option value="case_number" <?php echo $search_type === 'case_number' ? 'selected' : ''; ?>><?php echo t('case_number'); ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold"><?php echo t('search_query_label'); ?></label>
                                        <input type="text" name="search_query" class="form-control form-control-custom" 
                                               placeholder="<?php echo t('search_placeholder'); ?>" value="<?php echo htmlspecialchars($search_query); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="search" class="btn btn-primary-custom w-100">
                                            <i class="fas fa-search me-2"></i><?php echo t('search_btn'); ?>
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
                                        <?php echo t('found_records'); ?> <strong><?php echo count($search_results); ?></strong> <?php echo t('record(s)'); ?> 
                                        <?php echo t('for'); ?> "<?php echo htmlspecialchars($search_query); ?>"
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($search_error): ?>
                                <div class="alert alert-error">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo htmlspecialchars($search_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search Results: Read-only view -->
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
                                                    <h6 class="mb-1"><?php echo htmlspecialchars(($record['first_name'] ?? '') . ' ' . ($record['last_name'] ?? '')); ?></h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-id-card me-1"></i>
                                                        <?php echo htmlspecialchars($record['national_id'] ?? 'N/A'); ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('date_of_birth'); ?></small>
                                                    <div><?php echo !empty($record['date_of_birth']) ? htmlspecialchars($record['date_of_birth']) : 'Not specified'; ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('gender'); ?></small>
                                                    <div><?php echo htmlspecialchars($record['gender'] ?? 'Not specified'); ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted"><?php echo t('status'); ?></small>
                                                    <div>
                                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? 'first-offender')); ?>">
                                                            <?php echo htmlspecialchars($record['status'] ?? 'First Offender'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <!-- Read-only: View only, no create/edit -->
                                                    <a href="view_criminal_record.php?id=<?php echo $record['id']; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> <?php echo t('view'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-results">
                                        <i class="fas fa-search"></i>
                                        <h4><?php echo t('no_records_found'); ?></h4>
                                        <p><?php echo t('no_match_criteria'); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Quick Search Tips: Educational content for clerks -->
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
                                                <h6><?php echo t('tip_name'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('tip_name_desc'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle p-3 me-3">
                                                <i class="fas fa-id-card text-white"></i>
                                            </div>
                                            <div>
                                                <h6><?php echo t('tip_id'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('tip_id_desc'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle p-3 me-3">
                                                <i class="fas fa-folder text-white"></i>
                                            </div>
                                            <div>
                                                <h6><?php echo t('tip_case'); ?></h6>
                                                <p class="text-muted mb-0"><?php echo t('tip_case_desc'); ?></p>
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
        // Auto-focus on search input for quick access
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search_query"]');
            if (searchInput) {
                searchInput.focus();
            }
        });
        
        // Clear search query when changing type
        document.querySelector('select[name="search_type"]').addEventListener('change', function() {
            document.querySelector('input[name="search_query"]').value = '';
        });
    </script>
</body>
</html>