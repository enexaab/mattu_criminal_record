<?php
// dashboard.php
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/clerk_functions.php';

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
        'title' => 'Police Chief Dashboard - Mattu City Strategic Command Center',
        'navbar_brand' => 'Mattu City Police Command',
        'nav_dashboard' => 'Dashboard',
        'nav_reports' => 'Reports',
        'logout_confirm' => 'Are you sure you want to logout?',
        'chief_badge' => 'Chief',
        'header_title' => 'Strategic Command Dashboard',
        'header_subtitle' => 'Real-time crime analytics and performance insights for Mattu City',
        'date_filter_custom' => 'Custom Range',
        'date_filter_7days' => 'Last 7 Days',
        'date_filter_30days' => 'Last 30 Days',
        'date_filter_90days' => 'Last 90 Days',
        'date_filter_year' => 'Last Year',
        'btn_filter' => 'Filter',
        'kpi_total_cases' => 'Total Cases Filed',
        'kpi_solved_cases' => 'Cases Solved',
        'kpi_active_cases' => 'Active Cases',
        'kpi_clearance_rate' => 'Clearance Rate',
        'chart_top_offenses' => 'Top Offense Categories',
        'chart_officer_performance' => 'Officer Performance Metrics',
        'table_officer' => 'Officer',
        'table_records_added' => 'Records Added',
        'table_cases_closed' => 'Cases Closed',
        'table_performance' => 'Performance',
        'no_officer_data' => 'No officer performance data available',
        'quick_links_title' => 'Strategic Command Links',
        'quick_link_reports' => 'Generate Reports',
        'quick_link_reports_desc' => 'Create detailed analytical reports for stakeholders',
        'quick_link_cases' => 'Manage Cases',
        'quick_link_cases_desc' => 'View and manage all criminal cases',
        'no_offense_data' => 'No offense data available for the selected period.',
        'performance_excellent' => 'EXCELLENT',
        'performance_good' => 'GOOD',
        'performance_average' => 'AVERAGE',
        'performance_needs_improvement' => 'NEEDS IMPROVEMENT',
        'notification_chart_failed' => 'Chart.js failed to load. Please refresh the page.',
        'notification_data_loaded' => 'Live data loaded successfully!',
        'notification_fetch_failed' => 'Failed to load live data:',
        'debug_realtime_started' => 'Real-time updates started',
        'debug_fetching_data' => 'Fetching live data for',
        'debug_data_loaded' => 'Data loaded:',
        'debug_error' => 'Error:',
        'logout' => 'Logout',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'am' => [
        'title' => 'የፖሊስ ጂኔራል ዳሽቦርድ - ማቱ ከተማ ስትራቴጂካዊ ትከታታይ ማእከል',
        'navbar_brand' => 'ማቱ ከተማ ፖሊስ ትከታታይ',
        'nav_dashboard' => 'ዳሽቦርድ',
        'nav_reports' => 'ሪፖርቶች',
        'logout_confirm' => 'በእርግጠኝነት ይወጣሉ?',
        'chief_badge' => 'ጂኔራል',
        'header_title' => 'ስትራቴጂካዊ ትከታታይ ዳሽቦርድ',
        'header_subtitle' => 'ለማቱ ከተማ በጊዜው ላይ የወንጀል ትንታኔ እና አፈጻጸም ትንታኔ',
        'date_filter_custom' => 'ልዩ ክልል',
        'date_filter_7days' => 'የመጨረሻ 7 ቀናት',
        'date_filter_30days' => 'የመጨረሻ 30 ቀናት',
        'date_filter_90days' => 'የመጨረሻ 90 ቀናት',
        'date_filter_year' => 'የመጨረሻ አመት',
        'btn_filter' => 'አዝር',
        'kpi_total_cases' => 'ጠቅላላ ጉዳዮች ተመድበዋል',
        'kpi_solved_cases' => 'ተፈቱ ጉዳዮች',
        'kpi_active_cases' => 'ንቁ ጉዳዮች',
        'kpi_clearance_rate' => 'የመጥፎ ተመድብ',
        'chart_top_offenses' => 'ከፍተኛ የወንጀል ምድቦች',
        'chart_officer_performance' => 'የባለሙያ አፈጻጸም መለኪያዎች',
        'table_officer' => 'ባለሙያ',
        'table_records_added' => 'የተጨመሩ መዝገቦች',
        'table_cases_closed' => 'የተዘገቡ ጉዳዮች',
        'table_performance' => 'አፈጻጸም',
        'no_officer_data' => 'የባለሙያ አፈጻጸም ውሂብ የለም',
        'quick_links_title' => 'ስትራቴጂካዊ ትከታታይ ጥገናዎች',
        'quick_link_reports' => 'ሪፖርቶች ይፍጠሩ',
        'quick_link_reports_desc' => 'ለባለድርሻ ሰዎች ዝርዝር ትንታኔ ሪፖርቶች ይፍጠሩ',
        'quick_link_cases' => 'ጉዳዮችን ይቆጣጠሩ',
        'quick_link_cases_desc' => 'ሁሉንም የወንጀል ጉዳዮች ይመልከቱ እና ይቆጣጠሩ',
        'no_offense_data' => 'ለተመረጠው ጊዜ የወንጀል ውሂብ የለም።',
        'performance_excellent' => 'እጅግ ጥሩ',
        'performance_good' => 'ጥሩ',
        'performance_average' => 'መካከለኛ',
        'performance_needs_improvement' => 'አሻሽል ይጠይቃል',
        'notification_chart_failed' => 'Chart.js ማግኘት አልተሳካም። እባክዎ ዳግም ያድሱ።',
        'notification_data_loaded' => 'በጊዜው ላይ ውሂብ በተሳካ ሁኔታ ተጫነ!',
        'notification_fetch_failed' => 'በጊዜው ላይ ውሂብ ማግኘት አልተሳካም፡',
        'debug_realtime_started' => 'በጊዜው ላይ የሚያዘጋጅ ዝርዝሮች ተጀመሩ',
        'debug_fetching_data' => 'በጊዜው ላይ ውሂብ ይሰማል ለ',
        'debug_data_loaded' => 'ውሂብ ተጫነ፡',
        'debug_error' => 'ስህተት፡',
        'logout' => 'ውጣ',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
    'om' => [
        'title' => 'Dashboardii Polisii Garaa - Mattu City Strategic Command Center',
        'navbar_brand' => 'Mattu City Polisii Command',
        'nav_dashboard' => 'Dashboardii',
        'nav_reports' => 'Riportoota',
        'logout_confirm' => 'Fufiisi barbaachisa?',
        'chief_badge' => 'Garaa',
        'header_title' => 'Strategic Command Dashboardii',
        'header_subtitle' => 'Yoo taane crime analytics fi performance insights Mattu City',
        'date_filter_custom' => 'Custom Range',
        'date_filter_7days' => 'Har\'aa 7',
        'date_filter_30days' => 'Har\'aa 30',
        'date_filter_90days' => 'Har\'aa 90',
        'date_filter_year' => 'Har\'a',
        'btn_filter' => 'Filter',
        'kpi_total_cases' => 'Caasoota Jijjiirama',
        'kpi_solved_cases' => 'Caasoota Yoo Taane',
        'kpi_active_cases' => 'Caasoota Hojiin',
        'kpi_clearance_rate' => 'Clearance Rate',
        'chart_top_offenses' => 'Top Offense Categories',
        'chart_officer_performance' => 'Officer Performance Metrics',
        'table_officer' => 'Officer',
        'table_records_added' => 'Records Added',
        'table_cases_closed' => 'Cases Closed',
        'table_performance' => 'Performance',
        'no_officer_data' => 'No officer performance data available',
        'quick_links_title' => 'Strategic Command Links',
        'quick_link_reports' => 'Riportoota Ummisi',
        'quick_link_reports_desc' => 'Detailed analytical reports stakeholders irratti',
        'quick_link_cases' => 'Caasoota Imaammisi',
        'quick_link_cases_desc' => 'All criminal cases argisi fi imaammisi',
        'no_offense_data' => 'Selected period offense data miti.',
        'performance_excellent' => 'EXCELLENT',
        'performance_good' => 'GOOD',
        'performance_average' => 'AVERAGE',
        'performance_needs_improvement' => 'NEEDS IMPROVEMENT',
        'notification_chart_failed' => 'Chart.js load miti. Page dagaa.',
        'notification_data_loaded' => 'Live data yoo taane argame!',
        'notification_fetch_failed' => 'Live data load miti:',
        'debug_realtime_started' => 'Real-time updates started',
        'debug_fetching_data' => 'Live data fetching for',
        'debug_data_loaded' => 'Data loaded:',
        'debug_error' => 'Error:',
        'logout' => 'Fufiisi',
        'lang_english' => 'English',
        'lang_amharic' => 'አማርኛ',
        'lang_oromo' => 'Afaan Oromoo',
    ],
];

function t($key) {
    global $translations, $current_lang;
    return $translations[$current_lang][$key] ?? $key;
}

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Fixed: Redirect to login instead of self
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Set default date range
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Check which table exists
$casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
$caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
$tableName = $casesTableExists ? 'cases' : 'case_files';

// Function to get dashboard statistics with better error handling
function getDashboardStats($db, $tableName, $start_date, $end_date) {
    $stats = [];
    
    try {
        // Total Cases - Use DATE for consistent comparison
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE DATE(COALESCE(created_at, date_reported)) BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        $stats['total_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Solved Cases
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status = 'Closed' AND DATE(COALESCE(created_at, date_reported)) BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        $stats['solved_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Active Cases
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status IN ('Open', 'In Progress', 'Pending') AND DATE(COALESCE(created_at, date_reported)) BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Clearance Rate
        $stats['clearance_rate'] = $stats['total_cases'] > 0 ? 
            round(($stats['solved_cases'] / $stats['total_cases']) * 100, 1) : 0;
            
        // TOP OFFENSE CATEGORIES - FROM case_type COLUMN (CORRECTED)
        // First check if case_type column exists
        $columnCheck = $db->query("SHOW COLUMNS FROM $tableName LIKE 'case_type'");
        $caseTypeExists = $columnCheck->rowCount() > 0;
        
        if ($caseTypeExists) {
            $stmt = $db->prepare("
                SELECT 
                    case_type, 
                    COUNT(*) as count
                FROM $tableName 
                WHERE DATE(COALESCE(created_at, date_reported)) BETWEEN ? AND ? AND case_type IS NOT NULL
                GROUP BY case_type 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $stmt->execute([$start_date, $end_date]);
            $stats['top_offenses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stats['top_offenses'] = [];
        }

        // Get today's cases
        $today_query = "SELECT COUNT(*) as today_cases FROM $tableName WHERE DATE(COALESCE(created_at, date_reported)) = CURDATE()";
        $stmt = $db->query($today_query);
        $stats['today_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['today_cases'] ?? 0;
        
        // Get active officers count
        $usersTableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
        if ($usersTableExists) {
            $officers_query = "SELECT COUNT(*) as active_officers FROM users WHERE role IN ('officer', 'admin', 'chief') AND is_active = 1";
            $stmt = $db->query($officers_query);
            $stats['active_officers'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_officers'] ?? 1;
        } else {
            $stats['active_officers'] = 1;
        }
        
        // Add empty arrays for other chart data to prevent errors
        $stats['monthly_trends'] = [];
        $stats['case_types'] = [];
        $stats['status_distribution'] = [];
        $stats['officer_performance'] = [];
        
    } catch (Exception $e) {
        // Log the error for debugging (optional, comment out in production)
        error_log("Dashboard stats error: " . $e->getMessage());
        // Return default values if there's an error
        return [
            'total_cases' => 0,
            'solved_cases' => 0,
            'active_cases' => 0,
            'clearance_rate' => 0,
            'today_cases' => 0,
            'active_officers' => 1,
            'top_offenses' => [],
            'monthly_trends' => [],
            'case_types' => [],
            'status_distribution' => [],
            'officer_performance' => []
        ];
    }
    
    return $stats;
}
// Get dashboard statistics
$stats = getDashboardStats($db, $tableName, $start_date, $end_date);

// Prepare data for JavaScript
$monthly_labels = [];
$monthly_cases = [];
$monthly_solved = [];

foreach ($stats['monthly_trends'] as $month) {
    $monthly_labels[] = date('M Y', strtotime($month['month'] . '-01'));
    $monthly_cases[] = $month['total_cases'];
    $monthly_solved[] = $month['solved_cases'];
}
// Prepare top offenses data for initial page load
$top_offense_labels = [];
$top_offense_data = [];
foreach ($stats['top_offenses'] as $offense) {
    $top_offense_labels[] = $offense['case_type'];
    $top_offense_data[] = $offense['count'];
}

// If no offenses data, use fallback
if (empty($top_offense_labels)) {
    $top_offense_labels = ['Theft', 'Robbery', 'Fraud', 'Domestic Violence'];
    $top_offense_data = [1, 1, 1, 1];
}

// Prepare case types data
$case_type_labels = [];
$case_type_data = [];
foreach ($stats['case_types'] as $case_type) {
    $case_type_labels[] = $case_type['case_type'];
    $case_type_data[] = $case_type['count'];
}

// Prepare status distribution data
$status_labels = [];
$status_data = [];
$status_colors = ['#ff6b6b', '#ffa726', '#66bb6a', '#42a5f5', '#ab47bc'];

foreach ($stats['status_distribution'] as $status) {
    $status_labels[] = $status['status'];
    $status_data[] = $status['count'];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
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
    
    <!-- Chart.js (Fixed: Using official jsDelivr CDN for latest stable) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* ... (All your existing CSS unchanged) ... */
        body {
            box-sizing: border-box;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Remove temporary debug styles to avoid interference */
        /* #topOffensesContainer {
            border: 2px solid red !important;
            background: yellow !important;
            min-height: 400px !important;
        } */
        
        /* Animated background elements */
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
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            box-shadow: 0 8px 40px rgba(0,0,0,0.12);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 900;
            font-size: 1.5rem;
            color: #1e3c72 !important;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .chief-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
            border: 1px solid rgba(255,255,255,0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        
        .navbar-nav {
            align-items: center;
        }
        
        .nav-link-custom {
            background: rgba(255, 255, 255, 0.9);
            color: #495057;
            border-radius: 12px;
            padding: 10px 20px;
            margin: 0 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .nav-link-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            transition: all 0.4s ease;
            z-index: -1;
        }
        
        .nav-link-custom:hover::before {
            left: 0;
        }
        
        .nav-link-custom:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 60, 114, 0.3);
        }
        
        .dashboard-container {
            padding: 40px 0;
        }
        
        .command-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .command-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 4s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .command-header h1 {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            margin-bottom: 15px;
            font-size: 3.2rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .command-header p {
            color: #6c757d;
            font-size: 1.3rem;
            margin-bottom: 30px;
            font-weight: 500;
        }
        
        .date-filter-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            margin-bottom: 40px;
        }
        
        .date-filter-container h5 {
            color: #1e3c72;
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .date-input {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }
        
        .date-input:focus {
            outline: none;
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        
        .filter-btn {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(30, 60, 114, 0.3);
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(30, 60, 114, 0.4);
            color: white;
        }
        
        .analytics-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-bottom: 30px;
            overflow: hidden;
            position: relative;
        }
        
        .analytics-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
        
        .analytics-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 70px rgba(0,0,0,0.15);
        }
        
        .card-header-analytics {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 25px 30px;
            border: none;
            font-weight: 700;
            font-size: 1.3rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .card-body-analytics {
            padding: 30px;
            position: relative;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
        
        .chart-container.large {
            height: 500px;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .kpi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px 25px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .kpi-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(45deg);
            transition: all 0.6s ease;
        }
        
        .kpi-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.4);
        }
        
        .kpi-card:hover::before {
            transform: rotate(45deg) translate(20px, 20px);
        }
        
        .kpi-number {
            font-size: 2.8rem;
            font-weight: 900;
            margin-bottom: 8px;
            display: block;
            position: relative;
            z-index: 2;
        }
        
        .kpi-label {
            font-size: 0.95rem;
            opacity: 0.95;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }
        
        .kpi-card.success {
            background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
            box-shadow: 0 10px 30px rgba(86, 171, 47, 0.3);
        }
        
        .kpi-card.warning {
            background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            box-shadow: 0 10px 30px rgba(247, 151, 30, 0.3);
        }
        
        .kpi-card.danger {
            background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
            box-shadow: 0 10px 30px rgba(252, 74, 26, 0.3);
        }
        
        .officer-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .officer-table th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-weight: 700;
            padding: 20px 15px;
            border: none;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }
        
        .officer-table td {
            padding: 18px 15px;
            border-bottom: 1px solid #f8f9fa;
            font-weight: 500;
            vertical-align: middle;
        }
        
        .performance-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
      .performance-badge.excellent {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    color: white;
}

.performance-badge.good {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.performance-badge.average {
    background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    color: white;
}

.performance-badge.needs-improvement {
    background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%);
    color: white;
}
        
        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .quick-link-card {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid transparent;
            color: #495057;
            padding: 30px 25px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        
        .quick-link-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            transition: all 0.4s ease;
            z-index: -1;
        }
        
        .quick-link-card:hover::before {
            left: 0;
        }
        
        .quick-link-card:hover {
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(30, 60, 114, 0.3);
        }
        
        /* Real-time update styles */
        .live-indicator {
            display: inline-flex;
            align-items: center;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 15px;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .blink {
            animation: blink 2s infinite;
        }
        
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }
        
        .number-pulse {
            animation: pulse 0.5s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        /* Debug panel */
        .debug-panel {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 9999;
            font-size: 12px;
            max-width: 300px;
        }
        
        .no-data-message {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 50px 20px;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .chief-badge {
                padding: 6px 15px;
                font-size: 0.8rem;
            }
            
            .nav-link-custom {
                padding: 8px 15px;
                margin: 5px 0;
            }
            
            .command-header {
                padding: 30px 20px;
            }
            
            .command-header h1 {
                font-size: 2.5rem;
            }
            
            .kpi-number {
                font-size: 2.2rem;
            }
            
            .chart-container {
                height: 300px;
            }
            
            .chart-container.large {
                height: 350px;
            }
            
            .dashboard-container {
                padding: 20px 0;
            }
        }
        
        /* Language Selector in Navbar */
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
            background: #1e3c72;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                <?php echo t('navbar_brand'); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="dashboard.php">
                            <i class="fas fa-chart-line me-2"></i> <?php echo t('nav_dashboard'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="reports.php">
                            <i class="fas fa-file-alt me-2"></i> <?php echo t('nav_reports'); ?>
                        </a>
                    </li>
                   
                </ul>
                
                <div class="d-flex align-items-center ms-lg-3">
                    <span class="chief-badge me-2">
                        <i class="fas fa-star me-1"></i>
                        <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                <div class="lang-selector" style="margin-left: 5cm;">
    <form method="post">
        <select name="lang" onchange="this.form.submit()" 
                class="form-select form-select-sm" 
                style="background-color: #ffffff; color: #000000; border: 2px solid #000;">
            <option value="en" <?php echo $current_lang=='en'?'selected':''; ?>>
                <?php echo t('lang_english'); ?>
            </option>
            <option value="am" <?php echo $current_lang=='am'?'selected':''; ?>>
                <?php echo t('lang_amharic'); ?>
            </option>
            <option value="om" <?php echo $current_lang=='om'?'selected':''; ?>>
                <?php echo t('lang_oromo'); ?>
            </option>
        </select>
    </form>
</div>

                    <a href="../logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('<?php echo addslashes(t('logout_confirm')); ?>')">
                        <i class="fas fa-sign-out-alt me-1"></i> <?php echo t('logout'); ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Dashboard Content -->
    <div class="dashboard-container">
        <div class="container-fluid">
            <!-- Command Header -->
            <div class="command-header">
                <h1><i class="fas fa-chart-line me-3"></i><?php echo t('header_title'); ?></h1>
                <p><?php echo t('header_subtitle'); ?></p>
                
                <!-- Date Range Filter -->
                <form id="dateFilterForm" method="GET" class="d-flex justify-content-center align-items-center mt-4 flex-wrap gap-2">
                    <select id="quickSelect" class="form-select" style="width: auto; min-width: 150px;">
                        <option value=""><?php echo t('date_filter_custom'); ?></option>
                        <option value="7" <?php echo (strtotime($end_date) - strtotime($start_date)) / 86400 == 7 ? 'selected' : ''; ?>><?php echo t('date_filter_7days'); ?></option>
                        <option value="30" <?php echo (strtotime($end_date) - strtotime($start_date)) / 86400 == 30 ? 'selected' : ''; ?>><?php echo t('date_filter_30days'); ?></option>
                        <option value="90" <?php echo (strtotime($end_date) - strtotime($start_date)) / 86400 == 90 ? 'selected' : ''; ?>><?php echo t('date_filter_90days'); ?></option>
                        <option value="365" <?php echo (strtotime($end_date) - strtotime($start_date)) / 86400 == 365 ? 'selected' : ''; ?>><?php echo t('date_filter_year'); ?></option>
                    </select>
                    <input type="date" id="startDate" name="start_date" class="form-control date-input" style="width: auto; min-width: 160px;" value="<?php echo $start_date; ?>">
                    <input type="date" id="endDate" name="end_date" class="form-control date-input" style="width: auto; min-width: 160px;" value="<?php echo $end_date; ?>">
                    <button type="submit" class="btn filter-btn">
                        <i class="fas fa-search me-1"></i><?php echo t('btn_filter'); ?>
                    </button>
                </form>
            </div>
            
            <!-- KPI Overview -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <span class="kpi-number" id="totalCases"><?php echo $stats['total_cases']; ?></span>
                    <div class="kpi-label"><?php echo t('kpi_total_cases'); ?></div>
                </div>
                <div class="kpi-card success">
                    <span class="kpi-number" id="solvedCases"><?php echo $stats['solved_cases']; ?></span>
                    <div class="kpi-label"><?php echo t('kpi_solved_cases'); ?></div>
                </div>
                <div class="kpi-card warning">
                    <span class="kpi-number" id="activeCases"><?php echo $stats['active_cases']; ?></span>
                    <div class="kpi-label"><?php echo t('kpi_active_cases'); ?></div>
                </div>
                <div class="kpi-card danger">
                    <span class="kpi-number" id="clearanceRate"><?php echo $stats['clearance_rate']; ?>%</span>
                    <div class="kpi-label"><?php echo t('kpi_clearance_rate'); ?></div>
                </div>
            </div>
            
            <div class="row">
                <!-- Top Offenses Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-chart-bar me-2"></i><?php echo t('chart_top_offenses'); ?>
                        </div>
                        <div class="card-body-analytics">
                            <div class="chart-container" id="topOffensesContainer">
                                <canvas id="topOffensesChart" style="display: <?php echo empty($top_offense_labels) ? 'none' : 'block'; ?>;"></canvas>
                                <div id="offensesFallback" class="no-data-message" style="display: <?php echo empty($top_offense_labels) ? 'block' : 'none'; ?>;">
                                    <i class="fas fa-chart-bar fa-3x mb-3 text-muted"></i>
                                    <p><?php echo t('no_offense_data'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Officer Performance Table -->
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-users me-2"></i><?php echo t('chart_officer_performance'); ?>
                        </div>
                        <div class="card-body-analytics">
                            <div class="table-responsive">
                                <table class="table officer-table" id="officerPerformanceTable">
                                    <thead>
                                        <tr>
                                            <th><?php echo t('table_officer'); ?></th>
                                            <th><?php echo t('table_records_added'); ?></th>
                                            <th><?php echo t('table_cases_closed'); ?></th>
                                            <th><?php echo t('table_performance'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="officerTableBody">
                                        <?php if (empty($stats['officer_performance'])): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    <?php echo t('no_officer_data'); ?>
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($stats['officer_performance'] as $officer): 
                                                $performance = 'average';
                                                if ($officer['cases_closed'] >= 20) $performance = 'excellent';
                                                elseif ($officer['cases_closed'] >= 10) $performance = 'good';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-user-shield me-2 text-primary"></i>
                                                            <strong><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold"><?php echo $officer['records_added']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="fw-bold"><?php echo $officer['cases_closed']; ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="performance-badge <?php echo $performance; ?>">
                                                            <?php echo t('performance_' . $performance); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Links Section -->
            <div class="analytics-card">
                <div class="card-header-analytics">
                    <i class="fas fa-external-link-alt me-2"></i><?php echo t('quick_links_title'); ?>
                </div>
                <div class="card-body-analytics">
                    <div class="quick-links-grid">
                        <a href="reports.php" class="quick-link-card">
                            <i class="fas fa-file-export"></i>
                            <h5><?php echo t('quick_link_reports'); ?></h5>
                            <p><?php echo t('quick_link_reports_desc'); ?></p>
                        </a>
                   
                        <a href="manage_cases.php" class="quick-link-card">
                            <i class="fas fa-users-cog"></i>
                            <h5><?php echo t('quick_link_cases'); ?></h5>
                            <p><?php echo t('quick_link_cases_desc'); ?></p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Debug Panel -->


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
<script>
    // Global variables
    let topOffensesChart;
    let realtimeUpdateInterval;
    
    // PHP data for JavaScript
    const offensesData = {
        labels: <?php echo json_encode($top_offense_labels); ?>,
        data: <?php echo json_encode($top_offense_data); ?>
    };

    // Real-time statistics storage
    let currentStats = {
        total_cases: <?php echo $stats['total_cases']; ?>,
        solved_cases: <?php echo $stats['solved_cases']; ?>,
        active_cases: <?php echo $stats['active_cases']; ?>,
        clearance_rate: <?php echo $stats['clearance_rate']; ?>,
        today_cases: <?php echo $stats['today_cases']; ?>,
        active_officers: <?php echo $stats['active_officers']; ?>
    };
    
    // Function to generate colors dynamically
    function generateColors(num) {
        const colors = [];
        for (let i = 0; i < num; i++) {
            const hue = (i * 360 / num);
            colors.push(`hsl(${hue}, 70%, 50%)`);
        }
        return colors;
    }
    
    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 Dashboard initializing...');
        console.log('Initial offenses data:', offensesData);
        initializeDateFilters();
        initializeCharts();
        animateKPIs();
        startRealtimeUpdates();
    });
    
    // Initialize date filters
    function initializeDateFilters() {
        const quickSelect = document.getElementById('quickSelect');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');
        
        if (quickSelect) {
            quickSelect.addEventListener('change', function() {
                if (this.value) {
                    const days = parseInt(this.value);
                    const end = new Date();
                    const start = new Date();
                    start.setDate(end.getDate() - days);
                    
                    if (startDate) startDate.value = start.toISOString().split('T')[0];
                    if (endDate) endDate.value = end.toISOString().split('T')[0];
                    
                    // Auto-submit form when quick select changes
                    const form = document.getElementById('dateFilterForm');
                    if (form) form.submit();
                }
            });
        }
    }
    
    // Initialize charts (Simplified: Only Top Offenses)
    function initializeCharts() {
        console.log('📊 Initializing charts...');
        
        // Check if Chart.js loaded
        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js not loaded! Check CDN link.');
            showNotification('<?php echo addslashes(t("notification_chart_failed")); ?>', 'error');
            return;
        }
        
        // Initialize Top Offenses Chart
        const offensesCtx = document.getElementById('topOffensesChart');
        const fallbackDiv = document.getElementById('offensesFallback');
        if (offensesCtx) {
            console.log('Creating offenses chart with:', offensesData);
            if (offensesData.labels.length === 0) {
                console.warn('No data for offenses chart, showing fallback message');
                if (fallbackDiv) fallbackDiv.style.display = 'block';
                offensesCtx.style.display = 'none';
            } else {
                const dynamicColors = generateColors(offensesData.labels.length);
                topOffensesChart = new Chart(offensesCtx, {
                    type: 'bar',
                    data: {
                        labels: offensesData.labels,
                        datasets: [{
                            label: 'Number of Cases',
                            data: offensesData.data,
                            backgroundColor: dynamicColors,
                            borderColor: dynamicColors.map(c => c.replace('70%', '50%').replace('50%', '30%')),
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Number of Cases'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Offense Type'
                                }
                            }
                        }
                    }
                });
                console.log('✅ Top offenses chart initialized with', offensesData.labels.length, 'items');
            }
        } else {
            console.error('❌ Top offenses chart canvas not found');
        }
    }
    
    // Start real-time updates
    function startRealtimeUpdates() {
        console.log('🔄 Starting real-time updates...');
        updateDebugInfo('<?php echo t("debug_realtime_started"); ?>');
        
        // Update immediately
        updateRealtimeStats();
        
        // Set up interval for updates every 30 seconds
        realtimeUpdateInterval = setInterval(updateRealtimeStats, 30000);
    }
    
    // Update real-time statistics
    function updateRealtimeStats() {
        const startDate = document.getElementById('startDate')?.value || '<?php echo $start_date; ?>';
        const endDate = document.getElementById('endDate')?.value || '<?php echo $end_date; ?>';
        const url = `api/realtime_stats.php?start_date=${startDate}&end_date=${endDate}`;
        
        console.log('🔄 Fetching real-time data from:', url);
        updateDebugInfo(`<?php echo t("debug_fetching_data"); ?> ${startDate} to ${endDate}...`);
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ API Response received:', data);
                
                if (data.success && data.data) {
                    updateDebugInfo(`<?php echo t("debug_data_loaded"); ?> ${data.data.top_offenses ? data.data.top_offenses.length : 0} offense types`);
                    updateDashboardData(data.data);
                    showNotification('<?php echo addslashes(t("notification_data_loaded")); ?>', 'success');
                } else {
                    throw new Error(data.message || 'Invalid API response');
                }
            })
            .catch(error => {
                console.error('❌ API Fetch Failed:', error);
                updateDebugInfo('<?php echo t("debug_error"); ?> ' + error.message);
                showNotification('<?php echo addslashes(t("notification_fetch_failed")); ?> ' + error.message, 'error');
            });
    }
    
    // Update dashboard with new data (Simplified)
    function updateDashboardData(newStats) {
        console.log('📊 Updating dashboard with real data:', newStats);
        
        // Store current stats
        currentStats = {...newStats};
        
        // Update KPI numbers with REAL data
        updateKPI('totalCases', newStats.total_cases || 0);
        updateKPI('solvedCases', newStats.solved_cases || 0);
        updateKPI('activeCases', newStats.active_cases || 0);
        updateKPI('clearanceRate', newStats.clearance_rate || 0, '%');
        
        // Update Top Offenses Chart with REAL data
        if (newStats.top_offenses && newStats.top_offenses.length > 0) {
            console.log('🎯 Updating offenses chart with:', newStats.top_offenses);
            updateTopOffensesChart(newStats.top_offenses);
        } else {
            console.warn('No offenses data available');
            showNoDataMessage();
        }
        
        if (newStats.officer_performance && newStats.officer_performance.length > 0) {
            updateOfficerPerformanceTable(newStats.officer_performance);
        }
        
        // Update last update time
        updateLastRefreshedTime();
        
        console.log('✅ Dashboard updated with real data');
    }

    // Update top offenses chart
    function updateTopOffensesChart(offensesData) {
        console.log('📈 Rendering offenses chart:', offensesData);
        
        if (!offensesData || offensesData.length === 0) {
            console.warn('No offenses data to display');
            showNoDataMessage();
            return;
        }
        
        const labels = offensesData.map(item => item.case_type || 'Unknown');
        const data = offensesData.map(item => parseInt(item.count) || 0);
        
        console.log('Chart labels:', labels);
        console.log('Chart data:', data);
        
        const fallbackDiv = document.getElementById('offensesFallback');
        const canvas = document.getElementById('topOffensesChart');
        
        if (fallbackDiv) fallbackDiv.style.display = 'none';
        if (canvas) canvas.style.display = 'block';
        
        const dynamicColors = generateColors(labels.length);
        
        if (topOffensesChart) {
            topOffensesChart.data.labels = labels;
            topOffensesChart.data.datasets[0].data = data;
            topOffensesChart.data.datasets[0].backgroundColor = dynamicColors;
            topOffensesChart.data.datasets[0].borderColor = dynamicColors.map(c => c.replace('70%', '50%').replace('50%', '30%'));
            topOffensesChart.update('active');
            console.log('✅ Top offenses chart updated successfully');
        } else {
            console.error('Top offenses chart not initialized - reinitializing');
            // Re-initialize if needed
            initializeCharts();
        }
    }

    // Show no data message (Simplified)
    function showNoDataMessage() {
        const fallbackDiv = document.getElementById('offensesFallback');
        const canvas = document.getElementById('topOffensesChart');
        if (fallbackDiv) fallbackDiv.style.display = 'block';
        if (canvas) canvas.style.display = 'none';
    }

    // Update officer performance table
    function updateOfficerPerformanceTable(officersData) {
        const tableBody = document.getElementById('officerTableBody');
        
        if (tableBody && officersData.length > 0) {
            let html = '';
            
            officersData.forEach(officer => {
                const fullName = `${officer.first_name || ''} ${officer.last_name || ''}`.trim();
                const totalCases = parseInt(officer.total_cases) || 0;
                const solvedCases = parseInt(officer.solved_cases) || 0;
                const clearanceRate = parseFloat(officer.clearance_rate) || 0;
                
                let performance = 'average';
                if (clearanceRate >= 80) performance = 'excellent';
                else if (clearanceRate >= 60) performance = 'good';
                else if (clearanceRate >= 40) performance = 'average';
                else performance = 'needs-improvement';
                
                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-shield me-2 text-primary"></i>
                                <strong>${fullName || 'Unknown Officer'}</strong>
                            </div>
                        </td>
                        <td><span class="fw-bold">${totalCases}</span></td>
                        <td><span class="fw-bold">${solvedCases}</span></td>
                        <td>
                            <span class="performance-badge ${performance}">
                                ${clearanceRate.toFixed(1)}%
                            </span>
                        </td>
                    </tr>
                `;
            });
            
            tableBody.innerHTML = html;
        }
    }
    
    // Update individual KPI with animation
    function updateKPI(elementId, newValue, suffix = '') {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const currentValue = parseFloat(element.textContent.replace(/[^\d.]/g, ''));
        newValue = parseFloat(newValue) || 0;
        
        if (Math.abs(currentValue - newValue) > 0.1) {
            animateNumberChange(elementId, currentValue, newValue, suffix);
        } else {
            element.textContent = newValue.toLocaleString() + suffix;
        }
    }
    
    // Animate number change
    function animateNumberChange(elementId, startValue, endValue, suffix = '') {
        const element = document.getElementById(elementId);
        const duration = 1000;
        const startTime = performance.now();
        
        element.classList.add('number-pulse');
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = startValue + (endValue - startValue) * easeOutQuart(progress);
            
            if (suffix === '%') {
                element.textContent = current.toFixed(1) + suffix;
            } else {
                element.textContent = Math.floor(current).toLocaleString() + suffix;
            }
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.classList.remove('number-pulse');
            }
        }
        
        requestAnimationFrame(update);
    }
    
    // Update last refreshed time
    function updateLastRefreshedTime() {
        const lastUpdated = document.getElementById('lastUpdated');
        if (lastUpdated) {
            const now = new Date();
            lastUpdated.textContent = `Last updated: ${now.toLocaleTimeString()}`;
        }
    }
    
    // Animate KPI numbers on initial load
    function animateKPIs() {
        const kpis = [
            { id: 'totalCases', value: currentStats.total_cases },
            { id: 'solvedCases', value: currentStats.solved_cases },
            { id: 'activeCases', value: currentStats.active_cases },
            { id: 'clearanceRate', value: currentStats.clearance_rate, suffix: '%' }
        ];
        
        kpis.forEach(kpi => {
            animateNumber(kpi.id, kpi.value, kpi.suffix || '');
        });
    }
    
    // Animate number counting
    function animateNumber(elementId, targetValue, suffix = '') {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const startValue = 0;
        const duration = 2000;
        const startTime = performance.now();
        
        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = startValue + (targetValue - startValue) * easeOutQuart(progress);
            
            if (suffix === '%') {
                element.textContent = currentValue.toFixed(1) + suffix;
            } else {
                element.textContent = Math.floor(currentValue).toLocaleString() + suffix;
            }
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            }
        }
        
        requestAnimationFrame(updateNumber);
    }
    
    // Easing function
    function easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed`;
        notification.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 10px;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-3"></i>
                <strong>${message}</strong>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Manual refresh function
    function manualRefresh() {
        console.log('🔄 Manual refresh triggered');
        updateDebugInfo('Manual refresh started...');
        updateRealtimeStats();
    }
    
    // Debug function
    function updateDebugInfo(message) {
        const debugInfo = document.getElementById('debugInfo');
        if (debugInfo) {
            debugInfo.innerHTML = message + '<br>' + new Date().toLocaleTimeString();
        }
    }
    
    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        if (realtimeUpdateInterval) {
            clearInterval(realtimeUpdateInterval);
        }
    });
</script>
</body>
</html>