<?php
// includes/database.php

class Database {
    private $host = 'localhost';
    private $dbName = 'mattu_crm_db';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function __construct() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbName . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (\PDOException $e) {
            // Log error in production, but die for now during development
            die("Database Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create global $db variable for your forms to use
$database = new Database();
$db = $database->getConnection();
?>