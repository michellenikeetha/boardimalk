<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - BodimBuddy.LK</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/about.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1><a href="index.php"><!-- <h1>බෝඩිම.LK</h1> --><img src="../../RESOURCES/logos-04.png" alt="Logo"></a></h1>
        </div>
        <nav>
            <a href="index.php">Home</a>
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

    <div class="about-page">
        <div class="about-section">
            <!-- <h2>ABOUT PAGE</h2> -->
            <div class="vision-mission">
                <div class="vision">
                    <img src="../../RESOURCES/eye-icon.png" alt="Vision Icon" class="icon">
                    <h3>Vision Statement</h3>
                    <p>To be the leading platform connecting university students and young professionals with safe, affordable, and convenient housing options, fostering a supportive community for all renters.</p>
                </div>
                <div class="mission">
                    <img src="../../RESOURCES/target-icon.png" alt="Mission Icon" class="icon">
                    <h3>Mission Statement</h3>
                    <p>Our mission is to simplify the room rental process by providing a user-friendly platform that offers transparent listings, secure transactions, and resources for renters. We aim to empower students and young adults to find their ideal living spaces while promoting inclusivity, safety, and a sense of community.</p>
                </div>
            </div>
        </div>

        <div class="about-content">
            <div class="about-header">
                <h2>About Us</h2>
                <img src="../../RESOURCES/logos-04.png" alt="BodimBuddy.LK Logo" class="logo">   
            </div>
            <div class="about-text">
                <p><strong>Welcome to BodimBuddy.LK</strong>, your ultimate platform for discovering the perfect boarding places across Sri Lanka! Whether you're a student, professional, or traveler, we aim to streamline your search for comfortable, affordable, and convenient accommodations that suit your lifestyle.</p>
                <p>Our platform offers a broad range of boarding options, from private rooms to shared spaces, across major cities like Colombo, Kandy, Galle.</p>
                <p><strong>At BodimBuddy.LK, both boarding place providers (renters) and tenants looking for a place to stay can register on our site</strong>, creating a community where finding and offering accommodations is seamless and efficient. We feature a wide selection of boarding options across Sri Lanka, with listings covering everything from hostels and private rooms to shared spaces. Whether you're looking for a long-term stay or something temporary, BodimBuddy.LK helps you find the perfect accommodation.</p>
                <p>Our advanced search and filtering tools make finding exactly what you’re looking for easy. You can filter by location, budget, amenities, parking, gender suitability, and more to find the ideal place that matches your preferences.</p>
                <p>But we don’t stop at just listings; our platform delivers a complete, interactive booking journey to ensure that your boarding process is as smooth as possible. Our platform gives you access to countless boarding options across Sri Lanka and empowers you with detailed information to make informed choices. From real-time availability to location-based recommendations, we are here to help you find the right place, just for you.</p>
                <p><strong>Have a boarding place to offer?</strong> Sign up for a free account to start listing your accommodations! It takes less than 2 minutes to post attractive listings that catch the eye of potential tenants. If you manage multiple properties or boarding places, consider upgrading your membership in our special packages and enjoy additional benefits like enhanced visibility for your listings. We also offer some fantastic tools to help your listing stand out from the rest.</p>
                <p>To see the <a href="membership-packages.php" class="membership-link"> Membership Packages <span> &#10145;</span></a></p>
            </div>
        </div>
    </div>

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
