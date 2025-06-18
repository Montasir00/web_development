<?php
require 'utils.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    if ($username) {
        sendOTP($username);
        echo "OTP sent to your Telegram!";
    } else {
        echo "Username is required.";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Enter username" required />
    <button type="submit">Send OTP</button>
</form>
