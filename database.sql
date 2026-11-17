-- Library Book Management System
CREATE DATABASE IF NOT EXISTS library_management
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE library_management;

CREATE TABLE IF NOT EXISTS books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    total_copies INT UNSIGNED NOT NULL,
    available_copies INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_total_copies CHECK (total_copies >= 0),
    CONSTRAINT chk_available_copies CHECK (
        available_copies >= 0 AND available_copies <= total_copies
    )
);

INSERT IGNORE INTO books
(title, author, category, isbn, total_copies, available_copies)
VALUES
('The Alchemist', 'Paulo Coelho', 'Fiction', '9780061122415', 8, 5),
('Clean Code', 'Robert C. Martin', 'Technology', '9780132350884', 6, 3),
('Atomic Habits', 'James Clear', 'Self-help', '9780735211292', 10, 7),
('A Brief History of Time', 'Stephen Hawking', 'Science', '9780553380163', 5, 4),
('The Hobbit', 'J. R. R. Tolkien', 'Fantasy', '9780547928227', 7, 2),
('Database System Concepts', 'Abraham Silberschatz', 'Education', '9780073523323', 9, 6),
('Pride and Prejudice', 'Jane Austen', 'Classic', '9780141439518', 4, 4),
('The Psychology of Money', 'Morgan Housel', 'Finance', '9780857197689', 6, 1),
('Introduction to Algorithms', 'Thomas H. Cormen', 'Technology', '9780262046305', 5, 2),
('Animal Farm', 'George Orwell', 'Classic', '9780451526342', 8, 8);
