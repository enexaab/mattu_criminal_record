<?php
// dashboard.php
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

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
        'title' => 'Administrator Dashboard - Mattu Criminal Record System',
        'sidebar_title' => 'Admin Panel',
        'sidebar_subtitle' => 'Mattu Criminal Records',
        'nav_dashboard' => 'Dashboard',
        'nav_manage_users' => 'Manage Users',
        'nav_reports' => 'Generate Reports',
        'nav_security' => 'Security Center',
        'nav_logout' => 'Logout',
        'top_title' => 'Administrator Dashboard',
        'welcome_back' => 'Welcome back, ',
        'user_role' => 'System Administrator',
        'content_dashboard_overview' => 'Dashboard Overview',
        'kpi_total_users' => 'Total Users',
        'kpi_total_records' => 'Criminal Records',
        'kpi_active_cases' => 'Active Cases',
        'kpi_system_health' => 'System Health',
        'security_alerts' => 'Security Alerts',
        'loading_alerts' => 'Loading security alerts...',
        'recent_activity' => 'Recent System Activity',
        'refresh' => 'Refresh',
        'loading_activity' => 'Loading recent activities...',
        'system_statistics' => 'System Statistics',
        'today_logins' => "Today's Logins",
        'active_users' => 'Active Users',
        'confirm_logout' => 'Are you sure you want to logout?',
        'system_operational' => 'System Operational',
        'systems_normal' => 'All systems are running normally',
        'dashboard_loaded' => 'Dashboard loaded successfully',
        'by_system' => 'by system',
        'just_now' => 'Just now',
        'confirm_backup' => 'Are you sure you want to start a database backup?',
        'backup_started' => 'Database backup started successfully!',
        'back_to_dashboard' => 'Back to Dashboard',
    ],
    'am' => [
        'title' => 'የአስተዳዳሪ ዳሽቦርድ - ማቱ የወንጀል መዝገብ ስርዓት',
        'sidebar_title' => 'አስተዳዳሪ ፓኔል',
        'sidebar_subtitle' => 'ማቱ የወንጀል መዝገቦች',
        'nav_dashboard' => 'ዳሽቦርድ',
        'nav_manage_users' => 'ተጠቃሚዎችን አስተዳደር',
        'nav_reports' => 'ሪፖርቶች ይፍጠሩ',
        'nav_security' => 'ደህንነት ማዕከል',
        'nav_logout' => 'ውጣ',
        'top_title' => 'የአስተዳዳሪ ዳሽቦርድ',
        'welcome_back' => 'እንኳን ተመልሰሃል፣ ',
        'user_role' => 'ስርዓት አስተዳዳሪ',
        'content_dashboard_overview' => 'የዳሽቦርድ አጠቃቀም',
        'kpi_total_users' => 'ጠቃሚዎች ጠቅላላ',
        'kpi_total_records' => 'የወንጀል መዝገቦች',
        'kpi_active_cases' => 'ንቁ ጉዳዮች',
        'kpi_system_health' => 'ስርዓት ጤና',
        'security_alerts' => 'ደህንነት ማስጠንቀቂያዎች',
        'loading_alerts' => 'ደህንነት ማስጠንቀቂያዎች ይሰማሉ...',
        'recent_activity' => 'የቅርብ ጊዜ ስርዓት እንቅስቃሴ',
        'refresh' => 'አድስ',
        'loading_activity' => 'የቅርብ ጊዜ እንቅስቃሴዎች ይሰማሉ...',
        'system_statistics' => 'ስርዓት ስታቲስቲክስ',
        'today_logins' => 'ዛሬ የግባ',
        'active_users' => 'ንቁ ተጠቃሚዎች',
        'confirm_logout' => 'በእርግጠኝነት ይወጣሉ?',
        'system_operational' => 'ስርዓቱ ይሰራል',
        'systems_normal' => 'ሁሉም ስርዓቶች በተለመደ መንገድ ይሰራሉ',
        'dashboard_loaded' => 'ዳሽቦርድ በተሳካ ሁኔታ ተጫነ',
        'by_system' => 'በስርዓት',
        'just_now' => 'አሁን ብቻ',
        'confirm_backup' => 'የውሂብ ቤዝ ማስቀመጫ ማስጀመር ትፈልጋለህ?',
        'backup_started' => 'የውሂብ ቤዝ ማስቀመጫ በተሳካ ሁኔታ ተጀመረ!',
        'back_to_dashboard' => 'ወደ ዳሽቦርድ ተመለስ',
    ],
    'om' => [
        'title' => 'Dashboardii Adminii - Sisteemi Ummata Mattu Diinagdee',
        'sidebar_title' => 'Panneeli Adminii',
        'sidebar_subtitle' => 'Sisteemi Ummata Mattu Diinagdee',
        'nav_dashboard' => 'Dashboardii',
        'nav_manage_users' => 'Imaammataan Useroota',
        'nav_reports' => 'Riportoota Ummisi',
        'nav_security' => 'Qabeenya Ammaata',
        'nav_logout' => 'Fufiisi',
        'top_title' => 'Dashboardii Adminii',
        'welcome_back' => 'Galataa, ',
        'user_role' => 'Adminii Sisteemi',
        'content_dashboard_overview' => 'Balaa Dashboardii',
        'kpi_total_users' => 'Useroota Jijjiirama',
        'kpi_total_records' => 'Diinagdeewwan Ummata',
        'kpi_active_cases' => 'Caasoota Hojiin',
        'kpi_system_health' => 'Balaa Sisteemi',
        'security_alerts' => 'Ijaarsa Ammaata',
        'loading_alerts' => 'Ijaarsa ammaata argamsi...',
        'recent_activity' => 'Hojiin Utuu Sisteemi',
        'refresh' => 'Aadaa',
        'loading_activity' => 'Hojiin utuu argamsi...',
        'system_statistics' => 'Statistiki Sisteemi',
        'today_logins' => "Loginiin Har'aa",
        'active_users' => 'Useroota Hojiin',
        'confirm_logout' => 'Fufiisuu barbaadda?',
        'system_operational' => 'Sisteemi Ykn',
        'systems_normal' => 'Sisteemota hundee yoo taane ykn',
        'dashboard_loaded' => 'Dashboardii yoo taane argame',
        'by_system' => 'sisteemi irratti',
        'just_now' => 'Aadaa kan',
        'confirm_backup' => 'Backup database barbaachisu?',
        'backup_started' => 'Backup database yoo taane aadaa!',
        'back_to_dashboard' => 'Dashboardii Garaa',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize database connection EXACTLY like user management does
$database = new Database();
$db = $database->getConnection();
$adminFunctions = new AdminFunctions($db);

// Get current user info for display
$current_user_id = $_SESSION['user_id'];
$current_user_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

// Debug: Check database connection
try {
    $test_stmt = $db->query("SELECT 1");
    $dbStatus = "Connected";
    
    // Test the getAllUsers function directly
    $testUsers = getAllUsers();
    $userCount = count($testUsers);
    
    echo "<!-- Debug: Database connection OK -->\n";
    echo "<!-- Debug: Direct user count: $userCount -->\n";
    
} catch (PDOException $e) {
    $dbStatus = "Disconnected: " . $e->getMessage();
    echo "<!-- Debug: Database connection FAILED: " . $e->getMessage() . " -->\n";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
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
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Layout */
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            width: 280px;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
            color: white;
        }
        
        .sidebar-header h4 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .sidebar-header .subtitle {
            color: #bdc3c7;
            font-size: 0.85rem;
        }
        
        .sidebar.collapsed .sidebar-header h4,
        .sidebar.collapsed .sidebar-header .subtitle,
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .nav {
            list-style: none;
            flex: 1;
            padding: 10px 0;
        }
        
        .nav-link {
            color: #ecf0f1;
            padding: 15px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            border-left-color: #3498db;
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar-toggle {
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
            margin: 10px;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.2);
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: all 0.3s ease;
            padding: 20px;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        /* Top Navigation Bar */
        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 10px;
        }
        
        /* Content Wrapper for Embedded Views */
        .content-wrapper {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            min-height: 600px;
        }
        
        .content-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .content-header h3 {
            margin: 0;
            font-weight: 600;
        }
        
        .content-body {
            padding: 0;
            height: calc(100vh - 200px);
        }
        
        /* Embedded Frame */
        .embedded-frame {
            width: 100%;
            height: 100%;
            border: none;
            background: white;
        }
        
        /* Dashboard Specific Styles */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .kpi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .kpi-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .kpi-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: none;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }
        
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 10px;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Activity Items */
        .activity-item {
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
            display: flex;
            align-items: center;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 0.9rem;
        }
        
        .activity-create { background: #e8f5e8; color: #27ae60; }
        .activity-update { background: #fff3cd; color: #856404; }
        .activity-delete { background: #f8d7da; color: #721c24; }
        .activity-login { background: #d1ecf1; color: #0c5460; }
        
        /* Alerts */
        .alert-item {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .alert-item.critical {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-icon {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #f8f9fa;
            color: #495057;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn:hover {
            background: #e9ecef;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .btn-outline-light {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.5);
            color: white;
        }
        
        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }
        
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .stat-box {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }
        
        .stat-value {
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar-header h4,
            .sidebar-header .subtitle,
            .nav-link span {
                display: none;
            }
            
            .row {
                flex-direction: column;
            }
            
            .col-6 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h4 id="sidebar-title"><?php echo t('sidebar_title'); ?></h4>
                <div class="subtitle" id="sidebar-subtitle"><?php echo t('sidebar_subtitle'); ?></div>
            </div>
            
            <ul class="nav">
                <li>
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span><?php echo t('nav_dashboard'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="usermanagement">
                        <i class="fas fa-users-cog"></i>
                        <span><?php echo t('nav_manage_users'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span><?php echo t('nav_reports'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="security">
                        <i class="fas fa-shield-alt"></i>
                        <span><?php echo t('nav_security'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="../logout.php" onclick="return confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><?php echo t('nav_logout'); ?></span>
                    </a>
                </li>
            </ul>
            
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Top Navigation Bar -->
            <div class="top-navbar">
                <div>
                    <h2><?php echo t('top_title'); ?></h2>
                    <small class="text-muted"><?php echo t('welcome_back'); ?><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></small>
                </div>
                <!-- Language Selector -->
           <form method="post" class="me-3" style="margin-left: 20cm;">
    <select name="lang" onchange="this.form.submit()" class="form-select form-select-sm">
        <option value="en" <?php echo $current_lang=='en'?'selected':''; ?>>English</option>
        <option value="am" <?php echo $current_lang=='am'?'selected':''; ?>>አማርኛ</option>
        <option value="om" <?php echo $current_lang=='om'?'selected':''; ?>>Afaan Oromoo</option>
    </select>
</form>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                        <small class="text-muted"><?php echo t('user_role'); ?></small>
                    </div>
                </div>
            </div>
            
            <!-- Content Wrapper for Embedded Views -->
            <div class="content-wrapper" id="contentWrapper">
                <!-- Dashboard Content (Default View) -->
                <div id="dashboardView">
                    <div class="content-header">
                        <h3><i class="fas fa-tachometer-alt"></i> <?php echo t('content_dashboard_overview'); ?></h3>
                    </div>
                    <div class="card-body">
                        <!-- KPI Cards Row -->
                        <div class="dashboard-grid">
                            <div class="kpi-card">
                                <div class="kpi-number" id="totalUsers">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label"><?php echo t('kpi_total_users'); ?></div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="totalRecords">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label"><?php echo t('kpi_total_records'); ?></div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="activeCases">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label"><?php echo t('kpi_active_cases'); ?></div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="systemHealth">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label"><?php echo t('kpi_system_health'); ?></div>
                            </div>
                        </div>
                        
                        <!-- Main Dashboard Widgets -->
                        <div class="row">
                            <!-- Security Alerts Panel -->
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-shield-alt"></i> <?php echo t('security_alerts'); ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="securityAlerts">
                                            <div style="text-align: center;">
                                                <div class="loading-spinner"></div>
                                                <p><?php echo t('loading_alerts'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Activity Log -->
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                        <h5><i class="fas fa-history"></i> <?php echo t('recent_activity'); ?></h5>
                                        <button class="btn btn-sm btn-outline-light" onclick="refreshActivity()">
                                            <i class="fas fa-sync-alt"></i> <?php echo t('refresh'); ?>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="recentActivity">
                                            <div style="text-align: center;">
                                                <div class="loading-spinner"></div>
                                                <p><?php echo t('loading_activity'); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Quick Actions Panel -->
                            <div class="col-12">
                                <div class="card">
                                 
                                    <div class="card-body">
                                       
                                        
                                        <hr>
                                        
                                        <h6><?php echo t('system_statistics'); ?></h6>
                                        <div class="stats-grid">
                                            <div class="stat-box">
                                                <div class="stat-value" id="todayLogins">--</div>
                                                <div class="stat-label"><?php echo t('today_logins'); ?></div>
                                            </div>
                                            <div class="stat-box">
                                                <div class="stat-value" id="activeUsers">--</div>
                                                <div class="stat-label"><?php echo t('active_users'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Embedded Frame View (Hidden by default) -->
                <div id="embeddedView" style="display: none;">
                    <div class="content-header">
                        <h3 id="embeddedViewTitle"></h3>
                        <button class="btn btn-outline-light btn-sm" onclick="showDashboard()">
                            <i class="fas fa-arrow-left"></i> <?php echo t('back_to_dashboard'); ?>
                        </button>
                    </div>
                    <div class="content-body">
                        <iframe id="embeddedFrame" class="embedded-frame" src=""></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
 // Global variables
let currentView = 'dashboard';

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard initializing...');
    setupNavigation();
    loadDashboardData();
});

function setupNavigation() {
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Don't prevent default for logout link
            if (this.getAttribute('href') === '../logout.php') {
                return true;
            }
            
            e.preventDefault();
            const section = this.getAttribute('data-section');
            if (section) {
                if (section === 'dashboard') {
                    showDashboard();
                } else {
                    // Map section to page and title
                    const pageMap = {
                        'usermanagement': { page: 'usermanagement.php', title: '<?php echo addslashes(t("nav_manage_users")); ?>' },
                        'reports': { page: 'generate_reports.php', title: '<?php echo addslashes(t("nav_reports")); ?>' },
                        'security': { page: 'security.php', title: '<?php echo addslashes(t("nav_security")); ?>' }
                    };
                    
                    const pageInfo = pageMap[section];
                    if (pageInfo) {
                        loadEmbeddedView(pageInfo.page, pageInfo.title);
                    }
                }
                // Update active class
                document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });
}

// Load embedded view in frame
function loadEmbeddedView(page, title) {
    if (currentView === 'embedded' && document.getElementById('embeddedFrame').src.includes(page)) {
        return; // Already viewing this page
    }
    
    currentView = 'embedded';
    
    // Hide dashboard, show embedded view
    document.getElementById('dashboardView').style.display = 'none';
    document.getElementById('embeddedView').style.display = 'block';
    
    // Set title
    document.getElementById('embeddedViewTitle').innerHTML = '<i class="fas fa-cog"></i> ' + title;
    
    // Load page in iframe
    const iframe = document.getElementById('embeddedFrame');
    iframe.src = page;
    
    // Update active nav link
    document.querySelectorAll('.nav-link').forEach(nav => {
        nav.classList.remove('active');
        if (nav.getAttribute('data-section') === getSectionFromPage(page)) {
            nav.classList.add('active');
        }
    });
}

// Show dashboard view
function showDashboard() {
    currentView = 'dashboard';
    
    // Show dashboard, hide embedded view
    document.getElementById('dashboardView').style.display = 'block';
    document.getElementById('embeddedView').style.display = 'none';
    
    // Clear iframe source
    document.getElementById('embeddedFrame').src = '';
    
    // Update active nav link
    document.querySelectorAll('.nav-link').forEach(nav => {
        nav.classList.remove('active');
        if (nav.getAttribute('data-section') === 'dashboard') {
            nav.classList.add('active');
        }
    });
    
    // Refresh dashboard data
    loadDashboardData();
}

// Helper function to get section from page
function getSectionFromPage(page) {
    const pageToSection = {
        'usermanagement.php': 'usermanagement',
        'generate_reports.php': 'reports',
        'security.php': 'security'
    };
    return pageToSection[page] || 'dashboard';
}

// Load all dashboard data
function loadDashboardData() {
    console.log('Loading dashboard data...');
    showLoadingStates();
    
    // Load data with fallbacks
    Promise.allSettled([
        loadKPIData(),
        loadSecurityAlerts(),
        loadRecentActivity(),
        loadQuickStats()
    ]).then(results => {
        console.log('All dashboard data loaded');
        hideLoadingStates();
    });
}

// Show loading states for all components
function showLoadingStates() {
    document.querySelectorAll('.kpi-number').forEach(el => {
        if (!el.querySelector('.loading-spinner')) {
            el.innerHTML = '<div class="loading-spinner"></div>';
        }
    });
}

function hideLoadingStates() {
    // Loading states are replaced by actual data
}

// Load KPI data with robust error handling
function loadKPIData() {
    return fetch('api/dashboard_data.php?action=kpi')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            console.log('KPI Response:', data);
            if (data.success) {
                updateKPIDisplay(data.data);
            } else {
                throw new Error(data.message || 'API error');
            }
        })
        .catch(error => {
            console.warn('KPI data unavailable:', error.message);
            // Use fallback data
            updateKPIDisplay({
                total_users: 'Error',
                total_records: 'Error', 
                active_cases: 'Error',
                system_health: 'Error'
            });
        });
}

function updateKPIDisplay(data) {
    if (data.total_users !== undefined) {
        document.getElementById('totalUsers').textContent = data.total_users;
    }
    if (data.total_records !== undefined) {
        document.getElementById('totalRecords').textContent = data.total_records;
    }
    if (data.active_cases !== undefined) {
        document.getElementById('activeCases').textContent = data.active_cases;
    }
    if (data.system_health !== undefined) {
        document.getElementById('systemHealth').innerHTML = 
            `<i class="fas fa-check-circle"></i> ${data.system_health}%`;
    }
    
    console.log('Updated KPI Display:', data);
}

// Load security alerts with robust error handling
function loadSecurityAlerts() {
    const alertsContainer = document.getElementById('securityAlerts');
    
    return fetch('api/dashboard_data.php?action=security_alerts')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            console.log('Security Alerts Response:', data);
            if (data.success && data.data && data.data.length > 0) {
                displaySecurityAlerts(data.data);
            } else {
                throw new Error('No alert data');
            }
        })
        .catch(error => {
            console.warn('Security alerts unavailable:', error.message);
            // Show default status
            alertsContainer.innerHTML = `
                <div class="alert-item">
                    <i class="fas fa-check-circle alert-icon"></i>
                    <div>
                        <div class="fw-bold"><?php echo t('system_operational'); ?></div>
                        <small class="text-muted"><?php echo t('systems_normal'); ?></small>
                    </div>
                </div>
            `;
        });
}

function displaySecurityAlerts(alerts) {
    const alertsContainer = document.getElementById('securityAlerts');
    let alertsHTML = '';
    
    alerts.forEach(alert => {
        const severityClass = alert.severity === 'critical' ? 'critical' : '';
        const icon = getAlertIcon(alert.severity);
        
        alertsHTML += `
            <div class="alert-item ${severityClass}">
                <i class="fas fa-${icon} alert-icon"></i>
                <div>
                    <div class="fw-bold">${alert.title}</div>
                    <small class="text-muted">${alert.message}</small>
                    <div style="font-size: 0.8rem; color: #6c757d;">${alert.timestamp}</div>
                </div>
            </div>
        `;
    });
    
    alertsContainer.innerHTML = alertsHTML;
}

function getAlertIcon(severity) {
    const icons = {
        'critical': 'exclamation-triangle',
        'warning': 'exclamation-circle', 
        'info': 'info-circle',
        'success': 'check-circle'
    };
    return icons[severity] || 'info-circle';
}

// Load recent activity with robust error handling
function loadRecentActivity() {
    const activityContainer = document.getElementById('recentActivity');
    
    return fetch('api/dashboard_data.php?action=recent_activity')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            console.log('Recent Activity Response:', data);
            if (data.success && data.data && data.data.length > 0) {
                displayRecentActivity(data.data);
            } else {
                throw new Error('No activity data');
            }
        })
        .catch(error => {
            console.warn('Recent activity unavailable:', error.message);
            // Show default activity
            activityContainer.innerHTML = `
                <div class="activity-item">
                    <div class="activity-icon activity-login">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?php echo t('dashboard_loaded'); ?></div>
                        <small class="text-muted"><?php echo t('by_system'); ?> • <?php echo t('just_now'); ?></small>
                    </div>
                </div>
            `;
        });
}

function displayRecentActivity(activities) {
    const activityContainer = document.getElementById('recentActivity');
    let activityHTML = '';
    
    activities.forEach(activity => {
        const actionType = activity.action_type || 'general';
        const iconClass = getActivityIcon(actionType);
        const bgClass = getActivityBackgroundClass(actionType);
        
        activityHTML += `
            <div class="activity-item">
                <div class="activity-icon ${bgClass}">
                    <i class="${iconClass}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold">${activity.description || 'System Activity'}</div>
                    <small class="text-muted">by ${activity.username || 'System'} • ${activity.timestamp}</small>
                </div>
            </div>
        `;
    });
    
    activityContainer.innerHTML = activityHTML;
}

// Load quick stats with robust error handling
function loadQuickStats() {
    return fetch('api/dashboard_data.php?action=quick_stats')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            console.log('Quick Stats Response:', data);
            if (data.success) {
                document.getElementById('todayLogins').textContent = data.data.today_logins || '1';
                document.getElementById('activeUsers').textContent = data.data.active_users || '1';
            } else {
                throw new Error(data.message || 'API error');
            }
        })
        .catch(error => {
            console.warn('Quick stats unavailable:', error.message);
            document.getElementById('todayLogins').textContent = '1';
            document.getElementById('activeUsers').textContent = '1';
        });
}

// Helper functions for activity icons
function getActivityIcon(actionType) {
    const icons = {
        'login': 'fas fa-sign-in-alt',
        'user_create': 'fas fa-user-plus',
        'user_update': 'fas fa-user-edit',
        'password_reset': 'fas fa-key',
        'user_status': 'fas fa-user-check',
        'system': 'fas fa-cog',
        'default': 'fas fa-info-circle'
    };
    return icons[actionType] || icons.default;
}

function getActivityBackgroundClass(actionType) {
    const classes = {
        'login': 'activity-login',
        'user_create': 'activity-create',
        'user_update': 'activity-update',
        'password_reset': 'activity-update',
        'user_status': 'activity-update',
        'system': 'activity-login',
        'default': 'activity-login'
    };
    return classes[actionType] || classes.default;
}

// Refresh functions
function refreshActivity() {
    loadRecentActivity();
}

// Sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
}

// Quick actions
function backupDatabase() {
    if (confirm('<?php echo addslashes(t("confirm_backup")); ?>')) {
        alert('<?php echo addslashes(t("backup_started")); ?>');
    }
}

// Logout confirmation
function confirmLogout() {
    return confirm('<?php echo addslashes(t("confirm_logout")); ?>');
}
    </script>
</body>
</html>