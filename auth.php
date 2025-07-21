<?php
session_start();

function getDB() {
    // Use absolute path to ensure database accessibility
    $dbPath = __DIR__ . '/database.db';
    return new PDO('sqlite:' . $dbPath);
}

function checkAuth() {
    if (isset($_SESSION['username'])) {
        return true;
    }
    if (isset($_COOKIE['remember'])) {
        $token = $_COOKIE['remember'];
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT username FROM users WHERE password = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch();
            if ($user) {
                $_SESSION['username'] = $user['username'];
                return true;
            }
        } catch (PDOException $e) {
            // Log error (in production, use proper logging)
            error_log("Database error in checkAuth: " . $e->getMessage());
        }
    }
    return false;
}

function login($username, $password, $remember) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            if ($remember) {
                setcookie('remember', $user['password'], time() + (30 * 24 * 60 * 60), "/");
            }
            return true;
        }
    } catch (PDOException $e) {
        // Log error (in production, use proper logging)
        error_log("Database error in login: " . $e->getMessage());
    }
    return false;
}

function logout() {
    session_destroy();
    setcookie('remember', '', time() - 3600, "/");
}
?>