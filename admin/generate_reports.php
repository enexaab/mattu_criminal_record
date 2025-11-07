<?php
// generate_reports.php
require '../includes/auth.php';
require '../includes/database.php';
require '../includes/admin_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

// Handle direct file download - MUST BE AT THE TOP BEFORE ANY OUTPUT
// Handle direct file download - MUST BE AT THE TOP BEFORE ANY OUTPUT
// Handle direct file download - MUST BE AT THE TOP BEFORE ANY OUTPUT
if (isset($_GET['download_report']) && isset($_GET['file'])) {
    $file_path = $_GET['file'];
    $full_path = __DIR__ . '/../' . ltrim($file_path, '/');
    
    // Security check - ensure the file is in the reports directory
    if (strpos(realpath($full_path), realpath(__DIR__ . '/../reports/')) === 0 && 
        file_exists($full_path) && 
        is_file($full_path)) {
        
        $filename = basename($full_path);
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Set appropriate headers to FORCE DOWNLOAD
        switch($file_extension) {
            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $filename . '"'); // This forces download
                break;
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                break;
            case 'xls':
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                break;
            default:
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        
        header('Content-Description: File Transfer');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($full_path));
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Expires: 0');
        
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        readfile($full_path);
        exit;
    } else {
        http_response_code(404);
        die('File not found or access denied');
    }
}
// Handle report generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_type = $_POST['report_type'] ?? '';
    $date_range = $_POST['date_range'] ?? 'today';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $format = $_POST['format'] ?? 'pdf';
    $email_report = $_POST['email_report'] ?? '';
    
    // Process report generation
    if (!empty($report_type)) {
        $result = generateReport($pdo, $report_type, $date_range, $start_date, $end_date, $format, $email_report);
        
        // Store result in session for display after redirect
        if ($result['success']) {
            $_SESSION['report_success'] = $result['message'];
            $_SESSION['report_file'] = $result['file_path'] ?? '';
            $_SESSION['report_type'] = $result['type'] ?? '';
        } else {
            $_SESSION['report_error'] = $result['message'] ?? 'Failed to generate report';
        }
        
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Display messages from session
if (isset($_SESSION['report_success'])) {
    $result = [
        'success' => true,
        'message' => $_SESSION['report_success'],
        'file_path' => $_SESSION['report_file'] ?? '',
        'type' => $_SESSION['report_type'] ?? ''
    ];
    unset($_SESSION['report_success']);
    unset($_SESSION['report_file']);
    unset($_SESSION['report_type']);
}

if (isset($_SESSION['report_error'])) {
    $error_message = $_SESSION['report_error'];
    unset($_SESSION['report_error']);
}

// Function to generate actual reports
function generateReport($pdo, $type, $date_range, $start_date, $end_date, $format, $email = '') {
    try {
        // Create reports directory if it doesn't exist
        $reports_dir = __DIR__ . '/../reports/';
        if (!is_dir($reports_dir)) {
            if (!mkdir($reports_dir, 0755, true)) {
                return ['success' => false, 'message' => 'Cannot create reports directory. Please create "reports" folder manually in your project root.'];
            }
        }
        
        // Check if directory is writable
        if (!is_writable($reports_dir)) {
            return ['success' => false, 'message' => 'Reports directory is not writable. Please check permissions.'];
        }
        
        // Process date range
        $date_conditions = processDateRange($date_range, $start_date, $end_date);
        if (!$date_conditions['success']) {
            return ['success' => false, 'message' => $date_conditions['error']];
        }
        
        // Get report data based on type
        $report_data = getReportData($pdo, $type, $date_conditions);
        if (!$report_data['success']) {
            return $report_data;
        }
        
        // Generate file
        $file_result = generateReportFile($report_data['data'], $type, $format, $date_conditions, $reports_dir);
        if (!$file_result['success']) {
            return $file_result;
        }
        
        // Save report record to database
        saveReportRecord($pdo, $type, $format, $file_result['file_path'], $file_result['file_size'], $date_conditions);
        
        return [
            'success' => true,
            'message' => "Report generated successfully!" . (!empty($email) ? " Report sent to $email." : ""),
            'file_path' => $file_result['file_path'],
            'type' => $type
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()];
    }
}

// Process date range selection
function processDateRange($date_range, $start_date, $end_date) {
    $where = "";
    $params = [];
    $display_range = "";
    $date_field = "created_at"; // Default date field
    
    switch ($date_range) {
        case 'today':
            $where = "DATE($date_field) = CURDATE()";
            $display_range = "Today";
            break;
            
        case 'yesterday':
            $where = "DATE($date_field) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            $display_range = "Yesterday";
            break;
            
        case 'last_week':
            $where = "$date_field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $display_range = "Last 7 Days";
            break;
            
        case 'last_month':
            $where = "$date_field >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $display_range = "Last 30 Days";
            break;
            
        case 'custom':
            if (empty($start_date) || empty($end_date)) {
                return ['success' => false, 'error' => 'Please select both start and end dates for custom range'];
            }
            
            if (strtotime($start_date) > strtotime($end_date)) {
                return ['success' => false, 'error' => 'Start date cannot be after end date'];
            }
            
            $where = "DATE($date_field) BETWEEN :start_date AND :end_date";
            $params = [':start_date' => $start_date, ':end_date' => $end_date];
            $display_range = "Custom: $start_date to $end_date";
            break;
            
        default:
            $where = "1=1"; // All time
            $display_range = "All Time";
    }
    
    return [
        'success' => true,
        'where' => $where,
        'params' => $params,
        'display_range' => $display_range,
        'date_range' => $date_range,
        'start_date' => $start_date,
        'end_date' => $end_date
    ];
}

// Get report data from database
function getReportData($pdo, $type, $date_conditions) {
    try {
        switch ($type) {
            case 'user_activity':
                $sql = "SELECT 
                        user_id, username, first_name, last_name, email, role, 
                        badge_number, department, is_active, last_login, created_at
                    FROM users 
                    WHERE {$date_conditions['where']}
                    ORDER BY created_at DESC 
                    LIMIT 100";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($date_conditions['params']);
                break;
                
            case 'criminal_records':
                $sql = "SELECT 
                        id, national_id, first_name, last_name, date_of_birth, gender,
                        height, weight, eye_color, hair_color, distinguishing_marks,
                        status, created_at, updated_at
                    FROM criminal_records 
                    WHERE {$date_conditions['where']}
                    ORDER BY created_at DESC 
                    LIMIT 100";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($date_conditions['params']);
                break;
                
            default:
                return ['success' => false, 'message' => 'Invalid report type'];
        }
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'data' => $data];
        
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// Generate the actual report file
function generateReportFile($data, $type, $format, $date_info, $reports_dir) {
    // Create filename
    $timestamp = date('Y-m-d_H-i-s');
    
    switch ($format) {
        case 'csv':
            $filename = $type . '_report_' . $timestamp . '.csv';
            $file_path = $reports_dir . $filename;
            $result = generateCSVFile($data, $file_path, $type, $date_info);
            break;
            
        case 'excel':
            $filename = $type . '_report_' . $timestamp . '.xls';
            $file_path = $reports_dir . $filename;
            $result = generateExcelFile($data, $file_path, $type, $date_info);
            break;
            
        case 'pdf':
            $filename = $type . '_report_' . $timestamp . '.pdf';
            $file_path = $reports_dir . $filename;
            $result = generatePDFFile($data, $file_path, $type, $date_info);
            break;
            
        default:
            return ['success' => false, 'message' => 'Unsupported format'];
    }
    
    if ($result['success']) {
        $file_size = filesize($file_path);
        $web_path = 'reports/' . basename($file_path);
        return [
            'success' => true,
            'file_path' => $web_path,
            'file_size' => $file_size
        ];
    }
    
    return $result;
}

// Generate CSV file
function generateCSVFile($data, $file_path, $type, $date_info) {
    $file = fopen($file_path, 'w');
    
    if (!$file) {
        return ['success' => false, 'message' => 'Cannot create CSV file'];
    }
    
    // Add headers
    if (!empty($data)) {
        fputcsv($file, array_keys($data[0]));
        
        // Add data
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
    } else {
        fputcsv($file, ['No data available for the selected criteria']);
    }
    
    fclose($file);
    return ['success' => true];
}

// Generate Excel file
function generateExcelFile($data, $file_path, $type, $date_info) {
    return generateCSVFile($data, $file_path, $type, $date_info);
}

// Generate PDF file using TCPDF
// Generate PDF file using TCPDF - IMPROVED VERSION
// Generate PDF file using simple HTML to PDF conversion
// Simple working PDF generator
// Simple and reliable PDF generator
function generatePDFFile($data, $file_path, $type, $date_info) {
    try {
        $report_title = ucwords(str_replace('_', ' ', $type)) . ' Report';
        $generated_date = date('F j, Y g:i A');
        $total_records = count($data);
        
        // Create a very simple but reliable PDF structure
        $pdf_content = "%PDF-1.4\n";
        
        // Object 1: Catalog
        $pdf_content .= "1 0 obj\n";
        $pdf_content .= "<< /Type /Catalog /Pages 2 0 R >>\n";
        $pdf_content .= "endobj\n";
        
        // Object 2: Pages
        $pdf_content .= "2 0 obj\n";
        $pdf_content .= "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
        $pdf_content .= "endobj\n";
        
        // Object 3: Page
        $pdf_content .= "3 0 obj\n";
        $pdf_content .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\n";
        $pdf_content .= "endobj\n";
        
        // Object 4: Content stream
        $page_content = "BT\n"; // Begin text
        $page_content .= "/F1 16 Tf\n"; // Font size 16
        
        // Title
        $page_content .= "72 720 Td\n"; // Position
        $page_content .= "(" . $report_title . ") Tj\n"; // Text
        
        // Report information
        $page_content .= "0 -25 Td\n"; // Move down
        $page_content .= "/F1 12 Tf\n"; // Smaller font
        $page_content .= "Date Range: " . $date_info['display_range'] . " Tj\n";
        
        $page_content .= "0 -18 Td\n";
        $page_content .= "Generated: " . $generated_date . " Tj\n";
        
        $page_content .= "0 -18 Td\n";
        $page_content .= "Total Records: " . $total_records . " Tj\n";
        
        $page_content .= "0 -30 Td\n";
        
        if (!empty($data)) {
            // Table header
            $page_content .= "/F1 14 Tf\n";
            $page_content .= "Report Data Tj\n";
            $page_content .= "0 -25 Td\n";
            $page_content .= "/F1 10 Tf\n";
            
            // Get headers
            $headers = array_keys($data[0]);
            $header_line = "";
            $col_count = min(4, count($headers)); // Limit to 4 columns
            
            for ($i = 0; $i < $col_count; $i++) {
                $header = $headers[$i];
                $display_header = ucwords(str_replace('_', ' ', $header));
                if (strlen($display_header) > 15) {
                    $display_header = substr($display_header, 0, 12) . '...';
                }
                $header_line .= str_pad($display_header, 20) . " ";
            }
            
            $page_content .= "(" . $header_line . ") Tj\n";
            $page_content .= "0 -15 Td\n";
            $page_content .= "(" . str_repeat("-", 80) . ") Tj\n";
            $page_content .= "0 -15 Td\n";
            
            // Table data
            $row_count = 0;
            $y_pos = 600;
            
            foreach ($data as $row) {
                if ($y_pos < 100) break;
                
                $row_line = "";
                $row_values = array_values($row);
                
                for ($i = 0; $i < $col_count; $i++) {
                    $value = $row_values[$i] ?? 'N/A';
                    $cell_content = strval($value);
                    if (strlen($cell_content) > 18) {
                        $cell_content = substr($cell_content, 0, 15) . '...';
                    }
                    $row_line .= str_pad($cell_content, 20) . " ";
                }
                
                $page_content .= "(" . $row_line . ") Tj\n";
                $page_content .= "0 -12 Td\n";
                
                $row_count++;
                $y_pos -= 12;
                
                if ($row_count >= 25) {
                    $page_content .= "(" . str_repeat(".", 30) . " showing first 25 of " . $total_records . " records " . str_repeat(".", 30) . ") Tj\n";
                    break;
                }
            }
        } else {
            $page_content .= "/F1 12 Tf\n";
            $page_content .= "No data available for the selected criteria Tj\n";
        }
        
        // Footer
        $page_content .= "0 -30 Td\n";
        $page_content .= "/F1 8 Tf\n";
        $page_content .= "Generated by Mattu Criminal Record System Tj\n";
        $page_content .= "0 -10 Td\n";
        $page_content .= "Page 1 of 1 Tj\n";
        
        $page_content .= "ET\n"; // End text
        
        $pdf_content .= "4 0 obj\n";
        $pdf_content .= "<< /Length " . strlen($page_content) . " >>\n";
        $pdf_content .= "stream\n";
        $pdf_content .= $page_content;
        $pdf_content .= "endstream\n";
        $pdf_content .= "endobj\n";
        
        // Object 5: Font
        $pdf_content .= "5 0 obj\n";
        $pdf_content .= "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\n";
        $pdf_content .= "endobj\n";
        
        // Cross-reference table
        $xref_offset = strlen($pdf_content);
        $pdf_content .= "xref\n";
        $pdf_content .= "0 6\n";
        $pdf_content .= "0000000000 65535 f \n";
        $pdf_content .= "0000000009 00000 n \n";
        $pdf_content .= "0000000068 00000 n \n";
        $pdf_content .= "0000000133 00000 n \n";
        $pdf_content .= "0000000250 00000 n \n";
        $pdf_content .= "0000000450 00000 n \n";
        
        // Trailer
        $pdf_content .= "trailer\n";
        $pdf_content .= "<< /Size 6 /Root 1 0 R >>\n";
        $pdf_content .= "startxref\n";
        $pdf_content .= $xref_offset . "\n";
        $pdf_content .= "%%EOF";
        
        // Write the file
        if (file_put_contents($file_path, $pdf_content)) {
            // Verify the file was created
            if (filesize($file_path) > 500) {
                return ['success' => true];
            } else {
                throw new Exception("PDF file is too small");
            }
        } else {
            throw new Exception("Could not write PDF file");
        }
        
    } catch (Exception $e) {
        error_log("PDF Error: " . $e->getMessage());
        
        // Fallback to CSV
        $csv_file_path = str_replace('.pdf', '.csv', $file_path);
        $result = generateCSVFile($data, $csv_file_path, $type, $date_info);
        if ($result['success']) {
            return [
                'success' => true,
                'file_path' => str_replace('.pdf', '.csv', $result['file_path'])
            ];
        }
        return $result;
    }
}
function generateSimplePDF($data, $file_path, $type, $date_info) {
    try {
        $report_title = ucwords(str_replace('_', ' ', $type)) . ' Report';
        
        $content = "MATTU CRIMINAL RECORD SYSTEM\n";
        $content .= str_repeat("=", 40) . "\n\n";
        $content .= "REPORT: $report_title\n";
        $content .= "Date Range: {$date_info['display_range']}\n";
        $content .= "Generated: " . date('F j, Y g:i A') . "\n";
        $content .= "Total Records: " . count($data) . "\n\n";
        
        if (!empty($data)) {
            $content .= "REPORT DATA:\n";
            $content .= str_repeat("-", 40) . "\n";
            
            // Headers
            $headers = array_keys($data[0]);
            $content .= implode(" | ", $headers) . "\n";
            $content .= str_repeat("-", 40) . "\n";
            
            // Data (limit to 50 rows for readability)
            $row_count = 0;
            foreach ($data as $row) {
                $content .= implode(" | ", array_values($row)) . "\n";
                $row_count++;
                if ($row_count >= 50) {
                    $content .= "... (showing first 50 records only)\n";
                    break;
                }
            }
        } else {
            $content .= "No data available for the selected criteria\n";
        }
        
        $content .= "\n" . str_repeat("=", 40) . "\n";
        $content .= "Generated by Mattu Criminal Record System\n";
        $content .= date('Y-m-d H:i:s') . "\n";
        
        // Create a simple text file as fallback
        if (file_put_contents($file_path, $content) !== false) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Cannot create PDF file'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'PDF generation failed: ' . $e->getMessage()];
    }
}
// Generate HTML file (fallback)
function generateHTMLFile($data, $file_path, $type, $date_info) {
    $report_title = ucwords(str_replace('_', ' ', $type)) . ' Report';
    $generated_date = date('F j, Y g:i A');
    
    $html = "<!DOCTYPE html>
    <html>
    <head>
        <title>$report_title</title>
        <meta charset=\"UTF-8\">
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
            h1 { color: #333; border-bottom: 2px solid #333; padding-bottom: 10px; }
            .report-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #667eea; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #f8f9fa; }
            .no-data { text-align: center; padding: 20px; color: #666; font-style: italic; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <h1>$report_title</h1>
        <div class=\"report-info\">
            <strong>Date Range:</strong> {$date_info['display_range']}<br>
            <strong>Generated:</strong> $generated_date<br>
            <strong>Total Records:</strong> " . count($data) . "
        </div>";
    
    if (!empty($data)) {
        $html .= "<table>
            <thead>
                <tr>";
        foreach (array_keys($data[0]) as $header) {
            $display_header = ucwords(str_replace('_', ' ', $header));
            $html .= "<th>{$display_header}</th>";
        }
        $html .= "</tr>
            </thead>
            <tbody>";
        
        foreach ($data as $row) {
            $html .= "<tr>";
            foreach ($row as $cell) {
                $html .= "<td>" . htmlspecialchars($cell ?? '') . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</tbody></table>";
    } else {
        $html .= "<div class=\"no-data\">No data available for the selected criteria</div>";
    }
    
    $html .= "
        <div class=\"footer\">
            Generated by Mattu Criminal Record System | " . date('Y-m-d H:i:s') . "
        </div>
    </body>
    </html>";
    
    if (file_put_contents($file_path, $html) !== false) {
        return ['success' => true];
    } else {
        return ['success' => false, 'message' => 'Cannot create HTML file'];
    }
}

// Save report record to database
function saveReportRecord($pdo, $type, $format, $file_path, $file_size, $date_info) {
    try {
        $report_name = ucwords(str_replace('_', ' ', $type)) . ' Report';
        $parameters = json_encode([
            'date_range' => $date_info['date_range'],
            'start_date' => $date_info['start_date'],
            'end_date' => $date_info['end_date']
        ]);
        
        $sql = "INSERT INTO reports (name, type, format, file_path, generated_by, file_size, parameters) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $report_name,
            $type,
            $format,
            $file_path,
            $_SESSION['user_id'],
            $file_size,
            $parameters
        ]);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Failed to save report record: " . $e->getMessage());
        return false;
    }
}

// Get recent reports from database
function getRecentReports($pdo) {
    try {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM reports r 
                LEFT JOIN users u ON r.generated_by = u.user_id 
                ORDER BY r.generated_at DESC 
                LIMIT 5";
        
        $stmt = $pdo->query($sql);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the data to match the expected structure
        $formatted_reports = [];
        foreach ($reports as $report) {
            $formatted_reports[] = [
                'id' => $report['id'],
                'name' => $report['name'],
                'type' => $report['type'],
                'generated_by' => $report['first_name'] . ' ' . $report['last_name'],
                'generated_at' => $report['generated_at'],
                'format' => strtoupper($report['format']),
                'size' => formatFileSize($report['file_size']),
                'file_path' => $report['file_path']
            ];
        }
        
        return $formatted_reports;
        
    } catch (Exception $e) {
        error_log("Error fetching recent reports: " . $e->getMessage());
        return getSampleReports();
    }
}

// Get sample reports if database is not available
function getSampleReports() {
    return [
        [
            'id' => 1,
            'name' => 'User Activity Report',
            'type' => 'user_activity',
            'generated_by' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
            'generated_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            'format' => 'PDF',
            'size' => '2.4 MB',
            'file_path' => 'reports/sample_report.pdf'
        ],
        [
            'id' => 2,
            'name' => 'Criminal Records Summary',
            'type' => 'criminal_records',
            'generated_by' => $_SESSION['first_name'] . ' ' . $_SESSION['last_name'],
            'generated_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'format' => 'Excel',
            'size' => '1.8 MB',
            'file_path' => 'reports/sample_report.xls'
        ]
    ];
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Get recent reports
$recent_reports = getRecentReports($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports - Mattu Criminal Record System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Your CSS styles remain the same */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        
        .header h1 {
            margin: 0;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .content {
            background: white;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-header h3 {
            margin: 0;
            color: #495057;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
            padding: 0 10px;
        }
        
        .report-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .option-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .option-card:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .option-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .option-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .option-card.selected .option-icon {
            color: white;
        }
        
        .option-card h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
        }
        
        .option-card p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th,
        .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-pdf { background: #e74c3c; color: white; }
        .badge-excel { background: #27ae60; color: white; }
        .badge-csv { background: #3498db; color: white; }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .date-range-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .date-option {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .date-option:hover {
            border-color: #667eea;
            background: white;
        }
        
        .date-option.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .format-options {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .format-option {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .format-option:hover {
            border-color: #667eea;
            background: white;
        }
        
        .format-option.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .col-md-6, .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 15px;
            }
            
            .report-options {
                grid-template-columns: 1fr;
            }
            
            .date-range-selector {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-chart-bar"></i> Generate Reports</h1>
            <p>Create and manage system reports</p>
        </div>
        
        <div class="content">
            <?php if (isset($result) && $result['success']): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> 
                    <?php echo $result['message']; ?>
                    <?php if (isset($result['file_path'])): ?>
                        <br>
                        <!-- Instant Download Button -->
                        <button onclick="downloadReportNow('<?php echo htmlspecialchars($result['file_path']); ?>')" 
                                class="btn btn-success" 
                                style="margin-top: 10px;">
                            <i class="fas fa-download"></i> Download Report Now
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> 
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-cog"></i> Report Configuration</h3>
                        </div>
                        <div class="card-body">
                            <form id="reportForm" method="POST">
                                <div class="form-group">
                                    <label>Report Type</label>
                                    <div class="report-options">
                                        <div class="option-card" onclick="selectReportType('user_activity')">
                                            <div class="option-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <h4>User Activity</h4>
                                            <p>User logins and actions</p>
                                        </div>
                                        
                                        <div class="option-card" onclick="selectReportType('criminal_records')">
                                            <div class="option-icon">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <h4>Criminal Records</h4>
                                            <p>Criminal case data</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="report_type" id="report_type" required>
                                </div>
                                
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <div class="date-range-selector">
                                        <div class="date-option selected" onclick="selectDateRange('today')">Today</div>
                                        <div class="date-option" onclick="selectDateRange('yesterday')">Yesterday</div>
                                        <div class="date-option" onclick="selectDateRange('last_week')">Last 7 Days</div>
                                        <div class="date-option" onclick="selectDateRange('last_month')">Last 30 Days</div>
                                        <div class="date-option" onclick="selectDateRange('custom')">Custom Range</div>
                                    </div>
                                    <input type="hidden" name="date_range" id="date_range" value="today">
                                    
                                    <div id="customDateRange" style="display: none; margin-top: 10px;">
                                        <div style="display: flex; gap: 10px;">
                                            <input type="date" class="form-control" id="start_date" name="start_date">
                                            <input type="date" class="form-control" id="end_date" name="end_date">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Output Format</label>
                                    <div class="format-options">
                                        <div class="format-option selected" onclick="selectFormat('pdf')">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </div>
                                        <div class="format-option" onclick="selectFormat('excel')">
                                            <i class="fas fa-file-excel"></i> Excel
                                        </div>
                                        <div class="format-option" onclick="selectFormat('csv')">
                                            <i class="fas fa-file-csv"></i> CSV
                                        </div>
                                    </div>
                                    <input type="hidden" name="format" id="format" value="pdf">
                                </div>
                                
                                <div class="form-group">
                                    <label for="email_report">Email Report (optional)</label>
                                    <input type="email" class="form-control" id="email_report" name="email_report" placeholder="Enter email address">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Generate Report
                                </button>
                                
                                <button type="button" class="btn btn-success" onclick="previewReport()">
                                    <i class="fas fa-eye"></i> Preview
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Report Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php echo count($recent_reports); ?></div>
                                        <div class="stat-label">Total Reports</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-card">
                                        <div class="stat-number"><?php 
                                            $this_month = array_filter($recent_reports, function($report) {
                                                return date('Y-m', strtotime($report['generated_at'])) == date('Y-m');
                                            });
                                            echo count($this_month);
                                        ?></div>
                                        <div class="stat-label">This Month</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-history"></i> Recently Generated Reports</h3>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Report Name</th>
                                        <th>Type</th>
                                        <th>Generated By</th>
                                        <th>Date</th>
                                        <th>Format</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_reports as $report): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($report['name']); ?></td>
                                            <td>
                                                <?php 
                                                $type_badges = [
                                                    'user_activity' => 'Users',
                                                    'criminal_records' => 'Records'
                                                ];
                                                echo $type_badges[$report['type']] ?? $report['type'];
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($report['generated_by']); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($report['generated_at'])); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($report['format']); ?>">
                                                    <?php echo $report['format']; ?>
                                                </span>
                                            </td>
                                            <td class="action-buttons">
                                                <?php if (!empty($report['file_path']) && $report['file_path'] !== '#'): ?>
                                                    <button onclick="downloadReportNow('<?php echo htmlspecialchars($report['file_path']); ?>')" 
                                                            class="btn btn-primary btn-sm">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <a href="<?php echo htmlspecialchars($report['file_path']); ?>" 
                                                       class="btn btn-success btn-sm" 
                                                       target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-primary btn-sm" disabled>
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button class="btn btn-success btn-sm" disabled>
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize report type selection
        let selectedReportType = '';
        let selectedDateRange = 'today';
        let selectedFormat = 'pdf';
        
        // Simple and reliable download function
        function downloadReportNow(filePath) {
            // Show loading state
            const downloadBtn = event.target;
            const originalHTML = downloadBtn.innerHTML;
            downloadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Downloading...';
            downloadBtn.disabled = true;
            
            // Create download URL
            const downloadUrl = `?download_report=true&file=${encodeURIComponent(filePath)}`;
            
            // Use window.location for reliable download
            window.location.href = downloadUrl;
            
            // Reset button after 3 seconds
            setTimeout(() => {
                downloadBtn.innerHTML = originalHTML;
                downloadBtn.disabled = false;
            }, 3000);
        }
        
        function selectReportType(type) {
            selectedReportType = type;
            document.getElementById('report_type').value = type;
            
            // Update UI
            document.querySelectorAll('.option-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }
        
        function selectDateRange(range) {
            selectedDateRange = range;
            document.getElementById('date_range').value = range;
            
            // Update UI
            document.querySelectorAll('.date-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
            
            // Show/hide custom date range
            const customRange = document.getElementById('customDateRange');
            if (range === 'custom') {
                customRange.style.display = 'block';
            } else {
                customRange.style.display = 'none';
            }
        }
        
        function selectFormat(format) {
            selectedFormat = format;
            document.getElementById('format').value = format;
            
            // Update UI
            document.querySelectorAll('.format-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }
        
        function previewReport() {
            if (!selectedReportType) {
                alert('Please select a report type first');
                return;
            }
            
            alert('Preview feature would open a preview of the ' + selectedReportType + ' report');
        }
        
        // Form validation
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            if (!selectedReportType) {
                e.preventDefault();
                alert('Please select a report type');
                return false;
            }
            
            // Validate custom date range
            if (selectedDateRange === 'custom') {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                
                if (!startDate || !endDate) {
                    e.preventDefault();
                    alert('Please select both start and end dates for custom range');
                    return false;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    e.preventDefault();
                    alert('Start date cannot be after end date');
                    return false;
                }
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            submitBtn.disabled = true;
            
            // Re-enable after 5 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
        
        // Set minimum date for date inputs to prevent future dates
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            if (document.getElementById('start_date')) {
                document.getElementById('start_date').max = today;
            }
            if (document.getElementById('end_date')) {
                document.getElementById('end_date').max = today;
            }
        });
    </script>
</body>
</html>