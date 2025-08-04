<?php
require 'auth.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = register($username, $password);
        if ($result === true) {
            $success = 'Registration successful!';
            // No immediate redirect; handled by JavaScript
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
<body class="bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-2xl w-full max-w-md border border-white/20">
        <h2 class="text-3xl font-bold text-center mb-8 text-purple-900">Sign Up</h2>
        <?php if ($error): ?>
            <p class="text-red-400 bg-red-100/50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="text-green-400 bg-green-100/50 p-3 rounded-lg text-center mb-6" id="successMessage" style="display:none;"><?php echo htmlspecialchars($success); ?></p>
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
            <div>
                <label class="block text-purple-900 font-medium mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" class="w-full p-3 bg-white/20 border border-purple-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300" placeholder="Confirm password" required>
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 transition-all duration-300 transform hover:scale-105">Sign Up</button>
        </form>
        <p class="text-center text-purple-900 mt-6">Already have an account? <a href="index.php" class="text-purple-500 hover:underline">Sign in</a></p>
    </div>

    <script>
        <?php if ($success): ?>
            // Show toast and redirect after a short delay
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.textContent = '<?php echo addslashes($success); ?>';
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.className += ' show';
                setTimeout(() => {
                    window.location.href = 'index.php'; // Redirect after toast is visible
                }, 2000); // 2 seconds delay to show toast
            }, 100);
        <?php endif; ?>
    </script>
</body>
</html>