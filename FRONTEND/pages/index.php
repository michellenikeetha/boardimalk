<?php
    session_start(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodimBuddy.LK - Find Your Perfect Boarding</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/landingpage.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <!-- <h1>බෝඩිම.LK</h1> -->
            <img src="../../RESOURCES/logos-04.png" alt="Logo">
        </div>
        <nav>
            <a href="#home">Home</a>
            <a href="about.php">About</a>
            <a href="services.php">Services</a>
            <a href="customer_dashboard.php">Listings</a>
        </nav>
        <div class="cta-buttons">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['role'] === 'renter'): ?>
                <a href="renter_dashboard.php"><button class="btn-primary">Post Your Ad</button></a>
            <?php else: ?>
                <a href="register.php"><button class="btn-secondary">Register</button></a>
                <a href="login.php"><button class="btn-primary">Login</button></a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="text">
            <h1>Find Your Perfect Boarding</h1>
            <p>Welcome to BodimBuddy.LK, your premier platform to discover a wide range of exceptional boarding rentals tailored to your needs.</p>
            <div class="cta-buttons">
                <!-- <button class="btn-primary">Book Now</button> -->
                <a href="customer_dashboard.php"><button class="btn-secondary">Explore Listings</button></a>
            </div>
        </div>
        <img src="../../RESOURCES/room_image.jpg" alt="Cozy boarding room" class="hero-img">
    </section>

    <!-- Featured Rentals Section -->
    <section id="featured" class="featured-rentals">
        <h2>Discover Our Featured Boarding Rentals</h2>
        <p>Immerse yourself in the comfort and convenience of our carefully curated boarding rentals. From cozy studios to spacious apartments, each property is designed to provide you with an exceptional living experience.</p>
        <div class="rental-details">
            <img src="../../RESOURCES/featured_room.jpg" alt="Featured Room" class="featured-img">
            <div class="rental-text">
                <h3>Explore the Details</h3>
                <p>Discover the perfect blend of comfort, style, and convenience with our carefully selected boarding rentals. Each property is thoughtfully designed to offer you a seamless and exceptional living experience, catering to your unique needs and preferences.</p>
                <div class="cta-buttons">
                    <a href="customer_dashboard.php"><button class="btn-primary">Book Now</button></a>
                    <!-- <button class="btn-secondary">Learn More</button> -->
                </div>
            </div>
        </div>
    </section>

    <!-- Invite Landlords Section -->
    <section id="landlords" class="invite-landlords">
        <h2>Are You a Landlord?</h2>
        <p>Post your boarding rentals on our platform and reach thousands of potential tenants. With BodimBuddy.LK, you can manage your listings effortlessly and connect with renters quickly.</p>
        <div class="cta-buttons">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['role'] === 'renter'): ?>
                <a href="renter_dashboard.php"><button class="btn-primary">Post Your Ad</button></a>
            <?php else: ?>
                <a href="register.php"><button class="btn-secondary">Post Your Ad</button></a>
            <?php endif; ?>
            <a href="termsofservice.php"><button class="btn-secondary">Learn More</button></a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Connect with Us</h4>
                <a href="mailto:bodimbuddy.lk@gmail.com">bodimbuddy.lk@gmail.com</a><br/>
                <a href="tel:+94755009920">0755009920</a>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="about.php">About Us | </a>
                <a href="membership-packages.php">Membership | </a>
                <a href="termsofservice.php">Terms of Service</a>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <a href="https://web.facebook.com/BodimBuddy">Facebook | </a>
                <a href="https://www.instagram.com/bodim_buddy.lk?igsh=MzRlODBiNWFlZA==">Instagram </a>
                <!-- <a href="#twitter">Twitter</a> -->
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 BodimBuddy.LK. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>