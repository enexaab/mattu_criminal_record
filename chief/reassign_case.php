<?php
// reassign_case.php
require '../includes/auth.php';
require '../includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if user is chief or admin
$is_chief = ($_SESSION['role'] === 'chief' || $_SESSION['role'] === 'admin');
if (!$is_chief) {
    header("Location: manage_cases.php?error=Access denied. Chief role required.");
    exit();
}

// Check if case_id is provided
$case_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($case_id <= 0) {
    header("Location: manage_cases.php?error=Invalid case ID");
    exit();
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

// Get current user info
$current_user = [
    'full_name' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
    'role' => $_SESSION['role'] ?? 'chief',
    'user_id' => $_SESSION['user_id']
];

// Check which table exists
$casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
$caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
$tableName = $casesTableExists ? 'cases' : 'case_files';

// Get case information
try {
    $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
    $stmt->execute([$case_id]);
    $case = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$case) {
        header("Location: manage_cases.php?error=Case not found");
        exit();
    }
    
} catch (Exception $e) {
    error_log("Error fetching case: " . $e->getMessage());
    header("Location: manage_cases.php?error=Database error");
    exit();
}

// Get available officers - only officers and investigators
$officers = [];
try {
    $usersTableExists = $db->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    if ($usersTableExists) {
        // Only get officers and investigators, exclude chiefs and administrators
        $stmt = $db->prepare("
            SELECT user_id, first_name, last_name, badge_number, role 
            FROM users 
            WHERE role IN ('officer', 'investigator')
            AND (is_active = 1 OR is_active IS NULL)
            AND user_id != ?
            ORDER BY first_name, last_name
        ");
        $stmt->execute([$current_user['user_id']]);
        $officers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no officers found, try without the active filter
        if (empty($officers)) {
            $stmt = $db->prepare("
                SELECT user_id, first_name, last_name, badge_number, role 
                FROM users 
                WHERE role IN ('officer', 'investigator')
                AND user_id != ?
                ORDER BY first_name, last_name
            ");
            $stmt->execute([$current_user['user_id']]);
            $officers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        error_log("Found " . count($officers) . " officers/investigators for reassignment");
    }
} catch (Exception $e) {
    error_log("Error fetching officers: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $assigned_officer_id = $_POST['assigned_officer_id'] ?? '';
        $reassignment_reason = $_POST['reassignment_reason'] ?? '';
        
        // Validate required fields
        if (empty($assigned_officer_id)) {
            throw new Exception("Please select an officer to assign the case to");
        }
        
        if (empty($reassignment_reason)) {
            throw new Exception("Please provide a reason for reassignment");
        }
        
        // Get officer information - FIXED: use user_id instead of id
        $stmt = $db->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
        $stmt->execute([$assigned_officer_id]);
        $officer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$officer) {
            throw new Exception("Selected officer not found");
        }
        
        // Update case assignment
        $updateQuery = "
            UPDATE $tableName 
            SET assigned_officer_id = ?, lead_officer_id = ?, updated_at = NOW()
            WHERE id = ?
        ";
        
        $stmt = $db->prepare($updateQuery);
        $result = $stmt->execute([
            $assigned_officer_id,
            $assigned_officer_id,
            $case_id
        ]);
        
        if ($result) {
            // Log the reassignment activity
            if (function_exists('logOfficerActivity')) {
                logOfficerActivity(
                    $current_user['user_id'],
                    'case_reassigned',
                    "Reassigned case {$case['case_number']} to {$officer['first_name']} {$officer['last_name']}. Reason: $reassignment_reason"
                );
            }
            
            // Create reassignment record if reassignments table exists
            try {
                $reassignmentsTableExists = $db->query("SHOW TABLES LIKE 'case_reassignments'")->rowCount() > 0;
                if ($reassignmentsTableExists) {
                    $stmt = $db->prepare("
                        INSERT INTO case_reassignments 
                        (case_id, previous_officer_id, new_officer_id, reassigned_by, reason, reassigned_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([
                        $case_id,
                        $case['assigned_officer_id'] ?? $case['created_by'],
                        $assigned_officer_id,
                        $current_user['user_id'],
                        $reassignment_reason
                    ]);
                    
                    error_log("Reassignment recorded for case {$case['case_number']}");
                }
            } catch (Exception $e) {
                error_log("Error creating reassignment record: " . $e->getMessage());
                // Don't throw error - reassignment should still work even if logging fails
            }
            
            $success_message = "Case successfully reassigned to {$officer['first_name']} {$officer['last_name']}";
            
            // Refresh case data
            $stmt = $db->prepare("SELECT * FROM $tableName WHERE id = ?");
            $stmt->execute([$case_id]);
            $case = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            throw new Exception("Failed to reassign case");
        }
        
    } catch (Exception $e) {
        $error_message = "Error reassigning case: " . $e->getMessage();
        error_log("Case reassignment error: " . $e->getMessage());
    }
}

// Get current assigned officer info if exists
$current_officer = null;
if (!empty($case['assigned_officer_id'])) {
    try {
        $stmt = $db->prepare("SELECT first_name, last_name, badge_number FROM users WHERE user_id = ?");
        $stmt->execute([$case['assigned_officer_id']]);
        $current_officer = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching current officer: " . $e->getMessage());
    }
}

// Get case creator info
$case_creator = null;
if (!empty($case['created_by'])) {
    try {
        $stmt = $db->prepare("SELECT first_name, last_name FROM users WHERE user_id = ?");
        $stmt->execute([$case['created_by']]);
        $case_creator = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching case creator: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reassign Case - <?php echo htmlspecialchars($case['case_number']); ?> - Mattu City Criminal Management System</title>
    
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
        
        .reassign-case-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            max-width: 900px;
            position: relative;
            overflow: hidden;
        }
        
        .reassign-case-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .reassign-case-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .reassign-case-header h3 {
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
        
        .case-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #6c757d;
            font-weight: 500;
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
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(108, 117, 125, 0.4);
            color: white;
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
        
        .officer-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .officer-card:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }
        
        .officer-card.selected {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .officer-name {
            font-weight: 700;
            color: #495057;
            font-size: 1.1rem;
        }
        
        .officer-badge {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .officer-role {
            background: #667eea;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
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
        
        @media (max-width: 768px) {
            .reassign-case-container {
                margin: 10px;
                padding: 25px;
            }
            
            .reassign-case-header h3 {
                font-size: 2rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reassign-case-container">
            <!-- Header -->
            <div class="reassign-case-header">
                <h3><i class="fas fa-exchange-alt me-3"></i>Reassign Case</h3>
                <p class="lead">Transfer case responsibility to another officer</p>
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
                <div class="col-md-5">
                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-info-circle me-2"></i>Case Information</h5>
                        
                        <div class="case-info-card">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Case Number</span>
                                    <span class="info-value"><?php echo htmlspecialchars($case['case_number']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Case Type</span>
                                    <span class="info-value"><?php echo htmlspecialchars($case['case_type']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Status</span>
                                    <span class="info-value">
                                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $case['status'] ?? 'Open')); ?>">
                                            <?php echo htmlspecialchars($case['status'] ?? 'Open'); ?>
                                        </span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Date Reported</span>
                                    <span class="info-value">
                                        <?php echo date('M j, Y', strtotime($case['date_reported'] ?? $case['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <span class="info-label">Description</span>
                                <span class="info-value"><?php echo htmlspecialchars($case['description'] ?? 'No description available'); ?></span>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top">
                                <div class="info-item">
                                    <span class="info-label">Current Assignment</span>
                                    <span class="info-value">
                                        <?php if ($current_officer): ?>
                                            <i class="fas fa-user-shield me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($current_officer['first_name'] . ' ' . $current_officer['last_name']); ?>
                                            <?php if ($current_officer['badge_number']): ?>
                                                <br><small class="text-muted">Badge: <?php echo htmlspecialchars($current_officer['badge_number']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="info-item">
                                    <span class="info-label">Case Creator</span>
                                    <span class="info-value">
                                        <?php if ($case_creator): ?>
                                            <i class="fas fa-user me-2 text-success"></i>
                                            <?php echo htmlspecialchars($case_creator['first_name'] . ' ' . $case_creator['last_name']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Unknown</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="manage_cases.php" class="btn btn-secondary-custom">
                                <i class="fas fa-arrow-left me-2"></i>Back to Cases
                            </a>
                            <a href="view_criminal_record.php?id=<?php echo $case_id; ?>" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>View Case Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Reassignment Form -->
                <div class="col-md-7">
                    <div class="form-section">
                        <h5 class="mb-4"><i class="fas fa-users me-2"></i>Assign to Officer</h5>
                        
                        <form method="POST" id="reassignForm">
                            <div class="mb-4">
                                <label class="form-label-custom">Select Officer *</label>
                                <?php if (empty($officers)): ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No active officers or investigators found in the system.
                                        <br><small class="mt-1 d-block">
                                            You need to have officers or investigators in the system to reassign cases.
                                            <a href="../admin/manage_users.php" class="alert-link">Manage Users</a>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <div class="officers-list">
                                        <?php foreach ($officers as $officer): 
                                            $is_current = ($current_officer && $current_officer['first_name'] . ' ' . $current_officer['last_name'] === $officer['first_name'] . ' ' . $officer['last_name']);
                                        ?>
                                            <div class="officer-card <?php echo $is_current ? 'selected' : ''; ?>" 
                                                 onclick="selectOfficer(<?php echo $officer['user_id']; ?>, this)">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="officer-name">
                                                            <i class="fas fa-user-shield me-2 text-primary"></i>
                                                            <?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?>
                                                        </div>
                                                        <div class="officer-badge">
                                                            Badge: <?php echo htmlspecialchars($officer['badge_number'] ?? 'N/A'); ?>
                                                            | Role: <?php echo htmlspecialchars(ucfirst($officer['role'])); ?>
                                                        </div>
                                                    </div>
                                                    <div class="officer-role">
                                                        <?php echo htmlspecialchars(ucfirst($officer['role'])); ?>
                                                    </div>
                                                </div>
                                                <?php if ($is_current): ?>
                                                    <div class="mt-2 text-success">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                        Currently assigned
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <input type="hidden" name="assigned_officer_id" id="assignedOfficerId" 
                                           value="<?php echo $current_officer ? $case['assigned_officer_id'] : ''; ?>" required>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label-custom">Reason for Reassignment *</label>
                                <textarea class="form-control form-control-custom" name="reassignment_reason" 
                                          rows="4" required placeholder="Explain why this case is being reassigned..."><?php echo htmlspecialchars($_POST['reassignment_reason'] ?? ''); ?></textarea>
                                <div class="form-text">
                                    This reason will be recorded in the case history for audit purposes.
                                </div>
                            </div>
                            
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary-custom flex-fill" <?php echo empty($officers) ? 'disabled' : ''; ?>>
                                    <i class="fas fa-exchange-alt me-2"></i>Reassign Case
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
        function selectOfficer(officerId, element) {
            // Remove selected class from all officer cards
            document.querySelectorAll('.officer-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            element.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('assignedOfficerId').value = officerId;
        }
        
        // Form validation
        document.getElementById('reassignForm').addEventListener('submit', function(e) {
            const officerId = document.getElementById('assignedOfficerId').value;
            const reason = document.querySelector('textarea[name="reassignment_reason"]').value.trim();
            
            if (!officerId) {
                e.preventDefault();
                alert('Please select an officer to assign the case to');
                return;
            }
            
            if (!reason) {
                e.preventDefault();
                alert('Please provide a reason for reassignment');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Reassigning Case...';
        });
        
        // Auto-select current officer if exists
        document.addEventListener('DOMContentLoaded', function() {
            const currentOfficerId = <?php echo $current_officer ? $case['assigned_officer_id'] : 'null'; ?>;
            if (currentOfficerId) {
                document.getElementById('assignedOfficerId').value = currentOfficerId;
                
                // Also visually select the current officer card
                document.querySelectorAll('.officer-card').forEach(card => {
                    if (card.getAttribute('onclick').includes(currentOfficerId)) {
                        card.classList.add('selected');
                    }
                });
            }
        });
    </script>
</body>
</html>