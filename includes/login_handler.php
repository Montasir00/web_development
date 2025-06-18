<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\includes\login_handler.php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/telegram_config.php';

// Handle traditional email/password login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email and password.";
        header("Location: ../index.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'login_method' => 'email'
            ];

            session_regenerate_id(true);

            if ($user['role'] === 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
        }
    } else {
        $_SESSION['login_error'] = "No account found with that email address.";
    }

    header("Location: ../index.php");
    exit;
}

// Handle Telegram OTP login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['telegram_login'])) {
    $email = sanitize($_POST['email']);
    $entered_otp = sanitize($_POST['otp']);

    if (empty($email) || empty($entered_otp)) {
        $_SESSION['login_error'] = "Please enter both email and OTP.";
        header("Location: ../index.php");
        exit;
    }

    // Verify OTP
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

            if ($user['role'] === 'admin') {
                header("Location: ../admin_dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Account not found.";
        }
    } else {
        $_SESSION['login_error'] = "Invalid or expired OTP.";
    }

    header("Location: ../index.php");
    exit;
}

header("Location: ../index.php");
exit;
?>