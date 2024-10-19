<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Both email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Store username in session
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            switch($user['role']) {
                case 'renter':
                    header("Location: renter_dashboard.php");
                    break;
                case 'customer':
                    header("Location: customer_dashboard.php");
                    break;
                case 'admin':
                    header("Location: admin_dashboard.php"); //admin, admin123
                    break;
                default:
                    header("Location: login.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login to බෝඩිම.LK</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post" class="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <div class="login-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <!-- <p><a href="forgot_password.php">Forgot Password?</a></p> -->
        </div>
    </div>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Connect with Us</h4>
                <a href="#email">Email Us</a>
                <a href="#visit">Visit Us</a>
                <a href="#book">Book Now</a>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="#about">About Us</a>
                <a href="#faq">FAQ</a>
                <a href="#terms">Terms of Service</a>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <a href="#facebook">Facebook</a>
                <a href="#instagram">Instagram</a>
                <a href="#twitter">Twitter</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 බෝඩිම.LK. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>