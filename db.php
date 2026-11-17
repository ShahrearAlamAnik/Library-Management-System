<?php
// Local defaults are for a standard XAMPP setup. Set environment variables
// on your hosting provider for the live website; never commit real passwords.
$host = getenv("DB_HOST") ?: "localhost";
$dbname = getenv("DB_NAME") ?: "library_management";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";
$port = getenv("DB_PORT") ?: "3306";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Keep internal connection details out of public error messages.
    http_response_code(500);
    exit("Database connection failed. Check the hosting database settings.");
}
?>
