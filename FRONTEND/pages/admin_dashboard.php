<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Query the total properties, landlords, and customers
$stmt_properties = $pdo->query("SELECT COUNT(*) AS total_properties FROM rentals");
$total_properties = $stmt_properties->fetchColumn();

$stmt_landlords = $pdo->query("SELECT COUNT(*) AS total_landlords FROM users WHERE role = 'renter'");
$total_landlords = $stmt_landlords->fetchColumn();

$stmt_customers = $pdo->query("SELECT COUNT(*) AS total_customers FROM users WHERE role = 'customer'");
$total_customers = $stmt_customers->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
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
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <a href="admin_listings.php" class="stat-card-link">
                    <div class="stat-number"><?php echo $total_properties; ?></div>
                    <div class="stat-label">Total Properties</div>
                </a>
            </div>
            <div class="stat-card">
                <a href="admin_landlords.php" class="stat-card-link">
                    <div class="stat-number"><?php echo $total_landlords; ?></div>
                    <div class="stat-label">Total Landlords</div>
                </a>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_customers; ?></div>
                <div class="stat-label">Total Registered Customers</div>
            </div>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <ul class="activity-list">
                <li class="activity-item">
                    <span>New property listed: Beach View Apartment</span>
                    <span>2 hours ago</span>
                </li>
                <li class="activity-item">
                    <span>New user registration: John Doe</span>
                    <span>5 hours ago</span>
                </li>
                <li class="activity-item">
                    <span>Property booking: Mountain View Villa</span>
                    <span>1 day ago</span>
                </li>
            </ul>
        </div>

        <div class="quick-actions">
            <a href="#" class="action-btn">Add New Property</a>
            <a href="#" class="action-btn">Manage Users</a>
            <a href="#" class="action-btn">View Reports</a>
            <a href="#" class="action-btn">System Settings</a>
        </div>
    </div>
</body>
</html>
