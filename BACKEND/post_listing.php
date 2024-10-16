<?php
require_once 'db_connect.php';
require_once 'session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: ../FRONTEND/pages/login.php");
    exit();
}

$renter_id = $_SESSION['id'];  // Assuming user ID is stored in session

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $contact_whatsapp = $_POST['contact_whatsapp'];
    
    // Handle image uploads
    $image_paths = [];
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = $_FILES['images']['name'][$key];
            $image_tmp = $_FILES['images']['tmp_name'][$key];
            $target_path = "../../uploads/" . basename($image_name);
            move_uploaded_file($image_tmp, $target_path);
            $image_paths[] = $target_path;
        }
    }
    
    $images = implode(',', $image_paths);

    // Insert into the database
    $query = "INSERT INTO rentals (renter_id, title, description, location, price, contact_whatsapp, images) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssdss', $renter_id, $title, $description, $location, $price, $contact_whatsapp, $images);
    
    if ($stmt->execute()) {
        echo "Listing posted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
