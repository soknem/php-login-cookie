<?php
require 'auth.php';
if (checkAuth()) {
    header('Location: home.php');
    exit;
}
$error = '';
$username = $_GET['user'] ?? '';
$initial_remaining_seconds = 0;
$expire_message = '';

global $pdo;
if ($username) {
    $stmt = $pdo->prepare("SELECT expired_date FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $user['expired_date']) {
        $expire_time = strtotime($user['expired_date']);
        $current_time = time();
        if ($expire_time > $current_time) {
            $initial_remaining_seconds = $expire_time - $current_time;
            $minutes = floor($initial_remaining_seconds / 60);
            $seconds = $initial_remaining_seconds % 60;
            if ($minutes > 0) {
                $expire_message = "OTP expires in $minutes minute" . ($minutes > 1 ? 's' : '') . " and $seconds second" . ($seconds > 1 ? 's' : '');
            } else {
                $expire_message = "OTP expires in $seconds second" . ($seconds > 1 ? 's' : '');
            }
        } else {
            $expire_message = "OTP has expired. Please resend OTP.";
        }
    } else {
        $duration = get_global_duration();
        $initial_remaining_seconds = $duration * 60;
        $expire_message = "OTP is valid for $duration minute" . ($duration > 1 ? 's' : '');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpInput = $_POST['otp'] ?? '';
    if (verify_otp($username, $otpInput)) {
        $_SESSION['username'] = $username;
        header('Location: home.php');
        exit;
    } else {
        $error = 'Invalid or expired OTP';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-yellow-500 to-yellow-600 flex">
    <div class="flex w-full">
        <!-- Form Section -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-6">
            <div class="bg-white bg-opacity-90 rounded-xl p-8 w-full max-w-md border border-yellow-600 shadow-lg">
                <h2 class="text-3xl font-bold text-center mb-8 text-black">Verify OTP</h2>
                <?php if ($error): ?>
                    <p class="text-red-500 bg-red-100 bg-opacity-50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <p id="expire-message" class="text-black bg-yellow-100 bg-opacity-50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($expire_message); ?></p>
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-black font-medium mb-2">OTP Code</label>
                        <input type="text" name="otp" class="w-full p-3 bg-white bg-opacity-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black transition duration-300" placeholder="Enter OTP" required>
                    </div>
                    <button type="submit" class="w-full bg-black text-white p-3 rounded-lg hover:bg-gray-800 transition duration-300 transform hover:scale-105">Verify OTP</button>
                </form>
                <p class="text-center text-black mt-6">Didn't receive the OTP? <a href="resend_otp.php?user=<?php echo urlencode($username); ?>" class="text-black underline hover:text-gray-700">Resend OTP</a></p>
            </div>
        </div>
        <!-- Image Section -->
        <div class="hidden md:block w-1/2 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');"></div>
    </div>
    <script>
        // Real-time countdown
        let remainingSeconds = <?php echo $initial_remaining_seconds; ?>;
        const expireMessage = document.getElementById('expire-message');

        function updateCountdown() {
            if (remainingSeconds > 0) {
                const minutes = Math.floor(remainingSeconds / 60);
                const seconds = remainingSeconds % 60;
                if (minutes > 0) {
                    expireMessage.textContent = `OTP expires in ${minutes} minute${minutes > 1 ? 's' : ''} and ${seconds} second${seconds > 1 ? 's' : ''}`;
                } else {
                    expireMessage.textContent = `OTP expires in ${seconds} second${seconds > 1 ? 's' : ''}`;
                }
                remainingSeconds--;
            } else {
                expireMessage.textContent = 'OTP has expired. Please resend OTP.';
                clearInterval(countdownInterval);
            }
        }

        // Update every second if OTP is still valid
        if (remainingSeconds > 0) {
            updateCountdown(); // Initial update
            const countdownInterval = setInterval(updateCountdown, 1000);
        }
    </script>
</body>
</html>