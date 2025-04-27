<?php
// Start the session here before including any other files.
session_start();

// Include configuration and functions (if needed across pages)
require_once __DIR__ . '/includes/init.php'; // Corrected path to init.php
// Process login logic if form was submitted
// (You can also move this logic to a separate file like auth/process_login.php)
// (Optional: include 'auth/process_login.php';)

// Now include the page sections:
include 'includes/header.php';
include 'includes/banner.php';
include 'includes/features.php';
include 'includes/products.php';
include 'includes/cta.php';
include 'includes/reviews.php';
include 'includes/blogs.php';
include 'includes/footer.php';
?>
