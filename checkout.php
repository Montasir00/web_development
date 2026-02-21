<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\checkout.php
require_once __DIR__ . '/includes/init.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to proceed to checkout', 'error');
}

$error = '';
$user = getCurrentUser();

// Get cart items once at the beginning
$cart_items_for_check = getCartItems($conn);
$cart_total = getCartTotal($conn);

// Convert to array to avoid multiple queries
$cart_items_array = [];
$has_items = false;

if ($cart_items_for_check && mysqli_num_rows($cart_items_for_check) > 0) {
    $has_items = true;
    while($item = mysqli_fetch_assoc($cart_items_for_check)) {
        $cart_items_array[] = $item;
    }
}

// Process order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_require_or_fail(); // ✅ CSRF check FIRST

    // Add empty cart check here to prevent empty orders
    if (!$has_items) {
        $error = "Cannot place order with an empty cart. Please add items to your cart first.";
    } else {
        $shipping_address = sanitize($_POST['shipping_address'] ?? '');
        $payment_method = sanitize($_POST['payment_method'] ?? '');
        
        if (empty($shipping_address)) {
            $error = "Please provide a shipping address";
        } elseif (empty($payment_method)) {
            $error = "Please select a payment method";
        } else {
            // Store order data in session for OTP verification
            $order_items_array = [];
            foreach($cart_items_array as $item) {
                $order_items_array[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'name' => $item['name']
                ];
            }
            
            $_SESSION['pending_order'] = [
                'shipping_address' => $shipping_address,
                'payment_method' => $payment_method,
                'total' => $cart_total,
                'cart_items' => $order_items_array,
                'user_info' => [
                    'name' => sanitize($_POST['name'] ?? ''),
                    'email' => sanitize($_POST['email'] ?? ''),
                    'phone' => sanitize($_POST['phone'] ?? '')
                ]
            ];
            
            // Redirect to payment verification page
            header('Location: payment.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Checkout - Bloom & Basket">
    <title>Checkout - Bloom & Basket</title>
    <!-- icon -->
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/checkout.css">
</head>
<body>
    <!-- Header Section -->
    <?php include 'includes/header.php'; ?>
    
    <section class="checkout-container">
        <h1 class="heading">Check<span>out</span></h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php displayMessage(); ?>
        
        <div class="checkout-wrapper">
            <div class="checkout-cart">
                <h2>Order Summary</h2>
                
                <div class="cart-items">
                    <?php 
                    if($has_items):
                        foreach($cart_items_array as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                            <p class="subtotal">$<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    else:
                    ?>
                    <div class="empty-cart-message">
                        <p>Your cart is empty. <a href="index.php">Continue Shopping</a></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="cart-total">
                    <h3>Total: $<?php echo number_format($cart_total, 2); ?></h3>
                </div>
            </div>
            
            <div class="checkout-form">
                <h2>Shipping Information</h2>
                
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <?= csrf_input() ?> <!-- ✅ CSRF hidden field -->

                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" class="box" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" class="box" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" class="box" required>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" class="box" rows="3" required><?php echo isset($user['address']) ? htmlspecialchars($user['address']) : ''; ?></textarea>
                    </div>
                        
                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" id="credit_card" name="payment_method" value="credit_card" required>
                                <label for="credit_card">
                                    <i class="fa fa-credit-card"></i> Credit Card
                                </label>
                            </div>
                            
                            <div class="payment-option">
                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                <label for="paypal">
                                    <i class="fa fa-paypal"></i> PayPal
                                </label>
                            </div>
                            
                            <div class="payment-option">
                                <input type="radio" id="cash_on_delivery" name="payment_method" value="cash_on_delivery">
                                <label for="cash_on_delivery">
                                    <i class="fa fa-money"></i> Cash on Delivery
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="security-notice">
                        <p><i class="fa fa-shield"></i> <strong>Security Notice:</strong> After clicking "Place Order", you will be redirected to verify your identity via OTP for secure transaction processing.</p>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="Place Order" class="btn" <?php echo !$has_items ? 'disabled' : ''; ?>>
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Footer Section -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>
