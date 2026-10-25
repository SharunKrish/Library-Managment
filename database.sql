-- ==============================================================
-- Database Creation Script for Library Book Management System
-- Database Name: library_management
-- Table Name: books
-- ==============================================================

-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `library_management` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Select the database for subsequent operations
USE `library_management`;

-- Create the books table
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `author` VARCHAR(100) NOT NULL,
    `isbn` VARCHAR(20) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional sample starter records for instant testing
INSERT INTO `books` (`title`, `author`, `isbn`, `category`, `quantity`) VALUES
('To Kill a Mockingbird', 'Harper Lee', '978-0061120084', 'Classic Fiction', 5),
('Clean Code: A Handbook of Agile Software Craftsmanship', 'Robert C. Martin', '978-0132350884', 'Computer Science', 3),
('The Great Gatsby', 'F. Scott Fitzgerald', '978-0743273565', 'Classic Literature', 4),
('Design Patterns: Elements of Reusable Object-Oriented Software', 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides', '978-0201633610', 'Computer Science', 2);
