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
    <title>Admin Listings - BodimBuddy.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/admin_listings.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand"><a href="admin_dashboard.php"><!-- <h1>බෝඩිම.LK</h1> --><img src="../../RESOURCES/logos-02.png" alt="Logo"></a></div>
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
                        <tr onclick="openModal(<?php echo $property['id']; ?>)" class="<?php echo $property['removed_by_admin'] ? 'removed-property' : 'active-property'; ?>" >
                            <td><?php echo htmlspecialchars($property['title']); ?></td>
                            <td><?php echo htmlspecialchars($property['renter_name']); ?></td>
                            <td><?php echo htmlspecialchars($property['city']); ?></td>
                            <td>Rs.<?php echo number_format($property['price'], 2); ?></td>
                            <td>
                                <?php echo $property['removed_by_admin'] ? 'Removed' : 'Not Removed'; ?>
                            </td>
                            <td>
                                <button onclick="event.stopPropagation(); toggleRemovalStatus(<?php echo $property['id']; ?>, <?php echo $property['removed_by_admin'] ? 1 : 0; ?>)"  class="<?php echo $property['removed_by_admin'] ? 'restore-button' : 'remove-button'; ?>" >
                                    <?php echo $property['removed_by_admin'] ? 'Mark as Not Removed' : 'Mark as Removed'; ?>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal for Property Details -->
                        <div id="modal-<?php echo $property['id']; ?>" class="modal">
                            <div class="modal-content">
                                <span class="close-btn" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
                                <h2><?php echo htmlspecialchars($property['title']); ?></h2>

                                <!-- Display main image -->
                                <img id="main-image-<?php echo $property['id']; ?>" class="main-image" src="../<?php echo $property['images'] ? explode(',', $property['images'])[0] : 'placeholder.jpg'; ?>" alt="Property Image">
                                
                                <!-- Thumbnail carousel for all images -->
                                <div class="image-carousel">
                                    <?php 
                                    $images = explode(',', $property['images']);
                                    foreach ($images as $image): ?>
                                        <img src="../<?php echo $image; ?>" onclick="changeMainImage('<?php echo $property['id']; ?>', '../<?php echo $image; ?>')" alt="Property Image">
                                    <?php endforeach; ?>
                                </div>

                                <p><strong>Renter:</strong> <?php echo htmlspecialchars($property['renter_name']); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?></p>
                                <p><strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?></p>
                                <p><strong>Rent:</strong> Rs.<?php echo number_format($property['price'], 2); ?></p>
                                <p><strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?></p>
                                <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
                                <p><strong>Separate Utility Bills:</strong> <?php echo $property['separate_utility_bills'] ? 'Yes' : 'No'; ?></p>
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
                    </tbody>
                </table>
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

        // Close modal when clicking outside content
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                const modalId = event.target.id;
                closeModal(modalId.replace('modal-', ''));
            }
        }

        // Function to change the main image when a thumbnail is clicked
        function changeMainImage(id, imageUrl) {
            document.getElementById('main-image-' + id).src = imageUrl;
        }

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
