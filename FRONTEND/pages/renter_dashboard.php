<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
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
    <title>Renter Dashboard - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/renter_dashboard.css">
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
                <li><a href="#properties">My Properties</a></li>
                <li><a href="#bookings">Bookings</a></li>
                <li><a href="#messages">Messages</a></li>
                <li><a href="#profile">Profile</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h2>Renter Dashboard</h2>
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Properties</h3>
                    <p>5</p>
                </div>
                <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <p>3</p>
                </div>
                <div class="stat-card">
                    <h3>Messages</h3>
                    <p>12</p>
                </div>
            </div>
            
            <div>
                <h2>Post New Listing</h2>
                <form action="../../BACKEND/post_listing.php" method="POST" enctype="multipart/form-data">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" required>
                    
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" required></textarea>

                    <label for="location">Location:</label>
                    <input type="text" name="location" id="location" required>
                    
                    <label for="price">Price:</label>
                    <input type="number" step="0.01" name="price" id="price" required>
                    
                    <label for="contact_whatsapp">WhatsApp Contact:</label>
                    <input type="text" name="contact_whatsapp" id="contact_whatsapp" required>

                    <label for="images">Upload Photos:</label>
                    <input type="file" name="images[]" id="images" multiple>

                    <button type="submit">Post Listing</button>
                </form>
            </div>

            <div>
                <h2>My Listings</h2>
                <div class="listings">
                    <?php
                    $query = "SELECT * FROM rentals WHERE renter_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('i', $renter_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="listing">';
                            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                            echo '<p>Location: ' . htmlspecialchars($row['location']) . '</p>';
                            echo '<p>Price: ' . htmlspecialchars($row['price']) . '</p>';
                            echo '<p>WhatsApp: ' . htmlspecialchars($row['contact_whatsapp']) . '</p>';
                            echo '<img src="' . htmlspecialchars(explode(',', $row['images'])[0]) . '" alt="Listing Image" width="100" height="100">';
                            echo '<a href="edit_listing.php?id=' . $row['id'] . '">Edit</a> | ';
                            echo '<a href="../../BACKEND/delete_listing.php?id=' . $row['id'] . '">Remove</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No listings found.</p>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
</body>
</html>