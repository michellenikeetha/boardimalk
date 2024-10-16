<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Initialize variables
$isGuest = !isLoggedIn();
$username = $isGuest ? 'Guest' : $_SESSION['username'];
$role = $isGuest ? 'guest' : $_SESSION['role'];

// If logged in but not a customer, redirect to appropriate dashboard
if (!$isGuest && $role !== 'customer') {
    switch($role) {
        case 'renter':
            header("Location: renter_dashboard.php");
            break;
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
    }
    exit();
}

// Fetch statistics if user is logged in
if (!$isGuest) {
    try {
        // Fetch active bookings count
        // $bookingsStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = 'active'");
        // $bookingsStmt->execute([$_SESSION['user_id']]);
        // $activeBookings = $bookingsStmt->fetchColumn();

        // Fetch saved properties count
        $savedStmt = $pdo->prepare("SELECT COUNT(*) FROM saved_properties WHERE user_id = ?");
        $savedStmt->execute([$_SESSION['user_id']]);
        $savedProperties = $savedStmt->fetchColumn();

        // Fetch unread messages count
        // $msgStmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND read_status = 'unread'");
        // $msgStmt->execute([$_SESSION['user_id']]);
        // $unreadMessages = $msgStmt->fetchColumn();

    } catch (PDOException $e) {
        // Handle database errors gracefully
        error_log("Database Error: " . $e->getMessage());
        $activeBookings = $savedProperties = $unreadMessages = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/customer_dashboard.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">
            <a href="index.php">බෝඩිම.LK</a>
        </div>
        <div class="nav-items">
            <?php if ($isGuest): ?>
                <span>Welcome, Guest!</span>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            <?php else: ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar">
            <ul>
                <?php if ($isGuest): ?>
                    <li><a href="search_properties.php">Search Properties</a></li>
                    <li><a href="featured_properties.php">Featured Properties</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                <?php else: ?>
                    <li><a href="bookings.php">My Bookings</a></li>
                    <li><a href="favorites.php">Favorites</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="search_properties.php">Search Properties</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="main-content">
            <?php if ($isGuest): ?>
                <div class="guest-welcome">
                    <h2>Welcome to බෝඩිම.LK</h2>
                    <p>Create an account to access all features:</p>
                    <ul class="feature-list">
                        <li>Save your favorite properties</li>
                        <!-- <li>Book properties directly</li>
                        <li>Message property owners</li>
                        <li>Get notifications about new properties</li> -->
                    </ul>
                    <div class="cta-buttons">
                        <a href="register.php" class="btn-primary">Register Now</a>
                        <a href="login.php" class="btn-secondary">Login</a>
                    </div>
                </div>
                
                <div class="featured-properties">
                    <h3>Featured Properties</h3>
                    <!-- Add featured properties grid here -->
                    <div class="property-grid">
                        <!-- This section would be populated with actual property data from your database -->
                        <p>Loading featured properties...</p>
                    </div>
                </div>
            <?php else: ?>
                <h2>Customer Dashboard</h2>
                <div class="dashboard-stats">
                    <!-- <div class="stat-card">
                        <h3>Active Bookings</h3>
                        <p><?php echo $activeBookings; ?></p>
                    </div> -->
                    <div class="stat-card">
                        <h3>Saved Properties</h3>
                        <p><?php echo $savedProperties; ?></p>
                    </div>
                    <!-- <div class="stat-card">
                        <h3>Unread Messages</h3>
                        <p><?php echo $unreadMessages; ?></p>
                    </div> -->
                </div>

                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    <!-- Add recent activity section here -->
                </div>

                <div class="recommended-properties">
                    <h3>Recommended for You</h3>
                    <!-- Add recommended properties section here -->
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>