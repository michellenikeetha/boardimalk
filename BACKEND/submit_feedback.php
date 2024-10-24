<?php
require_once 'db_connect.php';
require_once 'session.php';

if (!isLoggedIn() || $_SESSION['role'] !== 'customer') {
    header('Location: ../FRONTEND/pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['user_id'];
    $rental_id = $_POST['rental_id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    try {
        // Insert feedback into the database
        $stmt = $pdo->prepare("INSERT INTO feedback (customer_id, rental_id, rating, feedback) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customer_id, $rental_id, $rating, $feedback]);

        // Update the average rating for the property
        $stmt_avg = $pdo->prepare("UPDATE rentals r 
            SET r.rating = (SELECT AVG(f.rating) FROM feedback f WHERE f.rental_id = ?)
            WHERE r.id = ?");
        $stmt_avg->execute([$rental_id, $rental_id]);

        header('Location: ../FRONTEND/pages/customer_dashboard.php?feedback_submitted=1');
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        header('Location: customer_dashboard.php?error=1');
    }
}
?>
