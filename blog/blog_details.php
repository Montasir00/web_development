<?php
require_once __DIR__ . '/../includes/init.php'; 

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid blog ID.');
}

$blog_id = (int)$_GET['id'];

// Fetch the blog details from the database
$sql = "SELECT * FROM blogs WHERE id = $blog_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die('Blog not found.');
}

$blog = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="description" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Blog Details</title>

</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?> 

    <section class="blog-details">
        <div class="container">
            <h1><?php echo htmlspecialchars($blog['title']); ?></h1>
            <div class="blog-meta">
                <span><i class="fa fa-user"></i> By <?php echo htmlspecialchars($blog['author']); ?></span>
                <span><i class="fa fa-calendar"></i> <?php echo date('jS F, Y', strtotime($blog['created_at'])); ?></span>
            </div>
            <img src="/<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="blog-image">
            <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
        </div>
    </section>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>