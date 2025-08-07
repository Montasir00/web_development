<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\activate_chat_bot.php

header('Content-Type: application/json');

try {
    // Your bot token
    $token = "7151167341:AAEgHFNm1zwluKo-LnFROLL9aQ2-Kl6IyCg";
    
    // Test if bot is working
    $test_url = "https://api.telegram.org/bot{$token}/getMe";
    $test_response = file_get_contents($test_url);
    $test_data = json_decode($test_response, true);
    
    if (!$test_data || !$test_data['ok']) {
        throw new Exception('Bot token is invalid or bot is not responding');
    }
    
    // Start the bot monitoring in background
    $chat_bot_file = __DIR__ . '/chat_id_bot.php';
    
    if (!file_exists($chat_bot_file)) {
        throw new Exception('Chat bot file not found');
    }
    
    // Execute the bot script
    $command = "php " . escapeshellarg($chat_bot_file) . " > /dev/null 2>&1 &";
    
    if (PHP_OS_FAMILY === 'Windows') {
        $command = "start /B php " . escapeshellarg($chat_bot_file);
    }
    
    exec($command, $output, $return_code);
    
    echo json_encode([
        'success' => true,
        'message' => 'Bot activated successfully! Now go to Telegram.',
        'bot_name' => $test_data['result']['username'] ?? 'Unknown'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>