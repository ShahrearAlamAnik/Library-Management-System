# Library Book Management System

Add, view, search, update, and delete book records. Displays title, copy, available-copy, and average-copy summaries.

## Technology
- HTML and CSS
- PHP 8+
- MySQL
- PDO prepared statements

## Features
- Add, list, search, edit, and delete book records.
- Search by title, author, category, or ISBN.
- Live summaries using COUNT, SUM, and AVG.
- Input validation and prepared SQL statements.

## Run locally with XAMPP
1. Install XAMPP and start Apache and MySQL (or use your existing MySQL Server).
2. Copy this project folder into `C:\xampp\htdocs\`.
3. Open phpMyAdmin or MySQL Workbench and import/execute `database.sql`.
   The script creates the `library_management` database and its sample records.
4. The default `db.php` settings work with a local MySQL account named `root`
   with an empty password. If your local MySQL account differs, set the
   `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, and optionally `DB_PORT`
   environment variables, or adjust local settings in `db.php`.
5. Visit `http://localhost/Library_Book_Management/` in your browser.

## Deploy to PHP/MySQL hosting
GitHub stores the source code; GitHub Pages does not execute PHP or connect
this application to MySQL. Deploy the PHP files to a PHP-capable host and
create a MySQL database there. Import `database.sql` (if the host requires
it, remove the `CREATE DATABASE` and `USE` statements and import into the
provided database). Configure these environment variables in the host's
control panel:

- `DB_HOST` — database hostname supplied by the host
- `DB_PORT` — database port (usually 3306)
- `DB_NAME` — database name supplied by the host
- `DB_USER` — database username supplied by the host
- `DB_PASS` — database password supplied by the host

Do not publish real database credentials in GitHub. Do not upload `.env`
files, passwords, or private hosting details.

## Repository contents
- `index.php` — application interface and CRUD operations
- `db.php` — database connection using environment variables
- `database.sql` — schema and sample data
- `Project_Report.pdf` — project report
