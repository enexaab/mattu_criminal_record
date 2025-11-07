<?php
// edit_case.php
require '../includes/database.php';
require '../includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if case_id is provided
$case_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($case_id <= 0) {
    header("Location: manage_cases.php?error=Invalid case ID");
    exit();
}

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id']
];

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get case information and verify ownership
try {
    // Check which table exists
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    $tableName = $casesTableExists ? 'cases' : 'case_files';
    
    // Get case with creator information
    $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
    $stmt->execute([$case_id]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        header("Location: manage_cases.php?error=Case not found");
        exit();
    }
    
    // Check if current user is the creator or has admin role
    $is_creator = isset($case['created_by']) && $case['created_by'] == $current_user['user_id'];
    $is_admin = $current_user['role'] === 'admin';
    
    if (!$is_creator && !$is_admin) {
        header("Location: manage_cases.php?error=You can only edit cases you created");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error fetching case: " . $e->getMessage());
    header("Location: manage_cases.php?error=Database error");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $case_number = $_POST['case_number'] ?? '';
        $case_type = $_POST['case_type'] ?? '';
        $status = $_POST['status'] ?? '';
        $description = $_POST['description'] ?? '';
        $location = $_POST['location'] ?? '';
        $date_reported = $_POST['date_reported'] ?? '';
        $priority = $_POST['priority'] ?? 'medium';
        
        // Validate required fields
        if (empty($case_number) || empty($case_type) || empty($description)) {
            throw new Exception("Please fill in all required fields");
        }
        
        // Update case
        $updateQuery = "
            UPDATE $tableName 
            SET case_number = ?, case_type = ?, status = ?, description = ?, 
                location = ?, date_reported = ?, priority = ?, updated_at = NOW()
            WHERE id = ?
        ";
        
        $stmt = $db->prepare($updateQuery);
        $result = $stmt->execute([
            $case_number,
            $case_type,
            $status,
            $description,
            $location,
            $date_reported,
            $priority,
            $case_id
        ]);
        
        if ($result) {
            // Log activity
            if (function_exists('logOfficerActivity')) {
                logOfficerActivity(
                    $current_user['user_id'],
                    'case_updated',
                    "Updated case {$case_number} (Case ID: $case_id)"
                );
            }
            
            $success_message = "Case updated successfully!";
            
            // Refresh case data
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$case_id]);
            $case = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception("Failed to update case");
        }
        
    } catch (Exception $e) {
        $error_message = "Error updating case: " . $e->getMessage();
        error_log("Case update error: " . $e->getMessage());
    }
}

// Get available case types from database or use defaults
$case_types = [];
try {
    // Try to get case types from database if table exists
    $caseTypesTableExists = $db->query("SHOW TABLES LIKE 'case_types'")->rowCount() > 0;
    if ($caseTypesTableExists) {
        $stmt = $db->query("SELECT type_name FROM case_types ORDER BY type_name");
        $case_types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (Exception $e) {
    // Use default case types if table doesn't exist
    error_log("Case types table not found, using defaults");
}

if (empty($case_types)) {
    $case_types = [
        'Theft', 'Assault', 'Burglary', 'Robbery', 'Fraud', 'Drug Offense',
        'Traffic Violation', 'Domestic Violence', 'Cyber Crime', 'Homicide',
        'Sexual Assault', 'Kidnapping', 'Arson', 'Vandalism', 'Other'
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>edit case</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Case - <?php echo htmlspecialchars($case['case_number']); ?> - Mattu City Criminal Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .edit-case-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            max-width: 1000px;
            position: relative;
            overflow: hidden;
        }
        
        .edit-case-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .edit-case-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .edit-case-header h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .form-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-label-custom {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 2px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 2px solid #dc3545;
        }
        
        .case-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            justify-content: between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            min-width: 150px;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        .priority-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #212529; }
        .priority-low { background: #28a745; color: white; }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-open { background: #28a745; color: white; }
        .status-in-progress { background: #ffc107; color: #212529; }
        .status-in-court { background: #17a2b8; color: white; }
        .status-closed { background: #6c757d; color: white; }
        .status-suspended { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="edit-case-container">
            <!-- Header -->
            <div class="edit-case-header">
                <h3><i class="fas fa-edit me-3"></i>Edit Case</h3>
                <p class="lead">Update case information for <strong><?php echo htmlspecialchars($case['case_number']); ?></strong></p>
            </div>
            
            <!-- Status Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert-custom alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert-custom alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Case Information -->
                <div class="col-md-4">
                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Case Information</h5>
                        
                        <div class="case-info-card">
                            <div class="info-item">
                                <span class="info-label">Case Number:</span>
                                <span class="info-value"><?php echo htmlspecialchars($case['case_number']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Current Status:</span>
                                <span class="info-value">
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'Open')); ?>">
                                        <?php echo htmlspecialchars($case['status'] ?? 'Open'); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Priority:</span>
                                <span class="info-value">
                                    <span class="priority-badge priority-<?php echo strtolower($case['priority'] ?? 'medium'); ?>">
                                        <?php echo htmlspecialchars($case['priority'] ?? 'Medium'); ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date Created:</span>
                                <span class="info-value">
                                    <?php echo date('M j, Y g:i A', strtotime($case['created_at'])); ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Updated:</span>
                                <span class="info-value">
                                    <?php echo isset($case['updated_at']) ? date('M j, Y g:i A', strtotime($case['updated_at'])) : 'Never'; ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="manage_cases.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cases
                            </a>
                            <a href="view_criminal_record.php?id=<?php echo $case_id; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View Case Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Form -->
                <div class="col-md-8">
                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-edit me-2"></i>Edit Case Details</h5>
                        
                        <form method="POST" id="editCaseForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Case Number *</label>
                                    <input type="text" class="form-control form-control-custom" name="case_number" 
                                           value="<?php echo htmlspecialchars($case['case_number']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Case Type *</label>
                                    <select class="form-control form-control-custom" name="case_type" required>
                                        <option value="">Select Case Type</option>
                                        <?php foreach ($case_types as $type): ?>
                                            <option value="<?php echo htmlspecialchars($type); ?>" 
                                                <?php echo ($case['case_type'] ?? '') === $type ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($type); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Status *</label>
                                    <select class="form-control form-control-custom" name="status" required>
                                        <option value="Open" <?php echo ($case['status'] ?? '') === 'Open' ? 'selected' : ''; ?>>Open</option>
                                        <option value="In Progress" <?php echo ($case['status'] ?? '') === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="In Court" <?php echo ($case['status'] ?? '') === 'In Court' ? 'selected' : ''; ?>>In Court</option>
                                        <option value="Closed" <?php echo ($case['status'] ?? '') === 'Closed' ? 'selected' : ''; ?>>Closed</option>
                                        <option value="Suspended" <?php echo ($case['status'] ?? '') === 'Suspended' ? 'selected' : ''; ?>>Suspended</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label-custom">Priority</label>
                                    <select class="form-control form-control-custom" name="priority">
                                        <option value="low" <?php echo ($case['priority'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                                        <option value="medium" <?php echo ($case['priority'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                        <option value="high" <?php echo ($case['priority'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label-custom">Location</label>
                                <input type="text" class="form-control form-control-custom" name="location" 
                                       value="<?php echo htmlspecialchars($case['location'] ?? ''); ?>" 
                                       placeholder="Where did the incident occur?">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label-custom">Date Reported</label>
                                <input type="date" class="form-control form-control-custom" name="date_reported" 
                                       value="<?php echo htmlspecialchars($case['date_reported'] ?? date('Y-m-d')); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label-custom">Description *</label>
                                <textarea class="form-control form-control-custom" name="description" rows="5" required 
                                          placeholder="Detailed description of the case..."><?php echo htmlspecialchars($case['description'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary-custom flex-fill">
                                    <i class="fas fa-save me-2"></i>Update Case
                                </button>
                                <a href="manage_cases.php" class="btn btn-secondary flex-fill">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form validation
            document.getElementById('editCaseForm').addEventListener('submit', function(e) {
                const caseNumber = document.querySelector('input[name="case_number"]').value.trim();
                const caseType = document.querySelector('select[name="case_type"]').value;
                const description = document.querySelector('textarea[name="description"]').value.trim();
                
                if (!caseNumber || !caseType || !description) {
                    e.preventDefault();
                    alert('Please fill in all required fields marked with *');
                    return;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating Case...';
            });
        });
    </script>
</body>
</html>