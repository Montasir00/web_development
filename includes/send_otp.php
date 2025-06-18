<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\includes\send_otp.php
session_start();
require_once 'telegram_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    // Check if user exists and get their chat_id
    $stmt = $conn->prepare("SELECT id, name, email, telegram_chat_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'No account found with this email']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Generate and store OTP
    $otp = generateOTP();
    
    if (storeOTP($username, $otp)) {
        if (sendTelegramOTP($username, $otp)) {
            // Check if user has chat_id to customize message
            if ($user['telegram_chat_id']) {
                $message = 'OTP sent successfully! Check your Telegram for the OTP.';
            } else {
                $message = 'OTP sent to admin! User has no Telegram linked - only admin received OTP.';
            }
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'user_has_telegram' => !empty($user['telegram_chat_id'])
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to send OTP via Telegram'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to generate OTP'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>