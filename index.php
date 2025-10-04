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
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-500 to-yellow-600 flex">
    <div class="flex w-full">
        <!-- Form Section -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div class="bg-white bg-opacity-90 rounded-xl p-8 w-full max-w-md border border-yellow-600 shadow-lg">
                <h2 class="text-3xl font-bold text-center mb-8 text-black">Sign In</h2>
                <?php if ($error): ?>
                    <p class="text-red-500 bg-red-100 bg-opacity-50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-black font-medium mb-2">Username</label>
                        <input type="text" name="username" class="w-full p-3 bg-white bg-opacity-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black transition duration-300" placeholder="Enter username" required>
                    </div>
                    <div>
                        <label class="block text-black font-medium mb-2">Password</label>
                        <input type="password" name="password" class="w-full p-3 bg-white bg-opacity-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black transition duration-300" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="w-full bg-black text-white p-3 rounded-lg hover:bg-gray-800 transition duration-300 transform hover:scale-105">Sign In</button>
                </form>
                <p class="text-center text-black mt-6">Don't have an account? <a href="register.php" class="text-black underline hover:text-gray-700">Sign up</a></p>
            </div>
        </div>
        <!-- Image Section -->
        <div class="hidden md:block w-1/2 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');"></div>
    </div>
</body>
</html>