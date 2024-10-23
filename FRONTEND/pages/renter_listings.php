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

// Fetch all active properties uploaded by the renter
$query_active = "SELECT * FROM rentals WHERE renter_id = :renter_id AND is_active = 1 AND removed_by_admin = 0";
$stmt_active = $pdo->prepare($query_active);
$stmt_active->bindValue(':renter_id', $renter_id);
$stmt_active->execute();
$active_properties = $stmt_active->fetchAll(PDO::FETCH_ASSOC);

// Fetch all inactive properties uploaded by the renter
$query_inactive = "SELECT * FROM rentals WHERE renter_id = :renter_id AND is_active = 0 AND removed_by_admin = 0";
$stmt_inactive = $pdo->prepare($query_inactive);
$stmt_inactive->bindValue(':renter_id', $renter_id);
$stmt_inactive->execute();
$inactive_properties = $stmt_inactive->fetchAll(PDO::FETCH_ASSOC);

// Fetch all properties removed by the admin
$query_removed = "SELECT * FROM rentals WHERE renter_id = :renter_id AND removed_by_admin = 1";
$stmt_removed = $pdo->prepare($query_removed);
$stmt_removed->bindValue(':renter_id', $renter_id);
$stmt_removed->execute();
$removed_properties = $stmt_removed->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/renter_dashboard.css">
    <link rel="stylesheet" href="../CSS/renter_listings.css"> 
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

            <!-- Active Listings Section -->
            <h3>Active Listings</h3>
            <?php if (empty($active_properties)): ?>
                <p>You have no active properties.</p>
            <?php else: ?>
                <div class="property-cards-container">
                    <?php foreach ($active_properties as $property): ?>
                        <?php
                        // Split the stored image string to get individual image URLs
                        $images = explode(',', $property['images']);
                        $random_image = $images[array_rand($images)];
                        ?>
                        <div class="property-card" onclick="openModal(<?php echo $property['id']; ?>)">
                            <img src="../<?php echo $random_image; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <div class="property-actions">
                                <button class="toggle-status-btn <?php echo $property['is_active'] ? 'btn-deactivate' : 'btn-activate'; ?>" 
                                        onclick="toggleStatus(<?php echo $property['id']; ?>, <?php echo $property['is_active'] ? 1 : 0; ?>)">
                                    <?php echo $property['is_active'] ? 'Mark as Inactive' : 'Mark as Active'; ?>
                                </button>
                            </div>
                        </div>

                        <!-- Modal for Property Details -->
                        <div id="modal-<?php echo $property['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-btn" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
                                <h2><?php echo htmlspecialchars($property['title']); ?></h2>

                                <!-- Display main image -->
                                <img id="main-image-<?php echo $property['id']; ?>" class="main-image" src="../<?php echo $images[0]; ?>" alt="Property Image">
                                
                                <!-- Thumbnail carousel for all images -->
                                <div class="image-carousel">
                                    <?php foreach ($images as $image): ?>
                                        <img src="../<?php echo $image; ?>" onclick="changeMainImage('<?php echo $property['id']; ?>', '../<?php echo $image; ?>')" alt="Property Image">
                                    <?php endforeach; ?>
                                </div>

                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?></p>
                                <p><strong>Rent:</strong> Rs.<?php echo number_format($property['price'], 2); ?></p>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <p><strong>Furnished:</strong> <?php echo $property['is_furnished'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Separate Utility Bills:</strong> <?php echo $property['separate_utility_bills'] ? 'Yes' : 'No'; ?></p>
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

            <!-- Inactive Listings Section -->
            <h3>Inactive Listings</h3>
            <?php if (empty($inactive_properties)): ?>
                <p>You have no inactive properties.</p>
            <?php else: ?>
                <div class="property-cards-container">
                    <?php foreach ($inactive_properties as $property): ?>
                        <?php
                        // Split the stored image string to get individual image URLs
                        $images = explode(',', $property['images']);
                        $random_image = $images[array_rand($images)];
                        ?>
                        <div class="property-card" onclick="openModal(<?php echo $property['id']; ?>)">
                            <img src="../<?php echo $random_image; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <div class="property-actions">
                                <button class="toggle-status-btn <?php echo $property['is_active'] ? 'btn-deactivate' : 'btn-activate'; ?>" 
                                        onclick="toggleStatus(<?php echo $property['id']; ?>, <?php echo $property['is_active'] ? 1 : 0; ?>)">
                                    <?php echo $property['is_active'] ? 'Mark as Inactive' : 'Mark as Active'; ?>
                                </button>
                            </div>
                        </div>

                        <!-- Modal for Property Details -->
                        <div id="modal-<?php echo $property['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-btn" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
                                <h2><?php echo htmlspecialchars($property['title']); ?></h2>

                                <!-- Display main image -->
                                <img id="main-image-<?php echo $property['id']; ?>" class="main-image" src="../<?php echo $images[0]; ?>" alt="Property Image">
                                
                                <!-- Thumbnail carousel for all images -->
                                <div class="image-carousel">
                                    <?php foreach ($images as $image): ?>
                                        <img src="../<?php echo $image; ?>" onclick="changeMainImage('<?php echo $property['id']; ?>', '../<?php echo $image; ?>')" alt="Property Image">
                                    <?php endforeach; ?>
                                </div>

                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?></p>
                                <p><strong>Rent:</strong> Rs.<?php echo number_format($property['price'], 2); ?></p>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <p><strong>Furnished:</strong> <?php echo $property['is_furnished'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Separate Utility Bills:</strong> <?php echo $property['separate_utility_bills'] ? 'Yes' : 'No'; ?></p>
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

            <!-- Removed by Admin Listings Section -->
            <h3>Removed by Admin Listings</h3>
            <?php if (empty($removed_properties)): ?>
                <p>You have no properties removed by the admin.</p>
            <?php else: ?>
                <div class="property-cards-container">
                    <?php foreach ($removed_properties as $property): ?>
                        <?php
                        // Split the stored image string to get individual image URLs
                        $images = explode(',', $property['images']);
                        $random_image = $images[array_rand($images)];
                        ?>
                        <div class="property-card" onclick="openModal(<?php echo $property['id']; ?>)">
                            <img src="../<?php echo $random_image; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <p><strong>Status:</strong> Removed by Admin</p>
                        </div>

                        <!-- Modal for Property Details -->
                        <div id="modal-<?php echo $property['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-btn" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
                                <h2><?php echo htmlspecialchars($property['title']); ?></h2>

                                <!-- Display main image -->
                                <img id="main-image-<?php echo $property['id']; ?>" class="main-image" src="../<?php echo $images[0]; ?>" alt="Property Image">
                                
                                <!-- Thumbnail carousel for all images -->
                                <div class="image-carousel">
                                    <?php foreach ($images as $image): ?>
                                        <img src="../<?php echo $image; ?>" onclick="changeMainImage('<?php echo $property['id']; ?>', '../<?php echo $image; ?>')" alt="Property Image">
                                    <?php endforeach; ?>
                                </div>

                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?></p>
                                <p><strong>Rent:</strong> Rs.<?php echo number_format($property['price'], 2); ?></p>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <p><strong>Furnished:</strong> <?php echo $property['is_furnished'] ? 'Yes' : 'No'; ?></p>
                                <p><strong>Separate Utility Bills:</strong> <?php echo $property['separate_utility_bills'] ? 'Yes' : 'No'; ?></p>
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

        // Function to change the main image when a thumbnail is clicked
        function changeMainImage(id, imageUrl) {
            document.getElementById('main-image-' + id).src = imageUrl;
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

        // Function to toggle the status between active and inactive
        function toggleStatus(propertyId, isActive) {
            let action = isActive ? 'inactive' : 'active';
            if (confirm(`Are you sure you want to mark this property as ${action}?`)) {
                // Send an AJAX request to toggle the 'is_active' status
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../../BACKEND/toggle_active_status.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert("Property status updated.");
                        window.location.reload();
                    }
                };
                xhr.send("property_id=" + propertyId + "&is_active=" + isActive);
            }
        }

    </script>
</body>
</html>
