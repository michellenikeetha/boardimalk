<?php
require_once 'db_connect.php';
require_once 'session.php';

// Check if the request is POST and if user is an admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'admin') {
    $property_id = $_POST['property_id'];
    $is_removed = $_POST['is_removed'] == 1 ? 0 : 1;  // Toggle between 1 (removed) and 0 (not removed)

    // Update the removed_by_admin status
    $stmt = $pdo->prepare("UPDATE rentals SET removed_by_admin = :is_removed WHERE id = :property_id");
    $stmt->bindValue(':is_removed', $is_removed, PDO::PARAM_INT);
    $stmt->bindValue(':property_id', $property_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }
}
?>
