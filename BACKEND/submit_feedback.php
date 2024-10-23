<?php
require_once 'db_connect.php';
require_once 'session.php';

// Ensure the user is logged in and is a customer
if (!isLoggedIn() || $_SESSION['role'] !== 'customer') {
    header("Location: ../FRONTEND/pages/login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rental_id = $_POST['rental_id'];
    $rating = $_POST['rating'];
    $feedback_text = $_POST['feedback'];
    $customer_id = $_SESSION['user_id'];

    // Validate inputs
    if (empty($rental_id) || empty($rating) || empty($feedback_text)) {
        echo "All fields are required.";
        exit();
    }

    try {
        // Insert the feedback into the database
        $query = "INSERT INTO feedback (customer_id, rental_id, rating, feedback) 
                  VALUES (:customer_id, :rental_id, :rating, :feedback)";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':customer_id', $customer_id);
        $stmt->bindValue(':rental_id', $rental_id);
        $stmt->bindValue(':rating', $rating);
        $stmt->bindValue(':feedback', $feedback_text);
        $stmt->execute();

        // Update the average rating in the rentals table
        $updateRatingQuery = "UPDATE rentals r 
                              SET r.rating = (SELECT AVG(f.rating) 
                                              FROM feedback f 
                                              WHERE f.rental_id = r.id) 
                              WHERE r.id = :rental_id";
        $updateStmt = $pdo->prepare($updateRatingQuery);
        $updateStmt->bindValue(':rental_id', $rental_id);
        $updateStmt->execute();

        echo "Feedback submitted successfully.";
    } catch (PDOException $e) {
        echo "Error submitting feedback: " . $e->getMessage();
    }
}
?>
