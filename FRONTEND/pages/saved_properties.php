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
    // Fetch all saved properties including inactive or removed properties
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
            <a href="index.php"><a href="index.html">බෝඩිම.LK</a></a>
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
                            <!-- Property Image -->
                            <?php 
                            $images = explode(',', $property['images']);
                            $main_image = $images[0];
                            ?>
                            <img src="../<?php echo $main_image; ?>" alt="Property Image">
                            
                            <!-- Property Title and Location -->
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['district']); ?></p>
                            
                            <!-- Rent and other details -->
                            <p>Rent: Rs.<?php echo number_format($property['price'], 2); ?></p>
                            <p>Rating: <?php echo $property['rating'] ? number_format($property['rating'], 2) : 'No ratings yet'; ?>/5</p>
                            
                            <!-- Labels for inactive or removed properties -->
                            <?php if (!$property['is_active']): ?>
                                <span class="inactive-label">Inactive</span>
                            <?php elseif ($property['removed_by_admin']): ?>
                                <span class="admin-removed-label">Removed by Admin</span>
                            <?php endif; ?>
                            
                            <!-- View Details and Remove Options -->
                            <a href="property_details.php?id=<?php echo $property['id']; ?>" class="view-btn">View Details</a>
                            <a href="remove_saved_property.php?id=<?php echo $property['saved_id']; ?>" class="remove-btn">Remove</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .inactive-label {
            background-color: #ffcc00;
            color: #000;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 5px;
            display: inline-block;
        }
        .admin-removed-label {
            background-color: #ff0000;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 5px;
            display: inline-block;
        }
        .property-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px;
            border-radius: 8px;
        }
        .property-cards-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .property-card img {
            max-width: 100%;
            border-radius: 8px;
        }
        .view-btn, .remove-btn {
            display: block;
            margin-top: 10px;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .view-btn {
            background-color: #4CAF50;
            color: #fff;
        }
        .remove-btn {
            background-color: #f44336;
            color: #fff;
        }
    </style>
</body>
</html>
