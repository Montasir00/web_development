<?php
// filepath: c:\Users\fazlu\OneDrive\Desktop\projects\web_developmet\includes\add_to_cart.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/init.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart.']);
    exit;
}

// Decode JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate JSON payload
if (!$data || !isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity = (int)$data['quantity'];

$user_id = $_SESSION['user']['id'];

// Validate product exists - assuming getProductById uses prepared statements
// If not, replace with a direct prepared statement query
$product_result = getProductById($conn, $product_id);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// Check if product is already in cart - use prepared statement
$check_stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $product_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        // Update quantity - use prepared statement
        $cart_item = mysqli_fetch_assoc($result);
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        $update_stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = ? WHERE id = ?");
        mysqli_stmt_bind_param($update_stmt, "ii", $new_quantity, $cart_item['id']);
        
        if (!mysqli_stmt_execute($update_stmt)) {
            echo json_encode(['success' => false, 'message' => 'Error updating cart.']);
            exit;
        }
    } else {
        // Add new item to cart - use prepared statement
        $insert_stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($insert_stmt, "iii", $user_id, $product_id, $quantity);
        
        if (!mysqli_stmt_execute($insert_stmt)) {
            echo json_encode(['success' => false, 'message' => 'Error adding to cart.']);
            exit;
        }
    }

    // Get updated cart count and total in one query - use prepared statement
    $total_stmt = mysqli_prepare($conn, 
        "SELECT 
            SUM(c.quantity) AS item_count,
            SUM(c.quantity * p.price) AS cart_total
         FROM cart c 
         JOIN products p ON c.product_id = p.id 
         WHERE c.user_id = ?"
    );
    mysqli_stmt_bind_param($total_stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($total_stmt)) {
        $result = mysqli_stmt_get_result($total_stmt);
        $cart_data = mysqli_fetch_assoc($result);
        
        $cart_count = $cart_data['item_count'] ?? 0;
        $cart_total = $cart_data['cart_total'] ?? 0;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Item added to cart successfully',
            'cart_count' => (int)$cart_count,
            'cart_total' => (float)$cart_total
        ]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error calculating cart totals.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error checking cart.']);
    exit;
}
?>