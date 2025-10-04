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

require 'vendor/autoload.php'; // For PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function get_global_duration() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = 'otp_duration'");
    $stmt->execute();
    return (int)$stmt->fetchColumn() ?: 2; // Fallback to 2 if not found
}

function checkAuth() {
    return isset($_SESSION['username']);
}

function check_credentials($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function logout() {
    session_destroy();
}

function register($username, $email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetchColumn() > 0) {
        return "Username or email already exists";
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $hashedPassword]);
        return true;
    } catch (PDOException $e) {
        return "Registration failed: " . $e->getMessage();
    }
}

function generate_otp($username) {
    global $pdo;
    $duration = get_global_duration();
    $otp = sprintf("%06d", mt_rand(0, 999999));
    $expired_date = date('Y-m-d H:i:s', time() + ($duration * 60));
    $stmt = $pdo->prepare("UPDATE users SET token = ?, expired_date = ? WHERE username = ?");
    $stmt->execute([$otp, $expired_date, $username]);
    return $otp;
}

function send_otp_email($email, $otp, $websiteName = 'SU5 Website') {
    $duration = get_global_duration();
    // HTML email template
    $emailBody = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Your OTP Code</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Arial, sans-serif; background-color: #f4f4f4;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f4; padding: 20px;">
            <tr>
                <td align="center">
                    <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        <!-- Header -->
                        <tr>
                            <td style="background-color: #4a90e2; padding: 20px; text-align: center;">
                                <h1 style="color: #ffffff; font-size: 24px; margin: 0;">Your OTP Code</h1>
                            </td>
                        </tr>
                        <!-- Body -->
                        <tr>
                            <td style="padding: 30px; text-align: center;">
                                <h2 style="color: #333333; font-size: 20px; margin: 0 0 20px;">Verify Your Action</h2>
                                <p style="color: #666666; font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                    Hello! Please use the following One-Time Password (OTP) to complete your action:
                                </p>
                                <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                    <span style="font-size: 32px; font-weight: bold; color: #4a90e2; letter-spacing: 5px;">' . $otp . '</span>
                                </div>
                                <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0 0 20px;">
                                    This OTP is valid for ' . $duration . ' minutes. Do not share it with anyone.
                                </p>
                                <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0;">
                                    If you didn’t request this, please ignore this email or contact our support team.
                                </p>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999999;">
                                <p style="margin: 0;">&copy; ' . date('Y') . ' ' . $websiteName . '. All rights reserved.</p>
                                <p style="margin: 5px 0 0;">
                                    <a href="https://yourwebsite.com" style="color: #4a90e2; text-decoration: none;">Visit our website</a> | 
                                    <a href="mailto:support@yourwebsite.com" style="color: #4a90e2; text-decoration: none;">Contact Support</a>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    $mail = new PHPMailer(true);
    try {
        // Server settings for Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sokname56@gmail.com'; // Replace with your Gmail address
        $mail->Password = 'lspk mhrx gyhq csib'; // Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sokname56@gmail.com', $websiteName);
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = $emailBody;
        $mail->AltBody = "Your OTP Code is: $otp\nThis OTP is valid for " . $duration . " minutes. Do not share it with anyone.\nIf you didn’t request this, please ignore this email or contact support.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function verify_otp($username, $otpInput) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT token, expired_date FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['token'] === $otpInput && strtotime($user['expired_date']) > time()) {
        $stmt = $pdo->prepare("UPDATE users SET token = NULL, expired_date = NULL WHERE username = ?");
        $stmt->execute([$username]);
        return true;
    }
    return false;
}
?>