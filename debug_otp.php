<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\debug_otp.php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/mfa/utils.php';

echo "<h1>🔐 Enhanced OTP System Debug Tool</h1>";

// Show current system status
debugOTPStorage();

if (isset($_GET['action'])) {
    echo "<hr><h2>🎬 Action Results:</h2>";
    
    switch ($_GET['action']) {
        case 'send':
            try {
                $test_email = $_GET['email'] ?? 'armando@bloombasket.com';
                echo "<p>📤 Sending OTP to: <strong>{$test_email}</strong></p>";
                sendOTP($test_email);
                echo "<p style='color: green;'>✅ OTP sent successfully!</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
            break;
            
        case 'verify':
            try {
                $test_email = $_GET['email'] ?? 'armando@bloombasket.com';
                $test_otp = $_GET['otp'] ?? '';
                echo "<p>🔍 Verifying OTP: <strong>{$test_otp}</strong> for: <strong>{$test_email}</strong></p>";
                $result = verifyOTP($test_email, $test_otp);
                if ($result === "OTP verified!") {
                    echo "<p style='color: green;'>✅ {$result}</p>";
                } else {
                    echo "<p style='color: red;'>❌ {$result}</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
            break;
            
        case 'test_create':
            createTestOTP($_GET['email'] ?? 'test@example.com');
            break;
            
        case 'recreate_tables':
            try {
                $conn = getDbConnection();
                
                echo "<p>🗑️ Dropping existing tables...</p>";
                $conn->query("DROP TABLE IF EXISTS otp_storage");
                $conn->query("DROP TABLE IF EXISTS otp_log");
                
                echo "<p>🏗️ Creating otp_storage table...</p>";
                $create_storage = "CREATE TABLE otp_storage (
                    username VARCHAR(255) NOT NULL PRIMARY KEY,
                    otp VARCHAR(6) NOT NULL,
                    timestamp INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                
                if ($conn->query($create_storage)) {
                    echo "<p style='color: green;'>✅ otp_storage table created!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create otp_storage: " . $conn->error . "</p>";
                }
                
                echo "<p>🏗️ Creating otp_log table...</p>";
                $create_log = "CREATE TABLE otp_log (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(255) NOT NULL,
                    otp VARCHAR(10),
                    action ENUM('created', 'verified', 'expired', 'failed', 'deleted') NOT NULL,
                    timestamp INT NOT NULL,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_username (username),
                    INDEX idx_action (action)
                )";
                
                if ($conn->query($create_log)) {
                    echo "<p style='color: green;'>✅ otp_log table created!</p>";
                } else {
                    echo "<p style='color: red;'>❌ Failed to create otp_log: " . $conn->error . "</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
            }
            break;
            
        case 'clear':
            try {
                $conn = getDbConnection();
                $conn->query("DELETE FROM otp_storage");
                $conn->query("DELETE FROM otp_log");
                echo "<p style='color: blue;'>🗑️ All OTPs and logs cleared!</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Clear error: " . $e->getMessage() . "</p>";
            }
            break;
    }
    
    echo "<hr>";
    debugOTPStorage();
}

// Action buttons
echo "<hr><h2>🎛️ Test Actions:</h2>";
echo "<div style='margin: 10px 0;'>";
echo "<a href='?action=send&email=armando@bloombasket.com' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; margin: 5px; border-radius: 5px;'>📤 Send Real OTP</a> ";
echo "<a href='?action=test_create&email=test@example.com' style='background: #17a2b8; color: white; padding: 10px 15px; text-decoration: none; margin: 5px; border-radius: 5px;'>🧪 Create Test OTP</a> ";
echo "<a href='?action=recreate_tables' style='background: #6f42c1; color: white; padding: 10px 15px; text-decoration: none; margin: 5px; border-radius: 5px;'>🏗️ Recreate Tables</a> ";
echo "<a href='?action=clear' style='background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; margin: 5px; border-radius: 5px;'>🗑️ Clear All</a> ";
echo "<a href='debug_otp.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; margin: 5px; border-radius: 5px;'>🔄 Refresh</a>";
echo "</div>";

// Verification form
echo "<hr><h2>🔍 Test OTP Verification:</h2>";
echo "<form method='GET' style='margin: 10px 0;'>";
echo "<input type='hidden' name='action' value='verify'>";
echo "<input type='email' name='email' placeholder='Email' value='armando@bloombasket.com' style='padding: 8px; margin: 5px;'>";
echo "<input type='text' name='otp' placeholder='Enter OTP' maxlength='6' style='padding: 8px; margin: 5px;'>";
echo "<input type='submit' value='🔍 Verify OTP' style='background: #17a2b8; color: white; padding: 8px 15px; border: none; border-radius: 3px; margin: 5px;'>";
echo "</form>";

echo "<hr><p><strong>📋 Current Time:</strong> " . date('Y-m-d H:i:s') . " (Timestamp: " . time() . ")</p>";
echo "<p><strong>📍 Timezone:</strong> " . date_default_timezone_get() . "</p>";
?>