<?php
require_once 'db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Simulate sending a reset link (redirect to forgot_password.php)
            $success = "A reset password link has been sent to your email.";
            header("Location: forgot_password.php?success=" . urlencode($success));
            exit;
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
    <meta name="description" content="Reset Password - Bloom & Basket">
    <title>Reset Password - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/reset_password.css">
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>
    
    <section class="reset-password-container">
        <h1 class="heading">Reset <span>Password</span></h1>
        
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
                <input type="submit" value="Send Reset Link" class="btn">
            </div>
        </form>
    </section>
    
    <!-- Footer Section -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>