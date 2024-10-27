<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if a property ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to the main dashboard if no valid ID is provided
    header("Location: customer_dashboard.php");
    exit();
}

$propertyId = (int)$_GET['id'];

// Fetch property details
$query = "
    SELECT rentals.*, 
    COALESCE((SELECT AVG(feedback.rating) FROM feedback WHERE feedback.rental_id = rentals.id), 0) AS avg_rating 
    FROM rentals 
    WHERE id = ? AND is_active = 1 AND removed_by_admin = 0";
$stmt = $pdo->prepare($query);
$stmt->execute([$propertyId]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "Property not found.";
    exit();
}

// Fetch feedback for this property
$query_feedback = "SELECT u.username, f.rating, f.feedback, f.created_at FROM feedback f JOIN users u ON f.customer_id = u.id WHERE f.rental_id = ?";
$stmt_feedback = $pdo->prepare($query_feedback);
$stmt_feedback->execute([$propertyId]);
$feedbacks = $stmt_feedback->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - Property Details - BodimBuddy.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/customer_dashboard.css">
</head>
<body>

    <nav class="dashboard-nav">
        <div class="nav-brand"><a href="index.php"><!-- <h1>බෝඩිම.LK</h1> --><img src="../../RESOURCES/logos-02.png" alt="Logo"></a></div>
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

    <div class="property-details-page">
        <h2><?php echo htmlspecialchars($property['title']); ?></h2>
        <p>City: <?php echo htmlspecialchars($property['city']); ?></p>
        <p>District: <?php echo htmlspecialchars($property['district']); ?></p>
        <p>Price: Rs. <?php echo number_format($property['price'], 2); ?></p>

        <!-- Image display -->
        <?php
        $images = explode(',', $property['images']);
        foreach ($images as $image): ?>
            <img src="../<?php echo $image; ?>" alt="Property Image">
        <?php endforeach; ?>

        <!-- Property features -->
        <p>Rooms: <?php echo htmlspecialchars($property['rooms']); ?></p>
        <p>Bathrooms: <?php echo htmlspecialchars($property['bathrooms']); ?></p>

        <!-- Display other property details similarly -->
        <h3>Rating and Reviews</h3>
        <?php if (empty($feedbacks)): ?>
            <p>No feedback yet for this property.</p>
        <?php else: ?>
            <?php foreach ($feedbacks as $fb): ?>
                <p><strong><?php echo htmlspecialchars($fb['username']); ?>:</strong> Rated <?php echo $fb['rating']; ?>/5</p>
                <p><?php echo htmlspecialchars($fb['feedback']); ?></p>
                <small>Submitted on <?php echo $fb['created_at']; ?></small>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="customer_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
