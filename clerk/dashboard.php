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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clerk Dashboard - Mattu Criminal Record System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
        
        .suggestion-item:hover {
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
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-search me-2"></i>
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
                            <i class="fas fa-search me-1"></i> Search & View Records
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="clerk-badge me-3">
                        <i class="fas fa-user-tie me-1"></i>
                        Clerk <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
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
            <!-- Global Search Hero Section -->
            <div class="search-hero">
                <h1><i class="fas fa-search me-3"></i>Criminal Record Search</h1>
                <p>Search by Criminal Name, National ID, or Case File ID</p>
                
                <div class="global-search-container">
                    <div class="position-relative">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            class="global-search-input" 
                            id="globalSearch"
                            placeholder="Enter name, ID number, or case file ID..."
                            autocomplete="off"
                        >
                        <div class="search-suggestions" id="searchSuggestions"></div>
                    </div>
                    <button class="search-btn" onclick="performSearch()">
                        <i class="fas fa-search me-2"></i>Search Records
                    </button>
                </div>
                
                <div class="search-tips">
                    <h6><i class="fas fa-lightbulb me-2"></i>Search Tips</h6>
                    <ul class="text-start">
                        <li>Use full names for better results (e.g., "John Smith")</li>
                        <li>National ID format: 12-digit number</li>
                        <li>Case IDs start with "CASE-" followed by numbers</li>
                        <li>Search is case-insensitive</li>
                    </ul>
                </div>
            </div>
            
            <!-- System Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="totalRecords">
                        <?php echo number_format($systemStats['total_records']); ?>
                    </span>
                    <div class="stat-label">Total Criminal Records</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);">
                    <span class="stat-number" id="activeCases">
                        <?php echo number_format($systemStats['active_cases']); ?>
                    </span>
                    <div class="stat-label">Active Cases</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);">
                    <span class="stat-number" id="pendingCases">
                        <?php echo number_format($systemStats['pending_cases']); ?>
                    </span>
                    <div class="stat-label">Pending Cases</div>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #ef5350 0%, #f44336 100%);">
                    <span class="stat-number" id="closedCases">
                        <?php echo number_format($systemStats['closed_cases']); ?>
                    </span>
                    <div class="stat-label">Closed Cases</div>
                </div>
            </div>
            
            <div class="row">
                <!-- Quick View Links -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bolt me-2"></i>Quick View Links
                        </div>
                        <div class="card-body-custom">
                            <div class="quick-view-grid">
                                <a href="search_records.php?filter=active" class="quick-view-btn">
                                    <i class="fas fa-folder-open"></i>
                                    View Active Cases
                                </a>
                                <a href="search_records.php?filter=pending" class="quick-view-btn">
                                    <i class="fas fa-clock"></i>
                                    View Pending Cases
                                </a>
                                <a href="search_records.php?filter=recent" class="quick-view-btn">
                                    <i class="fas fa-history"></i>
                                    Recent Records
                                </a>
                                <a href="search_records.php?filter=all" class="quick-view-btn">
                                    <i class="fas fa-database"></i>
                                    Browse All Records
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- System News/Updates -->
                <div class="col-lg-6 mb-4">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <i class="fas fa-bullhorn me-2"></i>System News & Updates
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
                                    <h5>No Recent Updates</h5>
                                    <p>System announcements and data entry guidelines will appear here.</p>
                                </div>
                            <?php endif; ?>
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
        let searchTimeout;
        let currentSuggestions = [];
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeSearch();
            updateStats();
        });
        
        // Initialize search functionality
        function initializeSearch() {
            const searchInput = document.getElementById('globalSearch');
            const suggestionsContainer = document.getElementById('searchSuggestions');
            
            // Real-time search suggestions
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
            
            // Handle Enter key
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
            
            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    hideSuggestions();
                }
            });
        }
        
        // Fetch search suggestions
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
        
        // Display search suggestions
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
        
        // Hide suggestions
        function hideSuggestions() {
            document.getElementById('searchSuggestions').style.display = 'none';
            currentSuggestions = [];
        }
        
        // Select suggestion
        function selectSuggestion(index) {
            if (currentSuggestions[index]) {
                const suggestion = currentSuggestions[index];
                document.getElementById('globalSearch').value = suggestion.title;
                hideSuggestions();
                
                // Navigate to the record/case
                if (suggestion.type === 'record') {
                    window.location.href = `view_record.php?id=${suggestion.id}`;
                } else if (suggestion.type === 'case') {
                    window.location.href = `view_criminal_record.php?id=${suggestion.id}`;
                }
            }
        }
        
        // Navigate suggestions with arrow keys
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
        
        // Perform main search
        function performSearch() {
            const query = document.getElementById('globalSearch').value.trim();
            
            if (query.length === 0) {
                alert('Please enter a search term');
                return;
            }
            
            // Redirect to search results page
            window.location.href = `search_records.php?q=${encodeURIComponent(query)}`;
        }
        
        // Update statistics
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
        
        // Auto-refresh stats every 5 minutes
        setInterval(updateStats, 300000);
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'985c96b82205f7f3',t:'MTc1ODk5Mjc1Ny4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>

<?php
// api/search_suggestions.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Enforce clerk access
requireRole(['clerk']);

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode(['success' => false, 'message' => 'Query too short']);
    exit;
}

try {
    $suggestions = [];
    
    // Search criminal records
    $stmt = $db->prepare("
        SELECT 
            id,
            CONCAT(first_name, ' ', last_name) as full_name,
            national_id,
            'record' as type
        FROM criminal_records 
        WHERE (CONCAT(first_name, ' ', last_name) LIKE ? 
               OR national_id LIKE ? 
               OR record_number LIKE ?)
        AND status != 'deleted'
        ORDER BY created_at DESC
        LIMIT 5
    ");
    
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($records as $record) {
        $suggestions[] = [
            'id' => $record['id'],
            'type' => 'record',
            'title' => $record['full_name'],
            'subtitle' => 'ID: ' . $record['national_id']
        ];
    }
    
    // Search cases
    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.case_number,
            c.title,
            'case' as type
        FROM cases c
        WHERE (c.case_number LIKE ? OR c.title LIKE ?)
        AND c.status != 'deleted'
        ORDER BY c.created_at DESC
        LIMIT 5
    ");
    
    $stmt->execute([$searchTerm, $searchTerm]);
    $cases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cases as $case) {
        $suggestions[] = [
            'id' => $case['id'],
            'type' => 'case',
            'title' => $case['case_number'],
            'subtitle' => $case['title']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'suggestions' => array_slice($suggestions, 0, 8) // Limit to 8 total suggestions
    ]);
    
} catch (Exception $e) {
    error_log("Search suggestions error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Search error']);
}

// api/clerk_stats.php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';

// Enforce clerk access
requireRole(['clerk']);

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

try {
    $stats = [];
    
    // Total criminal records
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM criminal_records WHERE status != 'deleted'");
    $stmt->execute();
    $stats['total_records'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active cases
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'active'");
    $stmt->execute();
    $stats['active_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Pending cases
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'pending'");
    $stmt->execute();
    $stats['pending_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Closed cases
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM cases WHERE status = 'closed'");
    $stmt->execute();
    $stats['closed_cases'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Clerk stats error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Stats error']);
}

// includes/clerk_functions.php
