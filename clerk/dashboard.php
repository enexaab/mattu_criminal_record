<?php
// dashboard.php
// This file serves as the main dashboard for users with the 'clerk' role in the Mattu Criminal Record System.
// A 'clerk' is a non-administrative user who can search, view, and browse criminal records but cannot create, edit, or delete them.
// Clerks focus on data retrieval, case tracking, and viewing system updates. No write access to prevent unauthorized changes.

// Required includes for authentication, database connection, and clerk-specific functions
require '../includes/auth.php';      // Handles session validation and login checks
require '../includes/database.php';  // Provides PDO database connection
require '../includes/clerk_functions.php'; // Clerk-specific methods (e.g., stats, search)

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
        'title' => 'Clerk Dashboard - Mattu Criminal Record System',
        'navbar_brand' => 'Mattu Criminal Records',
        'dashboard' => 'Dashboard',
        'search_view' => 'Search & View Records',
        'clerk_badge' => 'Clerk',
        'logout' => 'Logout',
        'hero_title' => 'Criminal Record Search',
        'hero_sub' => 'Search by Criminal Name, National ID, or Case File ID',
        'search_placeholder' => 'Enter name, ID number, or case file ID...',
        'search_btn' => 'Search Records',
        'search_tips' => 'Search Tips',
        'tip1' => 'Use full names for better results (e.g., "John Smith")',
        'tip2' => 'National ID format: 12-digit number',
        'tip3' => 'Case IDs start with "CASE-" followed by numbers',
        'tip4' => 'Search is case-insensitive',
        'total_records' => 'Total Criminal Records',
        'active_cases' => 'Active Cases',
        'pending_cases' => 'Pending Cases',
        'closed_cases' => 'Closed Cases',
        'quick_view' => 'Quick View Links',
        'view_active' => 'View Active Cases',
        'view_pending' => 'View Pending Cases',
        'recent_records' => 'Recent Records',
        'browse_all' => 'Browse All Records',
        'system_news' => 'System News & Updates',
        'no_updates' => 'No Recent Updates',
        'updates_desc' => 'System announcements and data entry guidelines will appear here.',
        'search_term_alert' => 'Please enter a search term',
    ],
    'am' => [
        'title' => 'የጸሐፊ ዳሽቦርድ - ማቱ የወንጀል መዝገብ ስርዓት',
        'navbar_brand' => 'ማቱ የወንጀል መዝገቦች',
        'dashboard' => 'ዳሽቦርድ',
        'search_view' => 'ፍለጋ እና መዝገቦችን ተመልከት',
        'clerk_badge' => 'ጸሐፊ',
        'logout' => 'ውጣ',
        'hero_title' => 'የወንጀል መዝገብ ፍለጋ',
        'hero_sub' => 'በወንጀል ስም ብሔራዊ መለያ ወይም ጉዳይ ፋይል መለያ ፍለጋ',
        'search_placeholder' => 'ስም፣ መለያ ቁጥር ወይም ጉዳይ ፋይል መለያ ያስገቡ...',
        'search_btn' => 'መዝገቦች ፍለጋ',
        'search_tips' => 'የፍለጋ ምክሮች',
        'tip1' => 'በተሻለ ውጤት ለመስጠት ሙሉ ስሞችን ይጠቀሙ (ለምሳሌ፣ "ጆን ስሚት")',
        'tip2' => 'ብሔራዊ መለያ ቅርጸት፡ 12-ቁጥር',
        'tip3' => 'ጉዳይ መለያዎች "CASE-" በመጀመሪያ ቁጥሮች ይከተላሉ',
        'tip4' => 'የፍለጋ ጥልቅ የለም',
        'total_records' => 'ጠቃሚ የወንጀል መዝገቦች',
        'active_cases' => 'ንቁ ጉዳዮች',
        'pending_cases' => 'ተረጋጋ ጉዳዮች',
        'closed_cases' => 'ዝጋ ጉዳዮች',
        'quick_view' => 'ፈጣን እይታ ማገናኛዎች',
        'view_active' => 'ንቁ ጉዳዮችን ተመልከት',
        'view_pending' => 'ተረጋጋ ጉዳዮችን ተመልከት',
        'recent_records' => 'የቅርብ ጊዜ መዝገቦች',
        'browse_all' => 'ሁሉንም መዝገቦች ማግለፍ',
        'system_news' => 'ስርዓት ዜናዎች እና ዝማኔዎች',
        'no_updates' => 'የቅርብ ጊዜ ዝማኔ የለም',
        'updates_desc' => 'ስርዓት ጋዜጣዎች እና ውሂብ ግብዝ መመሪያዎች እዚህ ይታያሉ',
        'search_term_alert' => 'እባክዎ የፍለጋ ቃል ያስገቡ',
    ],
    'om' => [
        'title' => 'Dashboardii Karraa - Sisteemi Ummata Mattu Diinagdee',
        'navbar_brand' => 'Sisteemi Ummata Mattu Diinagdee',
        'dashboard' => 'Dashboardii',
        'search_view' => 'Gadisi Mattu Diinagdeewwan Argisi',
        'clerk_badge' => 'Karraa',
        'logout' => 'Fufiisi',
        'hero_title' => 'Gadii Ummata Diinagdee',
        'hero_sub' => 'Gadii Ummata Isa, ID Qaama Oromiyaa ykn ID Fayila Caasaa',
        'search_placeholder' => 'Isa, ID naamma ykn ID Fayila Caasaa fidu...',
        'search_btn' => 'Diinagdeewwan Gadisi',
        'search_tips' => 'Malkaa Gadisi',
        'tip1' => 'Waliin gadii fiigicha (e.g., "John Smith") barbaachisu',
        'tip2' => 'ID Qaama Oromiyaa: 12-digit naamma',
        'tip3' => 'ID Caasaa "CASE-" jedhamuun hojjetu',
        'tip4' => 'Gadii case-insensitive',
        'total_records' => 'Diinagdeewwan Ummata Hundee',
        'active_cases' => 'Caasoota Hojiin',
        'pending_cases' => 'Caasoota Barumsa',
        'closed_cases' => 'Caasoota Dhabame',
        'quick_view' => 'Magaalaa Fiigicicha',
        'view_active' => 'Caasoota Hojiin Argisi',
        'view_pending' => 'Caasoota Barumsa Argisi',
        'recent_records' => 'Diinagdeewwan Utuu',
        'browse_all' => 'Diinagdeewwan Hundee Baradisi',
        'system_news' => 'Yaadni Sisteemi Mattu Ijaarsa',
        'no_updates' => 'Ijaarsa Utuu Hin Taane',
        'updates_desc' => 'Ijaarsa Sisteemi Mattu Ibbaan Qorannoo Dataa Ijaarsa Kannee Barameera',
        'search_term_alert' => 'Gadii termii dhiisi',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Initialize database connection (FIX: This was missing, causing undefined $pdo error leading to 500)
$database = new Database();
$pdo = $database->getConnection();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // Fixed redirect to login page (was dashboard.php, which would loop)
    exit();
}

// Enforce clerk role (defined in auth.php - assumes requireRole(['clerk']) is called there or here)
requireRole(['clerk']); // Ensures only clerks can access this page

// Fetch system stats and news using clerk functions (these query the real-time database)
$clerk = new ClerkFunctions($pdo); // $pdo from database.php
$systemStats = $clerk->getSystemStats(); // Gets counts from DB: total records, active/pending/closed cases
$systemNews = $clerk->getSystemNews();   // Gets recent announcements from DB
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
    <!-- External CSS/JS for styling and interactivity -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        /* Custom CSS for a modern, responsive dashboard */
        body {
            box-sizing: border-box;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
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
        
        .dashboard-container {
            padding: 40px 0;
        }
        
        .search-hero {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 50px 40px;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .search-hero h1 {
            color: #1565c0;
            font-weight: 700;
            margin-bottom: 15px;
            font-size: 2.5rem;
        }
        
        .search-hero p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .global-search-container {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .global-search-input {
            width: 100%;
            padding: 20px 25px 20px 60px;
            border: 2px solid #e3f2fd;
            border-radius: 50px;
            font-size: 1.2rem;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .global-search-input:focus {
            outline: none;
            border-color: #1976d2;
            box-shadow: 0 8px 25px rgba(25, 118, 210, 0.2);
            transform: translateY(-2px);
        }
        
        .search-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: #1976d2;
            font-size: 1.3rem;
        }
        
        .search-btn {
            background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
            border: none;
            color: white;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }
        
        .search-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(25, 118, 210, 0.4);
            color: white;
        }
        
        .search-suggestions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: 15px;
            max-height: 300px;
            overflow-y: auto;
            display: none;
            position: absolute;
            width: 100%;
            z-index: 1000;
        }
        
        .suggestion-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .suggestion-item:hover, .suggestion-item.active {
            background-color: #f8f9fa;
        }
        
        .suggestion-item:last-child {
            border-bottom: none;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #42a5f5 0%, #1e88e5 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(66, 165, 245, 0.3);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .quick-view-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .quick-view-btn {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid #e3f2fd;
            color: #1565c0;
            padding: 20px;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
        }
        
        .quick-view-btn:hover {
            background: #1976d2;
            color: white;
            border-color: #1976d2;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(25, 118, 210, 0.3);
        }
        
        .quick-view-btn i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .news-item {
            background: #f8f9fa;
            border-left: 4px solid #1976d2;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 0 10px 10px 0;
        }
        
        .news-item h6 {
            color: #1565c0;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .news-item p {
            margin: 0;
            color: #666;
            font-size: 0.95rem;
        }
        
        .news-item .news-date {
            font-size: 0.8rem;
            color: #999;
            margin-top: 5px;
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
        
        .search-tips {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .search-tips h6 {
            color: #1565c0;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .search-tips ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .search-tips li {
            color: #666;
            margin-bottom: 5px;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #1976d2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .search-hero {
                padding: 30px 20px;
            }
            
            .search-hero h1 {
                font-size: 2rem;
            }
            
            .global-search-input {
                padding: 15px 20px 15px 50px;
                font-size: 1rem;
            }
            
            .search-btn {
                padding: 15px 30px;
                font-size: 1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar: Provides quick access to dashboard and search pages -->
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
                        <a class="nav-link-custom active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> <?php echo t('dashboard'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="search_records.php">
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
                    
                    <!-- Displays current clerk's name (read-only from session) -->
                    <span class="clerk-badge me-3">
                        <i class="fas fa-user-tie me-1"></i>
                        <?php echo t('clerk_badge'); ?> <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                    </span>
                    <!-- Logout button: Confirms before logging out (redirects to logout.php which destroys session) -->
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('<?php echo t('logout'); ?>?')">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Dashboard Content: Dynamic via PHP and AJAX for real-time feel -->
    <div class="dashboard-container">
        <div class="container">
            <!-- Global Search Hero: Central search interface with real-time suggestions -->
            <div class="search-hero">
                <h1><i class="fas fa-search me-3"></i><?php echo t('hero_title'); ?></h1>
                <p><?php echo t('hero_sub'); ?></p>
                
                <div class="global-search-container">
                    <div class="position-relative">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            class="global-search-input" 
                            id="globalSearch"
                            placeholder="<?php echo t('search_placeholder'); ?>"
                            autocomplete="off"
                        >
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    <button class="search-btn" onclick="performSearch()">
                        <i class="fas fa-search me-2"></i><?php echo t('search_btn'); ?>
                    </button>
                </div>
                
                <div class="search-tips">
                    <h6><i class="fas fa-lightbulb me-2"></i><?php echo t('search_tips'); ?></h6>
                    <ul class="text-start">
                        <li><?php echo t('tip1'); ?></li>
                        <li><?php echo t('tip2'); ?></li>
                        <li><?php echo t('tip3'); ?></li>
                        <li><?php echo t('tip4'); ?></li>
                    </ul>
                </div>
            </div>
            
            <!-- System Statistics: Real-time counts from database (refreshes every 5 min via JS) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="totalRecords">
                        <?php echo number_format($systemStats['total_records']); ?>
                    </span>
                    <div class="stat-label"><?php echo t('total_records'); ?></div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);">
                    <span class="stat-number" id="activeCases">
                        <?php echo number_format($systemStats['active_cases']); ?>
                    </span>
                    <div class="stat-label"><?php echo t('active_cases'); ?></div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);">
                    <span class="stat-number" id="pendingCases">
                        <?php echo number_format($systemStats['pending_cases']); ?>
                    </span>
                    <div class="stat-label"><?php echo t('pending_cases'); ?></div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ef5350 0%, #f44336 100%);">
                    <span class="stat-number" id="closedCases">
                        <?php echo number_format($systemStats['closed_cases']); ?>
                    </span>
                    <div class="stat-label"><?php echo t('closed_cases'); ?></div>
                </div>
            </div>
            
            <div class="row">
                <!-- Quick View Links: Direct links to filtered searches (read-only views) -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bolt me-2"></i><?php echo t('quick_view'); ?>
                        </div>
                        <div class="card-body-custom">
                            <div class="quick-view-grid">
                                <a href="search_records.php?filter=active" class="quick-view-btn">
                                    <i class="fas fa-folder-open"></i>
                                    <?php echo t('view_active'); ?>
                                </a>
                                <a href="search_records.php?filter=pending" class="quick-view-btn">
                                    <i class="fas fa-clock"></i>
                                    <?php echo t('view_pending'); ?>
                                </a>
                                <a href="search_records.php?filter=recent" class="quick-view-btn">
                                    <i class="fas fa-history"></i>
                                    <?php echo t('recent_records'); ?>
                                </a>
                                <a href="search_records.php?filter=all" class="quick-view-btn">
                                    <i class="fas fa-database"></i>
                                    <?php echo t('browse_all'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System News/Updates: Displays announcements from DB (clerk-targeted) -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bullhorn me-2"></i><?php echo t('system_news'); ?>
                        </div>
                        <div class="card-body-custom">
                            <?php if (!empty($systemNews)): ?>
                                <?php foreach ($systemNews as $news): ?>
                                    <div class="news-item">
                                        <h6><?php echo htmlspecialchars($news['title']); ?></h6>
                                        <p><?php echo htmlspecialchars($news['content']); ?></p>
                                        <div class="news-date">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo date('F j, Y', strtotime($news['created_at'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-newspaper fa-3x mb-3"></i>
                                    <h5><?php echo t('no_updates'); ?></h5>
                                    <p><?php echo t('updates_desc'); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS for responsive navbar and components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // JavaScript for interactive features: Real-time search suggestions and stats refresh
        // All interactions are read-only; no data modification
        
        // Global variables for search state
        let searchTimeout;
        let currentSuggestions = [];
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            updateStats(); // Initial stats load (already PHP-loaded, but JS refreshes)
        });
        
        // Set up real-time search with AJAX suggestions (queries DB via api/search_suggestions.php)
        function initializeSearch() {
            const searchInput = document.getElementById('globalSearch');
            const suggestionsContainer = document.getElementById('searchSuggestions');
            
            // Debounced input for suggestions (min 2 chars)
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                clearTimeout(searchTimeout);
                
                if (query.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        fetchSearchSuggestions(query);
                    }, 300);
                } else {
                    hideSuggestions();
                }
            });
            
            // Keyboard navigation for suggestions
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    performSearch();
                } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    navigateSuggestions(e.key === 'ArrowDown' ? 1 : -1);
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });
            
            // Hide on outside click
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    hideSuggestions();
                }
            });
        }
        
        // AJAX fetch suggestions from DB (real-time query)
        function fetchSearchSuggestions(query) {
            fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.suggestions.length > 0) {
                        displaySuggestions(data.suggestions);
                    } else {
                        hideSuggestions();
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    hideSuggestions();
                });
        }
        
        // Render suggestions dropdown
        function displaySuggestions(suggestions) {
            const container = document.getElementById('searchSuggestions');
            currentSuggestions = suggestions;
            
            let html = '';
            suggestions.forEach((suggestion, index) => {
                const icon = suggestion.type === 'record' ? 'fas fa-user' : 'fas fa-folder';
                html += `
                    <div class="suggestion-item" onclick="selectSuggestion(${index})" data-index="${index}">
                        <div class="d-flex align-items-center">
                            <i class="${icon} me-3 text-primary"></i>
                            <div>
                                <div class="fw-bold">${suggestion.title}</div>
                                <small class="text-muted">${suggestion.subtitle}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            container.style.display = 'block';
        }
        
        // Hide suggestions dropdown
        function hideSuggestions() {
            document.getElementById('searchSuggestions').style.display = 'none';
            currentSuggestions = [];
        }
        
        // Handle suggestion click: Redirect to view page (read-only)
        function selectSuggestion(index) {
            if (currentSuggestions[index]) {
                const suggestion = currentSuggestions[index];
                document.getElementById('globalSearch').value = suggestion.title;
                hideSuggestions();
                
                // Redirect based on type (view_record.php or view_criminal_record.php - assume these exist for viewing)
                if (suggestion.type === 'record') {
                    window.location.href = `view_record.php?id=${suggestion.id}`;
                } else if (suggestion.type === 'case') {
                    window.location.href = `view_criminal_record.php?id=${suggestion.id}`;
                }
            }
        }
        
        // Arrow key navigation in suggestions
        function navigateSuggestions(direction) {
            const suggestions = document.querySelectorAll('.suggestion-item');
            if (suggestions.length === 0) return;
            
            const current = document.querySelector('.suggestion-item.active');
            let newIndex = 0;
            
            if (current) {
                current.classList.remove('active');
                const currentIndex = parseInt(current.dataset.index);
                newIndex = currentIndex + direction;
                
                if (newIndex < 0) newIndex = suggestions.length - 1;
                if (newIndex >= suggestions.length) newIndex = 0;
            }
            
            suggestions[newIndex].classList.add('active');
        }
        
        // Perform full search: Redirect to search_results.php with query param
        function performSearch() {
            const query = document.getElementById('globalSearch').value.trim();
            
            if (query.length === 0) {
                alert('<?php echo t('search_term_alert'); ?>');
                return;
            }
            
            window.location.href = `search_records.php?q=${encodeURIComponent(query)}`;
        }
        
        // AJAX refresh stats from DB (api/clerk_stats.php) every 5 minutes for "real-time" updates
        function updateStats() {
            fetch('api/clerk_stats.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalRecords').textContent = 
                            new Intl.NumberFormat().format(data.stats.total_records);
                        document.getElementById('activeCases').textContent = 
                            new Intl.NumberFormat().format(data.stats.active_cases);
                        document.getElementById('pendingCases').textContent = 
                            new Intl.NumberFormat().format(data.stats.pending_cases);
                        document.getElementById('closedCases').textContent = 
                            new Intl.NumberFormat().format(data.stats.closed_cases);
                    }
                })
                .catch(error => {
                    console.error('Error updating stats:', error);
                });
        }
        
        setInterval(updateStats, 300000); // Refresh every 5 minutes
    </script>
</body>
</html>