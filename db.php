<?php
/**
 * Database Connection File (db.php)
 *
 * Establishes a secure connection using PHP Data Objects (PDO).
 * Supports:
 *  1. MySQL (via environment variables or local default settings)
 *  2. Self-contained SQLite fallback (auto-initialized with tables & seed data for Render/cloud hosting)
 */

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = null;

// Check if MySQL connection credentials are provided
$db_host = getenv('DB_HOST') ?: (getenv('RENDER') ? '' : 'localhost');
$db_name = getenv('DB_DATABASE') ?: 'library_management';
$db_user = getenv('DB_USERNAME') ?: 'root';
$db_pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$charset = 'utf8mb4';

// 1. Attempt MySQL connection if a host is specified
if (!empty($db_host)) {
    try {
        $dsn = "mysql:host={$db_host};dbname={$db_name};charset={$charset}";
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    } catch (PDOException $e) {
        $pdo = null; // Fall through to self-contained SQLite
    }
}

// 2. Self-contained SQLite fallback (allows instant deployment on Render with zero dependencies)
if (!$pdo) {
    try {
        $dataDir = __DIR__ . '/data';
        if (!is_dir($dataDir)) {
            @mkdir($dataDir, 0777, true);
        }
        $sqliteFile = $dataDir . '/library_management.sqlite';
        $isNewDb = !file_exists($sqliteFile);

        $pdo = new PDO("sqlite:" . $sqliteFile, null, null, $options);

        // Auto-create schema and seed sample books on first launch
        if ($isNewDb) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS books (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(150) NOT NULL,
                author VARCHAR(100) NOT NULL,
                isbn VARCHAR(20) DEFAULT NULL,
                category VARCHAR(100) DEFAULT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            $seedStmt = $pdo->prepare("INSERT INTO books (title, author, isbn, category, quantity) VALUES (?, ?, ?, ?, ?)");
            $sampleBooks = [
                ['To Kill a Mockingbird', 'Harper Lee', '978-0061120084', 'Classic Fiction', 5],
                ['Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', '978-0132350884', 'Computer Science', 3],
                ['The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 'Classic Literature', 4],
                ['Design Patterns: Elements of Reusable Object-Oriented Software', 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides', '978-0201633610', 'Computer Science', 2]
            ];

            foreach ($sampleBooks as $book) {
                $seedStmt->execute($book);
            }
        }
    } catch (PDOException $e) {
        http_response_code(500);
        die("Database Connection Error: Could not initialize database.");
    }
}
