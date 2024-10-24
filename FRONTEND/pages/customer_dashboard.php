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

// Get search and filter inputs
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : null;
$minRating = isset($_GET['rating']) && is_numeric($_GET['rating']) ? (int)$_GET['rating'] : null;

// Build the query to fetch active properties with average ratings
$query_active = "
    SELECT rentals.*, 
    COALESCE((
        SELECT AVG(feedback.rating) 
        FROM feedback 
        WHERE feedback.rental_id = rentals.id
    ), 0) AS avg_rating 
    FROM rentals 
    WHERE is_active = 1 AND removed_by_admin = 0";
$conditions = [];
$params = [];

// If a search query is provided, add it to the conditions
if (!empty($searchQuery)) {
    $conditions[] = "(title LIKE ? OR city LIKE ? OR district LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

// If a minimum price is provided, add it to the conditions
if ($minPrice !== null) {
    $conditions[] = "price >= ?";
    $params[] = $minPrice;
}

// If a maximum price is provided, add it to the conditions
if ($maxPrice !== null) {
    $conditions[] = "price <= ?";
    $params[] = $maxPrice;
}

// If a minimum rating is provided, add it to the conditions
if ($minRating !== null) {
    $conditions[] = "rating >= ?";
    $params[] = $minRating;
}

// Add the conditions to the query if there are any
if (!empty($conditions)) {
    $query_active .= " AND " . implode(" AND ", $conditions);
}

// Prepare and execute the query
$stmt_active = $pdo->prepare($query_active);
$stmt_active->execute($params);
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
        <div class="nav-brand"><a href="index.html">බෝඩිම.LK</a></div>
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

            <!-- <div class="dashboard-stats">
                <a href="<?php echo $isGuest ? 'login.php' : 'saved_properties.php'; ?>" class="stat-card-link">
                    <div class="stat-card">
                        <div class="stat-icon">💾</div>
                        <div class="stat-info">
                            <h3>Saved Properties</h3>
                            <p><?php echo $isGuest ? 'Login to view' : $savedProperties; ?></p>
                        </div>
                    </div>
                </a>
            </div> -->

            <div class="notifications">
                <?php if (isset($_GET['feedback_submitted']) && $_GET['feedback_submitted'] == 1): ?>
                    <div class="success-msg">Thank you! Your feedback has been submitted successfully.</div>
                <?php elseif (isset($_GET['error']) && $_GET['error'] == 1): ?>
                    <div class="error-msg">Oops! There was an issue submitting your feedback. Please try again.</div>
                <?php endif; ?>
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
                            <p>Rating: <?php echo $property['avg_rating'] > 0 ? number_format($property['avg_rating'], 2) : 'No ratings yet'; ?>/5</p>
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

                                <div class="property-details">
                                    <div class="details-column">
                                        <div class="info-card">
                                            <h3>Property Information</h3>
                                            <div class="highlight-info">
                                                <div class="highlight-card">
                                                    <strong>Rent</strong>
                                                    <span>Rs.<?php echo number_format($property['price'], 2); ?></span>
                                                </div>
                                                <div class="highlight-card">
                                                    <strong>Rating</strong>
                                                    <span><?php echo $property['rating'] ? number_format($property['rating'], 2) : 'No ratings yet'; ?> /5</span>
                                                </div>
                                            </div>
                                            <div class="features-grid">
                                                <div class="feature-item">
                                                    <strong>Rooms:</strong> <?php echo htmlspecialchars($property['rooms']); ?>
                                                </div>
                                                <div class="feature-item">
                                                    <strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?>
                                                </div>
                                                <div class="feature-item-checkbox">
                                                    <div class="feature-item-ch <?php echo $property['is_furnished'] ? 'yes' : 'no'; ?>">
                                                        <strong>Furnished:</strong>
                                                        <span><?php echo $property['is_furnished'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['separate_utility_bills'] ? 'yes' : 'no'; ?>">
                                                        <strong>Separate Utility Bills:</strong>
                                                        <span><?php echo $property['separate_utility_bills'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['has_garden'] ? 'yes' : 'no'; ?>">
                                                        <strong>Garden:</strong>
                                                        <span><?php echo $property['has_garden'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['has_kitchen'] ? 'yes' : 'no'; ?>">
                                                        <strong>Kitchen:</strong>
                                                        <span><?php echo $property['has_kitchen'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['is_air_conditioned'] ? 'yes' : 'no'; ?>">
                                                        <strong>Air Conditioned:</strong>
                                                        <span><?php echo $property['is_air_conditioned'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['has_parking'] ? 'yes' : 'no'; ?>">
                                                        <strong>Parking:</strong>
                                                        <span><?php echo $property['has_parking'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                    <div class="feature-item-ch <?php echo $property['has_security_cameras'] ? 'yes' : 'no'; ?>">
                                                        <strong>Security Cameras:</strong>
                                                        <span><?php echo $property['has_security_cameras'] ? 'Yes' : 'No'; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <h3> Rating and Reviews</h3>
                                            <?php
                                            // Fetch feedback for this rental
                                            $query_feedback = "SELECT u.username, f.rating, f.feedback, f.created_at FROM feedback f JOIN users u ON f.customer_id = u.id WHERE f.rental_id = ?";
                                            $stmt_feedback = $pdo->prepare($query_feedback);
                                            $stmt_feedback->execute([$property['id']]);
                                            $feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);

                                            if (empty($feedbacks)): ?>
                                                <p>No feedback yet for this property.</p>
                                            <?php else: ?>
                                                <?php foreach ($feedbacks as $fb): ?>
                                                    <div class="feedback-item">
                                                        <p><strong><?php echo htmlspecialchars($fb['username']); ?>:</strong> Rated <?php echo $fb['rating']; ?>/5</p>
                                                        <p><?php echo htmlspecialchars($fb['feedback']); ?></p>
                                                        <small>Submitted on <?php echo $fb['created_at']; ?></small>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="details-column">
                                        <div class="info-card">
                                            <h3>Location and Contact</h3>
                                            <div class="features-grid">
                                                <div class="feature-item">
                                                    <strong>City:</strong> <?php echo htmlspecialchars($property['city']); ?>
                                                </div>
                                                <div class="feature-item">
                                                    <strong>District:</strong> <?php echo htmlspecialchars($property['district']); ?>
                                                </div>
                                                <div class="feature-item">
                                                    <strong>Proximity to Road:</strong> <?php echo htmlspecialchars($property['proximity_to_road']); ?>
                                                </div>
                                                <div class="feature-item">
                                                    <strong>Address:</strong> <?php echo htmlspecialchars($property['full_address']); ?>
                                                </div>
                                                <div class="feature-item">
                                                    <strong>WhatsApp Contact:</strong> <?php echo htmlspecialchars($property['contact_whatsapp']); ?>
                                                    <!-- WhatsApp Contact Button -->
                                                    <a href="https://wa.me/<?php echo htmlspecialchars($property['contact_whatsapp']); ?>" 
                                                    target="_blank" class="whatsapp-btn">
                                                        <svg class="whatsapp-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="info-card">
                                            <h3>Description</h3>
                                            <p><?php echo htmlspecialchars($property['description']); ?></p>
                                        </div>
                                        <div class="info-card" id="feedback-form">
                                            <?php if (!$isGuest && $role == 'customer'): ?>
                                                <!-- Feedback form -->
                                                <h3>Submit Feedback</h3>
                                                <form action="../../BACKEND/submit_feedback.php" method="POST">
                                                    <input type="hidden" name="rental_id" value="<?php echo $property['id']; ?>">
                                                    <label for="rating">Rating (1-5):</label>
                                                    <select name="rating" id="rating" required>
                                                        <option value="">Select</option>
                                                        <option value="5">5 ★★★★★</option>
                                                        <option value="4">4 ★★★★☆</option>
                                                        <option value="3">3 ★★★☆☆</option>
                                                        <option value="2">2 ★★☆☆☆</option>
                                                        <option value="1">1 ★☆☆☆☆</option>
                                                    </select>

                                                    <label for="feedback">Feedback:</label>
                                                    <textarea name="feedback" id="feedback" rows="4" placeholder="Write your feedback here..." required></textarea>

                                                    <button type="submit">Submit Feedback</button>
                                                </form>
                                            <?php else: ?>
                                                <p><a href="login.php">Log in </a> &nbsp to submit feedback and ratings.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

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
