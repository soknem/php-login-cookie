<?php
require 'auth.php';
if (checkAuth()) {
    header('Location: home.php');
    exit;
}

$username = $_GET['user'] ?? '';
if (!$username) {
    header('Location: index.php');
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT email, token, expired_date FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user || !$user['token']) {
    header('Location: index.php');
    exit;
}

$error = '';
$currentTime = time();
$expiredTime = strtotime($user['expired_date']);
if ($currentTime > $expiredTime) {
    $error = 'OTP has expired. Please resend a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otpInput = implode('', $_POST['otp'] ?? []);
    if (verify_otp($username, $otpInput)) {
        $_SESSION['username'] = $username;
        header('Location: home.php');
        exit;
    } else {
        $error = 'Invalid or expired OTP. Please try again.';
    }
}

$timeLeft = max(0, $expiredTime - $currentTime);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-600 flex items-center justify-center min-h-screen">
    <div class="bg-white/10 backdrop-blur-xl p-8 rounded-xl shadow-2xl w-full max-w-md border border-gray-400">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">OTP Verification</h2>
        <?php if ($error): ?>
            <p class="text-red-400 bg-red-100/50 p-3 rounded-lg text-center mb-6"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <p class="text-center text-gray-800 mb-6">Enter the 6-digit code sent to your email</p>
        <form method="POST" class="space-y-6">
            <div class="flex justify-center gap-3">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="otp[]" maxlength="1" class="w-12 h-12 text-center p-3 bg-white/20 border border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-700 transition-all duration-300 text-xl font-semibold" required>
                <?php endfor; ?>
            </div>
            <p id="timer" class="text-center text-gray-800 mb-2">Time remaining: <span id="timeLeft"></span></p>
            <button type="submit" class="w-full bg-gray-700 text-white p-3 rounded-lg hover:bg-blue-800 transition-all duration-300 transform hover:scale-105">Verify OTP</button>
            <p class="text-center text-gray-800 mt-6">Didn't receive a code? <a href="resend_otp.php?user=<?php echo urlencode($username); ?>" class="text-blue-800 hover:underline">Click to resend</a></p>
        </form>
    </div>
    <script>
        let timeLeft = <?php echo $timeLeft; ?>;
        const timerElement = document.getElementById('timeLeft');
        function updateTimer() {
            if (timeLeft > 0) {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            } else {
                timerElement.textContent = 'Expired';
            }
        }
        updateTimer();
        setInterval(updateTimer, 1000);

        // Auto-focus next OTP input field
        const otpInputs = document.querySelectorAll('input[name="otp[]"]');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>