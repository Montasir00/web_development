<?php
require_once '../includes/init.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get product ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product details
$sql = "SELECT * FROM products WHERE id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    redirect('manage_products.php', 'Product not found.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $price = sanitize($_POST['price']);
    $image = sanitize($_POST['image']); // Assume image is a URL or path

    if (empty($name) || empty($price) || empty($image)) {
        $error = "All fields are required.";
    } else {
        $sql = "UPDATE products SET name = '$name', price = '$price', image = '$image' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            redirect('manage_products.php', 'Product updated successfully.');
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Edit Product - Admin">
    <title>Edit Product</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/edit_products.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <section class="edit-product">
       <h1 class="heading">Edit <span>Product</span></h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            <input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" required>
            <button type="submit" class="btn">Update Product</button>
        </form>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>