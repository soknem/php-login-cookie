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

// Fetch all users from the database
global $pdo;
$stmt = $pdo->query("SELECT username, password, remember_token FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-600 min-h-screen font-['Inter']">
    <header class="bg-white/10  backdrop-blur-sm border-b border-gray-400 sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-red-800">Dashboard</h1>
            <form method="POST">
                <button type="submit" name="logout" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-105">Sign Out</button>
            </form>
        </div>
    </header>
    <main class="container mx-auto px-6 py-8">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white/10 backdrop-blur-sm rounded-xl shadow-2xl border border-gray-400">
                <thead>
                    <tr class="bg-gray-700 text-white">
                        <th class="py-3 px-4 text-left">Username</th>
                        <th class="py-3 px-4 text-left">Password (Hashed)</th>
                        <th class="py-3 px-4 text-left">Remember Token</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-b border-gray-400 hover:bg-gray-100/10">
                            <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($user['password']); ?></td>
                            <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($user['remember_token'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <footer class="bg-white/10 backdrop-blur-sm border-t border-gray-400 py-4 mt-12">
        <div class="container mx-auto px-6 text-center text-gray-700">
            <p>&copy; 2025 Your App. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>