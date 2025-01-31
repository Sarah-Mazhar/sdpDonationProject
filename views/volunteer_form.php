<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/facades/VolunteerFacade.php';

$database = Database::getInstance();
$conn = $database->getConnection();
$volunteerFacade = new VolunteerFacade($conn);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in!');</script>";
    exit;
}

$userId = $_SESSION['user_id'];
$eventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

try {
    $user = $volunteerFacade->getUserDetails($userId);
    $event = $volunteerFacade->getEventDetails($eventId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['volunteer'])) {
        $volunteerFacade->incrementApplicants($eventId);
        echo "<script>alert('Thank you for volunteering!');</script>";
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    $user = null;
    $event = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered User and Event Details</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Registered User and Event Details</h1>
        <?php if ($user && $event) : ?>
            <h2>User Details</h2>
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <th>Mobile</th>
                    <td><?= htmlspecialchars($user['mobile']) ?></td>
                </tr>
            </table>

            <h2>Event Details</h2>
            <table class="table table-bordered">
                <tr>
                    <th>Event Name</th>
                    <td><?= htmlspecialchars($event['event_name']) ?></td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td><?= date("F j, Y", strtotime($event['event_date'])) ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?= nl2br(htmlspecialchars($event['event_description'])) ?></td>
                </tr>
                <tr>
                    <th>Location</th>
                    <td><?= htmlspecialchars($event['event_location']) ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= htmlspecialchars($event['status']) ?></td>
                </tr>
            </table>

            <form method="POST">
                <button type="submit" name="volunteer" class="btn btn-primary">Volunteer</button>
            </form>
        <?php else : ?>
            <div class="alert alert-danger text-center">User or event not found.</div>
        <?php endif; ?>
    </div>
</body>
</html>
