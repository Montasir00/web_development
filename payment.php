<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\payment.php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/mfa/utils.php';


// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to proceed', 'error');
}

// Check if order data exists in session
if (!isset($_SESSION['pending_order'])) {
    redirect('checkout.php', 'No pending order found', 'error');
}

$pending_order = $_SESSION['pending_order'];
$error = '';
$success = '';

// Get user email - check different session structures
$user_email = '';
if (isset($_SESSION['user']['email'])) {
    $user_email = $_SESSION['user']['email'];
} elseif (isset($_SESSION['email'])) {
    $user_email = $_SESSION['email'];
} elseif (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
} else {
    // Fallback: get from database using user ID
    $user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
    if ($user_id) {
        $stmt = mysqli_prepare($conn, "SELECT email FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $user_email = $row['email'];
        }
    }
}

// Handle OTP sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    csrf_require_or_fail(); // ✅ CSRF check FIRST

    if (empty($user_email)) {
        $error = "User email not found. Please login again.";
    } else {
        try {
            sendOTP($user_email);
            $success = "OTP sent to your registered Telegram account!";
        } catch (Exception $e) {
            $error = "Failed to send OTP: " . $e->getMessage();
        }
    }
}

// Handle OTP verification and order processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    csrf_require_or_fail(); // ✅ CSRF check FIRST

    $otp = sanitize($_POST['otp']);
    
    if (empty($user_email)) {
        $error = "User email not found. Please login again.";
    } elseif (empty($otp)) {
        $error = "Please enter the OTP";
    } else {
        try {
            $verification_result = verifyOTP($user_email, $otp);
            
            if ($verification_result === "OTP verified!") {
                // Process the order now
                mysqli_begin_transaction($conn);
                
                try {
                    // Create order
                    $user_id = $_SESSION['user']['id'] ?? $_SESSION['user_id'];
                    $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) 
                            VALUES (?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "idss", 
                        $user_id, 
                        $pending_order['total'], 
                        $pending_order['shipping_address'], 
                        $pending_order['payment_method']
                    );
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to create order");
                    }
                    
                    $order_id = mysqli_insert_id($conn);
                    
                    // Transfer cart items to order_items table
                    foreach ($pending_order['cart_items'] as $item) {
                        $item_stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                    VALUES (?, ?, ?, ?)");
                        mysqli_stmt_bind_param($item_stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                        
                        if (!mysqli_stmt_execute($item_stmt)) {
                            throw new Exception("Failed to add order item");
                        }
                    }
                    
                    // Clear the user's cart
                    $clear_stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
                    mysqli_stmt_bind_param($clear_stmt, "i", $user_id);
                    
                    if (!mysqli_stmt_execute($clear_stmt)) {
                        throw new Exception("Failed to clear cart");
                    }
                    
                    // Commit the transaction
                    mysqli_commit($conn);
                    
                    // Clear pending order from session
                    unset($_SESSION['pending_order']);
                    
                    // Redirect to confirmation page
                    redirect('order_confirmation.php?id=' . $order_id, 'Order placed successfully!');
                    
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $error = "Error processing order: " . $e->getMessage();
                }
            } else {
                $error = $verification_result; // Show OTP error message
            }
        } catch (Exception $e) {
            $error = "Verification error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Verification - Bloom & Basket</title>
    <link rel="icon" type="image/png" href="image/icon.png">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/payment.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <section class="checkout-container">
        <h1 class="heading">Payment <span>Verification</span></h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="payment-verification">
            <div class="order-summary">
                <h2>Order Summary</h2>
                <p><strong>Total Amount:</strong> $<?php echo number_format($pending_order['total'], 2); ?></p>
                <p><strong>Payment Method:</strong> <?php echo ucfirst($pending_order['payment_method']); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($pending_order['shipping_address']); ?></p>
                <?php if (!empty($user_email)): ?>
                    <p><strong>Registered Email:</strong> <?php echo htmlspecialchars($user_email); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="otp-verification">
                <h2>Security Verification</h2>
                <p>To complete your order, please verify your identity using OTP.</p>
                
                <?php if (!empty($user_email)): ?>
                    <!-- Send OTP Form -->
                    <form method="POST" style="margin-bottom: 20px;">
                        <?= csrf_input() ?> <!-- ✅ CSRF hidden field -->
                        <input type="hidden" name="send_otp" value="1">
                        <input type="submit" value="Send OTP to Telegram" class="btn">
                    </form>
                    
                    <!-- Verify OTP Form -->
                    <form method="POST">
                        <?= csrf_input() ?> <!-- ✅ CSRF hidden field -->
                        <div class="form-group">
                            <label for="otp">Enter OTP from Telegram:</label>
                            <input type="text" id="otp" name="otp" class="box" placeholder="123456" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="submit" name="verify_otp" value="Verify & Complete Order" class="btn">
                        </div>
                    </form>
                    
                    <p class="note">OTP will expire in 3 minutes. If you don't receive it, click "Send OTP" again.</p>
                <?php else: ?>
                    <div class="error-message">
                        <p>Email not found. Please <a href="login.php">login again</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>
