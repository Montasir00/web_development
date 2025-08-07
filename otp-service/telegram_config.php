<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\otp-service\telegram_config.php
require_once __DIR__ . '/../includes/init.php';

// Load environment variables
function loadTelegramEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($name, $value) = explode('=', $line, 2);
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
}

loadTelegramEnv();

// Send OTP to both admin (from .env) and user (from database)
function sendTelegramOTP($email, $otp) {
    global $conn;
    
    $token = "7151167341:AAEgHFNm1zwluKo-LnFROLL9aQ2-Kl6IyCg"; // bot token
    $admin_chat_id = getenv('TELEGRAM_CHAT_ID'); // chat_id from .env
    
    if (!$token) {
        error_log("Telegram token not found");
        return false;
    }
    
    if (!$admin_chat_id) {
        error_log("Admin chat ID not found in .env");
        return false;
    }
    
    // Get user details and their chat_id from database
    $stmt = $conn->prepare("SELECT name, telegram_chat_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("User not found: " . $email);
        return false;
    }
    
    $user = $result->fetch_assoc();
    $user_chat_id = $user['telegram_chat_id']; // Friend's chat_id from database
    $user_name = $user['name'];
    
    $success_count = 0;
    
    // Send to admin (you) - from .env
    $admin_message = "🔐 *OTP Login Request*\n\n";
    $admin_message .= "👤 *User:* " . $user_name . "\n";
    $admin_message .= "📧 *Email:* " . $email . "\n";
    $admin_message .= "🔑 *OTP:* `" . $otp . "`\n\n";
    $admin_message .= "⏰ *Expires:* 5 minutes\n";
    $admin_message .= "🕐 *Time:* " . date('Y-m-d H:i:s') . "\n";
    $admin_message .= "👥 *Sent to:* Admin + User";
    
    if (sendToTelegramChat($token, $admin_chat_id, $admin_message)) {
        $success_count++;
    } else {
        error_log("Failed to send to admin: " . $admin_chat_id);
    }
    
    // Send to user (friend) - from database
    if ($user_chat_id) {
        $user_message = "🔐 *Your Login OTP*\n\n";
        $user_message .= "Hello " . $user_name . "! 👋\n\n";
        $user_message .= "🔑 *OTP:* `" . $otp . "`\n\n";
        $user_message .= "⏰ *Expires in:* 5 minutes\n";
        $user_message .= "🔒 *For account:* " . $email . "\n\n";
        $user_message .= "💻 Enter this code on the website to login.";
        
        if (sendToTelegramChat($token, $user_chat_id, $user_message)) {
            $success_count++;
        } else {
            error_log("Failed to send to user: " . $user_chat_id);
        }
    } else {
        error_log("User has no telegram_chat_id: " . $email);
    }
    
    return $success_count > 0;
}

function sendToTelegramChat($token, $chat_id, $message) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Log the response for debugging
    if ($httpCode !== 200) {
        error_log("Telegram API Error - HTTP Code: " . $httpCode . ", Response: " . $response . ", Curl Error: " . $curl_error);
        return false;
    }
    
    $result = json_decode($response, true);
    if (!$result || !$result['ok']) {
        error_log("Telegram API returned error: " . $response);
        return false;
    }
    
    return true;
}

// Generate OTP
function generateOTP() {
    return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

// Store OTP in database
function storeOTP($username, $otp) {
    global $conn;
    
    $timestamp = time();
    
    // Clean expired OTPs first
    $expiry = $timestamp - 300; // 5 minutes ago
    $conn->query("DELETE FROM otp_storage WHERE timestamp < {$expiry}");
    
    // Store new OTP
    $stmt = $conn->prepare("REPLACE INTO otp_storage (username, otp, timestamp) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Failed to prepare OTP storage statement: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("ssi", $username, $otp, $timestamp);
    $result = $stmt->execute();
    
    if (!$result) {
        error_log("Failed to store OTP: " . $stmt->error);
    }
    
    $stmt->close();
    return $result;
}

// Verify OTP
function verifyTelegramOTP($username, $inputOtp) {
    global $conn;
    
    // Clean expired OTPs
    $expiry = time() - 300; // 5 minutes ago
    $conn->query("DELETE FROM otp_storage WHERE timestamp < {$expiry}");
    
    // Get stored OTP
    $stmt = $conn->prepare("SELECT otp, timestamp FROM otp_storage WHERE username = ?");
    if (!$stmt) {
        return false;
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $stmt->close();
        return false;
    }
    
    $data = $result->fetch_assoc();
    $stmt->close();
    
    // Delete OTP after use
    $delete_stmt = $conn->prepare("DELETE FROM otp_storage WHERE username = ?");
    if ($delete_stmt) {
        $delete_stmt->bind_param("s", $username);
        $delete_stmt->execute();
        $delete_stmt->close();
    }
    
    // Check if OTP matches
    return trim($inputOtp) === trim($data['otp']);
}
?>