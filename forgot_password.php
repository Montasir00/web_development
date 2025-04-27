<?php
require_once 'db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $new_password = sanitize($_POST['new_password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    
    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update the user's password
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);
            
            if ($stmt->execute()) {
                // Redirect to homepage with success message
                header("Location: index.php?success=" . urlencode("Password updated successfully!"));
                exit;
            } else {
                $error = "Failed to update password. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Forgot Password - Bloom & Basket">
    <title>Forgot Password - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/forgot_password.css">
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>
    
    <section class="forgot-password-container">
        <h1 class="heading">Forgot <span>Password</span></h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="box" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Reset Password" class="btn">
            </div>
        </form>
    </section>
    
    <!-- Footer Section -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>