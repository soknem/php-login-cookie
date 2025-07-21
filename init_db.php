<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
try {
    // Use absolute path for database
    $dbPath = __DIR__ . '/database.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT)");
    // Insert test user
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    $db->exec("INSERT OR IGNORE INTO users (username, password) VALUES ('testuser', '$hashedPassword')");
    echo "Database created and test user inserted!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>