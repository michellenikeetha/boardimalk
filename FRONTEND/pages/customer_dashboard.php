<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a customer
if (!isLoggedIn() || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/dashboard.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="sidebar">
            <ul>
                <li><a href="#bookings">My Bookings</a></li>
                <li><a href="#favorites">Favorites</a></li>
                <li><a href="#messages">Messages</a></li>
                <li><a href="#profile">Profile</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2>Customer Dashboard</h2>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <p>1</p>
                </div>
                <div class="stat-card">
                    <h3>Saved Properties</h3>
                    <p>8</p>
                </div>
                <div class="stat-card">
                    <h3>Messages</h3>
                    <p>5</p>
                </div>
            </div>
            <!-- Add more dashboard content here -->
        </div>
    </div>
</body>
</html>