<?php
require_once 'db.php';
require_once 'includes/functions.php';

// Check if user is logged in
if(!isLoggedIn()) {
    redirect('login.php', 'Please login to proceed to checkout', 'error');
}

$error = '';
$user = getCurrentUser();
$cart_items = getCartItems();
$cart_total = getCartTotal();

// Check if cart is empty
if(!$cart_items || mysqli_num_rows($cart_items) == 0) {
    redirect('index.php', 'Your cart is empty', 'error');
}

// Process order submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = sanitize($_POST['shipping_address']);
    $payment_method = sanitize($_POST['payment_method']);
    
    if(empty($shipping_address)) {
        $error = "Please provide a shipping address";
    } elseif(empty($payment_method)) {
        $error = "Please select a payment method";
    } else {
        // Create order
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO orders (user_id, total_amount, shipping_address, payment_method) 
                VALUES ($user_id, $cart_total, '$shipping_address', '$payment_method')";
        
        if(mysqli_query($conn, $sql)) {
            $order_id = mysqli_insert_id($conn);
            
            // Add order items
            $cart_items = getCartItems(); // Refresh cart items
            while($item = mysqli_fetch_assoc($cart_items)) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES ($order_id, $product_id, $quantity, $price)";
                mysqli_query($conn, $sql);
            }
            
            // Clear cart
            $sql = "DELETE FROM cart WHERE user_id = $user_id";
            mysqli_query($conn, $sql);
            
            // Redirect to order confirmation
            redirect('order_confirmation.php?id=' . $order_id, 'Order placed successfully!');
        } else {
            $error = "Error placing order: " . mysqli_error($conn);
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
</head>
<body>
    <!-- Header Section (You can include it using PHP include) -->
    <?php include 'includes/header.php'; ?>
    
    <section class="checkout-container">
        <h1 class="heading">Check<span>out</span></h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="checkout-wrapper">
            <div class="checkout-cart">
                <h2>Order Summary</h2>
                
                <div class="cart-items">
                    <?php 
                    $cart_items = getCartItems(); // Refresh cart items
                    if($cart_items && mysqli_num_rows($cart_items) > 0):
                        while($item = mysqli_fetch_assoc($cart_items)):
                            $subtotal = $item['price'] * $item['quantity'];
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                        <div class="item-details">
                            <h3><?php echo $item['name']; ?></h3>
                            <p class="price">$<?php echo number_format($item['price'], 2); ?> x <?php echo $item['quantity']; ?></p>
                            <p class="subtotal">$<?php echo number_format($subtotal, 2); ?></p>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </div>
                
                <div class="cart-total">
                    <h3>Total: $<?php echo number_format($cart_total, 2); ?></h3>
                </div>
            </div>
            
            <div class="checkout-form">
                <h2>Shipping Information</h2>
                
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" class="box" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" class="box" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" class="box" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" class="box" rows="3" required><?php echo $user['address']; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" id="credit_card" name="payment_method" value="credit_card" required>
                                <label for="credit_card">Credit Card</label>
                            </div>
                            
                            <div class="payment-option">
                                <input type="radio" id="paypal" name="payment_method" value="paypal">
                                <label for="paypal">PayPal</label>
                            </div>
                            
                            <div class="payment-option">
                                <input type="radio" id="cash_on_delivery" name="payment_method" value="cash_on_delivery">
                                <label for="cash_on_delivery">Cash on Delivery</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <input type="submit" value="Place Order" class="btn">
                    </div>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Footer Section (You can include it using PHP include) -->
    <?php include 'includes/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="js/script.js"></script>
</body>
</html>