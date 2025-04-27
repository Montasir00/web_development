<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '../includes/init.php';
// Only allow admins
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Fetch users from the database
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<body>
    <?php include '../includes/header.php'; ?>

    <section class="manage-products">
        <h1>Manage Users</h1>
        <a href="add_new_user.php" class="btn">Add New User</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo isset($row['role']) ? htmlspecialchars($row['role']) : 'user'; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
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
