<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

$renter_id = $_SESSION['id'];  // Assuming user ID is stored in session

// Fetch the listing details based on the ID
if (isset($_GET['id'])) {
    $listing_id = $_GET['id'];

    // Check if the listing belongs to the logged-in renter
    $query = "SELECT * FROM rentals WHERE id = ? AND renter_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $listing_id, $renter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $listing = $result->fetch_assoc();
    } else {
        echo "Listing not found or unauthorized access.";
        exit();
    }
} else {
    echo "Invalid listing ID.";
    exit();
}

// Handle form submission for editing the listing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $contact_whatsapp = $_POST['contact_whatsapp'];

    // Handle image uploads if new images are uploaded
    $image_paths = $listing['images']; // Keep the existing images if none are uploaded
    if (isset($_FILES['images']) && $_FILES['images']['name'][0] != '') {
        $image_paths = [];
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = $_FILES['images']['name'][$key];
            $image_tmp = $_FILES['images']['tmp_name'][$key];
            $target_path = "../../uploads/" . basename($image_name);
            move_uploaded_file($image_tmp, $target_path);
            $image_paths[] = $target_path;
        }
        $image_paths = implode(',', $image_paths);
    }

    // Update the listing in the database
    $update_query = "UPDATE rentals SET title = ?, description = ?, location = ?, price = ?, contact_whatsapp = ?, images = ? WHERE id = ? AND renter_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('sssdssii', $title, $description, $location, $price, $contact_whatsapp, $image_paths, $listing_id, $renter_id);

    if ($update_stmt->execute()) {
        echo "Listing updated successfully.";
    } else {
        echo "Error updating listing: " . $conn->error;
    }

    header("Location: renter_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>
    <h2>Edit Listing</h2>
    <form action="edit_listing.php?id=<?php echo $listing_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($listing['title']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($listing['description']); ?></textarea>

        <label for="location">Location:</label>
        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($listing['location']); ?>" required>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" id="price" value="<?php echo htmlspecialchars($listing['price']); ?>" required>

        <label for="contact_whatsapp">WhatsApp Contact:</label>
        <input type="text" name="contact_whatsapp" id="contact_whatsapp" value="<?php echo htmlspecialchars($listing['contact_whatsapp']); ?>" required>

        <label for="images">Upload New Photos (optional):</label>
        <input type="file" name="images[]" id="images" multiple>

        <p>Current Images: <?php echo htmlspecialchars($listing['images']); ?></p>

        <button type="submit">Update Listing</button>
    </form>
</body>
</html>
