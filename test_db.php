<?php
// Database configuration
$host = 'localhost'; // Use IP to force TCP connection
$port = '3308'; // Port mapped in Docker Compose
$dbname = 'login_db';
$username = 'root';
$password = 'rootpassword';

// Set content type for browser output
if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html><body><pre>';
}

// Attempt to connect to the database
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test query to verify database access
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Connection successful!\n";
    echo "Database: $dbname\n";
    echo "Tables found: " . (count($tables) > 0 ? implode(', ', $tables) : 'None') . "\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}

// Close HTML tags for browser output
if (PHP_SAPI !== 'cli') {
    echo '</pre></body></html>';
}
?>