<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if required files exist before including them
$required_files = [
    '../includes/auth.php',
    '../includes/database.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Error: Required file $file not found. Please ensure all include files are properly set up.");
    }
}

try {
    require_once '../includes/auth.php';
    require_once '../includes/database.php';
} catch (Exception $e) {
    die("Error loading required files: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role check - Must be Investigator, Officer, or Admin
if (!function_exists('requireRole')) {
    // Basic session-based role check
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['investigator', 'officer', 'admin'])) {
        die("Access denied. Investigator, Officer, or Admin role required.");
    }
} else {
    // Use the requireRole function if it exists
    requireRole(['investigator', 'officer', 'admin']);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get the request URI to determine the action
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Handle National ID verification request
    if (strpos($request_uri, 'verify_national_id') !== false) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['national_id']) || empty($input['national_id'])) {
                throw new Exception("National ID is required");
            }
            
            $national_id = trim($input['national_id']);
            
            // Check database connection
            if (!isset($db) || !$db) {
                throw new Exception("Database connection not available");
            }
            
            // Check if National ID already exists
            $stmt = $db->prepare("SELECT id, first_name, last_name FROM criminal_records WHERE national_id = ?");
            $stmt->execute([$national_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                echo json_encode([
                    'success' => false,
                    'exists' => true,
                    'message' => "National ID already exists in the system",
                    'existing_record' => [
                        'id' => $existing['id'],
                        'name' => $existing['first_name'] . ' ' . $existing['last_name']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'exists' => false,
                    'message' => "National ID is available"
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit();
    }
    
    // Handle criminal record creation - FIXED VERSION
    try {
        error_log("=== FORM SUBMISSION STARTED ===");
        
        // Handle file upload if present
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/criminal_photos/';
            
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    throw new Exception("Failed to create upload directory");
                }
            }
            
            // Check if directory is writable
            if (!is_writable($upload_dir)) {
                throw new Exception("Upload directory is not writable");
            }
            
            $file_info = pathinfo($_FILES['photo']['name']);
            $allowed_types = ['jpg', 'jpeg', 'png'];
            $file_extension = strtolower($file_info['extension']);
            
            // Validate file type
            if (!in_array($file_extension, $allowed_types)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, and PNG files are allowed.");
            }
            
            // Validate file size (5MB max)
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                throw new Exception("File size too large. Maximum size is 5MB.");
            }
            
            // Validate MIME type for security
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES['photo']['tmp_name']);
            finfo_close($finfo);
            
            $allowed_mimes = ['image/jpeg', 'image/png'];
            if (!in_array($mime_type, $allowed_mimes)) {
                throw new Exception("Invalid file format detected. Only JPEG and PNG images are allowed.");
            }
            
            // Generate unique filename
            $filename = 'criminal_' . time() . '_' . uniqid() . '.' . $file_extension;
            $photo_path = $upload_dir . $filename;
            
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                throw new Exception("Failed to upload photo. Please try again.");
            }
            
            // Store relative path for database
            $photo_path = 'uploads/criminal_photos/' . $filename;
            error_log("Photo uploaded successfully: " . $photo_path);
        } else {
            error_log("No file uploaded or upload error: " . ($_FILES['photo']['error'] ?? 'No file'));
        }
        
        // Get form data
        $national_id = trim($_POST['national_id'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $date_of_birth = trim($_POST['date_of_birth'] ?? '');
        $gender = trim($_POST['gender'] ?? '');
        $height = !empty($_POST['height']) ? (float)$_POST['height'] : null;
        $weight = !empty($_POST['weight']) ? (float)$_POST['weight'] : null;
        $eye_color = !empty($_POST['eye_color']) ? trim($_POST['eye_color']) : null;
        $hair_color = !empty($_POST['hair_color']) ? trim($_POST['hair_color']) : null;
        $distinguishing_marks = !empty($_POST['distinguishing_marks']) ? trim($_POST['distinguishing_marks']) : null;
        
        error_log("Form data received - National ID: $national_id, Name: $first_name $last_name");
        
        // Validate mandatory fields
        $required_fields = [
            'national_id' => $national_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender
        ];
        
        foreach ($required_fields as $field => $value) {
            if (empty($value)) {
                throw new Exception("Missing required field: " . ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $date_of_birth)) {
            throw new Exception("Invalid date format for date of birth");
        }
        
        // Validate age (must be at least 10 years old)
        $birth_date = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($birth_date)->y;
        
        if ($age < 10) {
            throw new Exception("Person must be at least 10 years old to be registered");
        }
        
        // Check database connection
        if (!isset($db) || !$db) {
            throw new Exception("Database connection not available");
        }
        
        // Check National ID uniqueness again
        $check_stmt = $db->prepare("SELECT id FROM criminal_records WHERE national_id = ?");
        $check_stmt->execute([$national_id]);
        if ($check_stmt->fetch()) {
            throw new Exception("National ID already exists in the system. Please use a unique National ID.");
        }
        
        // Require photo upload
        if (!$photo_path) {
            throw new Exception("Photo is required. Please upload a clear identification photo.");
        }
        
        // Start database transaction
        $db->beginTransaction();
        
        // INSERT into criminal_records table - FIXED to match your database structure
        $stmt = $db->prepare("
            INSERT INTO criminal_records (
                national_id, first_name, last_name, date_of_birth, gender,
                height, weight, eye_color, hair_color, distinguishing_marks,
                photo, status, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'First Offender', ?, NOW(), NOW())
        ");
        
        $stmt->execute([
            $national_id,
            $first_name,
            $last_name,
            $date_of_birth,
            $gender,
            $height,
            $weight,
            $eye_color,
            $hair_color,
            $distinguishing_marks,
            $photo_path,
            $_SESSION['user_id']
        ]);
        
        $record_id = $db->lastInsertId();
        
        // Commit transaction
        $db->commit();
        
        error_log("Criminal record created successfully with ID: " . $record_id);
        
        // Log activity if function exists
        if (function_exists('logOfficerActivity')) {
            logOfficerActivity(
                $_SESSION['user_id'], 
                'criminal_record_created', 
                "Created new Criminal Record: $first_name $last_name (ID: $record_id)"
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Criminal record created successfully',
            'record_id' => $record_id,
            'full_name' => $first_name . ' ' . $last_name
        ]);
        
    } catch (Exception $e) {
        error_log("Error creating criminal record: " . $e->getMessage());
        
        // Rollback transaction on error
        if (isset($db) && $db && $db->inTransaction()) {
            $db->rollBack();
        }
        
        // Delete uploaded file if there was an error
        if (isset($photo_path) && $photo_path && file_exists('../' . $photo_path)) {
            @unlink('../' . $photo_path);
        }
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
    exit();
}

// Get current user info for display (with fallback)
$current_user = [
    'full_name' => $_SESSION['full_name'] ?? 'Officer',
    'role' => $_SESSION['role'] ?? 'officer',
    'user_id' => $_SESSION['user_id'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Criminal Record - Mattu City Criminal Management System</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            line-height: 1.6;
        }
        
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .form-container {
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
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .form-header h3 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .fieldset-custom {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            background: rgba(248, 249, 250, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .fieldset-custom:hover {
            border-color: #667eea;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.1);
        }
        
        .fieldset-custom legend {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            border: none;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }
        
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 10px;
        }
        
        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 10px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 10px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 10px;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        .form-label-custom {
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
            display: block;
        }
        
        .form-control-custom:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: #f8f9ff;
        }
        
        .form-control-custom.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 2.94 2.94L8.5 6.4l-.94-.94L4.5 8.5z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        
        .form-control-custom.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        
        .required-indicator {
            color: #dc3545;
            font-weight: bold;
        }
        
        .input-group {
            display: flex;
            align-items: stretch;
        }
        
        .input-group .form-control-custom {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-right: none;
        }
        
        .btn-verify {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
            color: white;
            padding: 8px 20px;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            cursor: pointer;
            white-space: nowrap;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
        }
        
        .btn-verify:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            cursor: pointer;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary-custom:disabled {
            opacity: 0.6;
            transform: none;
            cursor: not-allowed;
        }
        
        .btn-secondary-custom {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(108, 117, 125, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .photo-upload-area {
            border: 3px dashed #dee2e6;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            background: rgba(248, 249, 250, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .photo-upload-area:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
        }
        
        .photo-upload-area.dragover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            transform: scale(1.02);
        }
        
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin: 10px auto;
            display: none;
        }
        
        .upload-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .upload-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .upload-hint {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 20px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 20px;
            position: relative;
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
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
            border: 2px solid #ffc107;
        }
        
        .btn-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: inherit;
            opacity: 0.7;
        }
        
        .btn-close:hover {
            opacity: 1;
        }
        
        .form-text-custom {
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        .form-control-custom.is-invalid ~ .invalid-feedback {
            display: block;
        }
        
        .success-actions {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
            margin: 0 10px 10px 0;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        .me-2 {
            margin-right: 0.5rem;
        }
        
        .me-3 {
            margin-right: 1rem;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .form-container {
                margin: 10px;
                padding: 25px;
            }
            
            .form-header h3 {
                font-size: 2rem;
            }
            
            .fieldset-custom {
                padding: 20px;
            }
            
            .col-md-3,
            .col-md-4,
            .col-md-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .d-flex {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn-secondary-custom,
            .btn-primary-custom {
                width: 100%;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 10px;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .input-group {
                flex-direction: column;
            }
            
            .input-group .form-control-custom {
                border-radius: 12px;
                border-right: 2px solid #e9ecef;
                margin-bottom: 10px;
            }
            
            .btn-verify {
                border-radius: 12px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-overlay"></div>
    
    <div class="container">
        <div class="form-container">
            <!-- Form Header -->
            <div class="form-header">
                <h3><i class="fas fa-user-plus me-3"></i>Add Criminal Record</h3>
                <p>Create a new criminal record profile with identification details and photo</p>
            </div>
            
            <!-- Main Form -->
            <form id="addCriminalRecordForm" enctype="multipart/form-data" novalidate>
                
                <!-- Identity Details Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-id-card me-2"></i>Identity Details</legend>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="national_id" class="form-label form-label-custom">
                                    National ID <span class="required-indicator">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-custom" id="national_id" name="national_id" required placeholder="e.g., 1234567890123">
                                    <button type="button" class="btn-verify" id="verifyNationalIdBtn">
                                        <i class="fas fa-search me-1"></i>Check
                                    </button>
                                </div>
                                <div class="invalid-feedback">Please provide a valid National ID.</div>
                                <div class="form-text-custom">Unique national identification number</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="first_name" class="form-label form-label-custom">
                                    First Name <span class="required-indicator">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="first_name" name="first_name" required placeholder="Enter first name">
                                <div class="invalid-feedback">Please provide the first name.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="last_name" class="form-label form-label-custom">
                                    Last Name <span class="required-indicator">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="last_name" name="last_name" required placeholder="Enter last name">
                                <div class="invalid-feedback">Please provide the last name.</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_of_birth" class="form-label form-label-custom">
                                    Date of Birth <span class="required-indicator">*</span>
                                </label>
                                <input type="date" class="form-control form-control-custom" id="date_of_birth" name="date_of_birth" required>
                                <div class="invalid-feedback">Please provide a valid date of birth.</div>
                                <div class="form-text-custom">Person must be at least 10 years old</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gender" class="form-label form-label-custom">
                                    Gender <span class="required-indicator">*</span>
                                </label>
                                <select class="form-control form-control-custom" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                                <div class="invalid-feedback">Please select a gender.</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Physical Attributes Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-ruler me-2"></i>Physical Attributes</legend>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="height" class="form-label form-label-custom">Height (cm)</label>
                                <input type="number" class="form-control form-control-custom" id="height" name="height" min="50" max="250" step="0.1" placeholder="e.g., 175.5">
                                <div class="form-text-custom">Height in centimeters</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="weight" class="form-label form-label-custom">Weight (kg)</label>
                                <input type="number" class="form-control form-control-custom" id="weight" name="weight" min="20" max="300" step="0.1" placeholder="e.g., 70.5">
                                <div class="form-text-custom">Weight in kilograms</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="eye_color" class="form-label form-label-custom">Eye Color</label>
                                <select class="form-control form-control-custom" id="eye_color" name="eye_color">
                                    <option value="">Select Eye Color</option>
                                    <option value="Brown">Brown</option>
                                    <option value="Black">Black</option>
                                    <option value="Blue">Blue</option>
                                    <option value="Green">Green</option>
                                    <option value="Hazel">Hazel</option>
                                    <option value="Gray">Gray</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="hair_color" class="form-label form-label-custom">Hair Color</label>
                                <select class="form-control form-control-custom" id="hair_color" name="hair_color">
                                    <option value="">Select Hair Color</option>
                                    <option value="Black">Black</option>
                                    <option value="Brown">Brown</option>
                                    <option value="Blonde">Blonde</option>
                                    <option value="Red">Red</option>
                                    <option value="Gray">Gray</option>
                                    <option value="White">White</option>
                                    <option value="Bald">Bald</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="distinguishing_marks" class="form-label form-label-custom">Distinguishing Marks</label>
                                <textarea class="form-control form-control-custom" id="distinguishing_marks" name="distinguishing_marks" rows="3" placeholder="Describe any scars, tattoos, birthmarks, or other identifying features..."></textarea>
                                <div class="form-text-custom">Any visible scars, tattoos, birthmarks, or other identifying features</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Identification Photo Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-camera me-2"></i>Identification Photo <span class="required-indicator">*</span></legend>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="photo-upload-area" id="photoUploadArea">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">Click to upload or drag and drop</div>
                                    <div class="upload-hint">JPEG or PNG files only • Maximum 5MB</div>
                                    <img id="photoPreview" class="photo-preview" alt="Photo Preview">
                                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png" style="display: none;" required>
                                </div>
                                <div class="invalid-feedback">Please upload a clear identification photo.</div>
                                <div class="form-text-custom">Upload a clear, recent photograph for identification purposes</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-secondary-custom" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i>Cancel
                    </button>
                    
                    <button type="submit" class="btn-primary-custom" id="submitBtn" disabled>
                        <i class="fas fa-user-plus me-2"></i>Create Criminal Record
                    </button>
                </div>
                
                <!-- Status Message Container -->
                <div id="statusMessage" class="mt-4"></div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('addCriminalRecordForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusMessage = document.getElementById('statusMessage');
            const verifyNationalIdBtn = document.getElementById('verifyNationalIdBtn');
            const nationalIdInput = document.getElementById('national_id');
            const photoUploadArea = document.getElementById('photoUploadArea');
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('photoPreview');
            
            let nationalIdVerified = false;
            let photoUploaded = false;
            
            // Photo upload handling
            photoUploadArea.addEventListener('click', function() {
                photoInput.click();
            });
            
            photoUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });
            
            photoUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
            });
            
            photoUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    photoInput.files = files;
                    handlePhotoUpload(files[0]);
                }
            });
            
            photoInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handlePhotoUpload(e.target.files[0]);
                }
            });
            
            function handlePhotoUpload(file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    showMessage('Invalid file type. Please upload a JPEG or PNG image.', 'danger');
                    return;
                }
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showMessage('File size too large. Maximum size is 5MB.', 'danger');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    photoPreview.src = e.target.result;
                    photoPreview.style.display = 'block';
                    photoUploadArea.querySelector('.upload-text').textContent = 'Photo uploaded successfully';
                    photoUploadArea.querySelector('.upload-icon').innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i>';
                };
                reader.readAsDataURL(file);
                
                photoUploaded = true;
                photoInput.classList.remove('is-invalid');
                photoInput.classList.add('is-valid');
                checkFormValidity();
            }
            
            // National ID verification
            verifyNationalIdBtn.addEventListener('click', function() {
                verifyNationalId();
            });
            
            nationalIdInput.addEventListener('blur', function() {
                if (this.value.trim()) {
                    verifyNationalId();
                }
            });
            
            nationalIdInput.addEventListener('input', function() {
                nationalIdVerified = false;
                submitBtn.disabled = true;
                this.classList.remove('is-valid', 'is-invalid');
            });
            
          function verifyNationalId() {
    const nationalId = nationalIdInput.value.trim();
    
    console.log("Verifying National ID:", nationalId);
    
    if (!nationalId) {
        showMessage('Please enter a National ID to verify.', 'warning');
        return;
    }
    
    // Show loading state
    verifyNationalIdBtn.disabled = true;
    verifyNationalIdBtn.innerHTML = '<span class="loading-spinner me-1"></span>Checking...';
    
    // Make AJAX request to verify National ID
    // Use absolute URL to avoid path issues
    const verifyUrl = window.location.pathname + '?verify_national_id=1';
    console.log("Making request to:", verifyUrl);
    
    fetch(verifyUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            national_id: nationalId
        })
    })
    .then(response => {
        console.log("Response status:", response.status);
        return response.json();
    })
    .then(result => {
        console.log("Verification result:", result);
        
        if (result.success && !result.exists) {
            // National ID is available
            nationalIdVerified = true;
            nationalIdInput.classList.remove('is-invalid');
            nationalIdInput.classList.add('is-valid');
            
            checkFormValidity();
            
            showMessage(
                `<i class="fas fa-check-circle me-2"></i><strong>Available!</strong> National ID is unique and can be used.`, 
                'success'
            );
            
        } else if (result.exists) {
            // National ID already exists
            nationalIdVerified = false;
            nationalIdInput.classList.remove('is-valid');
            nationalIdInput.classList.add('is-invalid');
            submitBtn.disabled = true;
            
            showMessage(
                `<i class="fas fa-exclamation-triangle me-2"></i><strong>Already Exists!</strong> This National ID is already registered to: ${result.existing_record.name}`, 
                'warning'
            );
        } else {
            // Error occurred
            nationalIdVerified = false;
            nationalIdInput.classList.remove('is-valid');
            nationalIdInput.classList.add('is-invalid');
            submitBtn.disabled = true;
            
            showMessage(
                `<i class="fas fa-times-circle me-2"></i><strong>Error!</strong> ${result.message}`, 
                'danger'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage(
            `<i class="fas fa-times-circle me-2"></i><strong>Error!</strong> Failed to verify National ID. Please try again.`, 
            'danger'
        );
    })
    .finally(() => {
        // Reset button state
        verifyNationalIdBtn.disabled = false;
        verifyNationalIdBtn.innerHTML = '<i class="fas fa-search me-1"></i>Check';
    });
}
            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                    checkFormValidity();
                });
                
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                    checkFormValidity();
                });
            });
            
            // Check overall form validity
            function checkFormValidity() {
                const requiredFields = form.querySelectorAll('[required]');
                let allValid = nationalIdVerified && photoUploaded;
                
                requiredFields.forEach(field => {
                    if (field.type !== 'file' && (!field.value.trim() || field.classList.contains('is-invalid'))) {
                        allValid = false;
                    }
                });
                
                submitBtn.disabled = !allValid;
            }
            
            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!nationalIdVerified) {
                    showMessage('Please verify the National ID before submitting.', 'warning');
                    return;
                }
                
                if (!photoUploaded) {
                    showMessage('Please upload an identification photo before submitting.', 'warning');
                    return;
                }
                
                // Validate all fields
                let isValid = true;
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    showMessage('Please correct the errors in the form before submitting.', 'danger');
                    return;
                }
                
                // Show loading state
                showLoading(true);
                
                // Submit via AJAX with FormData for file upload
                const formData = new FormData(form);
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const successMessage = `
                            <div class="alert-custom alert-success" role="alert">
                                <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Success!</strong> ${result.message}
                                <br><small>Record ID: #${result.record_id} | Name: ${result.full_name}</small>
                            </div>
                            <div class="success-actions">
                                <h6 class="mb-3"><i class="fas fa-arrow-right me-2"></i>Next Steps</h6>
                                <p class="mb-3">Criminal record created successfully. You can now create a case file for this person.</p>
                                <a href="create_case.php?record_id=${result.record_id}" class="btn-success-custom">
                                    <i class="fas fa-folder-plus me-2"></i>Create Case File
                                </a>
                                <a href="view_criminal_record.php" class="btn-success-custom">
                                    <i class="fas fa-list me-2"></i>View All Records
                                </a>
                            </div>
                        `;
                        
                        statusMessage.innerHTML = successMessage;
                        form.reset();
                        
                        // Reset states
                        nationalIdVerified = false;
                        photoUploaded = false;
                        submitBtn.disabled = true;
                        photoPreview.style.display = 'none';
                        photoUploadArea.querySelector('.upload-text').textContent = 'Click to upload or drag and drop';
                        photoUploadArea.querySelector('.upload-icon').innerHTML = '<i class="fas fa-cloud-upload-alt"></i>';
                        
                        // Reset validation classes
                        inputs.forEach(input => {
                            input.classList.remove('is-valid', 'is-invalid');
                        });
                        
                        // Scroll to success message
                        statusMessage.scrollIntoView({ behavior: 'smooth' });
                        
                    } else {
                        showMessage(
                            `<i class="fas fa-exclamation-triangle me-2"></i><strong>Error!</strong> ${result.message}`, 
                            'danger'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(
                        `<i class="fas fa-times-circle me-2"></i><strong>Network Error!</strong> Please check your connection and try again.`, 
                        'danger'
                    );
                })
                .finally(() => {
                    showLoading(false);
                });
            });
            
            // Field validation function
            function validateField(field) {
                const value = field.value.trim();
                let isValid = true;
                
                // Skip file input validation here (handled separately)
                if (field.type === 'file') {
                    return true;
                }
                
                // Required field validation
                if (field.hasAttribute('required') && !value) {
                    isValid = false;
                }
                
                // Specific field validations
                switch (field.id) {
                    case 'national_id':
                        if (value && (value.length < 5 || value.length > 20)) {
                            field.setCustomValidity('National ID must be between 5 and 20 characters');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'first_name':
                    case 'last_name':
                        if (value && (value.length < 2 || !/^[a-zA-Z\s]+$/.test(value))) {
                            field.setCustomValidity('Name must be at least 2 characters and contain only letters');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'date_of_birth':
                        if (value) {
                            const birthDate = new Date(value);
                            const today = new Date();
                            const age = today.getFullYear() - birthDate.getFullYear();
                            
                            if (birthDate > today) {
                                field.setCustomValidity('Date of birth cannot be in the future');
                                isValid = false;
                            } else if (age < 10) {
                                field.setCustomValidity('Person must be at least 10 years old');
                                isValid = false;
                            } else {
                                field.setCustomValidity('');
                            }
                        }
                        break;
                        
                    case 'height':
                        if (value && (parseFloat(value) < 50 || parseFloat(value) > 250)) {
                            field.setCustomValidity('Height must be between 50 and 250 cm');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'weight':
                        if (value && (parseFloat(value) < 20 || parseFloat(value) > 300)) {
                            field.setCustomValidity('Weight must be between 20 and 300 kg');
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                }
                
                // Update field appearance
                if (isValid && (field.hasAttribute('required') || value)) {
                    field.classList.remove('is-invalid');
                    field.classList.add('is-valid');
                } else if (!isValid) {
                    field.classList.remove('is-valid');
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-valid', 'is-invalid');
                }
                
                return isValid;
            }
            
            // Show loading state
            function showLoading(show) {
                if (show) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Creating Record...';
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Create Criminal Record';
                    checkFormValidity(); // Re-enable based on form validity
                }
            }
            
            // Show status message
            function showMessage(message, type) {
                statusMessage.innerHTML = `
                    <div class="alert-custom alert-${type}" role="alert">
                        <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                        ${message}
                    </div>
                `;
                
                // Auto-hide success messages after 8 seconds
                if (type === 'success') {
                    setTimeout(() => {
                        const alert = statusMessage.querySelector('.alert-custom');
                        if (alert) {
                            alert.remove();
                        }
                    }, 8000);
                }
            }
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'986355b412214b73',t:'MTc1OTA2MzQ5NC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
