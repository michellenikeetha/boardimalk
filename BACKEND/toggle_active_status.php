<?php
require_once 'db_connect.php';
require_once 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['property_id'], $_POST['is_active'])) {
    $property_id = $_POST['property_id'];
    $renter_id = $_SESSION['user_id'];

    // Toggle the property status
    $new_status = $_POST['is_active'] ? 0 : 1;

    // Update the property status
    $query = "UPDATE rentals SET is_active = :new_status WHERE id = :property_id AND renter_id = :renter_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':new_status', $new_status);
    $stmt->bindValue(':property_id', $property_id);
    $stmt->bindValue(':renter_id', $renter_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }
}
?>
