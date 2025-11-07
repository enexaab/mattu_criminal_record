<?php
// search_records.php
require '../includes/database.php';
require '../includes/auth.php';

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
            $search_error = "Error performing search: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
     <!-- Add these cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Criminal Records - Mattu City Criminal Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
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
                        <a class="nav-link-custom active" href="search_records.php">
                            <i class="fas fa-search me-1"></i> Search Records
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="manage_cases.php">
                            <i class="fas fa-folder-open me-1"></i> My Cases
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link-custom" href="add_criminal_record.php">
                            <i class="fas fa-user-plus me-1"></i> Add Record
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <span class="officer-badge me-3">
                        <i class="fas fa-user-shield me-1"></i>
                        <?php echo htmlspecialchars($current_user['full_name']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
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
                            <i class="fas fa-search me-2"></i>Search Criminal Records
                        </div>
                        <div class="card-body-custom">
                            <!-- Search Form -->
                            <form method="POST" class="search-form">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Search Type</label>
                                        <select name="search_type" class="form-select form-control-custom">
                                            <option value="name" <?php echo $search_type === 'name' ? 'selected' : ''; ?>>Name</option>
                                            <option value="national_id" <?php echo $search_type === 'national_id' ? 'selected' : ''; ?>>National ID</option>
                                            <option value="case_number" <?php echo $search_type === 'case_number' ? 'selected' : ''; ?>>Case Number</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Search Query</label>
                                        <input type="text" name="search_query" class="form-control form-control-custom" 
                                               placeholder="Enter search term..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" name="search" class="btn btn-primary-custom w-100">
                                            <i class="fas fa-search me-2"></i>Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <?php if ($search_performed): ?>
                                <!-- Search Statistics -->
                                <div class="search-stats">
                                    <h5 class="mb-1">
                                        <i class="fas fa-chart-bar me-2"></i>
                                        Search Results
                                    </h5>
                                    <p class="mb-0">
                                        Found <strong><?php echo count($search_results); ?></strong> record(s) 
                                        for "<?php echo htmlspecialchars($search_query); ?>"
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
                                    Search Results
                                </span>
                                <span class="badge bg-light text-dark">
                                    <?php echo count($search_results); ?> records
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
                                                    <small class="text-muted">Date of Birth</small>
                                                    <div><?php echo !empty($record['date_of_birth']) ? htmlspecialchars($record['date_of_birth']) : 'Not specified'; ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted">Gender</small>
                                                    <div><?php echo htmlspecialchars($record['gender'] ?? 'Not specified'); ?></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <small class="text-muted">Status</small>
                                                    <div>
                                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $record['status'] ?? 'first-offender')); ?>">
                                                            <?php echo htmlspecialchars($record['status'] ?? 'First Offender'); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group">
                                                        <a href="view_criminal_record.php?id=<?php echo $record['id']; ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="create_case.php?record_id=<?php echo $record['id']; ?>" 
                                                           class="btn btn-sm btn-success">
                                                            <i class="fas fa-plus"></i> Case
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-results">
                                        <i class="fas fa-search"></i>
                                        <h4>No Records Found</h4>
                                        <p>No criminal records match your search criteria. Try adjusting your search terms.</p>
                                        <div class="mt-3">
                                            <a href="add_criminal_record.php" class="btn btn-primary">
                                                <i class="fas fa-user-plus me-2"></i>Add New Record
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
                                <i class="fas fa-lightbulb me-2"></i>Search Tips
                            </div>
                            <div class="card-body-custom">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-3 me-3">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <h6>Search by Name</h6>
                                                <p class="text-muted mb-0">Search using first name, last name, or both</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success rounded-circle p-3 me-3">
                                                <i class="fas fa-id-card text-white"></i>
                                            </div>
                                            <div>
                                                <h6>Search by National ID</h6>
                                                <p class="text-muted mb-0">Find records using national identification number</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle p-3 me-3">
                                                <i class="fas fa-folder text-white"></i>
                                            </div>
                                            <div>
                                                <h6>Search by Case</h6>
                                                <p class="text-muted mb-0">Find criminals associated with specific case numbers</p>
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