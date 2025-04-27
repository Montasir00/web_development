<section class="blogs" id="blogs">
    <h1 class="heading">Our <span>Blogs</span></h1>
    <div class="box-container">
    <?php
    $blogs = getBlogs($conn, 3);
    if (mysqli_num_rows($blogs) > 0):
        while ($blog = mysqli_fetch_assoc($blogs)):
    ?>
            <div class="box card">
            <img class="img-card-style" src="<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                <div class="content">
                    <div class="icons">
                        <a href="#"><i class="fa fa-user"></i> By <?php echo htmlspecialchars($blog['author']); ?></a>
                        <a href="#"><i class="fa fa-calendar"></i> <?php echo date('jS F, Y', strtotime($blog['created_at'])); ?></a>
                    </div>
                    <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                    <p><?php echo substr(htmlspecialchars($blog['content']), 0, 150); ?>...</p>
                    <a href="blog_details.php?id=<?php echo $blog['id']; ?>" class="btn">Read More</a>
                </div>
            </div>
    <?php
        endwhile;
    else:
        echo "<p>No blog posts found.</p>";
    endif;
    ?>
    </div>
</section>

