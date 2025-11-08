<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Language support
$languages = ['en', 'am', 'om'];
$current_lang = $_SESSION['lang'] ?? 'en';
if (isset($_POST['lang']) && in_array($_POST['lang'], $languages)) {
    $_SESSION['lang'] = $_POST['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], $languages)) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
$translations = [
    'en' => [
        'title' => 'Create Case File - Mattu City Criminal Management System',
        'create_case_file' => 'Create Case File',
        'initiate_new_investigation' => 'Initiate a new investigation by linking it to an existing criminal record',
        'suspect_information' => 'Suspect Information',
        'criminal_record_id' => 'Criminal Record ID',
        'criminal_record_id_placeholder' => 'Enter Criminal Record ID',
        'verify' => 'Verify',
        'suspect_name' => 'Suspect Name',
        'suspect_name_placeholder' => 'Will be populated after verification',
        'suspect_name_auto' => 'Automatically populated from criminal record',
        'national_id' => 'National ID',
        'status' => 'Status',
        'record_id' => 'Record ID',
        'case_details' => 'Case Details',
        'case_number' => 'Case Number',
        'case_number_placeholder' => 'e.g., CASE-2024-001',
        'generate' => 'Generate',
        'case_number_unique' => 'Unique identifier for this case file',
        'case_type' => 'Case Type',
        'select_case_type' => 'Select Case Type',
        'theft' => 'Theft',
        'fraud' => 'Fraud',
        'assault' => 'Assault',
        'burglary' => 'Burglary',
        'robbery' => 'Robbery',
        'drug_offense' => 'Drug Offense',
        'vandalism' => 'Vandalism',
        'domestic_violence' => 'Domestic Violence',
        'cybercrime' => 'Cybercrime',
        'money_laundering' => 'Money Laundering',
        'embezzlement' => 'Embezzlement',
        'other' => 'Other',
        'date_reported' => 'Date Reported',
        'incident_location' => 'Incident Location',
        'incident_location_placeholder' => 'Enter incident location',
        'where_incident_occurred' => 'Where the incident occurred',
        'case_description' => 'Case Description',
        'case_description_placeholder' => 'Provide detailed description of the incident, evidence, and circumstances...',
        'case_description_details' => 'Detailed description of the incident and investigation details',
        'officer_information' => 'Officer Information',
        'lead_officer' => 'Lead Officer',
        'lead_officer_assigned' => 'You will be assigned as the lead officer for this case',
        'your_role' => 'Your Role',
        'your_role_current' => 'Your current role in the system',
        'cancel' => 'Cancel',
        'create_case_file_btn' => 'Create Case File',
        'success_message' => 'Case file created successfully',
        'case_id' => 'Case ID',
        'suspect' => 'Suspect',
        'next_steps' => 'Next Steps',
        'case_created_success' => 'Case file created successfully. You can now manage the investigation.',
        'view_case_file' => 'View Case File',
        'all_cases' => 'All Cases',
        'create_another' => 'Create Another',
        'error_verify_record' => 'Please enter a Criminal Record ID to verify.',
        'verified' => 'Verified!',
        'criminal_record_found' => 'Criminal record found:',
        'not_found' => 'Not Found!',
        'record_id_not_exist' => 'Criminal Record ID does not exist in the system.',
        'error' => 'Error!',
        'failed_verify' => 'Failed to verify Criminal Record. Please try again.',
        'network_error' => 'Network Error! Please check your connection and try again.',
        'please_verify_record' => 'Please verify the Criminal Record ID before submitting.',
        'correct_errors' => 'Please correct the errors in the form before submitting.',
        'loading_creating' => 'Creating Case File...',
        'invalid_record_id' => 'Criminal Record ID must be a positive number',
        'case_number_min_length' => 'Case number must be at least 3 characters',
        'date_future' => 'Date reported cannot be in the future',
        'location_min_length' => 'Location must be at least 3 characters',
        'missing_required' => 'Missing required field:',
        'invalid_date_format' => 'Invalid date format for date reported. Use YYYY-MM-DD format.',
        'date_not_future' => 'Date reported cannot be in the future',
        'record_not_exist' => 'Criminal Record ID does not exist in the system',
        'case_number_exists' => 'Case Number already exists. Please use a unique case number.',
        'failed_insert_case' => 'Failed to insert into case table',
        'failed_insert_person' => 'Failed to insert into case_persons table',
    ],
    'am' => [
        'title' => 'ጉዳይ ፋይል ፍጠር - ማቱ ከተማ የወደንጀል አስተዳደር ስርዓት',
        'create_case_file' => 'ጉዳይ ፋይል ፍጠር',
        'initiate_new_investigation' => 'አዲስ ምርመራ ጀምር በተኖረ የወደንጀላዊ መዝገብ ጋር በመገናኘት',
        'suspect_information' => 'የተጠርጣሪ መረጃ',
        'criminal_record_id' => 'የወደንጀላዊ መዝገብ መለያ',
        'criminal_record_id_placeholder' => 'የወደንጀላዊ መዝገብ መለያ ያስገቡ',
        'verify' => 'ይገምግሙ',
        'suspect_name' => 'የተጠርጣሪ ስም',
        'suspect_name_placeholder' => 'በግምት ላይ ይሞላል',
        'suspect_name_auto' => 'አውቶማቲክ ከወደንጀላዊ መዝገብ ይሞላል',
        'national_id' => 'ብሔራዊ መለያ',
        'status' => 'ሁኔታ',
        'record_id' => 'መዝገብ መለያ',
        'case_details' => 'የጉዳይ ዝርዝር',
        'case_number' => 'የጉዳይ ቁጥር',
        'case_number_placeholder' => 'ለምሳሌ፣ CASE-2024-001',
        'generate' => 'ይፍጠሩ',
        'case_number_unique' => 'ይሁን የዚህ ጉዳይ ፋይል መለያ',
        'case_type' => 'የጉዳይ አይነት',
        'select_case_type' => 'የጉዳይ አይነት ይምረጡ',
        'theft' => 'ቃል ማግኘት',
        'fraud' => 'በሽታ',
        'assault' => 'ጥቃት',
        'burglary' => 'ቤት መበላሸት',
        'robbery' => 'ጉቦ ማግኘት',
        'drug_offense' => 'የአደገኛ መድሃኒት ጥፋት',
        'vandalism' => 'ጉቦ መበላሸት',
        'domestic_violence' => 'ቤታዊ ጥቃት',
        'cybercrime' => 'የአየር ገነት ወደንጀል',
        'money_laundering' => 'ገንዘብ ማጽዳት',
        'embezzlement' => 'ገንዘብ ማባል',
        'other' => 'ሌላ',
        'date_reported' => 'የተመረጠ ቀን',
        'incident_location' => 'የተከሰተው ቦታ',
        'incident_location_placeholder' => 'የተከሰተው ቦታ ያስገቡ',
        'where_incident_occurred' => 'የተከሰተው ቦታ',
        'case_description' => 'የጉዳይ መግለጫ',
        'case_description_placeholder' => 'የተከሰተውን ክስተት፣ ውህደት እና ሁኔታዎች ዝርዝር ይስጡ...',
        'case_description_details' => 'የተከሰተው ክስተት እና ምርመራ ዝርዝር',
        'officer_information' => 'የመኮንን መረጃ',
        'lead_officer' => 'መሪ መኮንን',
        'lead_officer_assigned' => 'በዚህ ጉዳይ መሪ መኮንን ታትማለሻሉ',
        'your_role' => 'የእርስዎ ሚና',
        'your_role_current' => 'በስርዓቱ ያለዎት ሚና',
        'cancel' => 'ይቅር',
        'create_case_file_btn' => 'ጉዳይ ፋይል ፍጠር',
        'success_message' => 'ጉዳይ ፋይል በተሳካ ሁኔታ ተፈጸመ',
        'case_id' => 'የጉዳይ መለያ',
        'suspect' => 'ተጠርጣሪ',
        'next_steps' => 'ቀጣይ እርምጃዎች',
        'case_created_success' => 'ጉዳይ ፋይል በተሳካ ሁኔታ ተፈጸመ። አሁን ምርመራውን መቆጣጠር ይችላሉ።',
        'view_case_file' => 'ጉዳይ ፋይል ይመልከቱ',
        'all_cases' => 'ሁሉም ጉዳዮች',
        'create_another' => 'ሌላ ይፍጠሩ',
        'error_verify_record' => 'የወደንጀላዊ መዝገብ መለያ ለግምት ያስገቡ።',
        'verified' => 'ተገምግመ!',
        'criminal_record_found' => 'የወደንጀላዊ መዝገብ ተገኘ፡',
        'not_found' => 'አልተገኘም!',
        'record_id_not_exist' => 'የወደንጀላዊ መዝገብ መለያ በስርዓቱ ውስጥ የለም።',
        'error' => 'ስህተት!',
        'failed_verify' => 'የወደንጀላዊ መዝገብ ለመገምግም ተሳካ አልተለመደም። እንደገና ይሞክሩ።',
        'network_error' => 'የኔትወርክ ስህተት! ግንኙነቱን ይመልከቱ እና እንደገና ይሞክሩ።',
        'please_verify_record' => 'በማስገባት አብራ የወደንጀላዊ መዝገብ መለያ ይገምግሙ።',
        'correct_errors' => 'በማስገባት አብራ በቅጽበት ያለውን ስህተት ይግርሱ።',
        'loading_creating' => 'ጉዳይ ፋይል በመፍጠር...',
        'invalid_record_id' => 'የወደንጀላዊ መዝገብ መለያ አንድ አለባበት ቁጥር መሆን አለበት',
        'case_number_min_length' => 'የጉዳይ ቁጥር ቢያንስ 3 ቁራጮች መሆን አለበት',
        'date_future' => 'የተመረጠ ቀን በወደፊት መሆን አይችልም',
        'location_min_length' => 'ቦታ ቢያንስ 3 ቁራጮች መሆን አለበት',
        'missing_required' => 'የሚያስፈልገው መስክ ይጎለች፡',
        'invalid_date_format' => 'የተመረጠ ቀን ቅርጸት ተገፋ። YYYY-MM-DD ቅርጸት ይጠቀሙ።',
        'date_not_future' => 'የተመረጠ ቀን በወደፊት መሆን አይችልም',
        'record_not_exist' => 'የወደንጀላዊ መዝገብ መለያ በስርዓቱ ውስጥ የለም',
        'case_number_exists' => 'የጉዳይ ቁጥር ቀደም ብሎ አለ። የተለየ የጉዳይ ቁጥር ይጠቀሙ።',
        'failed_insert_case' => 'በጉዳይ ሰንጠረዥ ውስጥ ማስገባት ተሳካ አልተለመደም',
        'failed_insert_person' => 'በጉዳይ_አንድ ሰንጠረዥ ውስጥ ማስገባት ተሳካ አልተለመደም',
    ],
    'om' => [
        'title' => 'Caasaa Fayila Qophaa - Sisteemi Diinagdee Mattu Kuta',
        'create_case_file' => 'Caasaa Fayila Qophaa',
        'initiate_new_investigation' => 'Qoricha qabuu argachuu qoricha diinagdee qabuu argisi',
        'suspect_information' => 'Qoricha Diinagdee',
        'criminal_record_id' => 'ID Qoricha Diinagdee',
        'criminal_record_id_placeholder' => 'ID Qoricha Diinagdee argisi',
        'verify' => 'Argisi',
        'suspect_name' => 'Maatii Diinagdee',
        'suspect_name_placeholder' => 'Argisi kennuu qoricha qabuu',
        'suspect_name_auto' => 'Aawtomaatikii qoricha diinagdee kennuu',
        'national_id' => 'ID Naamaa',
        'status' => 'Hakkina',
        'record_id' => 'ID Qoricha',
        'case_details' => 'Qoricha Caasaa',
        'case_number' => 'Naama Caasaa',
        'case_number_placeholder' => 'Mallisaa, CASE-2024-001',
        'generate' => 'Qophaa',
        'case_number_unique' => 'Naama ykn caasaa fayila qabuu',
        'case_type' => 'Aangoo Caasaa',
        'select_case_type' => 'Aangoo Caasaa Argisi',
        'theft' => 'Qoricha Qabuu',
        'fraud' => 'Qoricha Beshitaa',
        'assault' => 'Qoricha Qabuu',
        'burglary' => 'Biiroo Meqbaala',
        'robbery' => 'Qoricha Qabuu',
        'drug_offense' => 'Qoricha Medhaani',
        'vandalism' => 'Qoricha Meqbaala',
        'domestic_violence' => 'Qoricha Bitaa',
        'cybercrime' => 'Qoricha Aayyaa',
        'money_laundering' => 'Qoricha Gammachuu',
        'embezzlement' => 'Qoricha Gammachuu',
        'other' => 'Biroo',
        'date_reported' => 'Guyyaa Qoricha',
        'incident_location' => 'Mallattoo Qoricha',
        'incident_location_placeholder' => 'Mallattoo qoricha argisi',
        'where_incident_occurred' => 'Mallattoo qoricha qabeenya',
        'case_description' => 'Qoricha Caasaa',
        'case_description_placeholder' => 'Qoricha qoricha, ummata, fi qoricha qoricha qoricha...',
        'case_description_details' => 'Qoricha qoricha fi qoricha qoricha',
        'officer_information' => 'Qoricha Meekoonnin',
        'lead_officer' => 'Meekoonnin Qoricha',
        'lead_officer_assigned' => 'Caasaa kee qoricha meekoonnin kennuu',
        'your_role' => 'Mina Kee',
        'your_role_current' => 'Mina kee qoricha',
        'cancel' => 'Deebii',
        'create_case_file_btn' => 'Caasaa Fayila Qophaa',
        'success_message' => 'Caasaa fayila qophaa argame!',
        'case_id' => 'ID Caasaa',
        'suspect' => 'Diinagdee',
        'next_steps' => 'Qoricha Guyyaa',
        'case_created_success' => 'Caasaa fayila qophaa argame. Diinagdee qoricha qoricha.',
        'view_case_file' => 'Caasaa Fayila Argisi',
        'all_cases' => 'Caasoota Hunda',
        'create_another' => 'Biroo Qophaa',
        'error_verify_record' => 'ID Qoricha Diinagdee argisi kennuu.',
        'verified' => 'Argame!',
        'criminal_record_found' => 'Qoricha diinagdee argame:',
        'not_found' => 'Hin Arganne!',
        'record_id_not_exist' => 'ID Qoricha Diinagdee sisteemi keessatti hin taane.',
        'error' => 'Sagadduu!',
        'failed_verify' => 'Qoricha diinagdee argisi kennuu sagadduu. Eenyummaa argisi.',
        'network_error' => 'Sagadduu Netwirk! Gammachuu kee argisi fi eenyummaa argisi.',
        'please_verify_record' => 'Qoricha diinagdee argisi kennuu qoricha.',
        'correct_errors' => 'Qoricha sagadduu qoricha qoricha.',
        'loading_creating' => 'Caasaa Fayila Qophaa...',
        'invalid_record_id' => 'ID Qoricha Diinagdee qabuu argamuu',
        'case_number_min_length' => 'Naama caasaa 3 qoricha argamuu',
        'date_future' => 'Guyyaa qoricha qoricha hin taane',
        'location_min_length' => 'Mallattoo 3 qoricha argamuu',
        'missing_required' => 'Qoricha qoricha:',
        'invalid_date_format' => 'Guyyaa qoricha qoricha. YYYY-MM-DD argisi.',
        'date_not_future' => 'Guyyaa qoricha qoricha hin taane',
        'record_not_exist' => 'ID Qoricha Diinagdee sisteemi keessatti hin taane',
        'case_number_exists' => 'Naama caasaa qoricha. Naama ykn caasaa argisi.',
        'failed_insert_case' => 'Caasaa santeerijii keessatti qophaa sagadduu',
        'failed_insert_person' => 'Caasaa_persoonii santeerijii keessatti qophaa sagadduu',
    ],
];
function t($key) {
    global $translations, $current_lang;
    $trans = $translations[$current_lang][$key] ?? $key;
    // Replace placeholders if any
    if (strpos($trans, '{') !== false) {
        global $case_id, $case_number, $suspect_name, $error_message;
        $trans = str_replace('{case_id}', $case_id ?? '', $trans);
        $trans = str_replace('{case_number}', $case_number ?? '', $trans);
        $trans = str_replace('{suspect_name}', $suspect_name ?? '', $trans);
        $trans = str_replace('{error}', $error_message ?? '', $trans);
    }
    return $trans;
}

// Check if required files exist before including them
$required_files = [
    '../includes/auth.php',
    '../includes/database.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die(t('error_required_file') . " $file " . t('not_found'));
    }
}

try {
    require_once '../includes/auth.php';
    require_once '../includes/database.php';
    
    // Debug: Check if database connection is working
    if (!isset($db)) {
        throw new Exception(t('db_connection_failed'));
    }
    
    // Test the connection
    $db->query("SELECT 1");
    
} catch (Exception $e) {
    die(t('error_loading_files') . ": " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Role check - Must be Investigator or Officer
if (!function_exists('requireRole')) {
    // Basic session-based role check
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['investigator', 'officer'])) {
        die(t('access_denied'));
    }
} else {
    // Use the requireRole function if it exists
    requireRole(['investigator', 'officer']);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Get the request URI to determine the action
    $request_uri = $_SERVER['REQUEST_URI'];
    
    // Handle Criminal Record verification request
    if (strpos($request_uri, 'verify_record') !== false) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['record_id']) || empty($input['record_id'])) {
                throw new Exception(t('record_id_required'));
            }
            
            $record_id = trim($input['record_id']);
            
            // Check database connection
            if (!isset($db) || !$db) {
                throw new Exception(t('db_not_available'));
            }
            
            // Check if Criminal Record exists
            $stmt = $db->prepare("
                SELECT id, first_name, last_name, national_id, status, photo 
                FROM criminal_records 
                WHERE id = ?
            ");
            $stmt->execute([$record_id]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($record) {
                echo json_encode([
                    'success' => true,
                    'exists' => true,
                    'record' => [
                        'id' => $record['id'],
                        'full_name' => $record['first_name'] . ' ' . $record['last_name'],
                        'national_id' => $record['national_id'],
                        'status' => $record['status'],
                        'photo' => $record['photo']
                    ],
                    'message' => t('record_found_verified')
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'exists' => false,
                    'message' => t('record_id_not_found')
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
    
    // Handle case file creation - UPDATED FOR YOUR DATABASE STRUCTURE
    try {
        error_log("=== CASE FILE CREATION STARTED ===");
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception(t('invalid_json'));
        }
        
        error_log("Received input: " . print_r($input, true));
        
        // Extract and validate mandatory fields
        $record_id = isset($input['record_id']) ? trim($input['record_id']) : '';
        $case_number = isset($input['case_number']) ? trim($input['case_number']) : '';
        $case_type = isset($input['case_type']) ? trim($input['case_type']) : '';
        $date_reported = isset($input['date_reported']) ? trim($input['date_reported']) : '';
        $location = isset($input['location']) ? trim($input['location']) : '';
        $description = isset($input['description']) ? trim($input['description']) : '';
        
        error_log("Creating case file - Record ID: $record_id, Case Number: $case_number");
        
        // Validate mandatory fields
        $required_fields = [
            'record_id' => $record_id,
            'case_number' => $case_number,
            'case_type' => $case_type,
            'date_reported' => $date_reported,
            'location' => $location
        ];
        
        foreach ($required_fields as $field => $value) {
            if (empty($value)) {
                throw new Exception(t('missing_required') . " " . ucfirst(str_replace('_', ' ', $field)));
            }
        }
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $date_reported)) {
            throw new Exception(t('invalid_date_format'));
        }
        
        // Validate date is not in the future
        $report_date = new DateTime($date_reported);
        $today = new DateTime();
        if ($report_date > $today) {
            throw new Exception(t('date_not_future'));
        }
        
        // Check database connection
        if (!isset($db) || !$db) {
            throw new Exception(t('db_not_available'));
        }
        
        // Verify Criminal Record exists
        $record_check = $db->prepare("SELECT id, first_name, last_name FROM criminal_records WHERE id = ?");
        $record_check->execute([$record_id]);
        $criminal_record = $record_check->fetch(PDO::FETCH_ASSOC);
        
        if (!$criminal_record) {
            throw new Exception(t('record_not_exist') . " $record_id " . t('in_system'));
        }
        
        // Check which table exists - cases or case_files
        $table_check = $db->query("SHOW TABLES LIKE 'cases'");
        $cases_table_exists = $table_check->rowCount() > 0;
        
        $table_check = $db->query("SHOW TABLES LIKE 'case_files'");
        $case_files_table_exists = $table_check->rowCount() > 0;
        
        error_log("Cases table exists: " . ($cases_table_exists ? 'YES' : 'NO'));
        error_log("Case_files table exists: " . ($case_files_table_exists ? 'YES' : 'NO'));
        
        // Use the correct table name based on what exists
        $case_table_name = $cases_table_exists ? 'cases' : 'case_files';
        
        // Check Case Number uniqueness in the correct table
        $case_check = $db->prepare("SELECT id FROM $case_table_name WHERE case_number = ?");
        $case_check->execute([$case_number]);
        if ($case_check->fetch()) {
            throw new Exception(t('case_number_exists'));
        }
        
        // Start database transaction
        $db->beginTransaction();
        
        try {
            // INSERT into the correct cases table
            if ($case_table_name === 'cases') {
                // Insert into 'cases' table (your actual table structure)
                $case_stmt = $db->prepare("
                    INSERT INTO cases (
                        case_number, case_type, title, description, location, date_reported,
                        status, severity, assigned_officer_id, lead_officer_id, created_by, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, 'Open', 'Medium', ?, ?, ?, NOW())
                ");
                
                $case_title = "$case_type Case - $case_number";
                $case_result = $case_stmt->execute([
                    $case_number,
                    $case_type,
                    $case_title,
                    $description,
                    $location,
                    $date_reported,
                    $_SESSION['user_id'], // assigned_officer_id
                    $_SESSION['user_id'], // lead_officer_id  
                    $_SESSION['user_id']  // created_by
                ]);
            } else {
                // Insert into 'case_files' table (if that's what exists)
                $case_stmt = $db->prepare("
                    INSERT INTO case_files (
                        case_number, case_type, description, location, date_reported,
                        status, lead_officer_id, created_by, created_at
                    ) VALUES (?, ?, ?, ?, ?, 'Open', ?, ?, NOW())
                ");
                
                $case_result = $case_stmt->execute([
                    $case_number,
                    $case_type,
                    $description,
                    $location,
                    $date_reported,
                    $_SESSION['user_id'], // lead_officer_id
                    $_SESSION['user_id']  // created_by
                ]);
            }
            
            if (!$case_result) {
                throw new Exception(t('failed_insert_case'));
            }
            
            $case_id = $db->lastInsertId();
            error_log("Case created with ID: $case_id in table: $case_table_name");
            
            // INSERT into case_persons table (link case to criminal record)
            $person_stmt = $db->prepare("
                INSERT INTO case_persons (
                    case_id, record_id, role, added_by, added_at
                ) VALUES (?, ?, 'Suspect', ?, NOW())
            ");
            
            $person_result = $person_stmt->execute([
                $case_id,
                $record_id,
                $_SESSION['user_id']
            ]);
            
            if (!$person_result) {
                throw new Exception(t('failed_insert_person'));
            }
            
            // Commit transaction
            $db->commit();
            error_log("Case person link created successfully");
            
            // Log activity if function exists
            if (function_exists('logOfficerActivity')) {
                logOfficerActivity(
                    $_SESSION['user_id'], 
                    'case_file_created', 
                    t('created_new_case') . ": $case_number " . t('linked_to_record') . " $record_id"
                );
            }
            
            $suspect_name = $criminal_record['first_name'] . ' ' . $criminal_record['last_name'];
            echo json_encode([
                'success' => true,
                'message' => t('success_message'),
                'case_id' => $case_id,
                'case_number' => $case_number,
                'suspect_name' => $suspect_name
            ]);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $db->rollBack();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Error creating case file: " . $e->getMessage());
        
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

// Get record_id from URL if provided (for direct linking from criminal records)
$prefill_record_id = isset($_GET['record_id']) ? intval($_GET['record_id']) : '';

// Debug output to check if PHP is working
error_log(t('case_creation_loaded') . " - User: " . $current_user['full_name']);
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('title'); ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Google Fonts for Amharic and Oromo support -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Ethiopic:wght@400;700&display=swap" rel="stylesheet">
    
    <?php if ($current_lang == 'am' || $current_lang == 'om'): ?>
    <style>
        body {
            font-family: 'Noto Sans Ethiopic', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
    </style>
    <?php endif; ?>
    
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
        
        .col-md-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
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
        
        .form-control-custom:disabled {
            background-color: #f8f9fa;
            opacity: 0.8;
            cursor: not-allowed;
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
        
        .btn-generate {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
            color: #212529;
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
            cursor: pointer;
            margin-left: 10px;
        }
        
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
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
        
        .suspect-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px solid #2196f3;
            border-radius: 15px;
            padding: 20px;
            margin-top: 15px;
            display: none;
        }
        
        .suspect-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #2196f3;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
        }
        
        .suspect-details h6 {
            color: #1976d2;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .suspect-details p {
            color: #424242;
            margin-bottom: 3px;
            font-weight: 500;
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
            
            .col-md-4,
            .col-md-6,
            .col-md-8 {
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
            
            .suspect-info .d-flex {
                flex-direction: column;
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
            
            .btn-verify,
            .btn-generate {
                border-radius: 12px;
                width: 100%;
                margin-left: 0;
                margin-top: 10px;
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
                <h3><i class="fas fa-folder-plus me-3"></i><?php echo t('create_case_file'); ?></h3>
                <p><?php echo t('initiate_new_investigation'); ?></p>
            </div>
            
            <!-- Main Form -->
            <form id="createCaseFileForm" novalidate>
                
                <!-- Suspect Linkage Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-user-tie me-2"></i><?php echo t('suspect_information'); ?></legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="record_id" class="form-label form-label-custom">
                                    <?php echo t('criminal_record_id'); ?> <span class="required-indicator">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-custom" id="record_id" name="record_id" required placeholder="<?php echo t('criminal_record_id_placeholder'); ?>" value="<?php echo htmlspecialchars($prefill_record_id); ?>">
                                    <button type="button" class="btn-verify" id="verifyRecordBtn">
                                        <i class="fas fa-search me-1"></i><?php echo t('verify'); ?>
                                    </button>
                                </div>
                                <div class="invalid-feedback"><?php echo t('invalid_record_id'); ?></div>
                                <div class="form-text-custom"><?php echo t('suspect_name_auto'); ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="suspect_name" class="form-label form-label-custom"><?php echo t('suspect_name'); ?></label>
                                <input type="text" class="form-control form-control-custom" id="suspect_name" name="suspect_name" disabled placeholder="<?php echo t('suspect_name_placeholder'); ?>">
                                <div class="form-text-custom"><?php echo t('suspect_name_auto'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Suspect Information Display -->
                    <div id="suspectInfo" class="suspect-info">
                        <div class="d-flex align-items-center">
                            <img id="suspectPhoto" class="suspect-photo me-3" src="" alt="Suspect Photo">
                            <div class="suspect-details">
                                <h6 id="suspectFullName"></h6>
                                <p><strong><?php echo t('national_id'); ?>:</strong> <span id="suspectNationalId"></span></p>
                                <p><strong><?php echo t('status'); ?>:</strong> <span id="suspectStatus"></span></p>
                                <p><strong><?php echo t('record_id'); ?>:</strong> <span id="suspectRecordId"></span></p>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Case Details Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-clipboard-list me-2"></i><?php echo t('case_details'); ?></legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="case_number" class="form-label form-label-custom">
                                    <?php echo t('case_number'); ?> <span class="required-indicator">*</span>
                                </label>
                                <div class="d-flex">
                                    <input type="text" class="form-control form-control-custom" id="case_number" name="case_number" required placeholder="<?php echo t('case_number_placeholder'); ?>">
                                    <button type="button" class="btn-generate" id="generateCaseNumberBtn">
                                        <i class="fas fa-magic me-1"></i><?php echo t('generate'); ?>
                                    </button>
                                </div>
                                <div class="invalid-feedback"><?php echo t('case_number_min_length'); ?></div>
                                <div class="form-text-custom"><?php echo t('case_number_unique'); ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="case_type" class="form-label form-label-custom">
                                    <?php echo t('case_type'); ?> <span class="required-indicator">*</span>
                                </label>
                                <select class="form-control form-control-custom" id="case_type" name="case_type" required>
                                    <option value=""><?php echo t('select_case_type'); ?></option>
                                    <option value="Theft"><?php echo t('theft'); ?></option>
                                    <option value="Fraud"><?php echo t('fraud'); ?></option>
                                    <option value="Assault"><?php echo t('assault'); ?></option>
                                    <option value="Burglary"><?php echo t('burglary'); ?></option>
                                    <option value="Robbery"><?php echo t('robbery'); ?></option>
                                    <option value="Drug Offense"><?php echo t('drug_offense'); ?></option>
                                    <option value="Vandalism"><?php echo t('vandalism'); ?></option>
                                    <option value="Domestic Violence"><?php echo t('domestic_violence'); ?></option>
                                    <option value="Cybercrime"><?php echo t('cybercrime'); ?></option>
                                    <option value="Money Laundering"><?php echo t('money_laundering'); ?></option>
                                    <option value="Embezzlement"><?php echo t('embezzlement'); ?></option>
                                    <option value="Other"><?php echo t('other'); ?></option>
                                </select>
                                <div class="invalid-feedback"><?php echo t('select_case_type'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_reported" class="form-label form-label-custom">
                                    <?php echo t('date_reported'); ?> <span class="required-indicator">*</span>
                                </label>
                                <input type="date" class="form-control form-control-custom" id="date_reported" name="date_reported" required>
                                <div class="invalid-feedback"><?php echo t('date_future'); ?></div>
                                <div class="form-text-custom"><?php echo t('where_incident_occurred'); ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label form-label-custom">
                                    <?php echo t('incident_location'); ?> <span class="required-indicator">*</span>
                                </label>
                                <input type="text" class="form-control form-control-custom" id="location" name="location" required placeholder="<?php echo t('incident_location_placeholder'); ?>">
                                <div class="invalid-feedback"><?php echo t('location_min_length'); ?></div>
                                <div class="form-text-custom"><?php echo t('where_incident_occurred'); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label form-label-custom"><?php echo t('case_description'); ?></label>
                                <textarea class="form-control form-control-custom" id="description" name="description" rows="4" placeholder="<?php echo t('case_description_placeholder'); ?>"></textarea>
                                <div class="form-text-custom"><?php echo t('case_description_details'); ?></div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Officer Details Section -->
                <fieldset class="fieldset-custom">
                    <legend><i class="fas fa-badge me-2"></i><?php echo t('officer_information'); ?></legend>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lead_officer" class="form-label form-label-custom"><?php echo t('lead_officer'); ?></label>
                                <input type="text" class="form-control form-control-custom" id="lead_officer" name="lead_officer" disabled value="<?php echo htmlspecialchars($current_user['full_name']); ?>">
                                <div class="form-text-custom"><?php echo t('lead_officer_assigned'); ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="officer_role" class="form-label form-label-custom"><?php echo t('your_role'); ?></label>
                                <input type="text" class="form-control form-control-custom" id="officer_role" name="officer_role" disabled value="<?php echo ucfirst($current_user['role']); ?>">
                                <div class="form-text-custom"><?php echo t('your_role_current'); ?></div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-secondary-custom" onclick="window.history.back()">
                        <i class="fas fa-arrow-left me-2"></i><?php echo t('cancel'); ?>
                    </button>
                    
                    <button type="submit" class="btn-primary-custom" id="submitBtn" disabled>
                        <i class="fas fa-folder-plus me-2"></i><?php echo t('create_case_file_btn'); ?>
                    </button>
                </div>
                
                <!-- Status Message Container -->
                <div id="statusMessage" class="mt-4"></div>
            </form>
        </div>
    </div>
    
    <script>
        // Translations for JS
        const TRANSLATIONS = <?php echo json_encode($translations[$current_lang]); ?>;
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('createCaseFileForm');
            const submitBtn = document.getElementById('submitBtn');
            const statusMessage = document.getElementById('statusMessage');
            const verifyRecordBtn = document.getElementById('verifyRecordBtn');
            const generateCaseNumberBtn = document.getElementById('generateCaseNumberBtn');
            const recordIdInput = document.getElementById('record_id');
            const suspectNameInput = document.getElementById('suspect_name');
            const suspectInfo = document.getElementById('suspectInfo');
            const caseNumberInput = document.getElementById('case_number');
            const dateReportedInput = document.getElementById('date_reported');
            
            let recordVerified = false;
            
            // Set today's date as default for date_reported
            const today = new Date().toISOString().split('T')[0];
            dateReportedInput.value = today;
            
            // Auto-verify record if prefilled
            if (recordIdInput.value.trim()) {
                verifyRecord();
            }
            
            // Criminal Record verification
            verifyRecordBtn.addEventListener('click', function() {
                verifyRecord();
            });
            
            recordIdInput.addEventListener('blur', function() {
                if (this.value.trim()) {
                    verifyRecord();
                }
            });
            
            recordIdInput.addEventListener('input', function() {
                recordVerified = false;
                submitBtn.disabled = true;
                this.classList.remove('is-valid', 'is-invalid');
                suspectNameInput.value = '';
                suspectInfo.style.display = 'none';
            });
            
            function verifyRecord() {
                const recordId = recordIdInput.value.trim();
                
                if (!recordId) {
                    showMessage(TRANSLATIONS.error_verify_record, 'warning');
                    return;
                }
                
                // Show loading state
                verifyRecordBtn.disabled = true;
                verifyRecordBtn.innerHTML = '<span class="loading-spinner me-1"></span>' + TRANSLATIONS.verifying + '...';
                
                // Make AJAX request to verify Criminal Record
                fetch(window.location.href + '?verify_record=1', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        record_id: recordId
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success && result.exists) {
                        // Criminal Record found
                        recordVerified = true;
                        recordIdInput.classList.remove('is-invalid');
                        recordIdInput.classList.add('is-valid');
                        
                        // Populate suspect information
                        suspectNameInput.value = result.record.full_name;
                        
                        // Show suspect info card
                        document.getElementById('suspectFullName').textContent = result.record.full_name;
                        document.getElementById('suspectNationalId').textContent = result.record.national_id;
                        document.getElementById('suspectStatus').textContent = result.record.status;
                        document.getElementById('suspectRecordId').textContent = result.record.id;
                        
                        // Set photo or placeholder
                        const photoElement = document.getElementById('suspectPhoto');
                        if (result.record.photo) {
                            photoElement.src = '../' + result.record.photo;
                        } else {
                            photoElement.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iNDAiIGN5PSI0MCIgcj0iNDAiIGZpbGw9IiNlOWVjZWYiLz4KPHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIyMCIgeT0iMjAiPgo8cGF0aCBkPSJNMTIgMTJDMTQuNzYxNCAxMiAxNyA5Ljc2MTQyIDE3IDdDMTcgNC4yMzg1OCAxNC43NjE0IDIgMTIgMkM5LjIzODU4IDIgNyA0LjIzODU4IDcgN0M3IDkuNzYxNDIgOS4yMzg1OCAxMiAxMiAxMloiIGZpbGw9IiM2Yzc1N2QiLz4KPHN0cm9rZSBkPSJNMTIgMTRDNy41ODE3MiAxNCA0IDE3LjU4MTcgNCAyMlYyNEgyMFYyMkMyMCAxNy41ODE3IDE2LjQxODMgMTQgMTIgMTRaIiBmaWxsPSIjNmM3NTdkIi8+Cjwvc3ZnPgo8L3N2Zz4K';
                        }
                        
                        suspectInfo.style.display = 'block';
                        
                        checkFormValidity();
                        
                        showMessage(
                            `<i class="fas fa-check-circle me-2"></i><strong>${TRANSLATIONS.verified}</strong> ${TRANSLATIONS.criminal_record_found} ${result.record.full_name}`, 
                            'success'
                        );
                        
                    } else {
                        // Criminal Record not found
                        recordVerified = false;
                        recordIdInput.classList.remove('is-valid');
                        recordIdInput.classList.add('is-invalid');
                        suspectNameInput.value = '';
                        suspectInfo.style.display = 'none';
                        submitBtn.disabled = true;
                        
                        showMessage(
                            `<i class="fas fa-exclamation-triangle me-2"></i><strong>${TRANSLATIONS.not_found}</strong> ${TRANSLATIONS.record_id_not_exist}`, 
                            'warning'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(
                        `<i class="fas fa-times-circle me-2"></i><strong>${TRANSLATIONS.error}</strong> ${TRANSLATIONS.failed_verify}`, 
                        'danger'
                    );
                })
                .finally(() => {
                    // Reset button state
                    verifyRecordBtn.disabled = false;
                    verifyRecordBtn.innerHTML = '<i class="fas fa-search me-1"></i>' + TRANSLATIONS.verify;
                });
            }
            
            // Generate case number
            generateCaseNumberBtn.addEventListener('click', function() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const time = String(now.getHours()).padStart(2, '0') + String(now.getMinutes()).padStart(2, '0');
                
                const caseNumber = `CASE-${year}${month}${day}-${time}`;
                caseNumberInput.value = caseNumber;
                caseNumberInput.classList.add('is-valid');
                
                checkFormValidity();
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!input.disabled) {
                        validateField(this);
                        checkFormValidity();
                    }
                });
                
                input.addEventListener('input', function() {
                    if (!input.disabled && this.classList.contains('is-invalid')) {
                        validateField(this);
                    }
                    checkFormValidity();
                });
            });
            
            // Check overall form validity
            function checkFormValidity() {
                const requiredFields = form.querySelectorAll('[required]');
                let allValid = recordVerified;
                
                requiredFields.forEach(field => {
                    if (!field.disabled && (!field.value.trim() || field.classList.contains('is-invalid'))) {
                        allValid = false;
                    }
                });
                
                submitBtn.disabled = !allValid;
            }
            
            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!recordVerified) {
                    showMessage(TRANSLATIONS.please_verify_record, 'warning');
                    return;
                }
                
                // Validate all fields
                let isValid = true;
                inputs.forEach(input => {
                    if (!input.disabled && !validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    showMessage(TRANSLATIONS.correct_errors, 'danger');
                    return;
                }
                
                // Show loading state
                showLoading(true);
                
                // Prepare form data as JSON
                const formData = {
                    record_id: recordIdInput.value.trim(),
                    case_number: caseNumberInput.value.trim(),
                    case_type: document.getElementById('case_type').value,
                    date_reported: dateReportedInput.value,
                    location: document.getElementById('location').value.trim(),
                    description: document.getElementById('description').value.trim()
                };
                
                // Submit via AJAX
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const successMessage = `
                            <div class="alert-custom alert-success" role="alert">
                                <button type="button" class="btn-close" onclick="this.parentElement.remove()">&times;</button>
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>${TRANSLATIONS.success}!</strong> ${result.message}
                                <br><small>${TRANSLATIONS.case_id}: #${result.case_id} | ${TRANSLATIONS.case_number}: ${result.case_number} | ${TRANSLATIONS.suspect}: ${result.suspect_name}</small>
                            </div>
                            <div class="success-actions">
                                <h6 class="mb-3"><i class="fas fa-arrow-right me-2"></i>${TRANSLATIONS.next_steps}</h6>
                                <p class="mb-3">${TRANSLATIONS.case_created_success}</p>
                                <a href="view_case.php?id=${result.case_id}" class="btn-success-custom">
                                    <i class="fas fa-eye me-2"></i>${TRANSLATIONS.view_case_file}
                                </a>
                                <a href="manage_cases.php" class="btn-success-custom">
                                    <i class="fas fa-list me-2"></i>${TRANSLATIONS.all_cases}
                                </a>
                                <a href="create_case_file.php" class="btn-success-custom">
                                    <i class="fas fa-plus me-2"></i>${TRANSLATIONS.create_another}
                                </a>
                            </div>
                        `;
                        
                        statusMessage.innerHTML = successMessage;
                        form.reset();
                        
                        // Reset states
                        recordVerified = false;
                        submitBtn.disabled = true;
                        suspectNameInput.value = '';
                        suspectInfo.style.display = 'none';
                        dateReportedInput.value = today;
                        
                        // Reset validation classes
                        inputs.forEach(input => {
                            input.classList.remove('is-valid', 'is-invalid');
                        });
                        
                        // Scroll to success message
                        statusMessage.scrollIntoView({ behavior: 'smooth' });
                        
                    } else {
                        showMessage(
                            `<i class="fas fa-exclamation-triangle me-2"></i><strong>${TRANSLATIONS.error}!</strong> ${result.message}`, 
                            'danger'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(
                        `<i class="fas fa-times-circle me-2"></i><strong>${TRANSLATIONS.network_error}</strong>`, 
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
                
                // Required field validation
                if (field.hasAttribute('required') && !value) {
                    isValid = false;
                }
                
                // Specific field validations
                switch (field.id) {
                    case 'record_id':
                        if (value && (!/^\d+$/.test(value) || parseInt(value) <= 0)) {
                            field.setCustomValidity(TRANSLATIONS.invalid_record_id);
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'case_number':
                        if (value && value.length < 3) {
                            field.setCustomValidity(TRANSLATIONS.case_number_min_length);
                            isValid = false;
                        } else {
                            field.setCustomValidity('');
                        }
                        break;
                        
                    case 'date_reported':
                        if (value) {
                            const reportDate = new Date(value);
                            const today = new Date();
                            
                            if (reportDate > today) {
                                field.setCustomValidity(TRANSLATIONS.date_future);
                                isValid = false;
                            } else {
                                field.setCustomValidity('');
                            }
                        }
                        break;
                        
                    case 'location':
                        if (value && value.length < 3) {
                            field.setCustomValidity(TRANSLATIONS.location_min_length);
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
                    submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>' + TRANSLATIONS.loading_creating;
                } else {
                    submitBtn.innerHTML = '<i class="fas fa-folder-plus me-2"></i>' + TRANSLATIONS.create_case_file_btn;
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
</body>
</html>