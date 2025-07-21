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
    <title>Dashboard</title>
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-purple-100 to-indigo-100 min-h-screen font-['Inter']">
    <header class="bg-white/10 backdrop-blur-lg border-b border-white/20 sticky top-0 z-10">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-purple-900">Dashboard</h1>
            <form method="POST">
                <button type="submit" name="logout" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-105">Sign Out</button>
            </form>
        </div>
    </header>
    <main class="container mx-auto px-6 py-8">
        <section class="mb-12">
            <h1 class="text-4xl font-bold text-purple-900 mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-purple-800 text-lg">Explore your personalized dashboard and stay connected.</p>
        </section>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
                <h2 class="text-xl font-semibold text-purple-900 mb-4">Profile Overview</h2>
                <p class="text-purple-800">Username: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p class="text-purple-800">Status: Active</p>
                <button class="mt-4 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-all duration-300">Edit Profile</button>
            </div>
            <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
                <h2 class="text-xl font-semibold text-purple-900 mb-4">Quick Actions</h2>
                <ul class="text-purple-800 space-y-2">
                    <li><a href="#" class="hover:text-purple-500 transition-colors">View Recent Activity</a></li>
                    <li><a href="#" class="hover:text-purple-500 transition-colors">Manage Settings</a></li>
                    <li><a href="#" class="hover:text-purple-500 transition-colors">Check Notifications</a></li>
                </ul>
            </div>
            <div class="bg-white/10 backdrop-blur-lg p-6 rounded-xl shadow-2xl border border-white/20">
                <h2 class="text-xl font-semibold text-purple-900 mb-4">System Info</h2>
                <p class="text-purple-800">A secure PHP-based platform with:</p>
                <ul class="list-disc pl-6 text-purple-800 space-y-2 mt-2">
                    <li>Robust authentication</li>
                    <li>Persistent login support</li>
                    <li>Modern, responsive design</li>
                </ul>
            </div>
        </div>
    </main>
    <footer class="bg-white/10 backdrop-blur-lg border-t border-white/20 py-4 mt-12">
        <div class="container mx-auto px-6 text-center text-purple-800">
            <p>&copy; 2025 Your App. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>