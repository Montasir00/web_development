<?php
// Disable error display
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/init.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$sql = "SELECT c.*, p.name, p.price, p.image, p.id AS product_id
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$cart_items = [];
$total_price = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $cart_items[] = $row;
    $total_price += $row['price'] * $row['quantity'];
}

mysqli_stmt_close($stmt);

echo json_encode(['items' => $cart_items, 'total_price' => $total_price]);
exit;
?>
