<?php
require_once 'db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    
    // Validate data
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            $error = "Email already exists. Please use a different email or login.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $sql = "INSERT INTO users (name, email, password, address, phone) 
                    VALUES ('$name', '$email', '$hashed_password', '$address', '$phone')";
            
            if(mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);
                
                // Log the user in
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $name;
                
                // Redirect to home
                redirect('index.php', 'Registration successful! Welcome to Bloom & Basket.');
            } else {
                $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Register for Bloom & Basket - Fresh and Organic Products">
    <title>Register - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <!-- Header Section (You can include it using PHP include) -->
    <?php include 'includes/header.php'; ?>
    
    <section class="register-form-container">
        <h1 class="heading">Create an <span>Account</span></h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="box" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="box">
            </div>
            
            <div class="form-group">
                <input type="submit" value="Register" class="btn">
            </div>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </section>
    
    <!-- Footer Section (You can include it using PHP include) -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>