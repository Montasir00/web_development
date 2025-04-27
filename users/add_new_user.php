<?php
require_once __DIR__ . '/../includes/init.php';

error_reporting(E_ALL);
ini_set('display_errors', 1); // Keep enabled for debugging
ini_set('log_errors', 1);

// Handle POST requests first
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Only for JSON responses

    try {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to add a new user.']);
            exit;
        }

        // Get JSON data
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);

        // If no JSON, check standard form data
        if (!$data) {
            $data = $_POST;
        }

    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
    exit; // Stop execution after POST
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include '../includes/header.php'; ?>

<body>
    <section class="add-user">
        <h1 class="heading">Add New <span>User</span></h1>
        <form id="userForm" method="POST">
            <input type="text" name="name" placeholder="User Name" required>
            <input type="email" name="email" placeholder="User Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" class="btn">Add User</button>
        </form>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>