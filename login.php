<?php
session_start();

// Database connection
$host = "localhost";
$user = "root"; // your db username
$pass = "";     // your db password
$db = "bloom_and_basket"; // your db name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Simple validation (you should hash passwords in production!)
$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role']; // assuming 'role' column exists

    if ($user['role'] === 'admin') {
        // Redirect admin to dashboard
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($user['role'] === 'customer') {
        // Redirect customer to products page
        header("Location: customer_home.php");
        exit();
    } else {
        echo "Unknown role detected!";
    }
} else {
    echo "Invalid email or password!";
}

$conn->close();
?>
