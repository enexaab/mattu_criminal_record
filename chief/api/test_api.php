<?php
// chief/api/test_api.php
session_start();
require_once '../../includes/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo json_encode([
        'success' => true,
        'message' => 'API is reachable',
        'table_check' => [
            'cases_exists' => $db->query("SHOW TABLES LIKE 'cases'")->rowCount() > 0,
            'case_files_exists' => $db->query("SHOW TABLES LIKE 'case_files'")->rowCount() > 0
        ],
        'data_sample' => "Test successful"
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>