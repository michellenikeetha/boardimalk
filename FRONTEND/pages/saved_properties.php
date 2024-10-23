<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Ensure the user is logged in and is a customer
if (!isLoggedIn() || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch the saved properties
    $query = "SELECT rentals.*, saved_properties.id as saved_id 
              FROM rentals 
              INNER JOIN saved_properties ON rentals.id = saved_properties.property_id 
              WHERE saved_properties.user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();
    $saved_properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching saved properties: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Properties - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">
            <a href="index.php">බෝඩිම.LK</a>
        </div>
        <div class="nav-items">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="main-content">
            <h2>Saved Properties</h2>
            <?php if (empty($saved_properties)): ?>
                <p>You have no saved properties.</p>
            <?php else: ?>
                <div class="property-cards-container">
                    <?php foreach ($saved_properties as $property): ?>
                        <div class="property-card">
                            <img src="../<?php echo explode(',', $property['images'])[0]; ?>" alt="Property Image">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <a href="property_details.php?id=<?php echo $property['id']; ?>" class="view-btn">View Details</a>
                            <a href="remove_saved_property.php?id=<?php echo $property['saved_id']; ?>" class="remove-btn">Remove</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
