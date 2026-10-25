<?php
/**
 * Database Connection File (db.php)
 *
 * Establishes a secure connection to the MySQL database using PHP Data Objects (PDO).
 * Follows best practices: PDO prepared statements, UTF-8 charset, and exception error handling.
 */

// Database configuration settings
$db_host = 'localhost';
$db_name = 'library_management';
$db_user = 'root';
$db_pass = ''; // Set your MySQL password here if configured
$charset = 'utf8mb4';

// Data Source Name (DSN)
$dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";

// PDO options for security, reliability, and ease of use
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on SQL errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Return associative arrays by default
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use genuine prepared statements (prevents SQL injection)
];

try {
    // Instantiate PDO connection
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Handle database connection failure securely without leaking credentials
    http_response_code(500);
    die("Database Connection Error: Could not connect to the database. Please ensure MySQL is running and database configuration in db.php is correct.");
}
