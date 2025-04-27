<?php
require_once __DIR__ . '/includes/init.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Access control: only admins allowed
if (!isLoggedIn() || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    // Optional: set a flash message or error
    redirect('index.php', 'Access denied.');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Admin Dashboard - Bloom & Basket">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <section class="admin-dashboard">
        <h1 class="heading">Welcome, <span><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span></h1>
        <p>Here you can manage products, users, and more.</p>
        <div class="admin-actions">
            <a href="products/manage_products.php" class="btn">Manage Products</a>
            <a href="users/manage_users.php" class="btn">Manage Users</a>
        </div>
    </section>
    
    <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
