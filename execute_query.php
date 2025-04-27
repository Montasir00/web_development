<?php
require_once('db.php');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "UPDATE products SET name = 'Unnamed Product' WHERE name IS NULL;
UPDATE products SET description = 'No description available.' WHERE description IS NULL;
UPDATE products SET image = 'default.jpg' WHERE image IS NULL;";

if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    echo "All queries executed successfully.";
} else {
    echo "Error executing queries: " . mysqli_error($conn);
}

mysqli_close($conn);
?>