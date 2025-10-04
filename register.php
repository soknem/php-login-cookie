<?php
require 'auth.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } else {
        $result = register($username, $email, $password);
        if ($result === true) {
            $otp = generate_otp($username);
            if ($otp && send_otp_email($email, $otp)) {
                header("Location: otp.php?user=" . urlencode($username));
                exit;
            } else {
                $error = 'Failed to send OTP. Please try again.';
            }
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .toast {
            min-width: 250px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 6px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            top: -100px; /* Start above the panel */
            transform: translateX(-50%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: top 0.5s ease-in-out;
        }
        .toast.show {
            top: 20px; /* Show just above the panel */
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-600 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-400">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Sign Up</h2>
        <?php if ($error): ?>
            <p class="text-red-400 bg-red-100/50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-800 font-medium mb-2">Username</label>
                <input type="text" name="username" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-gray-800 font-medium mb-2">Email</label>
                <input type="email" name="email" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Enter email" required>
            </div>
            <div>
                <label class="block text-gray-800 font-medium mb-2">Password</label>
                <input type="password" name="password" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Enter password" required>
            </div>
            <div>
                <label class="block text-gray-800 font-medium mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Confirm password" required>
            </div>
            <button type="submit" class="w-full bg-gray-700 text-white p-3 rounded-lg hover:bg-gray-800 transition-all duration-300 transform hover:scale-105">Sign Up</button>
        </form>
        <p class="text-center text-gray-800 mt-6">Already have an account? <a href="index.php" class="text-blue-800 hover:underline">Sign in</a></p>
    </div>
</body>
</html>