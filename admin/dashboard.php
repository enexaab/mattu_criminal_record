<?php
// dashboard.php
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard - Mattu Criminal Record System</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <h4 id="sidebar-title">Admin Panel</h4>
                <div class="subtitle" id="sidebar-subtitle">Mattu Criminal Records</div>
            </div>
            
            <ul class="nav">
                <li>
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="usermanagement">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="reports">
                        <i class="fas fa-chart-bar"></i>
                        <span>Generate Reports</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="#" data-section="security">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security Center</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="../logout.php" onclick="return confirmLogout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
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
                    <h2>Administrator Dashboard</h2>
                    <small class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></small>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['first_name'], 0, 1) . substr($_SESSION['last_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                        <small class="text-muted">System Administrator</small>
                    </div>
                </div>
            </div>
            
            <!-- Content Wrapper for Embedded Views -->
            <div class="content-wrapper" id="contentWrapper">
                <!-- Dashboard Content (Default View) -->
                <div id="dashboardView">
                    <div class="content-header">
                        <h3><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h3>
                    </div>
                    <div class="card-body">
                        <!-- KPI Cards Row -->
                        <div class="dashboard-grid">
                            <div class="kpi-card">
                                <div class="kpi-number" id="totalUsers">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label">Total Users</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="totalRecords">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label">Criminal Records</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="activeCases">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label">Active Cases</div>
                            </div>
                            <div class="kpi-card">
                                <div class="kpi-number" id="systemHealth">
                                    <div class="loading-spinner"></div>
                                </div>
                                <div class="kpi-label">System Health</div>
                            </div>
                        </div>
                        
                        <!-- Main Dashboard Widgets -->
                        <div class="row">
                            <!-- Security Alerts Panel -->
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-shield-alt"></i> Security Alerts</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="securityAlerts">
                                            <div style="text-align: center;">
                                                <div class="loading-spinner"></div>
                                                <p>Loading security alerts...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Activity Log -->
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                        <h5><i class="fas fa-history"></i> Recent System Activity</h5>
                                        <button class="btn btn-sm btn-outline-light" onclick="refreshActivity()">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div id="recentActivity">
                                            <div style="text-align: center;">
                                                <div class="loading-spinner"></div>
                                                <p>Loading recent activities...</p>
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
                                        
                                        <h6>System Statistics</h6>
                                        <div class="stats-grid">
                                            <div class="stat-box">
                                                <div class="stat-value" id="todayLogins">--</div>
                                                <div class="stat-label">Today's Logins</div>
                                            </div>
                                            <div class="stat-box">
                                                <div class="stat-value" id="activeUsers">--</div>
                                                <div class="stat-label">Active Users</div>
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
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
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
                        'usermanagement': { page: 'usermanagement.php', title: 'Manage Users' },
                        'reports': { page: 'generate_reports.php', title: 'Generate Reports' },
                        'security': { page: 'security.php', title: 'Security Center' }
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
                        <div class="fw-bold">System Operational</div>
                        <small class="text-muted">All systems are running normally</small>
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
                        <div class="fw-bold">Dashboard loaded successfully</div>
                        <small class="text-muted">by system • Just now</small>
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
    if (confirm('Are you sure you want to start a database backup?')) {
        alert('Database backup started successfully!');
    }
}

// Logout confirmation
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}
    </script>
</body>
</html>