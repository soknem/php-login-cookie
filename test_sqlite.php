<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Test PDO SQLite
try {
    // Connect to SQLite in memory (you can also use a file like 'sqlite:mydb.sqlite')
    $db = new PDO('sqlite::memory:');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create a table
    $db->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL
    )");

    // Insert data
    $stmt = $db->prepare("INSERT INTO users (name) VALUES (:name)");
    $stmt->execute([':name' => 'Alice']);
    $stmt->execute([':name' => 'Bob']);

    // Select data
    $result = $db->query("SELECT * FROM users");
    foreach ($result as $row) {
        echo "ID: " . $row['id'] . " - Name: " . $row['name'] . "<br>";
    }

    echo "<br>✅ SQLite PDO is working correctly!";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
