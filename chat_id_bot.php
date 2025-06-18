<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\chat_id_bot.php

// Your bot token
$token = "7151167341:AAEgHFNm1zwluKo-LnFROLL9aQ2-Kl6IyCg";

// Function to send message
function sendMessage($chat_id, $message, $token) {
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
            'timeout' => 10
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    return $response !== false;
}

// Function to get updates
function getUpdates($token, $offset = 0) {
    $url = "https://api.telegram.org/bot{$token}/getUpdates?offset={$offset}&timeout=10";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 15
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    return json_decode($response, true);
}

// Process messages
$offset = 0;
$processed_count = 0;

// Run for 60 seconds
$start_time = time();
$duration = 60;

while ((time() - $start_time) < $duration) {
    $updates = getUpdates($token, $offset);
    
    if (isset($updates['result']) && !empty($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $offset = $update['update_id'] + 1;
            
            if (isset($update['message'])) {
                $chat_id = $update['message']['chat']['id'];
                $text = $update['message']['text'] ?? '';
                $first_name = $update['message']['from']['first_name'] ?? 'User';
                
                if (!empty($text)) {
                    $response_message = "🆔 <b>Your Chat ID:</b> <code>{$chat_id}</code>\n\n";
                    $response_message .= "👋 Hello {$first_name}!\n\n";
                    $response_message .= "📋 <b>Copy this number:</b> {$chat_id}\n";
                    $response_message .= "📱 Use it on Bloom & Basket website for OTP login!\n\n";
                    $response_message .= "🌐 Go to registration → Paste in Telegram Chat ID field";
                    
                    if (sendMessage($chat_id, $response_message, $token)) {
                        $processed_count++;
                    }
                }
            }
        }
    }
    
    // Small delay to prevent overwhelming the API
    sleep(1);
}

echo "Bot processed {$processed_count} messages in {$duration} seconds.";
?>