<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\mfa\verify.php
require_once __DIR__ . '/utils.php';

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $otp = trim($_POST['otp'] ?? '');

    if ($username && $otp) {
        try {
            $result = verifyOTP($username, $otp);
            
            // Style the response based on result
            if ($result === "OTP verified!") {
                echo "<div style='color: green; font-weight: bold; padding: 10px; border: 2px solid green; border-radius: 5px; margin: 10px 0;'>";
                echo "✅ " . $result;
                echo "</div>";
            } else {
                echo "<div style='color: red; font-weight: bold; padding: 10px; border: 2px solid red; border-radius: 5px; margin: 10px 0;'>";
                echo "❌ " . $result;
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div style='color: red; font-weight: bold; padding: 10px; border: 2px solid red; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ Error: " . $e->getMessage();
            echo "</div>";
        }
    } else {
        echo "<div style='color: orange; font-weight: bold; padding: 10px; border: 2px solid orange; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ Both username and OTP are required.";
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .form-group { margin: 15px 0; }
        input[type="text"], input[type="email"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; width: 100%; }
        button:hover { background: #218838; }
        .debug-info { background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 20px 0; font-size: 14px; }
    </style>
</head>
<body>
    <h1>🔐 OTP Verification</h1>
    
    <form method="POST">
        <div class="form-group">
            <label>Email/Username:</label>
            <input type="email" name="username" placeholder="Enter your email" value="armando@bloombasket.com" required />
        </div>
        
        <div class="form-group">
            <label>OTP Code:</label>
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required />
        </div>
        
        <button type="submit">🔍 Verify OTP</button>
    </form>
    
    <div class="debug-info">
        <strong>Debug Info:</strong><br>
        Current Time: <?php echo date('Y-m-d H:i:s'); ?><br>
        Timestamp: <?php echo time(); ?><br>
        Timezone: <?php echo date_default_timezone_get(); ?>
    </div>
    
    <p><a href="../debug_otp.php">🔧 Debug Tool</a> | <a href="../payment.php">💳 Back to Payment</a></p>
</body>
</html>