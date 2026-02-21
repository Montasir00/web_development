<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/otp-service/telegram_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
$showOtpForm = false;

if (!isset($_SESSION['pending_email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['pending_email'];

// Handle Send OTP button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    csrf_require_or_fail(); // ✅ CSRF check FIRST

    $otp = generateOTP();
    if (storeOTP($email, $otp) && sendTelegramOTP($email, $otp)) {
        $success = "OTP sent to your Telegram!";
        $showOtpForm = true;
    } else {
        $error = "Failed to send OTP. Please try again.";
    }
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['telegram_login'])) {
    csrf_require_or_fail(); // ✅ CSRF check FIRST

    $entered_otp = sanitize($_POST['otp']);

    if (empty($entered_otp)) {
        $error = "Please enter the OTP.";
        $showOtpForm = true;
    } else {
        if (verifyTelegramOTP($email, $entered_otp)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'login_method' => 'telegram'
                ];

                session_regenerate_id(true);
                header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php'));
                exit;
            } else {
                $error = "Account not found.";
                $showOtpForm = true;
            }
        } else {
            $error = "Invalid or expired OTP.";
            $showOtpForm = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<h1>🔑 Verify OTP</h1>

<?php if ($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="success-message"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div id="telegram-login">
    <!-- Email Step -->
    <?php if (!$showOtpForm): ?>
        <form method="POST">
            <?= csrf_input() ?> <!-- ✅ CSRF hidden field -->
            <div class="form-group">
                <label for="telegram-email">Email:</label>
                <input type="email" id="telegram-email-display" class="box" value="<?= htmlspecialchars($email) ?>" readonly>
            </div>
            <div class="form-group">
                <input type="submit" value="Send OTP" class="btn" name="send_otp">
            </div>
        </form>
    <?php endif; ?>

    <!-- OTP Step -->
    <?php if ($showOtpForm): ?>
        <form method="POST">
            <?= csrf_input() ?> <!-- ✅ CSRF hidden field -->
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <div class="form-group">
                <label for="otp">OTP:</label>
                <input type="text" name="otp" id="otp" placeholder="Enter OTP from Telegram" class="box" maxlength="6" required>
            </div>
            <div class="form-group">
                <span id="otp-timer" style="color:#28a745;font-weight:bold;"></span>
            </div>
            <div class="form-group">
                <input type="submit" value="Verify & Login" class="btn" name="telegram_login" id="verify-btn">
            </div>
        </form>
    <?php endif; ?>

    <p>Check your Telegram for OTP</p>
</div>

<?php include 'includes/footer.php'; ?>

<?php if ($showOtpForm): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var timerSpan = document.getElementById('otp-timer');
    var otpInput = document.getElementById('otp');
    var btn = document.getElementById('verify-btn');
    if (timerSpan) {
        var seconds = 30; // 30 seconds for demo
        function updateTimer() {
            var min = Math.floor(seconds / 60);
            var sec = seconds % 60;
            timerSpan.textContent = 'OTP expires in ' + min + ':' + (sec < 10 ? '0' : '') + sec;
            if (seconds > 0) {
                seconds--;
                setTimeout(updateTimer, 1000);
            } else {
                timerSpan.textContent = 'OTP expired. Please request a new one.';
                if (otpInput) otpInput.disabled = true;
                if (btn) btn.disabled = true;
                setTimeout(function() {
                    window.location.href = "login.php";
                }, 2000); // Redirect after 2 seconds
            }
        }
        updateTimer();
    }
});
</script>
<?php endif; ?>
</body>
</html>
