-- Create and use the database
CREATE DATABASE IF NOT EXISTS bloom_basket;
USE bloom_basket;

-- Users table
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        address TEXT,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    rating DECIMAL(3, 1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Features table
CREATE TABLE IF NOT EXISTS features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    rating DECIMAL(3, 1) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255) NOT NULL,
    author VARCHAR(255) DEFAULT 'Admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Newsletter subscribers
CREATE TABLE IF NOT EXISTS subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
-- Sample Users (password is "password123" hashed)
INSERT INTO users (name, email, password, address, phone) VALUES
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main St, Anytown', '+1234567890'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Elm St, Somewhere', '+0987654321');

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
INSERT INTO blogs (title, content, image, author, created_at) VALUES
('Fresh and Organic Vegetables', 'Discover the benefits of incorporating fresh, organic vegetables into your diet and how they contribute to a healthier lifestyle.', 'image/blog-1.jpg', 'Admin', '2025-03-15'),
('Top 10 Superfoods for Your Immune System', 'Boost your immune system naturally with these top superfoods that are packed with nutrients, antioxidants, and health benefits.', 'image/blog-2.jpg', 'Admin', '2025-03-12'),
('How to Start Your Own Organic Garden', 'Learn easy tips and tricks for starting your own organic garden at home and enjoy fresh, chemical-free produce year-round.', 'image/blog-3.jpg', 'Admin', '2025-03-10');
</antA


-- Add some sample data to cart
INSERT INTO cart (user_id, product_id, quantity) VALUES
(1, 1, 2),
(1, 3, 1),
(2, 2, 3);

-- Add indexes for better performance
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_orders_user ON orders(user_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Create an admin user if needed
INSERT INTO users (name, email, password, address, phone) VALUES
('Admin User', 'admin@bloombasket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Office, Bloom & Basket HQ', '+1122334455');