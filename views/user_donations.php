<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/facades/DonationFacade.php'; // Include the new facade class

// Get database connection
$database = Database::getInstance();
$conn = $database->getConnection();

// Initialize the Facade
$donationFacade = new DonationFacade($conn);

// Use the logged-in user's ID from the session
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in!');</script>";
    exit;
}

$userId = $_SESSION['user_id']; // Fetch user ID dynamically from the session

try {
    // Fetch user donations through the facade
    $donations = $donationFacade->getDonationsByUser($userId);
} catch (PDOException $e) {
    // Handle database error
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    $donations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Donations</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">User Donations</h1>
        <?php if (!empty($donations)) : ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Type</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($donations as $donation) : ?>
                        <tr>
                            <td><?= htmlspecialchars($donation['user_id']) ?></td>
                            <td><?= htmlspecialchars($donation['type']) ?></td>
                            <td><?= htmlspecialchars($donation['details']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <div class="alert alert-info text-center">No donations found for this user.</div>
        <?php endif; ?>
    </div>
</body>
</html>
