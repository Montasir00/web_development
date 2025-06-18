<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\includes\test_login.php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/telegram_config.php';

$error = '';
$success = '';

// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle traditional email/password login
    if (isset($_POST['login'])) {
        $email = sanitize($_POST['email']);
        $password = sanitize($_POST['password']);

        if (empty($email) || empty($password)) {
            $error = "Please enter both email and password.";
        } else {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Store email in session for OTP verification
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['name'] = $user['name'];

                    // Generate and send OTP
                    $otp = generateTelegramOTP($email);
                    if ($otp) {
                        $success = "Password verified! OTP sent to your Telegram.";
                    } else {
                        $error = "Failed to send OTP. Try again.";
                    }
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "No account found with that email address.";
            }
        }
    }

    // Handle Telegram OTP login
    if (isset($_POST['telegram_login'])) {
        $email = sanitize($_POST['email']);
        $entered_otp = sanitize($_POST['otp']);

        if (empty($email) || empty($entered_otp)) {
            $error = "Please enter both email and OTP.";
        } else {
            if (verifyTelegramOTP($email, $entered_otp)) {
                // Get user details
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

                    // Redirect based on user role
                    header("Location: ../" . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'index.php'));
                    exit;
                } else {
                    $error = "Account not found.";
                }
            } else {
                $error = "Invalid or expired OTP.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .form-group { margin: 15px 0; }
        .box { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; margin-bottom: 10px; }
        .btn { background: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        .btn:hover { background: #218838; }
        .error-message { color: red; font-weight: bold; padding: 10px; border: 2px solid red; border-radius: 5px; margin: 10px 0; }
        .success-message { color: green; font-weight: bold; padding: 10px; border: 2px solid green; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>🔐 Test Login</h1>

    <!-- Display Error or Success Messages -->
    <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
        </div>

        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter your password" class="box" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Login" class="btn" name="login">
        </div>
    </form>

    <!-- OTP Verification Form -->
    <?php if ($success): ?>
    <form method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>">
        <div class="form-group">
            <label>OTP:</label>
            <input type="text" name="otp" placeholder="Enter OTP from Telegram" class="box" maxlength="6" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Verify & Login" class="btn" name="telegram_login">
        </div>
    </form>
    <?php endif; ?>
</body>
</html>