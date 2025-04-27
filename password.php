<?php
require_once('db.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Generate a new hash for the password
$password = 'password123';
$newHash = password_hash($password, PASSWORD_BCRYPT);
echo "Generated Hash: " . $newHash . "<br>";

// Update the database with the new hash for specific users
$sql1 = "UPDATE users 
SET password = '$newHash' 
WHERE email IN ('john@example.com', 'jane@example.com', 'admin1@bloombasket.com')";

if (mysqli_query($conn, $sql1)) {
    echo "Passwords updated successfully.<br>";
} else {
    echo "Error updating passwords: " . mysqli_error($conn) . "<br>";
}

// Update the role for admin1@bloombasket.com
$sql2 = "UPDATE users 
SET role = 'admin' 
WHERE email = 'admin1@bloombasket.com'";

if (mysqli_query($conn, $sql2)) {
    echo "Role updated successfully.<br>";
} else {
    echo "Error updating role: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
?>