<?php
require 'auth.php';
if (checkAuth()) {
    header('Location: home.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $user = check_credentials($username, $password);
    if ($user) {
        $otp = generate_otp($username);
        if ($otp && send_otp_email($user['email'], $otp)) {
            header("Location: otp.php?user=" . urlencode($username));
            exit;
        } else {
            $error = 'Failed to send OTP. Please try again.';
        }
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-600 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-400">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Sign In</h2>
        <?php if ($error): ?>
            <p class="text-red-400 bg-red-100/50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-800 font-medium mb-2">Username</label>
                <input type="text" name="username" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-gray-800 font-medium mb-2">Password</label>
                <input type="password" name="password" class="w-full p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300" placeholder="Enter password" required>
            </div>
            <button type="submit" class="w-full bg-gray-700 text-white p-3 rounded-lg hover:bg-blue-800 transition-all duration-300 transform hover:scale-105">Sign In</button>
        </form>
        <p class="text-center text-gray-800 mt-6">Don't have an account? <a href="register.php" class="text-blue-800 hover:underline">Sign up</a></p>
    </div>
</body>
</html>