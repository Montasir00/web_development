<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\register.php
require_once __DIR__ . '/includes/init.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);
    $confirm_password = sanitize($_POST['confirm_password']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $telegram_chat_id = sanitize($_POST['telegram_chat_id']);
    
    // Validate data
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Name, email, and password fields are required";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif($password != $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if email already exists
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            $error = "Email already exists. Please use a different email or login.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user with telegram_chat_id
            $sql = "INSERT INTO users (name, email, password, address, phone, telegram_chat_id) 
                    VALUES ('$name', '$email', '$hashed_password', '$address', '$phone', '$telegram_chat_id')";
            
            if(mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);
                
                // Log the user in
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $name;
                
                // Redirect to home
                redirect('index.php', 'Registration successful! Welcome to Bloom & Basket.');
            } else {
                $error = "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Register for Bloom & Basket - Fresh and Organic Products">
    <title>Register - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/register.css">
    <style>
        .telegram-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            font-size: 1.4rem;
        }
        .telegram-info h4 {
            margin-top: 0;
            color: #0c5460;
            font-size: 1.6rem;
        }
        .simple-steps {
            background: #d1ecf1;
            padding: 1rem;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
            margin: 1rem 0;
        }
        .simple-steps strong {
            color: #0c5460;
        }
        .optional-label {
            color: #666;
            font-size: 1.2rem;
        }
        .bot-highlight {
            background: #fff3cd;
            padding: 0.5rem;
            border-radius: 4px;
            display: inline-block;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>
    
    <section class="register-form-container">
        <h1 class="heading">Create an <span>Account</span></h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="box" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="box" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="box">
            </div>
            
            <div class="form-group">
                <label for="telegram_chat_id">
                    Telegram Chat ID 
                    <span class="optional-label">(Optional - for OTP login)</span>
                </label>
                <input type="text" id="telegram_chat_id" name="telegram_chat_id" class="box" placeholder="e.g., 123456789">
            </div>
            
            <div class="telegram-info">
                <h4>📱 How to get your Chat ID (Super Easy!):</h4>
                <div class="simple-steps">
                    <strong>1.</strong> Click the button below to activate our bot 👇<br>
                    <button type="button" id="activate-bot-btn" class="btn" style="background: #28a745; margin: 10px 0;">
                        🤖 Activate Chat ID Bot
                    </button><br>
                    <strong>2.</strong> Start our bot: <span class="bot-highlight"><strong>@YourBotUsername</strong></span><br>
                    <strong>3.</strong> Send any message (like "hi" or "hello")<br>
                    <strong>4.</strong> Bot will reply with your Chat ID<br>
                    <strong>5.</strong> Copy the number and paste it above ☝️
                </div>
                
                <div id="bot-status" style="display: none; padding: 10px; margin: 10px 0; border-radius: 5px;">
                    <!-- Status will be shown here -->
                </div>
                
                <p><strong>💡 Why add Chat ID?</strong><br>
                ✅ Get OTP directly on Telegram<br>
                ✅ Faster login process<br>
                ✅ More secure than SMS</p>
                
                <p style="margin-bottom: 0;"><strong>🤖 Bot Name:</strong> Replace <code>@YourBotUsername</code> with your actual bot username</p>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Register" class="btn">
            </div>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </section>
    
    <!-- Footer Section -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
<script>
// Client-side validation for chat_id
document.getElementById('telegram_chat_id').addEventListener('input', function() {
    const chatId = this.value;
    if (chatId && !/^\d+$/.test(chatId)) {
        this.setCustomValidity('Chat ID should only contain numbers');
        this.style.borderColor = '#dc3545';
    } else {
        this.setCustomValidity('');
        this.style.borderColor = '';
    }
});

// Add helpful tooltip
document.getElementById('telegram_chat_id').addEventListener('focus', function() {
    if (!this.value) {
        this.placeholder = 'Start our bot first to get your Chat ID';
    }
});

document.getElementById('telegram_chat_id').addEventListener('blur', function() {
    if (!this.value) {
        this.placeholder = 'e.g., 123456789';
    }
});

// Activate bot button
document.getElementById('activate-bot-btn').addEventListener('click', function() {
    const button = this;
    const statusDiv = document.getElementById('bot-status');
    
    // Show loading
    button.innerHTML = '🔄 Activating Bot...';
    button.disabled = true;
    statusDiv.style.display = 'block';
    statusDiv.style.background = '#fff3cd';
    statusDiv.innerHTML = '⏳ Activating Chat ID Bot... Please wait.';
    
    // Call the bot activation
    fetch('activate_chat_bot.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Bot activation response:', data); // Debug log
            
            if (data.success) {
                button.innerHTML = '✅ Bot Activated!';
                button.style.background = '#28a745';
                statusDiv.style.background = '#d4edda';
                statusDiv.innerHTML = '✅ <strong>Bot is now active for 60 seconds!</strong><br>📱 Go to Telegram and start our bot: <strong>@' + (data.bot_name || 'YourBotUsername') + '</strong><br>💬 Send any message to get your Chat ID!<br>⏰ Bot will respond for the next 60 seconds.';
                
                // Auto-disable after 60 seconds
                setTimeout(() => {
                    button.innerHTML = '🤖 Activate Chat ID Bot';
                    button.style.background = '#28a745';
                    button.disabled = false;
                    statusDiv.innerHTML = '⏰ Bot activation period ended. Click again if you need to reactivate.';
                    statusDiv.style.background = '#f8f9fa';
                }, 60000);
                
            } else {
                button.innerHTML = '❌ Activation Failed';
                button.style.background = '#dc3545';
                statusDiv.style.background = '#f8d7da';
                statusDiv.innerHTML = '❌ Failed to activate bot: ' + (data.message || 'Unknown error');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Bot activation error:', error); // Debug log
            button.innerHTML = '❌ Error';
            button.style.background = '#dc3545';
            statusDiv.style.background = '#f8d7da';
            statusDiv.innerHTML = '❌ Connection error. Please try again. Error: ' + error.message;
            button.disabled = false;
        });
});
</script>
</body>
</html>