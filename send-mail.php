<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';


$otp = sprintf("%06d", mt_rand(100000, 999999));


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
                            <h2 style="color: #333333; font-size: 20px; margin: 0 0 20px;">Verify Your Login</h2>
                            <p style="color: #666666; font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                Hello! You’ve requested to log in to <strong>SU5 Website</strong>. Please use the following One-Time Password (OTP) to complete your login:
                            </p>
                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <span style="font-size: 32px; font-weight: bold; color: #4a90e2; letter-spacing: 5px;">' . $otp . '</span>
                            </div>
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0 0 20px;">
                                This OTP is valid for <strong>2 minutes</strong>. Do not share it with anyone.
                            </p>
                            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin: 0;">
                                If you didn’t request this, please ignore this email or contact our support team.
                            </p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999999;">
                            <p style="margin: 0;">&copy; ' . date('Y') . ' Your Website Name. All rights reserved.</p>
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
    $mail->Username = 'sokname56@gmail.com';
    $mail->Password = 'lspk mhrx gyhq csib';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('sokname56@gmail.com', 'SU5 Website');
    $mail->addAddress('povsoknem@gmails.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code for Login';
    $mail->Body = $emailBody;
    $mail->AltBody = "Your OTP Code for Your Website Name is: $otp\nThis OTP is valid for 10 minutes. Do not share it with anyone.\nIf you didn’t request this, please ignore this email or contact support.";

    // Send the email
    $mail->send();
    echo 'OTP email has been sent successfully';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>