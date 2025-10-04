<?php
require 'auth.php';

$username = $_GET['user'] ?? '';
if (!$username) {
    header('Location: index.php');
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT email FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    $otp = generate_otp($username);
    if ($otp && send_otp_email($user['email'], $otp)) {
        header("Location: otp.php?user=" . urlencode($username));
        exit;
    }
}
header("Location: otp.php?user=" . urlencode($username));
exit;
?>