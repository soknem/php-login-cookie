<?php
require 'auth.php';
if (!checkAuth()) {
    header('Location: index.php');
    exit;
}
if (isset($_POST['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <form method="POST">
                <button type="submit" name="logout" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Logout</button>
            </form>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">About This App</h2>
            <p class="text-gray-700">A simple, secure PHP login system with:</p>
            <ul class="list-disc pl-6 text-gray-700">
                <li>Static username and password storage</li>
                <li>Remember Me via cookies</li>
                <li>Modern Tailwind CSS design</li>
            </ul>
        </div>
    </div>
</body>
</html>