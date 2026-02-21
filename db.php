<?php
$servername = "db";
$username   = "bloomuser";
$password   = "bloompassword";
$dbname     = "bloom_basket";

$maxRetries = 10;
$connected = false;

while ($maxRetries--) {
    $conn = @mysqli_connect($servername, $username, $password, $dbname);
    if ($conn) {
        $connected = true;
        break;
    }
    echo "Waiting for MySQL... retries left: $maxRetries\n";
    sleep(2); // wait 2 seconds before retry
}

if (!$connected) {
    die("Connection failed after multiple attempts: " . mysqli_connect_error());
}
?>

