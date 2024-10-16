<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: ../FRONTEND/pages/login.php");
    exit();
}

$renter_id = $_SESSION['id'];  // Assuming user ID is stored in session

// Check if listing ID is provided
if (isset($_GET['id'])) {
    $listing_id = $_GET['id'];

    // Verify if the listing belongs to the logged-in renter
    $query = "SELECT * FROM rentals WHERE id = ? AND renter_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $listing_id, $renter_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Proceed to delete the listing
        $delete_query = "DELETE FROM rentals WHERE id = ? AND renter_id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('ii', $listing_id, $renter_id);
        if ($delete_stmt->execute()) {
            echo "Listing deleted successfully.";
        } else {
            echo "Error deleting listing: " . $conn->error;
        }
    } else {
        echo "Unauthorized action or listing not found.";
    }
} else {
    echo "Invalid listing ID.";
}

header("Location: ../FRONTEND/pages/renter_dashboard.php");
exit();
?>
