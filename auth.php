<?php
session_start();

function checkAuth() {
    if (isset($_SESSION['username'])) {
        return true;
    }
    if (isset($_COOKIE['remember'])) {
        $token = $_COOKIE['remember'];
        
        $staticToken = 'static_token_1234567890';
        if ($token === $staticToken) {
            $_SESSION['username'] = 'soknem';
            return true;
        }
    }
    return false;
}

function login($username, $password, $remember) {
    
    $staticUsername = 'soknem';
    $staticPassword = '123';
    $staticToken = 'static_token_1234567890';
    
    if ($username === $staticUsername && $password === $staticPassword) {
        $_SESSION['username'] = $username;
        if ($remember) {
            setcookie('remember', $staticToken, time() + (30 * 24 * 60 * 60), "/");
        }
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    setcookie('remember', '', time() - 3600, "/");
}
?>