<?php
require_once __DIR__ . '/includes/init.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "
-- Drop tables if they exist (for clean re-import)
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart;
DROP TABLE IF EXISTS otp_storage;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS blogs;
DROP TABLE IF EXISTS features;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    telegram_chat_id VARCHAR(64),
    address VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- OTP storage table
CREATE TABLE otp_storage (
    username VARCHAR(255) NOT NULL PRIMARY KEY,
    otp VARCHAR(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(100),
    stock INT DEFAULT 0,
    rating FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    rating FLOAT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blogs table
CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Features table
CREATE TABLE features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    shipping_address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Indexes for performance
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_cart_product ON cart(product_id);
CREATE INDEX idx_order_user ON orders(user_id);
CREATE INDEX idx_orderitem_order ON order_items(order_id);
CREATE INDEX idx_orderitem_product ON order_items(product_id);
CREATE INDEX idx_product_name ON products(name);
CREATE INDEX idx_review_name ON reviews(name);
CREATE INDEX idx_blog_title ON blogs(title);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Sample users
INSERT INTO users (name, email, password, role, address, phone) VALUES
('Admin', 'admin@bloombasket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Admin HQ', '+1122334455'),
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '123 Main St, Anytown', '+1234567890'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '456 Elm St, Somewhere', '+0987654321');

-- Sample Products
INSERT INTO products (name, description, price, image, category, stock, rating) VALUES
('Fresh Orange', 'Sweet and juicy oranges, perfect for your daily vitamin C boost.', 12.99, 'image/orange.jpg', 'fruits', 50, 4.5),
('Fresh Onion', 'Premium quality onions to add flavor to all your dishes.', 8.99, 'image/onion-2.jpg', 'vegetables', 100, 4.5),
('Fresh Meat', 'Tender and fresh meat cuts from grass-fed livestock.', 18.99, 'image/meat.jpg', 'meat', 30, 4.5),
('Watermelon', 'Refreshing and sweet watermelon, perfect for hot summer days.', 5.99, 'image/watermelon.jpg', 'fruits', 40, 4.2),
('Chicken', 'Free-range chicken, raised without antibiotics.', 4.50, 'image/chicken.jpg', 'meat', 45, 4.7);

-- Sample Features
INSERT INTO features (title, description, image) VALUES
('Farm-Fresh Goodness', 'Discover our hand-picked, organic vegetables that bring nature\'s bounty straight from the farm to your table.', 'image/feature-img-1.jpg'),
('Easy Payment', 'Enjoy a seamless, secure checkout experience with our hassle-free payment options designed to make shopping simple and stress-free.', 'image/feature-img-2.jpg'),
('Free Delivery', 'Get your fresh, organic produce delivered right to your door at no extra cost – saving you time and ensuring your meals stay as vibrant as ever.', 'image/feature-img-3.jpg');

-- Sample Reviews
INSERT INTO reviews (name, image, rating, comment) VALUES
('John Doe', 'image/pic-1.jpg', 4.5, 'This grocery shop always provides fresh items. Highly recommended!'),
('Jane Smith', 'image/pic-2.jpg', 5.0, 'Excellent customer service and very quick delivery. Loved the quality!'),
('Ali Khan', 'image/pic-3.jpg', 3.5, 'The fruits were fresh but packaging can be improved. Overall good!');

-- Sample Blogs
INSERT INTO blogs (title, author, image, content) VALUES
('Fresh and Organic Vegetables', 'Admin', 'image/blog-1.jpg', 'Discover the benefits of incorporating fresh, organic vegetables into your diet and how they contribute to a healthier lifestyle.'),
('Top 10 Superfoods for Your Immune System', 'Admin', 'image/blog-2.jpg', 'Boost your immune system naturally with these top superfoods that are packed with nutrients, antioxidants, and health benefits.'),
('How to Start Your Own Organic Garden', 'Admin', 'image/blog-3.jpg', 'Learn easy tips and tricks for starting your own organic garden at home and enjoy fresh, chemical-free produce year-round.');

-- Sample Cart
INSERT INTO cart (user_id, product_id, quantity) VALUES
(1, 1, 2),
(1, 3, 1),
(2, 2, 3);
";

if (!empty(trim($sql))) {
    if (mysqli_multi_query($conn, $sql)) {
        do {
            if ($result = mysqli_store_result($conn)) {
                mysqli_free_result($result);
            }
        } while (mysqli_next_result($conn));
        echo "Multi-query executed successfully.";
    } else {
        echo "Error executing multi-query: " . mysqli_error($conn);
    }
} else {
    echo "No SQL to execute.";
}

mysqli_close($conn);