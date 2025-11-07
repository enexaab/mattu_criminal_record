<?php
// dashboard.php
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/clerk_functions.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
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
        // Debug: Check if table exists and has data
        $checkTable = $db->query("SELECT COUNT(*) as count FROM $tableName")->fetch(PDO::FETCH_ASSOC);
        error_log("Table $tableName has {$checkTable['count']} records");
        
        // Debug: Check table structure
        $tableStructure = $db->query("DESCRIBE $tableName")->fetchAll(PDO::FETCH_COLUMN);
        error_log("Table structure: " . implode(', ', $tableStructure));
        
        // Total Cases - Fixed query
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE (created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?)");
        $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
        $stats['total_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        error_log("Total cases: {$stats['total_cases']}");

        // Solved Cases (Closed status) - Fixed query
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status = 'Closed' AND ((created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?))");
        $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
        $stats['solved_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        error_log("Solved cases: {$stats['solved_cases']}");

        // Active Cases - Fixed query
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM $tableName WHERE status IN ('Open', 'In Progress', 'Pending') AND ((created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?))");
        $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
        $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        error_log("Active cases: {$stats['active_cases']}");

        // Clearance Rate
        $stats['clearance_rate'] = $stats['total_cases'] > 0 ? 
            round(($stats['solved_cases'] / $stats['total_cases']) * 100, 1) : 0;
        error_log("Clearance rate: {$stats['clearance_rate']}%");
            
        // Case types distribution - Fixed query
        $stmt = $db->prepare("SELECT case_type, COUNT(*) as count FROM $tableName WHERE (created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?) GROUP BY case_type ORDER BY count DESC LIMIT 5");
        $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
        $stats['case_types'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Case types found: " . count($stats['case_types']));

        // Status distribution - Fixed query
        $stmt = $db->prepare("SELECT status, COUNT(*) as count FROM $tableName WHERE (created_at BETWEEN ? AND ?) OR (date_reported BETWEEN ? AND ?) GROUP BY status");
        $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
        $stats['status_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Status distribution: " . count($stats['status_distribution']));

        // Monthly trends (last 12 months) - Fixed query
        $monthly_query = "
            SELECT 
                DATE_FORMAT(COALESCE(created_at, date_reported), '%Y-%m') as month,
                COUNT(*) as total_cases,
                SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as solved_cases
            FROM $tableName 
            WHERE (created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)) 
               OR (date_reported >= DATE_SUB(NOW(), INTERVAL 12 MONTH))
            GROUP BY DATE_FORMAT(COALESCE(created_at, date_reported), '%Y-%m')
            ORDER BY month
        ";
        $stmt = $db->query($monthly_query);
        $stats['monthly_trends'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Monthly trends: " . count($stats['monthly_trends']));

        // Officer performance - Check if users table exists and has proper structure
        $usersTableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
        if ($usersTableExists) {
            // Check users table structure
            $usersStructure = $db->query("DESCRIBE users")->fetchAll(PDO::FETCH_COLUMN);
            error_log("Users table structure: " . implode(', ', $usersStructure));
            
            $officer_query = "
                SELECT 
                    u.id,
                    u.first_name,
                    u.last_name,
                    COUNT(c.id) as records_added,
                    SUM(CASE WHEN c.status = 'Closed' THEN 1 ELSE 0 END) as cases_closed
                FROM users u
                LEFT JOIN $tableName c ON u.id = c.created_by
                WHERE (c.created_at BETWEEN ? AND ?) OR (c.date_reported BETWEEN ? AND ?)
                GROUP BY u.id, u.first_name, u.last_name
                HAVING records_added > 0
                ORDER BY records_added DESC
                LIMIT 10
            ";
            $stmt = $db->prepare($officer_query);
            $stmt->execute([$start_date . ' 00:00:00', $end_date . ' 23:59:59', $start_date, $end_date]);
            $stats['officer_performance'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Officer performance records: " . count($stats['officer_performance']));
        } else {
            $stats['officer_performance'] = [];
            error_log("Users table does not exist");
        }
        
    } catch (Exception $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        // Return default values if there's an error
        return [
            'total_cases' => 0,
            'solved_cases' => 0,
            'active_cases' => 0,
            'clearance_rate' => 0,
            'case_types' => [],
            'status_distribution' => [],
            'monthly_trends' => [],
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
$color_index = 0;

foreach ($stats['status_distribution'] as $status) {
    $status_labels[] = $status['status'];
    $status_data[] = $status['count'];
    $color_index = ($color_index + 1) % count($status_colors);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Chief Dashboard - Mattu City Strategic Command Center</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    
    <style>
        body {
            box-sizing: border-box;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
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
        
        .navbar-toggler {
            border: none;
            padding: 10px;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(30, 60, 114, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .navbar-collapse {
            justify-content: space-between;
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
        
        .officer-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
            transform: scale(1.01);
            transition: all 0.2s ease;
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
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 25px;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #1e3c72;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        
        .quick-link-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            display: block;
            transition: all 0.3s ease;
        }
        
        .quick-link-card:hover i {
            transform: scale(1.1);
        }
        
        .trend-indicator {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .trend-indicator.up {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }
        
        .trend-indicator.down {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .trend-indicator.stable {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
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
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #1a3461 0%, #245087 100%);
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
                Mattu City Police Command
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="dashboard.php">
                            <i class="fas fa-chart-line me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="reports.php">
                            <i class="fas fa-file-alt me-2"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="analytics.php">
                            <i class="fas fa-analytics me-2"></i> Analytics
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center ms-lg-3">
                    <span class="chief-badge me-2">
                        <i class="fas fa-star me-1"></i>
                        <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
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
                <h1><i class="fas fa-chart-line me-3"></i>Strategic Command Dashboard</h1>
                <p>Real-time crime analytics and performance insights for Mattu City</p>
                
                <!-- Date Range Filter -->
                <div class="date-filter-container">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Analysis Period</h5>
                    <form method="GET" id="dateFilterForm">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">From Date</label>
                                <input type="date" class="form-control date-input" name="start_date" id="startDate" 
                                       value="<?php echo htmlspecialchars($start_date); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">To Date</label>
                                <input type="date" class="form-control date-input" name="end_date" id="endDate" 
                                       value="<?php echo htmlspecialchars($end_date); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Quick Select</label>
                                <select class="form-control date-input" id="quickSelect">
                                    <option value="">Custom Range</option>
                                    <option value="7">Last 7 Days</option>
                                    <option value="30" selected>Last 30 Days</option>
                                    <option value="90">Last 3 Months</option>
                                    <option value="365">Last 12 Months</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn filter-btn w-100">
                                    <i class="fas fa-sync-alt me-2"></i>Update Dashboard
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- KPI Overview -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <span class="kpi-number" id="totalCases"><?php echo $stats['total_cases']; ?></span>
                    <div class="kpi-label">Total Cases Filed</div>
                </div>
                <div class="kpi-card success">
                    <span class="kpi-number" id="solvedCases"><?php echo $stats['solved_cases']; ?></span>
                    <div class="kpi-label">Cases Solved</div>
                </div>
                <div class="kpi-card warning">
                    <span class="kpi-number" id="activeCases"><?php echo $stats['active_cases']; ?></span>
                    <div class="kpi-label">Active Cases</div>
                </div>
                <div class="kpi-card danger">
                    <span class="kpi-number" id="clearanceRate"><?php echo $stats['clearance_rate']; ?>%</span>
                    <div class="kpi-label">Clearance Rate</div>
                </div>
            </div>
            
            <div class="row">
                <!-- Crime Trends Chart -->
                <div class="col-lg-8 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-chart-line me-2"></i>Crime Trends Analysis (12 Months)
                        </div>
                        <div class="card-body-analytics">
                            <div class="chart-container large" id="crimeTrendsContainer">
                                <canvas id="crimeTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Case Status Breakdown -->
                <div class="col-lg-4 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-chart-pie me-2"></i>Case Status Distribution
                        </div>
                        <div class="card-body-analytics">
                            <div class="chart-container" id="statusBreakdownContainer">
                                <canvas id="statusBreakdownChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Top Offenses Chart -->
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-chart-bar me-2"></i>Top Offense Categories
                        </div>
                        <div class="card-body-analytics">
                            <div class="chart-container" id="topOffensesContainer">
                                <canvas id="topOffensesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Officer Performance Table -->
                <div class="col-lg-6 mb-4">
                    <div class="analytics-card">
                        <div class="card-header-analytics">
                            <i class="fas fa-users me-2"></i>Officer Performance Metrics
                        </div>
                        <div class="card-body-analytics">
                            <div class="table-responsive">
                                <table class="table officer-table" id="officerPerformanceTable">
                                    <thead>
                                        <tr>
                                            <th>Officer</th>
                                            <th>Records Added</th>
                                            <th>Cases Closed</th>
                                            <th>Performance</th>
                                        </tr>
                                    </thead>
                                    <tbody id="officerTableBody">
                                        <?php if (empty($stats['officer_performance'])): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">
                                                    No officer performance data available
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
                                                            <?php echo strtoupper($performance); ?>
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
                    <i class="fas fa-external-link-alt me-2"></i>Strategic Command Links
                </div>
                <div class="card-body-analytics">
                    <div class="quick-links-grid">
                        <a href="reports.php" class="quick-link-card">
                            <i class="fas fa-file-export"></i>
                            <h5>Generate Reports</h5>
                            <p>Create detailed analytical reports for stakeholders</p>
                        </a>
                        <a href="analytics.php" class="quick-link-card">
                            <i class="fas fa-chart-area"></i>
                            <h5>Advanced Analytics</h5>
                            <p>Deep dive into crime patterns and predictive insights</p>
                        </a>
                        <a href="manage_cases.php" class="quick-link-card">
                            <i class="fas fa-users-cog"></i>
                            <h5>Manage Cases</h5>
                            <p>View and manage all criminal cases</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables for charts
        let crimeTrendsChart, statusBreakdownChart, topOffensesChart;
        
        // PHP data for JavaScript
        const monthlyData = {
            labels: <?php echo json_encode($monthly_labels); ?>,
            cases: <?php echo json_encode($monthly_cases); ?>,
            solved: <?php echo json_encode($monthly_solved); ?>
        };
        
        const statusData = {
            labels: <?php echo json_encode($status_labels); ?>,
            data: <?php echo json_encode($status_data); ?>,
            colors: ['#ff6b6b', '#ffa726', '#66bb6a', '#42a5f5', '#ab47bc']
        };
        
        const offensesData = {
            labels: <?php echo json_encode($case_type_labels); ?>,
            data: <?php echo json_encode($case_type_data); ?>
        };
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeDateFilters();
            initializeCharts();
            animateKPIs();
        });
        
        // Initialize date filters
        function initializeDateFilters() {
            const quickSelect = document.getElementById('quickSelect');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            
            quickSelect.addEventListener('change', function() {
                if (this.value) {
                    const days = parseInt(this.value);
                    const end = new Date();
                    const start = new Date();
                    start.setDate(start.getDate() - days);
                    
                    startDate.value = start.toISOString().split('T')[0];
                    endDate.value = end.toISOString().split('T')[0];
                    
                    // Auto-submit form when quick select changes
                    document.getElementById('dateFilterForm').submit();
                }
            });
        }
        
        // Initialize charts with real data
        function initializeCharts() {
            // Crime Trends Chart
            const trendsCtx = document.getElementById('crimeTrendsChart').getContext('2d');
            crimeTrendsChart = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'Cases Filed',
                        data: monthlyData.cases,
                        borderColor: '#1e3c72',
                        backgroundColor: 'rgba(30, 60, 114, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#1e3c72',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }, {
                        label: 'Cases Solved',
                        data: monthlyData.solved,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#28a745',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#1e3c72',
                            borderWidth: 1,
                            cornerRadius: 10,
                            displayColors: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
            
            // Status Breakdown Chart
            const statusCtx = document.getElementById('statusBreakdownChart').getContext('2d');
            statusBreakdownChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.data,
                        backgroundColor: statusData.colors,
                        borderColor: '#fff',
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${context.parsed} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
            
            // Top Offenses Chart
            const offensesCtx = document.getElementById('topOffensesChart').getContext('2d');
            topOffensesChart = new Chart(offensesCtx, {
                type: 'bar',
                data: {
                    labels: offensesData.labels,
                    datasets: [{
                        label: 'Number of Cases',
                        data: offensesData.data,
                        backgroundColor: 'rgba(30, 60, 114, 0.8)',
                        borderColor: '#1e3c72',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            cornerRadius: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Animate KPI numbers
        function animateKPIs() {
            const kpis = [
                { id: 'totalCases', value: <?php echo $stats['total_cases']; ?> },
                { id: 'solvedCases', value: <?php echo $stats['solved_cases']; ?> },
                { id: 'activeCases', value: <?php echo $stats['active_cases']; ?> },
                { id: 'clearanceRate', value: <?php echo $stats['clearance_rate']; ?>, suffix: '%' }
            ];
            
            kpis.forEach(kpi => {
                animateNumber(kpi.id, kpi.value, kpi.suffix || '');
            });
        }
        
        // Animate number counting
        function animateNumber(elementId, targetValue, suffix = '') {
            const element = document.getElementById(elementId);
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
                min-width: 350px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.2);
                border-radius: 15px;
                backdrop-filter: blur(10px);
                border: none;
            `;
            
            const icons = {
                success: 'check-circle',
                warning: 'exclamation-triangle',
                error: 'times-circle',
                info: 'info-circle'
            };
            
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${icons[type] || icons.info} me-3"></i>
                    <strong>${message}</strong>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
        
        // Auto-refresh dashboard every 5 minutes
        setInterval(() => {
            window.location.reload();
        }, 300000);
    </script>
</body>
</html>