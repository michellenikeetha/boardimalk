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
            $_SESSION['loggedin'] = true;
            
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
    <title>Login - BodimBuddy.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1><a href="index.php"><!-- <h1>බෝඩිම.LK</h1> --><img src="../../RESOURCES/logos-04.png" alt="Logo"></a></h1>
        </div>
        <nav>
            <a href="index.php#home">Home</a>
            <a href="about.php">About</a>
            <a href="services.php">Services</a>
            <a href="customer_dashboard.php">Listings</a>
        </nav>
        <div class="cta-buttons">
            <a href="register.php"><button class="btn-secondary">Register</button></a>
            <a href="login.php"><button class="btn-primary">Login</button></a>
        </div>
    </header>

    <div class="login-container">
        <h2>Login to BodimBuddy.LK</h2>
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
                <a href="mailto:bodimbuddy.lk@gmail.com">bodimbuddy.lk@gmail.com</a><br/>
                <a href="tel:+94755009920">0755009920</a>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="about.php">About Us | </a>
                <a href="membership-packages.php">Membership | </a>
                <a href="termsofservice.php">Terms of Service</a>
            </div>
            <div class="footer-section">
                <h4>Follow Us</h4>
                <a href="https://web.facebook.com/BodimBuddy">Facebook | </a>
                <a href="https://www.instagram.com/bodim_buddy.lk?igsh=MzRlODBiNWFlZA==">Instagram </a>
                <!-- <a href="#twitter">Twitter</a> -->
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 BodimBuddy.LK. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>