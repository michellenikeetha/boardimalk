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

// Fetch properties for display
$searchQuery = '';
$filters = [];
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
    $minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
    $maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : PHP_INT_MAX;
    $location = isset($_GET['location']) ? $_GET['location'] : '';
    $rating = isset($_GET['rating']) ? $_GET['rating'] : 0;

    // Prepare the SQL query with filters
    $sql = "SELECT * FROM rentals WHERE (title LIKE ? OR city LIKE ?) AND price BETWEEN ? AND ? AND rating >= ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchQuery%", "%$searchQuery%", $minPrice, $maxPrice, $rating]);
    $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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

            <div class="property-list">
                <?php if (empty($properties)): ?>
                    <p>No properties found.</p>
                <?php else: ?>
                    <?php foreach ($properties as $property): ?>
                        <div class="property-card">
                            <img src="../<?php echo explode(',', $property['images'])[0]; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <p>Rating: <?php echo number_format($property['rating'], 1); ?> stars</p>
                            <a href="https://wa.me/<?php echo $property['contact']; ?>" target="_blank" class="whatsapp-btn">Contact on WhatsApp</a>
                            <button class="view-details-btn" onclick="openModal(<?php echo $property['id']; ?>)">View Details</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modals for Property Details -->
    <?php foreach ($properties as $property): ?>
    <div class="modal" id="modal-<?php echo $property['id']; ?>">
        <div class="modal-content">
            <span class="close" onclick="closeModal(<?php echo $property['id']; ?>)">&times;</span>
            <div class="modal-header">
                <h2><?php echo htmlspecialchars($property['title']); ?></h2>
            </div>
            <div class="modal-body">
                <p><?php echo htmlspecialchars($property['description']); ?></p>
                <div class="property-gallery">
                    <?php foreach (explode(',', $property['images']) as $image): ?>
                        <img src="../<?php echo $image; ?>" alt="Property Image">
                    <?php endforeach; ?>
                </div>
                <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                <p>Location: <?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                <p>Rating: <?php echo number_format($property['rating'], 1); ?> stars</p>
                <div class="feedback-section">
                    <h3>Feedback and Ratings</h3>
                    <form action="../../BACKEND/submit_feedback.php" method="POST">
                        <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                        <textarea name="feedback" placeholder="Leave your feedback here..." required></textarea>
                        <input type="number" name="rating" min="1" max="5" placeholder="Rate (1 to 5)" required>
                        <button type="submit">Submit Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script>
        function openModal(id) {
            document.getElementById('modal-' + id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).style.display = 'none';
        }

        window.onclick = function(event) {
            var modals = document.querySelectorAll('.modal');
            modals.forEach(function(modal) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>