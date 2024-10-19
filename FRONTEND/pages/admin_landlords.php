<?php
require_once '../../BACKEND/db_connect.php';
require_once '../../BACKEND/session.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all landlords
$query_landlords = "SELECT * FROM users WHERE role = 'renter'";
$stmt_landlords = $pdo->query($query_landlords);
$landlords = $stmt_landlords->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Landlords - බෝඩිම.LK</title>
    <link rel="stylesheet" href="../CSS/styles.css">
    <link rel="stylesheet" href="../CSS/admin_landlords.css">
</head>
<body>
    <nav class="dashboard-nav">
        <div class="nav-brand">බෝඩිම.LK</div>
        <div class="nav-items">
            <span>Welcome, Admin</span>
            <a href="../../BACKEND/logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>All Landlords</h2>

        <div class="landlord-listings">
            <?php if (empty($landlords)): ?>
                <p>No landlords found.</p>
            <?php else: ?>
                <table class="landlord-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($landlords as $landlord): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($landlord['username']); ?></td>
                            <td><?php echo htmlspecialchars($landlord['email']); ?></td>
                            <td>
                                <a href="admin_landlord_properties.php?landlord_id=<?php echo $landlord['id']; ?>">View Properties</a>
                                <!-- Add more actions as needed -->
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
