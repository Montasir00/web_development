<?php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage your cart']);
    exit;
}

// Get and decode JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate JSON payload
if (!$data || !isset($data['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$cart_id = (int)$data['cart_id'];
$user_id = $_SESSION['user_id'];

// Make sure the cart item belongs to the logged-in user
$sql = "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id";

if (mysqli_query($conn, $sql)) {
    // Calculate the new cart total
    $total_query = mysqli_query($conn, "SELECT SUM(c.quantity * p.price) AS total 
                                      FROM cart c 
                                      JOIN products p ON c.product_id = p.id 
                                      WHERE c.user_id = $user_id");
    $cart_total = mysqli_fetch_assoc($total_query)['total'] ?? 0;
    
    // Get updated cart count
    $cart_count = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = $user_id");
    $cart_count = mysqli_fetch_assoc($cart_count)['total'] ?? 0;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Item removed from cart successfully',
        'cart_total' => (float)$cart_total,
        'cart_count' => (int)$cart_count
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error removing item: ' . mysqli_error($conn)
    ]);
}
exit;
?>