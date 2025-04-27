<?php
// Fetch products from the database
function getProducts($conn, $limit = 3) {
    $sql = "SELECT * FROM products ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Fetch reviews from the database
function getReviews($conn, $limit = 3) {
    $sql = "SELECT * FROM reviews ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Fetch blog posts from the database
function getBlogs($conn, $limit = 3) {
    $sql = "SELECT * FROM blogs ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    return $result;
}

function getFeatures($conn, $limit = 3) {
    $sql = "SELECT * FROM features ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    return $result;
}


// Get cart items for the logged-in user
function getCartItems($conn) {
    if (isset($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];
        $sql = "SELECT c.*, p.name, p.price, p.image 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = $user_id"; // Filter by logged-in user
        $result = mysqli_query($conn, $sql);
        return $result;
    }
    return false;
}

// Calculate the cart total for the logged-in user
function getCartTotal($conn) {
    $total = 0;
    $cart_items = getCartItems($conn);
    if($cart_items) {
        while($item = mysqli_fetch_assoc($cart_items)) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}
function isLoggedIn() {
    return isset($_SESSION['user']['id']);
}

function displayMessage() {
    if (isset($_SESSION['message'])) {
        echo '<div class="message">' . $_SESSION['message'] . '</div>';
        unset($_SESSION['message']); // Clear the message after displaying
    }
}
function redirect($url, $message = '') {
    if (!headers_sent()) { // Check if headers have already been sent
        if (!empty($message)) {
            $_SESSION['message'] = $message;
        }
        header("Location: $url");
        exit;
    } else {
        echo "<script>window.location.href='$url';</script>";
        exit;
    }
}


function sanitize($data) {
    global $conn; // Use the database connection for escaping
    $data = trim($data); // Remove extra spaces
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special characters to HTML entities
    return mysqli_real_escape_string($conn, $data); // Escape special characters for SQL
}



function getProductById($conn, $product_id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result; // Return the mysqli_result object
}
?>
