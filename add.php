<?php
/**
 * Add Book Action Script (add.php)
 *
 * Processes POST requests to insert a new book record into the database.
 * Implements server-side validation and PDO prepared statements to prevent SQL injection.
 */

require_once 'db.php';

// Only allow HTTP POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit();
}

// 1. Sanitize and retrieve form inputs
$title    = isset($_POST['title']) ? trim($_POST['title']) : '';
$author   = isset($_POST['author']) ? trim($_POST['author']) : '';
$isbn     = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '1';

$errors = [];

// 2. Server-side validation
if ($title === '') {
    $errors[] = "Book title is required.";
} elseif (mb_strlen($title) > 150) {
    $errors[] = "Book title cannot exceed 150 characters.";
}

if ($author === '') {
    $errors[] = "Author name is required.";
} elseif (mb_strlen($author) > 100) {
    $errors[] = "Author name cannot exceed 100 characters.";
}

if ($isbn !== '' && mb_strlen($isbn) > 20) {
    $errors[] = "ISBN cannot exceed 20 characters.";
}

if ($category !== '' && mb_strlen($category) > 100) {
    $errors[] = "Category cannot exceed 100 characters.";
}

if (!filter_var($quantity, FILTER_VALIDATE_INT) || (int)$quantity < 1) {
    $errors[] = "Quantity must be a valid positive integer (minimum 1).";
} else {
    $quantity = (int)$quantity;
}

// If validation errors exist, redirect back with error message
if (!empty($errors)) {
    $errorMessage = implode(' ', $errors);
    header("Location: index.php?status=error&msg=" . urlencode($errorMessage));
    exit();
}

// 3. Insert record using PDO prepared statement
try {
    $sql = "INSERT INTO books (title, author, isbn, category, quantity) 
            VALUES (:title, :author, :isbn, :category, :quantity)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title'    => $title,
        ':author'   => $author,
        ':isbn'     => $isbn !== '' ? $isbn : null,
        ':category' => $category !== '' ? $category : null,
        ':quantity' => $quantity
    ]);

    // Redirect with success message to prevent duplicate submissions (Post-Redirect-Get pattern)
    header("Location: index.php?status=success&msg=" . urlencode("Book '{$title}' added successfully!"));
    exit();

} catch (PDOException $e) {
    // Redirect with database error message
    header("Location: index.php?status=error&msg=" . urlencode("Database error: Could not save the book."));
    exit();
}
