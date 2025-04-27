<?php
require_once __DIR__ . '/init.php'; 

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php', 'Please login to manage your cart', 'error');
}

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    if ($quantity > 0) {
        // Update the quantity in the cart
        $sql = "UPDATE cart SET quantity = $quantity WHERE id = $cart_id AND user_id = $user_id";
        mysqli_query($conn, $sql);
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php', 'Cart updated successfully');
    } else {
        // Remove the item if quantity is set to 0
        $sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";
        mysqli_query($conn, $sql);
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php', 'Item removed from cart successfully');
    }
} else {
    redirect('index.php', 'Invalid request', 'error');
}
?>