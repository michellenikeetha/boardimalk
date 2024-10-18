<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

// Get the property ID from the URL
if (!isset($_GET['id'])) {
    echo "No property ID provided.";
    exit();
}

$property_id = $_GET['id'];
$renter_id = $_SESSION['user_id'];

// Fetch the property details
$query = "SELECT * FROM rentals WHERE id = :property_id AND renter_id = :renter_id";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':property_id', $property_id);
$stmt->bindValue(':renter_id', $renter_id);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    echo "Property not found or you are not authorized to edit this property.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated property data from form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $rooms = $_POST['rooms'];
    $bathrooms = $_POST['bathrooms'];
    $is_furnished = isset($_POST['is_furnished']) ? 1 : 0;
    $has_garden = isset($_POST['has_garden']) ? 1 : 0;
    $has_kitchen = isset($_POST['has_kitchen']) ? 1 : 0;
    $is_air_conditioned = isset($_POST['is_air_conditioned']) ? 1 : 0;
    $has_parking = isset($_POST['has_parking']) ? 1 : 0;
    $has_security_cameras = isset($_POST['has_security_cameras']) ? 1 : 0;

    // Update the property
    $updateQuery = "UPDATE rentals SET 
                        title = :title, 
                        description = :description, 
                        price = :price, 
                        rooms = :rooms, 
                        bathrooms = :bathrooms, 
                        is_furnished = :is_furnished, 
                        has_garden = :has_garden, 
                        has_kitchen = :has_kitchen, 
                        is_air_conditioned = :is_air_conditioned, 
                        has_parking = :has_parking, 
                        has_security_cameras = :has_security_cameras 
                    WHERE id = :property_id AND renter_id = :renter_id";
    
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindValue(':title', $title);
    $updateStmt->bindValue(':description', $description);
    $updateStmt->bindValue(':price', $price);
    $updateStmt->bindValue(':rooms', $rooms);
    $updateStmt->bindValue(':bathrooms', $bathrooms);
    $updateStmt->bindValue(':is_furnished', $is_furnished);
    $updateStmt->bindValue(':has_garden', $has_garden);
    $updateStmt->bindValue(':has_kitchen', $has_kitchen);
    $updateStmt->bindValue(':is_air_conditioned', $is_air_conditioned);
    $updateStmt->bindValue(':has_parking', $has_parking);
    $updateStmt->bindValue(':has_security_cameras', $has_security_cameras);
    $updateStmt->bindValue(':property_id', $property_id);
    $updateStmt->bindValue(':renter_id', $renter_id);

    if ($updateStmt->execute()) {
        echo "<p>Property updated successfully. <a href='my_listings.php'>Go back to Listings</a></p>";
    } else {
        echo "<p>Error updating property.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="main-content">
            <h2>Edit Property: <?php echo htmlspecialchars($property['title']); ?></h2>

            <form method="POST" action="">
                <label for="title">Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required>

                <label for="description">Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($property['description']); ?></textarea>

                <label for="price">Price:</label>
                <input type="text" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required>

                <label for="rooms">Rooms:</label>
                <input type="number" name="rooms" value="<?php echo htmlspecialchars($property['rooms']); ?>" required>

                <label for="bathrooms">Bathrooms:</label>
                <input type="number" name="bathrooms" value="<?php echo htmlspecialchars($property['bathrooms']); ?>" required>

                <!-- Additional Features -->
                <label><input type="checkbox" name="is_furnished" <?php echo $property['is_furnished'] ? 'checked' : ''; ?>> Furnished</label>
                <label><input type="checkbox" name="has_garden" <?php echo $property['has_garden'] ? 'checked' : ''; ?>> Garden</label>
                <label><input type="checkbox" name="has_kitchen" <?php echo $property['has_kitchen'] ? 'checked' : ''; ?>> Kitchen</label>
                <label><input type="checkbox" name="is_air_conditioned" <?php echo $property['is_air_conditioned'] ? 'checked' : ''; ?>> Air Conditioned</label>
                <label><input type="checkbox" name="has_parking" <?php echo $property['has_parking'] ? 'checked' : ''; ?>> Parking</label>
                <label><input type="checkbox" name="has_security_cameras" <?php echo $property['has_security_cameras'] ? 'checked' : ''; ?>> Security Cameras</label>

                <button type="submit">Update Property</button>
            </form>

            <p><a href="my_listings.php">Go back to Listings</a></p>
        </div>
    </div>
</body>
</html>
