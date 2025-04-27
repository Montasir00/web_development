<?php
session_start();
require_once 'db.php';
require_once 'includes/functions.php';

$error = ''; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['role'] = $user['role']; // Store the user's role
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect('admin_dashboard.php', 'Welcome Admin!');
                } else {
                    redirect('index.php', 'Login successful! Welcome back.');
                }
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login to Bloom & Basket - Fresh and Organic Products">
    <title>Login - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
    <!-- Header Section (You can include it using PHP include) -->
    <?php include 'includes/header.php'; ?>
    
    <section class="login-form-container">
        <h1 class="heading">Login to Your <span>Account</span></h1>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php displayMessage(); ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="box" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Login" class="btn">
            </div>
            
            <p>Forgot your Password? <a href="reset_password.php">Click Here</a></p>
            <p>Don't have an Account? <a href="register.php">Create Now</a></p>
        </form>
    </section>
    
    <!-- Footer Section (You can include it using PHP include) -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>