<?php
require_once 'db_connect.php';
require_once 'session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: ../FRONTEND/pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $renter_id = $_SESSION['user_id'];

    // Check if there are at least 5 images
    if (empty($_FILES['images']['name'][0]) || count($_FILES['images']['name']) < 5) {
        $_SESSION['error_message'] = "Please upload at least 5 photos.";
        header("Location: ../FRONTEND/pages/renter_dashboard.php");
        exit();
    }

    // Collect form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $full_address = $_POST['full_address'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $price = $_POST['price'];
    $rooms = $_POST['rooms'];
    $bathrooms = $_POST['bathrooms'];
    $is_furnished = isset($_POST['is_furnished']) ? 1 : 0;
    $has_garden = isset($_POST['has_garden']) ? 1 : 0;
    $has_kitchen = isset($_POST['has_kitchen']) ? 1 : 0;
    $is_air_conditioned = isset($_POST['is_air_conditioned']) ? 1 : 0;
    $separate_utility_bills = isset($_POST['separate_utility_bills']) ? 1 : 0;
    $has_parking = isset($_POST['has_parking']) ? 1 : 0;
    $has_security_cameras = isset($_POST['has_security_cameras']) ? 1 : 0;
    $proximity_to_road = $_POST['proximity_to_road'];
    $contact_whatsapp = $_POST['contact_whatsapp'];

    // Handle image uploads
    $uploaded_images = [];
    $total_files = count($_FILES['images']['name']);
    
    for ($i = 0; $i < $total_files; $i++) {
        $file_name = $_FILES['images']['name'][$i];
        $file_tmp = $_FILES['images']['tmp_name'][$i];
        $file_path = '../RESOURCES/uploads/' . uniqid() . '-' . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $uploaded_images[] = $file_path;
        }
    }

    $images = implode(',', $uploaded_images);

    // Insert into the database
    $query = "INSERT INTO rentals (renter_id, title, full_address, city, district, price, rooms, bathrooms, is_furnished, has_garden, has_kitchen, is_air_conditioned, separate_utility_bills, has_parking, has_security_cameras, proximity_to_road, contact_whatsapp, images, description) 
              VALUES (:renter_id, :title, :full_address, :city, :district, :price, :rooms, :bathrooms, :is_furnished, :has_garden, :has_kitchen, :is_air_conditioned, :separate_utility_bills, :has_parking, :has_security_cameras, :proximity_to_road, :contact_whatsapp, :images, :description)";
    
    $stmt = $pdo->prepare($query);

    // Bind parameters using PDO's bindValue
    $stmt->bindValue(':renter_id', $renter_id);
    $stmt->bindValue(':title', $title);
    $stmt->bindValue(':full_address', $full_address);
    $stmt->bindValue(':city', $city);
    $stmt->bindValue(':district', $district);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':rooms', $rooms);
    $stmt->bindValue(':bathrooms', $bathrooms);
    $stmt->bindValue(':is_furnished', $is_furnished);
    $stmt->bindValue(':has_garden', $has_garden);
    $stmt->bindValue(':has_kitchen', $has_kitchen);
    $stmt->bindValue(':is_air_conditioned', $is_air_conditioned);
    $stmt->bindValue(':separate_utility_bills', $separate_utility_bills);
    $stmt->bindValue(':has_parking', $has_parking);
    $stmt->bindValue(':has_security_cameras', $has_security_cameras);
    $stmt->bindValue(':proximity_to_road', $proximity_to_road);
    $stmt->bindValue(':contact_whatsapp', $contact_whatsapp);
    $stmt->bindValue(':images', $images);
    $stmt->bindValue(':description', $description);

    // Execute the statement
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Listing created successfully.";
        header("Location: ../FRONTEND/pages/renter_dashboard.php");
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->errorInfo()[2];
        header("Location: ../FRONTEND/pages/renter_dashboard.php");
    }
}
?>
