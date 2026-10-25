<?php
/**
 * Main Library Dashboard (index.php)
 *
 * Serves as the primary entry point for the Library Book Management System.
 * Displays:
 *  - Form to create/add new books.
 *  - Dynamic table listing all current books in the MySQL database.
 *  - Edit & Delete action buttons for each record.
 *  - Flash notifications for operations (Success/Error).
 */

require_once 'db.php';

// Retrieve feedback notification parameters if present
$status  = isset($_GET['status']) ? trim($_GET['status']) : '';
$message = isset($_GET['msg']) ? trim($_GET['msg']) : '';

// Fetch all books from the database ordered by newest first
$books = [];
$db_error = null;

try {
    $stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
    $books = $stmt->fetchAll();
} catch (PDOException $e) {
    $db_error = "Could not fetch book records. Please ensure the database and table are properly created.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Book Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navigation Header -->
    <header class="navbar">
        <div class="container" style="margin: 0 auto; padding: 0;">
            <h1>Library Book Management System</h1>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="container">

        <!-- System Alerts (Success or Error Feedback) -->
        <?php if (!empty($message)): ?>
            <div class="alert <?= ($status === 'success') ? 'alert-success' : 'alert-error'; ?>">
                <strong><?= ($status === 'success') ? 'Success:' : 'Notice:'; ?></strong> 
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($db_error): ?>
            <div class="alert alert-error">
                <strong>Database Notice:</strong> <?= htmlspecialchars($db_error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Add Book Section -->
        <section class="card">
            <h2 class="card-title">Add New Book</h2>
            
            <form action="add.php" method="POST" id="bookForm">
                <div class="form-grid">
                    <!-- Title Input -->
                    <div class="form-group">
                        <label for="title">Book Title <span class="required">*</span></label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               class="form-control" 
                               maxlength="150" 
                               placeholder="Enter book title" 
                               required>
                    </div>

                    <!-- Author Input -->
                    <div class="form-group">
                        <label for="author">Author <span class="required">*</span></label>
                        <input type="text" 
                               id="author" 
                               name="author" 
                               class="form-control" 
                               maxlength="100" 
                               placeholder="Enter author name" 
                               required>
                    </div>

                    <!-- ISBN Input -->
                    <div class="form-group">
                        <label for="isbn">ISBN</label>
                        <input type="text" 
                               id="isbn" 
                               name="isbn" 
                               class="form-control" 
                               maxlength="20" 
                               placeholder="e.g. 978-0061120084">
                    </div>

                    <!-- Category Input -->
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" 
                               id="category" 
                               name="category" 
                               class="form-control" 
                               maxlength="100" 
                               placeholder="e.g. Fiction, History, Science">
                    </div>

                    <!-- Quantity Input -->
                    <div class="form-group">
                        <label for="quantity">Quantity <span class="required">*</span></label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               class="form-control" 
                               min="1" 
                               value="1" 
                               required>
                    </div>
                </div>

                <!-- Form Action Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </div>
            </form>
        </section>

        <!-- Book List Section -->
        <section class="card">
            <h2 class="card-title">Book Inventory (<?= count($books); ?> Total)</h2>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th style="width: 90px; text-align: center;">Quantity</th>
                            <th style="width: 150px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($books)): ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$book['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><strong><?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                    <td><?= htmlspecialchars($book['author'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($book['isbn'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?= htmlspecialchars($book['category'] ?: 'Uncategorized', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="text-align: center; font-weight: 600;"><?= htmlspecialchars((string)$book['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td style="text-align: center;">
                                        <div class="actions-cell" style="justify-content: center;">
                                            <!-- Edit Action -->
                                            <a href="edit.php?id=<?= urlencode((string)$book['id']); ?>" 
                                               class="btn btn-edit" 
                                               title="Edit this book">
                                                Edit
                                            </a>

                                            <!-- Delete Action (POST Method for Security) -->
                                            <form action="delete.php" method="POST" class="delete-form">
                                                <input type="hidden" name="id" value="<?= htmlspecialchars((string)$book['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <button type="submit" class="btn btn-delete" title="Delete this book">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-table">
                                    No books currently available in the catalog. Add a new book using the form above!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <!-- Client-side Validation & Confirmation Scripts -->
    <script src="script.js"></script>
</body>
</html>
