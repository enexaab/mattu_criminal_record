<?php
// api/search_suggestions.php
// API endpoint for real-time search suggestions. Queries the database for matching criminal records and cases.
// Returns JSON for AJAX consumption. Only accessible to authenticated clerks.
// Limits results to prevent overload; uses LIKE for fuzzy matching.

// Start session and include dependencies
session_start();
require_once '../includes/auth.php';     // Auth check
require_once '../includes/database.php'; // DB connection

// Role enforcement: Only clerks can use this API
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
    
    // Search criminal records (table: criminal_records) - real-time DB query
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
        AND status != 'deleted'  -- Exclude soft-deleted records
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
    
    // Search cases (table: cases) - real-time DB query
    $stmt = $db->prepare("
        SELECT 
            c.id,
            c.case_number,
            c.title,
            'case' as type
        FROM cases c
        WHERE (c.case_number LIKE ? OR c.title LIKE ?)
        AND c.status != 'deleted'  -- Exclude soft-deleted
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
    
    // Limit total suggestions to 8 for UI performance
    echo json_encode([
        'success' => true,
        'suggestions' => array_slice($suggestions, 0, 8)
    ]);
    
} catch (Exception $e) {
    error_log("Search suggestions error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Search error']);
}
?>