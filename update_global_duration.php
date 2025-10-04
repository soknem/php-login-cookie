<?php
require 'auth.php';
if (!checkAuth()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_duration = (int)$_POST['duration'] ?? 2;
    if ($new_duration > 0) {
        global $pdo;
        // Update global duration in settings
        $stmt = $pdo->prepare("REPLACE INTO settings (`key`, `value`) VALUES ('otp_duration', ?)");
        $stmt->execute([$new_duration]);

        // Update expired_date for all users
        $new_expired_date_expr = "DATE_ADD(NOW(), INTERVAL $new_duration MINUTE)";
        $pdo->query("UPDATE users SET expired_date = $new_expired_date_expr WHERE token IS NOT NULL");
        $pdo->query("UPDATE users SET expired_date = NULL WHERE token IS NULL");

        header("Location: home.php?msg=Global OTP duration updated to $new_duration minutes for all users");
        exit;
    }
}
header("Location: home.php?msg=Invalid duration");
exit;
?>