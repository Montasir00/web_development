<?php
require_once __DIR__ . '/../includes/init.php';
// Ensure user is admin and logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add a new user.']);
    exit;
}

// Handle form submission for adding products
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $image = $_POST['image'] ?? '';
        $category = $_POST['category'] ?? '';
        $stock = (int)($_POST['stock'] ?? 0);
        $rating = (float)($_POST['rating'] ?? 0);

        // Validate inputs
        if (empty($name) || empty($description) || $price <= 0 || empty($image) || empty($category) || $stock < 0 || $rating < 0 || $rating > 5) {
            throw new Exception('Invalid input data.');
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, category, stock, rating) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssid", $name, $description, $price, $image, $category, $stock, $rating);
        $stmt->execute();
        $stmt->close();

        header('Location: manage_products.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/add_products.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <section class="add-product">
        <h1 class="heading">Add New <span>Product</span></h1>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <input type="text" name="image" placeholder="Image URL" required>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="fruits">Fruits</option>
                <option value="vegetables">Vegetables</option>
                <option value="meat">Meat</option>
                <option value="dairy">Dairy</option>
            </select>
            <input type="number" name="stock" placeholder="Stock" required>
            <input type="number" step="0.1" name="rating" placeholder="Rating" min="0" max="5" required>
            <button type="submit" class="btn">Add Product</button>
        </form>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>