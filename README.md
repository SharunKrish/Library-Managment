# Library Book Management System

A clean, beginner-friendly, and responsive **Library Book Management System** built with **HTML5, CSS3, JavaScript, Core PHP (no frameworks), and MySQL**.

Designed with a warm **White & Brown library color theme** and complete CRUD operations.

---

## 📚 Project Structure

```
library-management/
├── index.php       # Dashboard: Add book form + dynamic book catalog table
├── add.php         # Server-side action to insert a new book record
├── edit.php        # Form view & action to retrieve and update book details
├── delete.php      # Server-side action to safely delete a book record via POST
├── db.php          # Database connection using PDO with prepared statements
├── style.css       # Clean styling adhering to the white & brown palette
├── script.js       # Client-side form validation and delete confirmation
├── database.sql    # Database schema creation script with sample data
└── README.md       # Project setup and documentation
```

---

## 🎨 Color Palette (White & Brown Library Theme)

- **Page Background:** White (`#FFFFFF`)
- **Card Background:** Light Cream (`#FAF6F2`)
- **Navigation Bar & Table Headers:** Dark Brown (`#5C4033`)
- **Primary Action Buttons (Add, Update):** Medium Brown (`#8B5E3C`), Hover (`#6F4A2E`)
- **Edit Button:** Light Brown (`#A47551`)
- **Delete Button:** Deep Brown (`#4E342E`)
- **Cancel Button:** Tan (`#D7C4B3`) with Dark Brown Text (`#3E2C23`)
- **Borders & Dividers:** Soft Beige (`#E6D5C3`)
- **Text:** Dark Charcoal / Dark Brown (`#3E2C23`)
- **Alert / Feedback Messages:** Light Tan (`#F3EFEA` / `#F5EBE1`) with Dark Brown Border (`#5C4033`)

---

## ⚙️ Features & CRUD Operations

1. **Add Book (Create):**
   - Fields: Title, Author, ISBN, Category, Quantity (Copies).
   - Validated both client-side (`script.js`) and server-side (`add.php`).
   - Prepared statement prevents SQL injection.
   - Post-Redirect-Get pattern prevents accidental duplicate submissions.

2. **View Books (Read):**
   - Displays all books in a responsive table.
   - Shows ID, Title, Author, ISBN, Category, Quantity, and Actions.
   - Safe against XSS using `htmlspecialchars()`.
   - Empty state fallback when no records are present.

3. **Edit Book (Update):**
   - Pre-populates selected book's current values.
   - Validates changes before updating database.
   - Includes "Update Book" and "Cancel" buttons.

4. **Delete Book (Delete):**
   - Interactive JavaScript `confirm()` dialog prevents accidental deletion.
   - Uses **HTTP POST** (inside a secure form) instead of vulnerable GET links.
   - Safely removes record from database.

---

## 🚀 Setup & Installation Instructions

### 1. Database Setup
1. Open your MySQL client (e.g., **phpMyAdmin**, **MySQL Workbench**, or the terminal CLI).
2. Import or run the SQL file:
   ```bash
   mysql -u root -p < database.sql
   ```
   *This creates the database `library_management` and the `books` table with initial starter records.*

### 2. Configure Database Connection
Open `db.php` and verify the database credentials:
```php
$db_host = 'localhost';
$db_name = 'library_management';
$db_user = 'root';
$db_pass = ''; // Enter your MySQL password if set
```

### 3. Deploy to Web Server
- **XAMPP / WAMP / LAMP:** Copy the `library-management` folder to your web root directory:
  - XAMPP: `C:/xampp/htdocs/library-management`
  - Ubuntu/Apache: `/var/www/html/library-management`
- **PHP Built-in Server (Development):**
  ```bash
  cd library-management
  php -S localhost:8000
  ```

### 4. Access the Application
Open your browser and navigate to:
```
http://localhost/library-management/
```
*(or `http://localhost:8000` if using the PHP built-in server)*

---

## 🔒 Security Best Practices Implemented

- **Prepared Statements (PDO):** Native parameterized SQL queries protect against SQL Injection.
- **XSS Prevention:** All output rendered to HTML is escaped with `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.
- **POST-Only Mutations:** Create, Update, and Delete actions exclusively accept `POST` requests.
- **Data Sanitization & Validation:** Inputs are trimmed, verified against character lengths, and quantities are validated as positive integers.
- **Graceful Error Handling:** Database exceptions are caught cleanly without exposing database credentials or server environment details to the client.
