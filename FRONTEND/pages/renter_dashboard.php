<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is a renter
if (!isLoggedIn() || $_SESSION['role'] !== 'renter') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Check for success or error messages
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear session messages after displaying
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renter Dashboard - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/renter_dashboard.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <span>Welcome, <?php echo htmlspecialchars($username); ?></span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <!-- <div class="sidebar">
            <ul>
                <li><a href="#properties">My Properties</a></li>
                <li><a href="#bookings">Bookings</a></li>
                <li><a href="#messages">Messages</a></li>
                <li><a href="#profile">Profile</a></li>
            </ul>
        </div> -->

        <div class="main-content">
            <h2>Renter Dashboard</h2>

            <!-- Display Success or Error Messages -->
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="dashboard-stats">
                <a href="listings.php">
                    <div class="stat-card">
                        <h3>Total Properties</h3>
                        <p>5</p>
                    </div>
                </a>
                <!-- <div class="stat-card">
                    <h3>Active Bookings</h3>
                    <p>3</p>
                </div> -->
                <div class="stat-card">
                    <h3>Messages</h3>
                    <p>12</p>
                </div>
            </div>
            
            <div>
                <h2>Post New Listing</h2>
                <form action="../../BACKEND/post_listing.php" method="POST" enctype="multipart/form-data">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" required>

                    <label for="full_address">Full Address:</label>
                    <input type="text" name="full_address" id="full_address" required>

                    <label for="city">City:</label>
                    <input type="text" name="city" id="city" required>

                    <label for="district">District:</label>
                    <select name="district" id="district" required>
                        <option value="">Select District</option>
                        <option value="Ampara">Ampara</option>
                        <option value="Anuradhapura">Anuradhapura</option>
                        <option value="Badulla">Badulla</option>
                        <option value="Batticaloa">Batticaloa</option>
                        <option value="Colombo">Colombo</option>
                        <option value="Galle">Galle</option>
                        <option value="Gampaha">Gampaha</option>
                        <option value="Hambantota">Hambantota</option>
                        <option value="Jaffna">Jaffna</option>
                        <option value="Kalutara">Kalutara</option>
                        <option value="Kandy">Kandy</option>
                        <option value="Kegalle">Kegalle</option>
                        <option value="Kilinochchi">Kilinochchi</option>
                        <option value="Kurunegala">Kurunegala</option>
                        <option value="Mannar">Mannar</option>
                        <option value="Matale">Matale</option>
                        <option value="Matara">Matara</option>
                        <option value="Moneragala">Moneragala</option>
                        <option value="Mullaitivu">Mullaitivu</option>
                        <option value="Nuwara Eliya">Nuwara Eliya</option>
                        <option value="Polonnaruwa">Polonnaruwa</option>
                        <option value="Puttalam">Puttalam</option>
                        <option value="Ratnapura">Ratnapura</option>
                        <option value="Trincomalee">Trincomalee</option>
                        <option value="Vavuniya">Vavuniya</option>
                    </select>

                    <label for="price">Monthly Rent:</label>
                    <input type="number" step="0.01" name="price" id="price" min="0" required>

                    <label for="rooms">No. of Rooms:</label>
                    <input type="number" name="rooms" id="rooms" min="0" required>

                    <label for="bathrooms">No. of Bathrooms:</label>
                    <input type="number" name="bathrooms" id="bathrooms" min="0" required>

                    <div class="checkbox-grid">
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_furnished" id="is_furnished">
                            <label for="is_furnished">Is Furnished?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="has_garden" id="has_garden">
                            <label for="has_garden">Has Garden?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="has_kitchen" id="has_kitchen">
                            <label for="has_kitchen">Has Kitchen?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_air_conditioned" id="is_air_conditioned">
                            <label for="is_air_conditioned">Air Conditioned?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="separate_utility_bills" id="separate_utility_bills">
                            <label for="separate_utility_bills">Separate Utility Bills?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="has_parking" id="has_parking">
                            <label for="has_parking">Has Parking?</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="has_security_cameras" id="has_security_cameras">
                            <label for="has_security_cameras">Has Security Cameras?</label>
                        </div>
                    </div>

                    <label for="proximity_to_road">Proximity to Main Road:</label>
                    <input type="text" name="proximity_to_road" id="proximity_to_road" required>

                    <label for="contact_whatsapp">WhatsApp Contact:</label>
                    <input type="text" name="contact_whatsapp" id="contact_whatsapp" required>

                    <div>
                        <label for="images">Upload Photos (minimum 5):</label>
                        <input type="file" name="images[]" id="images" multiple onchange="validateImages(this)" required>
                        <div id="imageValidationMessage" class="validation-message"></div>
                    </div>

                    <label for="description">Description:</label>
                    <textarea name="description" id="description" placeholder="Enter extra listing details"></textarea>

                    <button type="submit">Post Listing</button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function validateImages(input) {
            const validationMessage = document.getElementById('imageValidationMessage');
            const submitButton = document.querySelector('form button[type="submit"]');
            
            if (input.files.length < 5) {
                validationMessage.textContent = 'Please upload at least 5 photos.';
                validationMessage.className = 'validation-message show';
                input.classList.add('invalid-input');
                submitButton.disabled = true;
            } else {
                validationMessage.className = 'validation-message';
                input.classList.remove('invalid-input');
                submitButton.disabled = false;
            }
        }

        // Add form submission validation
        document.querySelector('form').addEventListener('submit', function(event) {
            const imageInput = document.getElementById('images');
            if (imageInput.files.length < 5) {
                event.preventDefault();
                const validationMessage = document.getElementById('imageValidationMessage');
                validationMessage.textContent = 'Please upload at least 5 photos.';
                validationMessage.className = 'validation-message show';
                imageInput.classList.add('invalid-input');
                imageInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>

</body>
</html>