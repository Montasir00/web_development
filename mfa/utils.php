<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\mfa\utils.php
date_default_timezone_set('Europe/Rome');

function loadEnv() {
    $envFile = __DIR__ . '/../.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '//') === 0 || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($name, $value) = explode('=', $line, 2);
            putenv("$name=$value");
            $_ENV[$name] = $value;
        }
    }
}

function getDbConnection() {
    // Try to use global connection first
    global $conn;
    if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
        return $conn;
    }
    
    // Create new connection
    try {
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'bloom_basket';
        
        $new_conn = new mysqli($host, $username, $password, $database);
        
        if ($new_conn->connect_error) {
            throw new Exception("Connection failed: " . $new_conn->connect_error);
        }
        
        $new_conn->set_charset("utf8");
        return $new_conn;
        
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection error: " . $e->getMessage());
    }
}

// OTP Logging Function
function logOTP($username, $otp, $action, $additional_info = '') {
    try {
        $conn = getDbConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 500);
        $timestamp = time();
        
        // Mask OTP for security (show only first and last digit)
        $masked_otp = $otp ? substr($otp, 0, 1) . '****' . substr($otp, -1) : 'N/A';
        
        $stmt = $conn->prepare("INSERT INTO otp_log (username, otp, action, timestamp, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log("OTP Log prepare failed: " . $conn->error);
            return false;
        }
        
        $stmt->bind_param("sssiss", $username, $masked_otp, $action, $timestamp, $ip, $user_agent);
        $result = $stmt->execute();
        
        if (!$result) {
            error_log("OTP Log execute failed: " . $stmt->error);
        } else {
            error_log("OTP Log: User={$username}, Action={$action}, IP={$ip}, Time=" . date('Y-m-d H:i:s', $timestamp));
        }
        
        $stmt->close();
        return $result;
        
    } catch (Exception $e) {
        error_log("Failed to log OTP activity: " . $e->getMessage());
        return false;
    }
}

// Get OTP History Function
function getOTPHistory($username = null, $limit = 50) {
    try {
        $conn = getDbConnection();
        
        if ($username) {
            $stmt = $conn->prepare("SELECT username, otp, action, timestamp, ip_address, created_at 
                                   FROM otp_log WHERE username = ? 
                                   ORDER BY created_at DESC LIMIT ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("si", $username, $limit);
        } else {
            $stmt = $conn->prepare("SELECT username, otp, action, timestamp, ip_address, created_at 
                                   FROM otp_log 
                                   ORDER BY created_at DESC LIMIT ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $limit);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $history = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $history;
        
    } catch (Exception $e) {
        error_log("Failed to get OTP history: " . $e->getMessage());
        return [];
    }
}

loadEnv();

function sendOTP($username) {
    try {
        $conn = getDbConnection();
        
        // Get Telegram credentials
        $token = getenv('TELEGRAM_BOT_TOKEN');
        $chat_id = getenv('TELEGRAM_CHAT_ID');
        
        if (!$token || !$chat_id) {
            logOTP($username, null, 'failed', 'Telegram credentials missing');
            throw new Exception("Telegram credentials not configured properly");
        }
        
        // Generate new OTP
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $timestamp = time();

        error_log("OTP GENERATION: Starting for {$username}, OTP: {$otp}, Timestamp: {$timestamp}");

        // SIMPLIFIED STORAGE - Use REPLACE to avoid transaction issues
        try {
            // Use REPLACE statement to handle duplicates automatically
            $stmt = $conn->prepare("REPLACE INTO otp_storage (username, otp, timestamp) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("REPLACE prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param("ssi", $username, $otp, $timestamp);
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("REPLACE execute failed: " . $stmt->error);
            }
            
            $affected_rows = $conn->affected_rows;
            $stmt->close();
            
            error_log("OTP STORAGE: REPLACE successful, affected rows: {$affected_rows}");
            
            // Verify the storage worked
            $verify_stmt = $conn->prepare("SELECT otp, timestamp FROM otp_storage WHERE username = ?");
            if (!$verify_stmt) {
                throw new Exception("Verify prepare failed: " . $conn->error);
            }
            
            $verify_stmt->bind_param("s", $username);
            $verify_stmt->execute();
            $verify_result = $verify_stmt->get_result();
            
            if ($verify_result->num_rows > 0) {
                $verify_data = $verify_result->fetch_assoc();
                error_log("OTP VERIFICATION: Found OTP {$verify_data['otp']} for {$username}");
                
                if ($verify_data['otp'] === $otp) {
                    logOTP($username, $otp, 'created', "OTP stored successfully using REPLACE");
                } else {
                    throw new Exception("OTP mismatch after storage. Expected: {$otp}, Found: {$verify_data['otp']}");
                }
            } else {
                throw new Exception("OTP not found after REPLACE operation");
            }
            
            $verify_stmt->close();
            
        } catch (Exception $e) {
            error_log("OTP STORAGE ERROR: " . $e->getMessage());
            
            // Try alternative method: Direct DELETE + INSERT
            try {
                error_log("OTP STORAGE: Trying DELETE + INSERT method");
                
                // Delete existing
                $delete_result = $conn->query("DELETE FROM otp_storage WHERE username = '{$username}'");
                if (!$delete_result) {
                    throw new Exception("DELETE failed: " . $conn->error);
                }
                
                $deleted = $conn->affected_rows;
                error_log("OTP STORAGE: Deleted {$deleted} existing records");
                
                // Insert new
                $insert_result = $conn->query("INSERT INTO otp_storage (username, otp, timestamp) VALUES ('{$username}', '{$otp}', {$timestamp})");
                
                if (!$insert_result) {
                    throw new Exception("Direct INSERT failed: " . $conn->error);
                }
                
                error_log("OTP STORAGE: Direct INSERT successful");
                logOTP($username, $otp, 'created', "OTP stored using direct INSERT method");
                
            } catch (Exception $e2) {
                logOTP($username, $otp, 'failed', 'Both storage methods failed: ' . $e2->getMessage());
                throw new Exception("Both storage methods failed: " . $e2->getMessage());
            }
        }

        // Send OTP via Telegram
        $italy_time = new DateTime('now', new DateTimeZone('Europe/Rome'));
        $msg = "🔐 Your OTP for Bloom & Basket: *{$otp}*\n\n⏰ Expires in 3 minutes\n🔒 Generated: " 
        . $italy_time->format('H:i:s') . "\n\nDo not share this code!";        
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        $payload = [
            'chat_id' => $chat_id, 
            'text' => $msg,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            logOTP($username, $otp, 'failed', 'Telegram cURL error: ' . $error);
            throw new Exception("Telegram API error: " . $error);
        }
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200) {
            logOTP($username, $otp, 'failed', 'Telegram HTTP error: ' . $http_code);
            throw new Exception("Telegram API returned HTTP code: " . $http_code);
        }
        
        $response_data = json_decode($response, true);
        if (!$response_data || !$response_data['ok']) {
            $error_desc = $response_data['description'] ?? 'Unknown error';
            logOTP($username, $otp, 'failed', 'Telegram API error: ' . $error_desc);
            throw new Exception("Telegram API error: " . $error_desc);
        }
        
        // Log successful send
        logOTP($username, $otp, 'created', 'OTP sent successfully via Telegram');
        
        error_log("OTP SUCCESS: Generated {$otp} for {$username} at " . date('Y-m-d H:i:s', $timestamp));
        
        return true;
        
    } catch (Exception $e) {
        logOTP($username, $otp ?? null, 'failed', 'Send error: ' . $e->getMessage());
        error_log("OTP SEND ERROR: " . $e->getMessage());
        throw new Exception("Failed to send OTP: " . $e->getMessage());
    }
}

function verifyOTP($username, $inputOtp) {
    try {
        $conn = getDbConnection();
        
        // Clean expired OTPs first
        cleanExpiredOTPs();
        
        // Get stored OTP
        $stmt = $conn->prepare("SELECT otp, timestamp FROM otp_storage WHERE username = ?");
        if (!$stmt) {
            logOTP($username, $inputOtp, 'failed', 'Database prepare error');
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            logOTP($username, $inputOtp, 'failed', 'No OTP found for user');
            $stmt->close();
            return "OTP not found or expired.";
        }
        
        $data = $result->fetch_assoc();
        $storedOtp = $data['otp'];
        $timestamp = $data['timestamp'];
        $stmt->close();
        
        // Debug log
        error_log("OTP VERIFY: User={$username}, Stored={$storedOtp}, Input={$inputOtp}, Age=" . (time() - $timestamp) . "s");

        // Delete the OTP record regardless of outcome (single use)
        $delete_stmt = $conn->prepare("DELETE FROM otp_storage WHERE username = ?");
        if ($delete_stmt) {
            $delete_stmt->bind_param("s", $username);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
        
        // Check if OTP has expired (3 minutes = 180 seconds)
        $age = time() - $timestamp;
        if ($age > 180) {
            logOTP($username, $inputOtp, 'expired', "OTP age: {$age} seconds");
            return "OTP has expired. Please request a new one.";
        }

        // Verify the OTP
        if (trim($inputOtp) === trim($storedOtp)) {
            logOTP($username, $inputOtp, 'verified', 'OTP verification successful');
            error_log("OTP VERIFY SUCCESS: {$username}");
            return "OTP verified!";
        } else {
            logOTP($username, $inputOtp, 'failed', "Invalid OTP. Expected: {$storedOtp}");
            error_log("OTP VERIFY FAILED: {$username} - Expected: {$storedOtp}, Got: {$inputOtp}");
            return "Invalid OTP.";
        }
        
    } catch (Exception $e) {
        logOTP($username, $inputOtp ?? null, 'failed', 'Verification error: ' . $e->getMessage());
        error_log("OTP VERIFY ERROR: " . $e->getMessage());
        return "Verification error occurred.";
    }
}

function cleanExpiredOTPs() {
    try {
        $conn = getDbConnection();
        $expiry = time() - 180; // 3 minutes ago
        
        // Count expired OTPs
        $count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM otp_storage WHERE timestamp < ?");
        if ($count_stmt) {
            $count_stmt->bind_param("i", $expiry);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $expired_count = $count_result->fetch_assoc()['count'];
            $count_stmt->close();
        } else {
            $expired_count = 0;
        }
        
        // Delete expired OTPs
        $stmt = $conn->prepare("DELETE FROM otp_storage WHERE timestamp < ?");
        if ($stmt) {
            $stmt->bind_param("i", $expiry);
            $stmt->execute();
            $stmt->close();
            
            if ($expired_count > 0) {
                error_log("OTP CLEANUP: Deleted {$expired_count} expired OTPs");
            }
        }
        
    } catch (Exception $e) {
        error_log("OTP CLEANUP ERROR: " . $e->getMessage());
    }
}

// Enhanced debug function
function debugOTPStorage() {
    try {
        $conn = getDbConnection();
        
        echo "<h3>🔍 Current OTP Storage Debug:</h3>";
        
        // Check table existence
        $table_check = $conn->query("SHOW TABLES LIKE 'otp_storage'");
        if ($table_check->num_rows === 0) {
            echo "<p style='color: red;'>❌ otp_storage table does NOT exist!</p>";
            echo "<p><strong>Create it with:</strong></p>";
            echo "<code>CREATE TABLE otp_storage (username VARCHAR(255) PRIMARY KEY, otp VARCHAR(6), timestamp INT);</code>";
            return;
        } else {
            echo "<p style='color: green;'>✅ otp_storage table exists</p>";
        }
        
        // Show table structure
        $structure = $conn->query("DESCRIBE otp_storage");
        echo "<h4>Table Structure:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Key</th><th>Null</th></tr>";
        while ($row = $structure->fetch_assoc()) {
            echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td><td>{$row['Null']}</td></tr>";
        }
        echo "</table>";
        
        // Show current records
        $result = $conn->query("SELECT username, otp, timestamp, FROM_UNIXTIME(timestamp) as readable_time, (UNIX_TIMESTAMP() - timestamp) as age_seconds FROM otp_storage ORDER BY timestamp DESC");
        
        echo "<h4>Current Records:</h4>";
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Username</th><th>OTP</th><th>Unix Timestamp</th><th>Readable Time</th><th>Age (seconds)</th><th>Status</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                $status = $row['age_seconds'] > 180 ? 'EXPIRED' : 'ACTIVE';
                $color = $status === 'EXPIRED' ? 'red' : 'green';
                echo "<tr>";
                echo "<td>{$row['username']}</td>";
                echo "<td><strong>{$row['otp']}</strong></td>";
                echo "<td>{$row['timestamp']}</td>";
                echo "<td>{$row['readable_time']}</td>";
                echo "<td>{$row['age_seconds']}</td>";
                echo "<td style='color: {$color}'><strong>{$status}</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>📭 No records found in otp_storage table</p>";
        }
        
        // Show recent logs
        echo "<h3>Recent OTP Logs:</h3>";
        $logs = getOTPHistory(null, 10);
        if (!empty($logs)) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Username</th><th>OTP</th><th>Action</th><th>Time</th><th>IP</th></tr>";
            foreach ($logs as $log) {
                $action_color = '';
                switch ($log['action']) {
                    case 'created': $action_color = 'green'; break;
                    case 'verified': $action_color = 'blue'; break;
                    case 'failed': $action_color = 'red'; break;
                    case 'expired': $action_color = 'orange'; break;
                }
                echo "<tr>";
                echo "<td>{$log['username']}</td>";
                echo "<td>{$log['otp']}</td>";
                echo "<td style='color: {$action_color}'><strong>{$log['action']}</strong></td>";
                echo "<td>" . date('Y-m-d H:i:s', $log['timestamp']) . "</td>";
                echo "<td>{$log['ip_address']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>No OTP logs found.</p>";
        }
        
        // Show timezone info
        $timezone_result = $conn->query("SELECT @@session.time_zone as session_tz, NOW() as mysql_time, UNIX_TIMESTAMP() as mysql_timestamp");
        if ($timezone_result) {
            $tz_data = $timezone_result->fetch_assoc();
            echo "<h4>⏰ Timezone Info:</h4>";
            echo "<p><strong>MySQL Time:</strong> {$tz_data['mysql_time']} (Timestamp: {$tz_data['mysql_timestamp']})</p>";
            echo "<p><strong>PHP Time:</strong> " . date('Y-m-d H:i:s') . " (Timestamp: " . time() . ")</p>";
            echo "<p><strong>Timezone:</strong> " . date_default_timezone_get() . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Debug error: " . $e->getMessage() . "</p>";
    }
}

// Test function for manual OTP creation
function createTestOTP($username = 'test@example.com') {
    try {
        $conn = getDbConnection();
        $test_otp = rand(100000, 999999);
        $timestamp = time();
        
        echo "<h4>🧪 Creating Test OTP</h4>";
        echo "<p>Username: {$username}</p>";
        echo "<p>OTP: {$test_otp}</p>";
        echo "<p>Timestamp: {$timestamp}</p>";
        
        // Try REPLACE method
        $stmt = $conn->prepare("REPLACE INTO otp_storage (username, otp, timestamp) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssi", $username, $test_otp, $timestamp);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        echo "<p style='color: green;'>✅ Test OTP created successfully!</p>";
        $stmt->close();
        
        // Verify
        $verify = $conn->query("SELECT * FROM otp_storage WHERE username = '{$username}'");
        if ($verify && $verify->num_rows > 0) {
            $data = $verify->fetch_assoc();
            echo "<p style='color: blue;'>✅ Verification: Found OTP {$data['otp']} for {$data['username']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Verification failed: OTP not found</p>";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Test OTP creation failed: " . $e->getMessage() . "</p>";
        return false;
    }
}
?>