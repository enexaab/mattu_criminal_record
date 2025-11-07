<?php
// add_evidence.php
require '../includes/database.php';
require '../includes/auth.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Check if case_id is provided
$case_id = isset($_GET['case_id']) ? intval($_GET['case_id']) : 0;
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

// Get case information
$database = new Database();
$db = $database->getConnection();

try {
    // Check which table exists
    $casesTableExists = $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0;
    $caseFilesTableExists = $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0;
    $tableName = $casesTableExists ? 'cases' : 'case_files';
    
    $stmt = $db->prepare("SELECT case_number, case_type FROM $tableName WHERE id = ?");
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

// Handle evidence submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $evidence_type = $_POST['evidence_type'] ?? '';
        $description = $_POST['description'] ?? '';
        $location_found = $_POST['location_found'] ?? '';
        $date_found = $_POST['date_found'] ?? date('Y-m-d');
        $chain_of_custody = $_POST['chain_of_custody'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        // Use current user ID for collected_by since it's an INT column
        $collected_by = $current_user['user_id'];
        
        // Handle file upload
        $file_path = null;
        
        if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/evidence/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['evidence_file']['name'], PATHINFO_EXTENSION);
            $filename = 'evidence_' . $case_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
            $file_path = $upload_dir . $filename;
            
            // Validate and move file
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'mp4', 'avi', 'mov', 'wav', 'mp3'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['evidence_file']['tmp_name'], $file_path)) {
                    $file_path = 'uploads/evidence/' . $filename;
                } else {
                    throw new Exception("Failed to upload file. Please try again.");
                }
            } else {
                throw new Exception("File type not supported. Please upload images, videos, audio, or documents.");
            }
        }
        
        // Insert evidence record - Use user ID for collected_by
        $stmt = $db->prepare("
            INSERT INTO evidence 
            (case_id, evidence_type, description, file_path, location_found, date_found, collected_by, chain_of_custody, notes, status, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'collected', ?)
        ");
        
        $stmt->execute([
            $case_id,
            $evidence_type,
            $description,
            $file_path,
            $location_found,
            $date_found,
            $collected_by, // Now using user ID (integer) instead of name
            $chain_of_custody,
            $notes,
            $current_user['user_id']
        ]);
        
        $evidence_id = $db->lastInsertId();
        
        // Log activity
        if (function_exists('logOfficerActivity')) {
            logOfficerActivity(
                $current_user['user_id'],
                'evidence_added',
                "Added evidence to case {$case['case_number']} (Evidence ID: $evidence_id)"
            );
        }
        
        $success_message = "Evidence added successfully! Evidence ID: #$evidence_id";
        
    } catch (Exception $e) {
        $error_message = "Error adding evidence: " . $e->getMessage();
        error_log("Evidence submission error: " . $e->getMessage());
    }
}

// Get existing evidence for this case with officer names
$existing_evidence = [];
try {
    $stmt = $db->prepare("
        SELECT e.*, u.first_name, u.last_name 
        FROM evidence e 
        LEFT JOIN users u ON e.collected_by = u.id 
        WHERE e.case_id = ? 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$case_id]);
    $existing_evidence = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If join fails, just get evidence without officer names
    try {
        $stmt = $db->prepare("SELECT * FROM evidence WHERE case_id = ? ORDER BY created_at DESC");
        $stmt->execute([$case_id]);
        $existing_evidence = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e2) {
        error_log("Error fetching existing evidence: " . $e2->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Evidence - Case <?php echo htmlspecialchars($case['case_number']); ?> - Mattu City Criminal Management System</title>
    
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
        .video-preview-container {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.video-preview-container:hover .file-preview {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

.file-preview {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    cursor: pointer;
}

/* Modal custom styles */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}

.modal-header .btn-close {
    filter: invert(1);
}
        .evidence-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 40px;
            margin: 20px auto;
            max-width: 1200px;
            position: relative;
            overflow: hidden;
        }
        
        .evidence-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .evidence-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .evidence-header h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .evidence-card {
            background: rgba(248, 249, 250, 0.8);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
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
        
        .file-upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .file-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .evidence-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin: 10px auto;
            display: none;
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
        
        .evidence-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .evidence-type-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-photo { background: #4facfe; color: white; }
        .badge-video { background: #f093fb; color: white; }
        .badge-document { background: #43e97b; color: white; }
        .badge-audio { background: #fa709a; color: white; }
        .badge-physical { background: #ff9a9e; color: white; }
        .badge-digital { background: #a8edea; color: #333; }
        
        .file-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        
        .chain-custody-track {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            margin-top: 15px;
        }
        
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #667eea;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #667eea;
            border: 2px solid white;
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
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <div class="evidence-container">
            <!-- Header -->
            <div class="evidence-header">
                <h3><i class="fas fa-fingerprint me-3"></i>Evidence Management</h3>
                <p class="lead">Case: <strong><?php echo htmlspecialchars($case['case_number']); ?></strong> - <?php echo htmlspecialchars($case['case_type']); ?></p>
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
    <!-- Add Evidence Form - Left Side -->
    <div class="col-lg-6">
        <div class="form-section">
            <h4 class="mb-4"><i class="fas fa-plus-circle me-2"></i>Add New Evidence</h4>
            
            <form method="POST" enctype="multipart/form-data" id="evidenceForm">
                <div class="mb-3">
                    <label class="form-label-custom">Evidence Type</label>
                    <select class="form-control form-control-custom" name="evidence_type" required>
                        <option value="">Select Evidence Type</option>
                        <option value="photo">Photograph</option>
                        <option value="video">Video Recording</option>
                        <option value="audio">Audio Recording</option>
                        <option value="document">Document</option>
                        <option value="physical">Physical Evidence</option>
                        <option value="digital">Digital Evidence</option>
                        <option value="weapon">Weapon</option>
                        <option value="clothing">Clothing</option>
                        <option value="biological">Biological Sample</option>
                        <option value="fingerprint">Fingerprint</option>
                        <option value="cctv">CCTV Footage</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Description</label>
                    <textarea class="form-control form-control-custom" name="description" rows="3" required placeholder="Detailed description of the evidence..."></textarea>
                </div>
                
                <!-- File Upload -->
                <div class="mb-3">
                    <label class="form-label-custom">Evidence File (Optional)</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="fw-bold mb-2">Click to upload or drag and drop</div>
                        <div class="text-muted small mb-3">
                            Supported: Images, Videos, Audio, Documents<br>
                            Max: 50MB per file
                        </div>
                        <img id="filePreview" class="evidence-preview" alt="File Preview">
                        <input type="file" name="evidence_file" id="evidence_file" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" style="display: none;">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Location Found</label>
                        <input type="text" class="form-control form-control-custom" name="location_found" placeholder="Where was the evidence found?">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Date Found</label>
                        <input type="date" class="form-control form-control-custom" name="date_found" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Collected By</label>
                    <input type="text" class="form-control form-control-custom" name="collected_by" value="<?php echo htmlspecialchars($current_user['full_name']); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Chain of Custody Notes</label>
                    <textarea class="form-control form-control-custom" name="chain_of_custody" rows="2" placeholder="Who handled the evidence and when..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label-custom">Additional Notes</label>
                    <textarea class="form-control form-control-custom" name="notes" rows="2" placeholder="Any additional information..."></textarea>
                </div>
                
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary-custom flex-fill">
                        <i class="fas fa-save me-2"></i>Add Evidence
                    </button>
                    <a href="manage_cases.php" class="btn btn-secondary flex-fill">
                        <i class="fas fa-arrow-left me-2"></i>Back to Cases
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Existing Evidence - Right Side -->
    <div class="col-lg-6">
        <div class="form-section">
            <h4 class="mb-4">
                <i class="fas fa-list me-2"></i>
                Existing Evidence 
                <span class="badge bg-primary"><?php echo count($existing_evidence); ?></span>
            </h4>
            
            <?php if (empty($existing_evidence)): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p>No evidence added yet</p>
                </div>
            <?php else: ?>
                <div style="max-height: 400px; overflow-y: auto;">
                    <?php foreach ($existing_evidence as $evidence): ?>
                        <div class="evidence-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="evidence-type-badge badge-<?php echo $evidence['evidence_type']; ?>">
                                        <?php echo ucfirst($evidence['evidence_type']); ?>
                                    </span>
                                    <small class="text-muted ms-2">#<?php echo $evidence['id']; ?></small>
                                </div>
                                <small class="text-muted">
                                    <?php echo date('M j, Y g:i A', strtotime($evidence['created_at'])); ?>
                                </small>
                            </div>
                            
                            <p class="mb-2"><strong><?php echo htmlspecialchars($evidence['description']); ?></strong></p>
                            
                            <?php if ($evidence['file_path']): ?>
                                <div class="mb-2">
                                    <?php
                                    $file_ext = pathinfo($evidence['file_path'], PATHINFO_EXTENSION);
                                    $is_image = in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif']);
                                    $is_video = in_array(strtolower($file_ext), ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm']);
                                    $is_audio = in_array(strtolower($file_ext), ['mp3', 'wav', 'ogg']);
                                    ?>
                                    
                                    <?php if ($is_image): ?>
                                        <img src="../<?php echo htmlspecialchars($evidence['file_path']); ?>" 
                                             class="file-preview" alt="Evidence Photo"
                                             style="cursor: pointer;" 
                                             onclick="openMediaModal('<?php echo htmlspecialchars($evidence['file_path']); ?>', 'image')">
                                    <?php elseif ($is_video): ?>
                                        <div class="file-preview bg-light d-flex align-items-center justify-content-center"
                                             style="cursor: pointer;"
                                             onclick="openMediaModal('<?php echo htmlspecialchars($evidence['file_path']); ?>', 'video')">
                                            <i class="fas fa-video text-muted fa-2x"></i>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-play-circle me-1"></i>Click to play video
                                        </small>
                                    <?php elseif ($is_audio): ?>
                                        <div class="file-preview bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-volume-up text-muted fa-2x"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="file-preview bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-file text-muted fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="small text-muted">
                                <div><strong>Found:</strong> <?php echo $evidence['location_found'] ? htmlspecialchars($evidence['location_found']) : 'Not specified'; ?></div>
                                <div><strong>Collected by:</strong> 
                                    <?php 
                                    if (isset($evidence['first_name']) && isset($evidence['last_name'])) {
                                        echo htmlspecialchars($evidence['first_name'] . ' ' . $evidence['last_name']);
                                    } else {
                                        echo htmlspecialchars($evidence['collected_by']);
                                    }
                                    ?>
                                </div>
                                <div><strong>Date:</strong> <?php echo $evidence['date_found'] ? date('M j, Y', strtotime($evidence['date_found'])) : 'Not specified'; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Chain of Custody Guide -->
        <div class="form-section">
            <h5 class="mb-3"><i class="fas fa-link me-2"></i>Chain of Custody Guidelines</h5>
            <div class="chain-custody-track">
                <div class="timeline">
                    <div class="timeline-item">
                        <strong>Collection</strong>
                        <p class="small mb-0">Document who collected the evidence and when</p>
                    </div>
                    <div class="timeline-item">
                        <strong>Packaging</strong>
                        <p class="small mb-0">Use proper evidence bags/containers</p>
                    </div>
                    <div class="timeline-item">
                        <strong>Labeling</strong>
                        <p class="small mb-0">Include case number, date, and collector info</p>
                    </div>
                    <div class="timeline-item">
                        <strong>Storage</strong>
                        <p class="small mb-0">Store in secure evidence locker</p>
                    </div>
                    <div class="timeline-item">
                        <strong>Transfer</strong>
                        <p class="small mb-0">Document every handover between personnel</p>
                    </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileInput = document.getElementById('evidence_file');
            const filePreview = document.getElementById('filePreview');
            
            // File upload handling
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });
            
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFilePreview(files[0]);
                }
            });
            
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFilePreview(e.target.files[0]);
                }
            });
            
            function handleFilePreview(file) {
                const reader = new FileReader();
                
                // Check file size (50MB limit)
                if (file.size > 50 * 1024 * 1024) {
                    alert('File size too large. Maximum size is 50MB.');
                    fileInput.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/avi', 'video/mov', 'audio/mpeg', 'audio/wav', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('File type not supported. Please upload images, videos, audio, or documents.');
                    fileInput.value = '';
                    return;
                }
                
                if (file.type.startsWith('image/')) {
                    reader.onload = function(e) {
                        filePreview.src = e.target.result;
                        filePreview.style.display = 'block';
                        fileUploadArea.querySelector('.fw-bold').textContent = 'Image ready for upload';
                    };
                    reader.readAsDataURL(file);
                } else {
                    filePreview.style.display = 'none';
                    fileUploadArea.querySelector('.fw-bold').textContent = 'File ready for upload: ' + file.name;
                }
            }
            
            // Form validation
            document.getElementById('evidenceForm').addEventListener('submit', function(e) {
                const evidenceType = document.querySelector('select[name="evidence_type"]').value;
                const description = document.querySelector('textarea[name="description"]').value;
                
                if (!evidenceType) {
                    e.preventDefault();
                    alert('Please select an evidence type.');
                    return;
                }
                
                if (!description.trim()) {
                    e.preventDefault();
                    alert('Please provide a description of the evidence.');
                    return;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Evidence...';
            });
        });
    </script>
    <!-- Media Modal -->
<!-- Media Modal -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mediaModalLabel">Evidence Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Evidence" class="img-fluid" style="display: none; max-height: 70vh;">
                <video id="modalVideo" controls class="w-100" style="display: none; max-height: 70vh;">
                    Your browser does not support the video tag.
                </video>
                <audio id="modalAudio" controls class="w-100" style="display: none;">
                    Your browser does not support the audio element.
                </audio>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadMedia" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function openMediaModal(filePath, mediaType) {
        const fullPath = '../' + filePath;
        const modalImage = document.getElementById('modalImage');
        const modalVideo = document.getElementById('modalVideo');
        const modalAudio = document.getElementById('modalAudio');
        const downloadLink = document.getElementById('downloadMedia');
        
        // Hide all media elements first
        modalImage.style.display = 'none';
        modalVideo.style.display = 'none';
        modalAudio.style.display = 'none';
        
        // Reset sources
        modalImage.src = '';
        modalVideo.src = '';
        modalAudio.src = '';
        
        // Set download link
        downloadLink.href = fullPath;
        
        // Show appropriate media element
        if (mediaType === 'image') {
            modalImage.src = fullPath;
            modalImage.style.display = 'block';
        } else if (mediaType === 'video') {
            modalVideo.src = fullPath;
            modalVideo.style.display = 'block';
            // Auto-play video when modal opens
            modalVideo.onloadeddata = function() {
                modalVideo.play().catch(e => console.log('Autoplay prevented:', e));
            };
        } else if (mediaType === 'audio') {
            modalAudio.src = fullPath;
            modalAudio.style.display = 'block';
        }
        
        // Show modal
        const mediaModal = new bootstrap.Modal(document.getElementById('mediaModal'));
        mediaModal.show();
        
        // Pause video when modal is closed
        document.getElementById('mediaModal').addEventListener('hidden.bs.modal', function() {
            if (mediaType === 'video') {
                modalVideo.pause();
                modalVideo.currentTime = 0;
            } else if (mediaType === 'audio') {
                modalAudio.pause();
                modalAudio.currentTime = 0;
            }
        });
    }
</script>
</body>
</html>