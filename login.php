<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/otp-service/telegram_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $_SESSION['pending_email'] = $email;
                    $_SESSION['pending_role'] = $user['role'];
                    $_SESSION['pending_name'] = $user['name'];
                    $success = "Login successful. Click 'Send OTP' to receive your verification code.";
                    header("Location: otp.php");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "No account found with that email address.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">   
    <link rel="stylesheet" type="text/css" href="/css/login.css">   
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <h1>🔐 Login</h1>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" id="login-email" placeholder="Enter your email" class="box" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" placeholder="Enter your password" class="box" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Login" class="btn" name="login">
        </div>
    </form>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
