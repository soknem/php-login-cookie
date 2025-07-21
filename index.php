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
    $remember = isset($_POST['remember']);
    if (login($username, $password, $remember)) {
        header('Location: home.php');
        exit;
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
<body class="bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl w-full max-w-md border border-white/20">
        <h2 class="text-3xl font-bold text-center mb-8 text-purple-900">Sign In</h2>
        <?php if ($error): ?>
            <p class="text-red-400 bg-red-100/50 p-3 rounded-lg text-center mb-6"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-purple-900 font-medium mb-2">Username</label>
                <input type="text" name="username" class="w-full p-3 bg-white/20 border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" placeholder="Enter username" required>
            </div>
            <div>
                <label class="block text-purple-900 font-medium mb-2">Password</label>
                <input type="password" name="password" class="w-full p-3 bg-white/20 border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" placeholder="Enter password" required>
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-purple-200 rounded">
                <label for="remember" class="ml-2 text-purple-900">Remember Me</label>
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 transition-all duration-300 transform hover:scale-105">Sign In</button>
        </form>
        <p class="text-center text-purple-900 mt-6">Don't have an account? <a href="#" class="text-purple-500 hover:underline">Sign up</a></p>
    </div>
</body>
</html>