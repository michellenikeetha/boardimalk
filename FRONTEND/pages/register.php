<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Username or email already exists.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password, $role])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                
                // Redirect based on role
                switch($role) {
                    case 'renter':
                        header("Location: renter_dashboard.php");
                        break;
                    case 'customer':
                        header("Location: customer_dashboard.php");
                        break;
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                }
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/register.css">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1><a href="index.html">බෝඩිම.LK</a></h1>
        </div>
        <nav>
            <a href="#home">Home</a>
            <a href="#featured">About</a>
            <a href="#landlords">Services</a>
            <a href="customer_dashboard.php">Listings</a>
        </nav>
        <div class="cta-buttons">
            <a href="register.php"><button class="btn-secondary">Register</button></a>
            <a href="login.php"><button class="btn-primary">Login</button></a>
        </div>
    </header>
    
    <div class="register-container">
        <h2>Register for බෝඩිම.LK</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="post" class="register-form">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="renter">Renter</option>
                    <option value="customer">Customer</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Register</button>
            </div>
        </form>
        <div class="register-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
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