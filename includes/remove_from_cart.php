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
$user_id = $_SESSION['user']['id'];  

// Make sure the cart item belongs to the logged-in user - use prepared statement
$stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    // Calculate the new cart total - use prepared statement
    $total_stmt = mysqli_prepare($conn, 
        "SELECT 
            SUM(c.quantity * p.price) AS total,
            SUM(c.quantity) AS count
         FROM cart c 
         JOIN products p ON c.product_id = p.id 
         WHERE c.user_id = ?"
    );
    mysqli_stmt_bind_param($total_stmt, "i", $user_id);
    mysqli_stmt_execute($total_stmt);
    $result = mysqli_stmt_get_result($total_stmt);
    $row = mysqli_fetch_assoc($result);
    
    $cart_total = $row['total'] ?? 0;
    $cart_count = $row['count'] ?? 0;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Item removed from cart successfully',
        'cart_total' => (float)$cart_total,
        'cart_count' => (int)$cart_count
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Error removing item from cart'
    ]);
}
exit;
?>