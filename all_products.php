<?php
require_once __DIR__ . '/includes/init.php'; // Corrected path to init.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Bloom & Basket - All Products">
    <title>All Products - Bloom & Basket</title>
    <link rel="icon" type="image/png" href="image/icon.png">
    <!-- Font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- CSS -->
    <!-- Global CSS first -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- Page-specific CSS second -->
    <link rel="stylesheet" type="text/css" href="css/all_products.css">
</head>
<body>
<?php include(__DIR__ . '/includes/header.php'); ?> <!-- Include header -->

<section class="all-products" id="all-products">
    <h1 class="heading">All <span>Products</span></h1>
    <div class="product-container">
    <?php
    // Fetch all products from the database
    $query = "SELECT * FROM products";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0):
        while ($product = mysqli_fetch_assoc($result)):
    ?>
            <div class="product-card card">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
                <div class="rating-stars">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($product['rating'])) {
                            echo '<i class="fa fa-star"></i>';
                        } elseif ($i - $product['rating'] < 1) {
                            echo '<i class="fa fa-star-half"></i>';
                        } else {
                            echo '<i class="fa fa-star-o"></i>';
                        }
                    }
                    ?>
                </div>
                <!-- Add to Cart Button -->
                <button class="add_to_cart_btn  btn" data-product-id="<?php echo $product['id']; ?>" data-quantity="1">Add to Cart</button>
            </div>
    <?php
        endwhile;
    else:
        echo "<p>No products found.</p>";
    endif;
    ?>
    </div>
</section>

<?php include(__DIR__ . '/includes/footer.php'); ?> <!-- Include footer -->
</body>
</html>