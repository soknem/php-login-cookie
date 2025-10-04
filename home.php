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

global $pdo;
$stmt = $pdo->query("SELECT id, username, email FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex flex-col min-h-screen bg-gradient-to-br from-yellow-500 to-yellow-600">
    <header class="bg-white bg-opacity-10 border-b border-gray-300 sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-black">Dashboard</h1>
            <form method="POST">
                <button type="submit" name="logout" class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition duration-300 transform hover:scale-105">Sign Out</button>
            </form>
        </div>
    </header>
    <main class="container mx-auto px-6 py-8 flex-grow">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($users as $user): ?>
                <div class="bg-white bg-opacity-95 rounded-xl p-6 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <h3 class="text-lg font-semibold text-black">User ID: <?php echo htmlspecialchars($user['id']); ?></h3>
                    <p class="text-black mt-2"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p class="text-black mt-1"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <footer class="bg-white bg-opacity-10 border-t border-gray-300 py-4">
        <div class="container mx-auto px-6 text-center text-black">
            <p>&copy; 2025 Your App. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>