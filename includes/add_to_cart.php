<?php
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

// Validate product exists
$product_result = getProductById($conn, $product_id);
$product = mysqli_fetch_assoc($product_result); // Fetch the single row

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// Check if product is already in cart
$sql = "SELECT id, quantity FROM cart WHERE user_id = $user_id AND product_id = $product_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        // Update quantity
        $cart_item = mysqli_fetch_assoc($result);
        $new_quantity = $cart_item['quantity'] + $quantity;

        $sql = "UPDATE cart SET quantity = $new_quantity WHERE id = {$cart_item['id']}";
        if (!mysqli_query($conn, $sql)) {
            echo json_encode(['success' => false, 'message' => 'Error updating cart.']);
            exit;
        }
    } else {
        // Add new item to cart
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        if (!mysqli_query($conn, $sql)) {
            echo json_encode(['success' => false, 'message' => 'Error adding to cart.']);
            exit;
        }
    }

    // Get updated cart count
    $cart_count_result = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = $user_id");
    if ($cart_count_result) {
        $cart_data = mysqli_fetch_assoc($cart_count_result);
        $cart_count = $cart_data['total'] ?? 0; // Use null coalescing operator to handle potential null
        echo json_encode(['success' => true, 'cart_count' => $cart_count]);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching cart count.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error checking cart.']);
    exit;
}

exit;
?>
