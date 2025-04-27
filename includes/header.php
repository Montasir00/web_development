<?php
require_once __DIR__ . '/init.php'; 
$cart_count = 0;
if (isLoggedIn()) {
    $cart_items = getCartItems($conn);
    if ($cart_items) {
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $cart_count += $item['quantity'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bloom & Basket - Fresh and Organic Products delivered to your doorstep!">
    <title>Bloom & Basket</title>
    <!-- Icon -->
    <link rel="icon" type="image/png" href="/icon.png">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/manage_products.css">
    <link rel="stylesheet" type="text/css" href="../css/add_products.css">
    <link rel="stylesheet" type="text/css" href="../css/add_new_user.css">
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/script.js" defer></script>
</head>
<body> 

    <!-- Header Section -->
    <header class="header">
        <a href="/index.php" class="logo">
            <i class="fa fa-shopping-basket"></i> Bloom & Basket
        </a>

        <nav class="navbar">
            <a href="/index.php">Home</a>
            <a href="/index.php#features">Features</a>
            <a href="/index.php#products">Products</a>
            <a href="/index.php#reviews">Review</a>
            <a href="/index.php#blogs">Blogs</a>
        </nav>  

        <div class="icons">
            <div class="fa fa-bars" id="menu-btn"></div>
            <div class="fa fa-search" id="search-btn"></div>
            <div class="fa fa-shopping-cart" id="cart-btn">
                <span id="cart-count"><?php echo $cart_count; ?></span>
            </div>

            <?php if (isLoggedIn() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                <!-- Admin Dashboard Button -->
                <div class="fa fa-tachometer" id="dashboard-btn" onclick="window.location.href='/admin_dashboard.php'"></div>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <!-- Logout Link -->
                <div class="fa fa-sign-out" id="logout-btn" onclick="window.location.href='/includes/logout.php'"></div>
            <?php else: ?>
                <!-- Login Button -->
                <div class="fa fa-user" id="login-btn"></div>
            <?php endif; ?>
        </div>

        <form class="search-form">
            <input type="search" id="search-box" placeholder="Search here ...">
            <label for="search-box" class="fa fa-search"></label>
        </form>

        <div class="shopping-cart">
            <div class="cart-items">
                <!-- Cart items will be dynamically managed by JavaScript -->
                <i class="fa fa-trash-o remove-from-cart-btn" data-cart-id="item.id"></i>
            </div>
            <div class="total">Total: $0.00</div>
            <a href="/checkout.php" class="btn">Checkout</a>
        </div>

        <?php if (!isLoggedIn()): ?>
        <form action="/includes/login_handler.php" method="POST" class="login-form">
            <h3>Login now</h3>
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_SESSION['login_error']); ?></div>
                <?php unset($_SESSION['login_error']); // Clear the error after displaying ?>
            <?php endif; ?>
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
            <input type="password" name="password" placeholder="Enter your password" class="box" required>
            <p>Forgot your Password? 
               <a href="/forgot_password.php">Click Here</a>
            </p>
            <p>Don't have an Account? 
               <a href="/register.php">Create Now</a>
            </p>
            <input type="submit" name="login" value="Login Now" class="btn">
        </form>
        <?php endif; ?>
    </header>
    <!-- Header Section End -->
</body>
</html>
