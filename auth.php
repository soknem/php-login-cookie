<?php
session_start();

$host = 'localhost'; 
$dbname = 'test';
$username = 'root';
$password = '';
$port = '3306'; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function checkAuth() {
    global $pdo;
    if (isset($_SESSION['username'])) {
        return true;
    }
    if (isset($_COOKIE['remember'])) {
        $token = $_COOKIE['remember'];
        $stmt = $pdo->prepare("SELECT username FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['username'] = $user['username'];
            return true;
        }
    }
    return false;
}

function login($username, $password, $remember) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        if ($remember) {
            $token = bin2hex(random_bytes(16));
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE username = ?");
            $stmt->execute([$token, $username]);
            setcookie('remember', $token, time() + (30 * 24 * 60 * 60), "/");
        }
        return true;
    }
    return false;
}

function logout() {
    global $pdo;
    if (isset($_SESSION['username'])) {
        $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE username = ?");
        $stmt->execute([$_SESSION['username']]);
    }
    session_destroy();
    setcookie('remember', '', time() - 3600, "/");
}

function register($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        return "Username already exists";
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    try {
        $stmt->execute([$username, $hashedPassword]);
        return true;
    } catch (PDOException $e) {
        return "Registration failed: " . $e->getMessage();
    }
}
?>