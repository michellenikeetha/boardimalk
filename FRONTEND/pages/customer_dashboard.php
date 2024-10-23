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

// Fetch all active properties
$query_active = "SELECT * FROM rentals WHERE is_active = 1 AND removed_by_admin = 0";
$stmt_active = $pdo->prepare($query_active);
$stmt_active->execute();
$active_properties = $stmt_active->fetchAll(PDO::FETCH_ASSOC);

// Fetch saved properties count for logged-in users
if (!$isGuest) {
    try {
        $savedStmt = $pdo->prepare("SELECT COUNT(*) FROM saved_properties WHERE user_id = ?");
        $savedStmt->execute([$_SESSION['user_id']]);
        $savedProperties = $savedStmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $savedProperties = 0;
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
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <?php if ($isGuest): ?>
                <span>Welcome, Guest!</span>
                <a href="login.php" class="login-btn">Login</a>
            <?php else: ?>
                <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
                <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="main-content">
            <h2>Customer Dashboard</h2>

            <div class="search-bar">
                <form action="customer_dashboard.php" method="GET">
                    <input type="text" name="search" placeholder="Search by title or location" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <input type="number" name="min_price" placeholder="Min Price" value="<?php echo isset($_GET['min_price']) ? $_GET['min_price'] : ''; ?>">
                    <input type="number" name="max_price" placeholder="Max Price" value="<?php echo isset($_GET['max_price']) ? $_GET['max_price'] : ''; ?>">
                    <input type="number" name="rating" placeholder="Minimum Rating" value="<?php echo isset($_GET['rating']) ? $_GET['rating'] : ''; ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Saved Properties</h3>
                    <p><a href="<?php echo $isGuest ? 'login.php' : 'saved_properties.php'; ?>">View Saved Properties</a></p>
                </div>
            </div>

            <!-- Active Listings Section -->
            <?php if (empty($active_properties)): ?>
                <p>No available properties at the moment.</p>
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
                                <p>
                                    <!-- WhatsApp Contact Button -->
                                    <a href="https://wa.me/<?php echo htmlspecialchars($property['contact_whatsapp']); ?>?text=Hello, I am interested in your property titled '<?php echo htmlspecialchars($property['title']); ?>'." 
                                    target="_blank" class="whatsapp-btn">Contact via WhatsApp</a>
                                </p>
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
    </script>
    
</body>
</html>
