<?php
// dashboard.php
require '../includes/database.php';
require '../includes/auth.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get officer information
$officerId = $_SESSION['user_id'];
$officerName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$officerRole = $_SESSION['role'] ?? 'officer';

// Get dashboard statistics
$database = new Database();
$db = $database->getConnection();

// Get total cases count
$totalCases = 0;
$activeCases = 0;
$weeklyRecords = 0;
$pendingTasks = 0;

try {
    // Check which tables exist
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    $criminalRecordsExists = $db->query("SHOW TABLES LIKE 'criminal_records'")->rowCount() > 0;

    // Total cases
    if ($casesTableExists) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM cases WHERE assigned_officer_id = ? OR lead_officer_id = ?");
        $stmt->execute([$officerId, $officerId]);
        $totalCases = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Active cases
        $stmt = $db->prepare("SELECT COUNT(*) as active FROM cases WHERE (assigned_officer_id = ? OR lead_officer_id = ?) AND status IN ('Open', 'Active', 'Under Investigation')");
        $stmt->execute([$officerId, $officerId]);
        $activeCases = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    } elseif ($caseFilesTableExists) {
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM case_files WHERE lead_officer_id = ?");
        $stmt->execute([$officerId]);
        $totalCases = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as active FROM case_files WHERE lead_officer_id = ? AND status IN ('Open', 'Active', 'Under Investigation')");
        $stmt->execute([$officerId]);
        $activeCases = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    }

    // Weekly records
    if ($criminalRecordsExists) {
        $stmt = $db->prepare("SELECT COUNT(*) as weekly FROM criminal_records WHERE created_by = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stmt->execute([$officerId]);
        $weeklyRecords = $stmt->fetch(PDO::FETCH_ASSOC)['weekly'];
    }

    // Pending tasks (cases that need attention)
    if ($casesTableExists) {
        $stmt = $db->prepare("SELECT COUNT(*) as pending FROM cases WHERE (assigned_officer_id = ? OR lead_officer_id = ?) AND status IN ('Open', 'Pending', 'Under Investigation')");
        $stmt->execute([$officerId, $officerId]);
        $pendingTasks = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    } elseif ($caseFilesTableExists) {
        $stmt = $db->prepare("SELECT COUNT(*) as pending FROM case_files WHERE lead_officer_id = ? AND status IN ('Open', 'Pending', 'Under Investigation')");
        $stmt->execute([$officerId]);
        $pendingTasks = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
    }

} catch (Exception $e) {
    error_log("Dashboard statistics error: " . $e->getMessage());
    // Continue with default values if there's an error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Officer Dashboard - Mattu Criminal Record System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            box-sizing: border-box;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .dashboard-container {
            padding: 30px 0;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
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
        
        .quick-action-btn {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border: none;
            color: white;
            padding: 15px 25px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 233, 123, 0.3);
            color: white;
        }
        
        .quick-action-btn.secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .quick-action-btn.secondary:hover {
            box-shadow: 0 10px 25px rgba(240, 147, 251, 0.3);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .case-table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .case-table th {
            background: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 15px;
        }
        
        .case-table td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        
        .case-table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }
        
        .case-table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-closed {
            background: #f8d7da;
            color: #721c24;
        }
        
        .recent-item {
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }
        
        .recent-item:hover {
            background-color: #f8f9fa;
            margin: 0 -15px;
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        .recent-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 0.9rem;
        }
        
        .recent-record {
            background: #e3f2fd;
            color: #1565c0;
        }
        
        .recent-case {
            background: #f3e5f5;
            color: #7b1fa2;
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
        
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
            height: 20px;
            margin-bottom: 10px;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            color: white;
        }
        
        .welcome-section h2 {
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .welcome-section p {
            margin: 0;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px 0;
            }
            
            .card-body-custom {
                padding: 20px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>
                Mattu Criminal Records
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link-custom" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="search_records.php">
                            <i class="fas fa-search me-1"></i> Search Records
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="manage_cases.php">
                            <i class="fas fa-folder-open me-1"></i> My Cases
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="officer-badge me-3">
                        <i class="fas fa-user-shield me-1"></i>
                        Officer <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Dashboard Content -->
    <div class="dashboard-container">
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h2>Welcome back, Officer <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h2>
                <p>Ready to serve and protect. Your dashboard is updated and ready for action.</p>
            </div>
            
            <div class="row">
                <!-- Quick Actions Panel -->
                <div class="col-lg-4 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </div>
                        <div class="card-body-custom">
                            <a href="add_criminal_record.php" class="quick-action-btn">
                                <i class="fas fa-user-plus me-2"></i>
                                Add New Criminal Record
                            </a>
                            <a href="create_case.php" class="quick-action-btn secondary">
                                <i class="fas fa-folder-plus me-2"></i>
                                Create New Case File
                            </a>
                            
                            <!-- Officer Activity Stats -->
                            <div class="stats-card mt-3">
                                <div class="stats-number" id="weeklyRecords">
                                    <div class="loading-skeleton" style="width: 60px; height: 40px; margin: 0 auto;"></div>
                                </div>
                                <div class="stats-label">Records Added This Week</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- My Assigned Cases -->
                <div class="col-lg-8 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-clipboard-list me-2"></i>My Assigned Cases</span>
                            <button class="btn btn-sm btn-outline-light" onclick="refreshCases()">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                        <div class="card-body-custom">
                            <div id="assignedCasesTable">
                                <!-- Loading skeleton -->
                                <div class="table-responsive">
                                    <table class="table case-table">
                                        <thead>
                                            <tr>
                                                <th>Case ID</th>
                                                <th>Criminal Name</th>
                                                <th>Status</th>
                                                <th>Last Updated</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                                <td><div class="loading-skeleton"></div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Searches -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-history me-2"></i>Recent Searches
                        </div>
                        <div class="card-body-custom">
                            <div id="recentSearches">
                                <!-- Loading skeleton -->
                                <div class="recent-item">
                                    <div class="recent-icon recent-record">
                                        <div class="loading-skeleton" style="width: 20px; height: 20px;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="loading-skeleton" style="width: 80%;"></div>
                                        <div class="loading-skeleton" style="width: 60%; height: 15px;"></div>
                                    </div>
                                </div>
                                <div class="recent-item">
                                    <div class="recent-icon recent-case">
                                        <div class="loading-skeleton" style="width: 20px; height: 20px;"></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="loading-skeleton" style="width: 70%;"></div>
                                        <div class="loading-skeleton" style="width: 50%; height: 15px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System Notifications -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </div>
                        <div class="card-body-custom">
                            <div id="notifications">
                                <div class="text-center text-muted">
                                    <i class="fas fa-bell fa-2x mb-2"></i>
                                    <p>Loading notifications...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
  <script>
    // Global variables
    let refreshInterval;
    const officerId = <?php echo $officerId; ?>;
    
    // Initialize dashboard
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Dashboard initialized for officer:', officerId);
        loadDashboardData();
        startAutoRefresh();
    });
    
    // Load all dashboard data
    function loadDashboardData() {
        console.log('Loading dashboard data...');
        loadStatistics();
        loadAssignedCases();
        loadRecentActivity();
        loadNotifications();
    }
    
    // Load statistics
    function loadStatistics() {
        fetch(`dashboard_api.php?action=statistics&officer_id=${officerId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Statistics data:', data);
                if (data.success) {
                    document.getElementById('totalCases').textContent = data.data.total_cases;
                    document.getElementById('activeCases').textContent = data.data.active_cases;
                    document.getElementById('weeklyRecords').textContent = data.data.weekly_records;
                    document.getElementById('pendingTasks').textContent = data.data.pending_tasks;
                } else {
                    showStatisticsError();
                }
            })
            .catch(error => {
                console.error('Error loading statistics:', error);
                showStatisticsError();
            });
    }
    
    // Load assigned cases
    function loadAssignedCases() {
        fetch(`dashboard_api.php?action=assigned_cases&officer_id=${officerId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Assigned cases data:', data);
                const tableContainer = document.getElementById('assignedCasesTable');
                
                if (data.success && data.data.length > 0) {
                    let tableHtml = `
                        <div class="table-responsive">
                            <table class="table case-table">
                                <thead>
                                    <tr>
                                        <th>Case Number</th>
                                        <th>Case Type</th>
                                        <th>Status</th>
                                        <th>Date Reported</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.data.forEach(caseItem => {
                        const statusClass = getStatusClass(caseItem.status);
                        tableHtml += `
                            <tr>
                                <td><strong>${caseItem.case_number}</strong></td>
                                <td>${caseItem.case_type}</td>
                                <td><span class="status-badge ${statusClass}">${caseItem.status}</span></td>
                                <td>${caseItem.date_reported}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewCase(${caseItem.id})">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    tableContainer.innerHTML = tableHtml;
                } else {
                    tableContainer.innerHTML = `
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <h5>No Cases Assigned</h5>
                            <p>You don't have any cases assigned at the moment.</p>
                            <a href="create_case.php" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Create New Case
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading assigned cases:', error);
                showErrorMessage('assignedCasesTable', 'cases');
            });
    }
    
    // Load recent activity
    function loadRecentActivity() {
        fetch(`dashboard_api.php?action=recent_activity&officer_id=${officerId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Recent activity data:', data);
                const container = document.getElementById('recentSearches');
                
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(activity => {
                        const iconClass = activity.type === 'record' ? 'recent-record' : 'recent-case';
                        const icon = activity.type === 'record' ? 'fas fa-user' : 'fas fa-folder';
                        
                        html += `
                            <div class="recent-item" onclick="navigateToItem('${activity.type}', '${activity.id}')">
                                <div class="recent-icon ${iconClass}">
                                    <i class="${icon}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${activity.title}</div>
                                    <small class="text-muted">${activity.description} • ${activity.timestamp}</small>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <p>No recent activity</p>
                            <small>Your recent actions will appear here</small>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading recent activity:', error);
            });
    }
    
    // Load notifications
    function loadNotifications() {
        fetch(`dashboard_api.php?action=notifications&officer_id=${officerId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Notifications data:', data);
                const container = document.getElementById('notifications');
                
                if (data.success && data.data.length > 0) {
                    let html = '';
                    data.data.forEach(notification => {
                        const alertClass = notification.type === 'urgent' ? 'alert-warning' : 'alert-info';
                        const icon = notification.type === 'urgent' ? 'fa-exclamation-triangle' : 'fa-info-circle';
                        
                        html += `
                            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                <i class="fas ${icon} me-2"></i>
                                <strong>${notification.title}</strong><br>
                                <small>${notification.message}</small>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p>All caught up!</p>
                            <small>No new notifications</small>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
            });
    }
    
    // Helper functions
    function getStatusClass(status) {
        const statusLower = status.toLowerCase();
        if (statusLower.includes('active') || statusLower.includes('open')) {
            return 'status-active';
        } else if (statusLower.includes('pending') || statusLower.includes('review')) {
            return 'status-pending';
        } else if (statusLower.includes('closed') || statusLower.includes('resolved')) {
            return 'status-closed';
        }
        return 'status-pending';
    }
    
    function showStatisticsError() {
        document.getElementById('totalCases').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        document.getElementById('activeCases').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        document.getElementById('weeklyRecords').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
        document.getElementById('pendingTasks').innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
    }
    
    function showErrorMessage(containerId, type) {
        document.getElementById(containerId).innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                <p>Error loading ${type} data. Please try again.</p>
                <button class="btn btn-outline-danger btn-sm" onclick="loadDashboardData()">
                    <i class="fas fa-redo me-1"></i> Retry
                </button>
            </div>
        `;
    }
    
    // Action functions
    function viewCase(caseId) {
        alert('View case: ' + caseId);
        // window.location.href = `view_case.php?id=${caseId}`;
    }
    
    function navigateToItem(type, id) {
        alert('Navigate to: ' + type + ' - ' + id);
        // if (type === 'record') {
        //     window.location.href = `view_criminal_record.php?id=${id}`;
        // } else if (type === 'case') {
        //     window.location.href = `view_case.php?id=${id}`;
        // }
    }
    
    function refreshDashboard() {
        loadDashboardData();
        // Show temporary success message
        const statusMessage = document.getElementById('statusMessage');
        statusMessage.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                Dashboard refreshed successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
    
    // Auto-refresh functionality
    function startAutoRefresh() {
        refreshInterval = setInterval(() => {
            loadDashboardData();
        }, 30000); // Refresh every 30 seconds
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985c91731609f7f3',t:'MTc1ODk5MjU0MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

<?php
// api/officer_data.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Enforce officer access
requireRole(['officer']);

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$action = $_GET['action'] ?? '';
$officerId = $_GET['officer_id'] ?? $_SESSION['user_id'];

// Security check: Ensure officer can only access their own data
if ($officerId != $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

try {
    switch($action) {
        case 'assigned_cases':
            echo json_encode(getAssignedCases($db, $officerId));
            break;
        case 'weekly_stats':
            echo json_encode(getWeeklyStats($db, $officerId));
            break;
        case 'recent_searches':
            echo json_encode(getRecentSearches($db, $officerId));
            break;
        case 'notifications':
            echo json_encode(getNotifications($db, $officerId));
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Officer Dashboard API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal server error']);
}

function getAssignedCases($db, $officerId) {
    try {
        $stmt = $db->prepare("
            SELECT 
                c.id,
                c.case_number,
                CONCAT(cr.first_name, ' ', cr.last_name) as criminal_name,
                c.status,
                c.updated_at,
                DATE_FORMAT(c.updated_at, '%M %d, %Y') as last_updated
            FROM cases c
            LEFT JOIN criminal_records cr ON c.criminal_record_id = cr.id
            WHERE c.assigned_officer_id = ?
            ORDER BY c.updated_at DESC
            LIMIT 5
        ");
        $stmt->execute([$officerId]);
        $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total count
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM cases WHERE assigned_officer_id = ?");
        $countStmt->execute([$officerId]);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return [
            'success' => true,
            'data' => $cases,
            'total' => $total
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getWeeklyStats($db, $officerId) {
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as records_added
            FROM criminal_records 
            WHERE created_by = ? 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$officerId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $stats
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getRecentSearches($db, $officerId) {
    try {
        $stmt = $db->prepare("
            SELECT 
                vs.target_type as type,
                vs.target_id as id,
                vs.target_title as title,
                DATE_FORMAT(vs.viewed_at, '%M %d at %h:%i %p') as viewed_at
            FROM view_history vs
            WHERE vs.user_id = ?
            ORDER BY vs.viewed_at DESC
            LIMIT 5
        ");
        $stmt->execute([$officerId]);
        $searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'success' => true,
            'data' => $searches
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getNotifications($db, $officerId) {
    try {
        $notifications = [];
        
        // Check for overdue cases
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM cases 
            WHERE assigned_officer_id = ? 
            AND status = 'active'
            AND due_date < CURDATE()
        ");
        $stmt->execute([$officerId]);
        $overdueCases = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($overdueCases > 0) {
            $notifications[] = [
                'type' => 'urgent',
                'title' => 'Overdue Cases',
                'message' => "You have $overdueCases overdue case(s) requiring attention"
            ];
        }
        
        // Check for new case assignments
        $stmt = $db->prepare("
            SELECT COUNT(*) as count
            FROM cases 
            WHERE assigned_officer_id = ? 
            AND status = 'active'
            AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute([$officerId]);
        $newCases = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($newCases > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'New Case Assignment',
                'message' => "$newCases new case(s) assigned to you in the last 24 hours"
            ];
        }
        
        return [
            'success' => true,
            'data' => $notifications
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// includes/officer_functions.php
