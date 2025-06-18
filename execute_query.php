<?php
require_once __DIR__ . '/includes/init.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the 'total_amount' column exists in the 'cart' table
$check_column_sql = " ";

if (mysqli_multi_query($conn, $create_trigger_sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    echo "Trigger created successfully.";
} else {
    echo "Error creating trigger: " . mysqli_error($conn);
}

mysqli_close($conn);