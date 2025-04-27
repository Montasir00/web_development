<section class="reviews" id="reviews">
    <h1 class="heading">Customer's <span>Reviews</span></h1>
    <div class="review-container">
    <?php
    $reviews = getReviews($conn, 3);
    if (mysqli_num_rows($reviews) > 0):
        while ($review = mysqli_fetch_assoc($reviews)):
    ?>
            <div class="review-card card">
                <img  class="img-card-style" src="<?php echo htmlspecialchars($review['image']); ?>" alt="<?php echo htmlspecialchars($review['name']); ?>">
                <h1><?php echo htmlspecialchars($review['name']); ?></h1>
                <div class="rating-stars">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($review['rating'])) {
                            echo '<i class="fa fa-star"></i>';
                        } elseif ($i - $review['rating'] < 1) {
                            echo '<i class="fa fa-star-half"></i>';
                        } else {
                            echo '<i class="fa fa-star-o"></i>';
                        }
                    }
                    ?>
                </div>
                <p><?php echo htmlspecialchars($review['comment']); ?></p>
            </div>
    <?php
        endwhile;
    else:
        echo "<p>No reviews found.</p>";
    endif;
    ?>
    </div>
</section>
