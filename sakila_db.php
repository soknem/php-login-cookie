<?php
$host = 'localhost';
$dbname = 'sakila';
$username = 'root';
$password = '';
$port = '3306';

try {
    $sakila_pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $sakila_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Sakila database connection failed: " . $e->getMessage());
}
?>