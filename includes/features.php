<!-- Feature Section Start -->
<section class="features" id="features">
    <h1 class="heading">Our <span>Features</span></h1>

    <div class="box-container">
        <?php
        $features = getFeatures($conn); 
        if (mysqli_num_rows($features) > 0):
            while ($feature = mysqli_fetch_assoc($features)):
        ?>
            <div class="box card">
                <img class="img-card-style" src="<?php echo htmlspecialchars($feature['image']); ?>" alt="<?php echo htmlspecialchars($feature['title']); ?>">
                <h3><?php echo htmlspecialchars($feature['title']); ?></h3>
                <p><?php echo htmlspecialchars($feature['description']); ?></p>
                <a href="#" class="btn">Read More</a>
            </div>
        <?php
            endwhile;
        else:
            echo "<p>No features found.</p>";
        endif;
        ?>
    </div>
</section>
<!-- Feature Section End -->
