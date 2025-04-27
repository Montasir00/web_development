<?php
$servername = "db";
$username   = "bloomuser";      // Change to your MySQL username
$password   = "bloompassword";          // Change to your MySQL password
$dbname     = "bloom_basket";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
