<?php
require_once __DIR__ . '/init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please enter both email and password.";
        header("Location: index.php");
        exit;
    }

    // Prepared statement to fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'role' => $user['role']
            ];

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Redirect based on user role
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

    header("Location: index.php");
    exit;
}
?>
