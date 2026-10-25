<?php
/**
 * Edit Book View & Action Script (edit.php)
 *
 * Displays a pre-populated form with the selected book's current details and
 * processes the POST request to update the book record in the MySQL database.
 */

require_once 'db.php';

// Variable initialization
$errors = [];
$book = null;

// Determine if this is a form submission (POST) or an initial load (GET)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Process Update Submission
    $id       = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;
    $title    = isset($_POST['title']) ? trim($_POST['title']) : '';
    $author   = isset($_POST['author']) ? trim($_POST['author']) : '';
    $isbn     = isset($_POST['isbn']) ? trim($_POST['isbn']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '1';

    // Validate ID
    if (!$id || $id < 1) {
        $errors[] = "Invalid book ID.";
    }

    // Validate fields
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

    // If no errors, perform database update
    if (empty($errors)) {
        try {
            $sql = "UPDATE books 
                    SET title = :title, 
                        author = :author, 
                        isbn = :isbn, 
                        category = :category, 
                        quantity = :quantity 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'    => $title,
                ':author'   => $author,
                ':isbn'     => $isbn !== '' ? $isbn : null,
                ':category' => $category !== '' ? $category : null,
                ':quantity' => $quantity,
                ':id'       => $id
            ]);

            // Redirect back to catalog with confirmation
            header("Location: index.php?status=success&msg=" . urlencode("Book '{$title}' updated successfully!"));
            exit();

        } catch (PDOException $e) {
            $errors[] = "Database error: Could not update the book.";
        }
    }

    // Reconstruct $book array to keep form fields populated after a validation error
    $book = [
        'id'       => $id,
        'title'    => $title,
        'author'   => $author,
        'isbn'     => $isbn,
        'category' => $category,
        'quantity' => $quantity
    ];

} else {
    // 2. Initial Page Load (GET) - Fetch existing book by ID
    $id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;

    if (!$id || $id < 1) {
        header("Location: index.php?status=error&msg=" . urlencode("Invalid book ID."));
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $book = $stmt->fetch();

        if (!$book) {
            header("Location: index.php?status=error&msg=" . urlencode("Book not found."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?status=error&msg=" . urlencode("Database error: Could not retrieve book details."));
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Library Book Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar">
        <div class="container" style="margin: 0 auto; padding: 0;">
            <h1>Library Book Management System</h1>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container">

        <!-- Error Notification (if server validation fails) -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Please correct the following:</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Edit Book Card Form -->
        <section class="card">
            <h2 class="card-title">Edit Book (ID #<?= htmlspecialchars((string)($book['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>)</h2>
            
            <form action="edit.php" method="POST" id="bookForm">
                <!-- Hidden input for book primary key ID -->
                <input type="hidden" name="id" value="<?= htmlspecialchars((string)($book['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-grid">
                    <!-- Title Field -->
                    <div class="form-group">
                        <label for="title">Book Title <span class="required">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control" 
                               maxlength="150" 
                               value="<?= htmlspecialchars($book['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                               required>
                    </div>

                    <!-- Author Field -->
                    <div class="form-group">
                        <label for="author">Author <span class="required">*</span></label>
                        <input type="text" 
                               id="author" 
                               name="author" 
                               class="form-control" 
                               maxlength="100" 
                               value="<?= htmlspecialchars($book['author'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                               required>
                    </div>

                    <!-- ISBN Field -->
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" 
                               id="isbn" 
                               name="isbn" 
                               class="form-control" 
                               maxlength="20" 
                               placeholder="e.g. 978-0132350884" 
                               value="<?= htmlspecialchars($book['isbn'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <!-- Category Field -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               class="form-control" 
                               maxlength="100" 
                               placeholder="e.g. Fiction, Science" 
                               value="<?= htmlspecialchars($book['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>

                    <!-- Quantity Field -->
                    <div class="form-group">
                        <label for="quantity">Quantity (Copies) <span class="required">*</span></label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               class="form-control" 
                               min="1" 
                               value="<?= htmlspecialchars((string)($book['quantity'] ?? '1'), ENT_QUOTES, 'UTF-8'); ?>" 
                               required>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <a href="index.php" class="btn btn-cancel">Cancel</a>
                </div>
            </form>
        </section>

    </div>

    <!-- Client-side JavaScript Validation -->
    <script src="script.js"></script>
</body>
</html>
