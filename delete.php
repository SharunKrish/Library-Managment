<?php
/**
 * Delete Book Action Script (delete.php)
 *
 * Processes POST requests to delete a book record by its ID.
 * Follows security practices: POST-only execution, integer filtering, and PDO prepared statements.
 */

require_once 'db.php';

// Only allow HTTP POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 1. Retrieve and validate book ID
$id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;

if (!$id || $id < 1) {
    header("Location: index.php?status=error&msg=" . urlencode("Invalid book ID provided for deletion."));
    exit();
}

// 2. Execute deletion using PDO prepared statement
try {
    $sql = "DELETE FROM books WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        header("Location: index.php?status=success&msg=" . urlencode("Book (ID #{$id}) was deleted successfully."));
    } else {
        header("Location: index.php?status=error&msg=" . urlencode("No book found with ID #{$id}."));
    }
    exit();

} catch (PDOException $e) {
    header("Location: index.php?status=error&msg=" . urlencode("Database error: Could not delete book."));
    exit();
}
