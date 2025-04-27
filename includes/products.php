<section class="products" id="products">
    <h1 class="heading">Our <span>Products</span></h1>
    <div class="product-container">
    <?php
    // Fetch products dynamically
    $products = getProducts($conn, 6); // Adjust limit as necessary
    if (mysqli_num_rows($products) > 0):
        while ($product = mysqli_fetch_assoc($products)):
    ?>
            <div class="product-card card">
                <!-- Use the image file path stored in the database -->
                <img class="img-card-style" src="<?php echo file_exists($product['image']) ? htmlspecialchars($product['image']) : 'image/default.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
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
                <a href="all_products.php" class="btn">See all products</a>
            </div>
    <?php
        endwhile;
    else:
        echo "<p>No products found.</p>";
    endif;
    ?>
    </div>
</section>