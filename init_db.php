<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
try {
    $db = new PDO('sqlite:D:/SETEC/SU5/Semester5/WDB/PhpFirstProject/login-cookie/database.db');
    $db->exec("CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT)");
    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    $db->exec("INSERT OR IGNORE INTO users (username, password) VALUES ('testuser', '$hashedPassword')");
    echo "Database created!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>