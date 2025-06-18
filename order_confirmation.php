<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\order_confirmation.php
require_once __DIR__ . '/includes/init.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to view your order', 'error');
}

// Check if order ID is provided
if (!isset($_GET['id'])) {
    redirect('index.php', 'Invalid order reference', 'error');
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user']['id'];

// Get order details
$order_sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

// Verify order exists and belongs to logged-in user
if (!$order_result || mysqli_num_rows($order_result) == 0) {
    redirect('index.php', 'Order not found', 'error');
}

$order = mysqli_fetch_assoc($order_result);
$order_date = date('F j, Y', strtotime($order['created_at'] ?? date('Y-m-d H:i:s')));

// Get order items
$items_sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$items_stmt = mysqli_prepare($conn, $items_sql);
mysqli_stmt_bind_param($items_stmt, "i", $order_id);
mysqli_stmt_execute($items_stmt);
$items_result = mysqli_stmt_get_result($items_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Order Confirmation - Bloom & Basket">
    <title>Order Confirmation - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/order_confirmation.css">
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>
    
    <div class="confirmation-container">
        <i class="fa fa-check-circle confirmation-icon" aria-hidden="true"></i>
        <h1 class="heading">Order <span>Confirmed</span>!</h1>
        
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="success-message"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        
        <p>Thank you for your order. We've received your request and will process it shortly.</p>
        
        <div class="order-details">
            <div class="order-info">
                <h2>Order Information</h2>
                <p><strong>Order Number:</strong> #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Order Date:</strong> <?php echo $order_date; ?></p>
                <p><strong>Payment Method:</strong> <?php echo ucfirst(htmlspecialchars($order['payment_method'] ?? 'Not specified')); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address'] ?? 'Not specified'); ?></p>
            </div>
            
            <h2>Order Summary</h2>
            <div class="item-list">
            <?php 
            if($items_result && mysqli_num_rows($items_result) > 0): 
                while($item = mysqli_fetch_assoc($items_result)): 
                    $subtotal = $item['price'] * $item['quantity'];
            ?>
                <div class="order-item">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="item-price">
                        $<?php echo number_format($subtotal, 2); ?>
                    </div>
                </div>
            <?php 
                endwhile; 
            else: 
            ?>
                <p>No items found for this order.</p>
            <?php endif; ?>
            </div>
                        
            <div style="text-align: right; margin-top: 2rem;">
                <h3 style="color: var(--primary-green, #27ae60);">Total: $<?php echo number_format($order['total_amount'] ?? 0, 2); ?></h3>
            </div>
        </div>
        
        <p>A confirmation email has been sent to your registered email address.</p>
        
        <a href="index.php" class="return-button">Continue Shopping</a>
    </div>
    
    <!-- Footer Section -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>