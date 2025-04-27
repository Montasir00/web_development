<?php
require_once '../includes/init.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Delete product
$sql = "DELETE FROM products WHERE id = $id";
if (mysqli_query($conn, $sql)) {
    redirect('manage_products.php', 'Product deleted successfully.');
} else {
    redirect('manage_products.php', 'Failed to delete product.');
}
?>