<?php
require_once 'db_connect.php';
require_once 'session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $renter_id = $_SESSION['id'];
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

    // Handle image uploads (ensuring at least 5 photos)
    $uploaded_images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $total_files = count($_FILES['images']['name']);
        if ($total_files >= 5) { // Check for at least 5 photos
            for ($i = 0; $i < $total_files; $i++) {
                $file_name = $_FILES['images']['name'][$i];
                $file_tmp = $_FILES['images']['tmp_name'][$i];
                $file_path = '../RESOURCES/uploads/' . uniqid() . '-' . $file_name;

                // Save the file to the uploads directory
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $uploaded_images[] = $file_path;
                }
            }
        } else {
            echo "Please upload at least 5 photos.";
            exit();
        }
    }

    $images = implode(',', $uploaded_images); // Store image paths as comma-separated values

    // Insert into the database
    $query = "INSERT INTO rentals (renter_id, title, description, full_address, city, district, price, rooms, bathrooms, is_furnished, has_garden, has_kitchen, is_air_conditioned, separate_utility_bills, has_parking, has_security_cameras, proximity_to_road, contact_whatsapp, images) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('isssssdiiiiiiiissss', $renter_id, $title, $description, $full_address, $city, $district, $price, $rooms, $bathrooms, $is_furnished, $has_garden, $has_kitchen, $is_air_conditioned, $separate_utility_bills, $has_parking, $has_security_cameras, $proximity_to_road, $contact_whatsapp, $images);

    if ($stmt->execute()) {
        header("Location: renter_dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
