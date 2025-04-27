<?php
require_once '../includes/init.php';

// Fetch products from the database
$query = "SELECT * FROM products";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Manage Products - Admin">
    <title>Manage Products</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" type="text/css" href="../css/manage_products.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <section class="manage-products">
        <h1>Manage Products</h1>
        <a href="add_products.php" class="btn">Add New Product</a>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                        <a href="edit_products.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                        <a href="delete_products.php?id=<?php echo $row['id']; ?>" class="btn">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
<?php
$conn->close(); // Close the database connection
?>