<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$renter_id = $_SESSION['user_id'];

// Fetch all properties uploaded by the renter
$query = "SELECT * FROM rentals WHERE renter_id = :renter_id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':renter_id', $renter_id);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/renter_dashboard.css">
    <link rel="stylesheet" href="../CSS/renter_listings.css"> <!-- Add a custom CSS file for listing cards and modal -->
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
        <div class="main-content">
            <h2>My Listings</h2>

            <?php if (empty($properties)): ?>
                <p>You haven't posted any properties yet.</p>
            <?php else: ?>
                <div class="property-cards-container">
                    <?php foreach ($properties as $property): ?>
                        <?php
                        // Split the stored image string to get individual image URLs
                        $images = explode(',', $property['images']);
                        
                        // Pick a random image from the array
                        $random_image = $images[array_rand($images)];
                        ?>

                        <div class="property-card" onclick="openModal(<?php echo $property['id']; ?>)">
                            <img src="../<?php echo $random_image; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                        </div>

                        <!-- Modal for Property Details -->
                        <div id="modal-<?php echo $property['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-btn" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
                                <h2><?php echo htmlspecialchars($property['title']); ?></h2>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?></p>
                                <p><strong>Rent:</strong> Rs.<?php echo number_format($property['price'], 2); ?></p>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <p><strong>Furnished:</strong> <?php echo $property['is_furnished'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Garden:</strong> <?php echo $property['has_garden'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Kitchen:</strong> <?php echo $property['has_kitchen'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Air Conditioned:</strong> <?php echo $property['is_air_conditioned'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Parking:</strong> <?php echo $property['has_parking'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Security Cameras:</strong> <?php echo $property['has_security_cameras'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Proximity to Road:</strong> <?php echo htmlspecialchars($property['proximity_to_road']); ?></p>
                                <p><strong>WhatsApp Contact:</strong> <?php echo htmlspecialchars($property['contact_whatsapp']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($property['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script>
        // Function to open the modal
        function openModal(id) {
            document.getElementById('modal-' + id).style.display = 'block';
        }

        // Function to close the modal
        function closeModal(id) {
            document.getElementById('modal-' + id).style.display = 'none';
        }

        // Close the modal when the user clicks outside of the modal content
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>