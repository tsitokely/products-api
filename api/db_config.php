<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'product_db');
define('DB_USER', 'root');  // Change to your DB username
define('DB_PASS', '');      // Change to your DB password

// Connect to database
function getDBConnection() {
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
        exit;
    }
}