<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all properties
$query_properties = "SELECT rentals.*, users.username AS renter_name FROM rentals 
                     JOIN users ON rentals.renter_id = users.id";
$stmt_properties = $pdo->query($query_properties);
$properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Listings - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/admin_dashboard.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <span>Welcome, Admin</span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>All Property Listings</h2>

        <div class="property-listings">
            <?php if (empty($properties)): ?>
                <p>No properties found.</p>
            <?php else: ?>
                <table class="property-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Renter</th>
                            <th>City</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($property['title']); ?></td>
                            <td><?php echo htmlspecialchars($property['renter_name']); ?></td>
                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                            <td>Rs.<?php echo number_format($property['price'], 2); ?></td>
                            <td>
                                <?php echo $property['removed_by_admin'] ? 'Removed' : 'Not Removed'; ?>
                            </td>
                            <td>
                                <button onclick="toggleRemovalStatus(<?php echo $property['id']; ?>, <?php echo $property['removed_by_admin'] ? 1 : 0; ?>)">
                                    <?php echo $property['removed_by_admin'] ? 'Mark as Not Removed' : 'Mark as Removed'; ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Function to toggle the removed_by_admin status
        function toggleRemovalStatus(propertyId, isRemoved) {
            let action = isRemoved ? 'Not Removed' : 'Removed';
            if (confirm(`Are you sure you want to mark this property as ${action}?`)) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../../BACKEND/toggle_admin_removal_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Property status updated.");
                        window.location.reload();
                    }
                };
                xhr.send("property_id=" + propertyId + "&is_removed=" + isRemoved);
            }
        }
    </script>
</body>
</html>
