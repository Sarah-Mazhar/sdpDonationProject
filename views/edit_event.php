<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/Database.php';

$database = Database::getInstance();
$conn = $database->getConnection();

// Get event ID from query parameter
$eventId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$eventId) {
    echo "No event ID provided.";
    die();
}

// Check if form is submitted
if (isset($_POST['save_event'])) {
    // Get form data
    $eventName = $_POST['event_name'];
    $month = $_POST['month'];
    $eventDescription = $_POST['event_description'];

    // Update query
    $query = "UPDATE events SET 
                event_name = :event_name,
                month = :month,
                event_description = :event_description,
                updated_at = NOW()
              WHERE id = :id";

    // Prepare the query
    $stmt = $conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':event_description', $eventDescription);
    $stmt->bindParam(':id', $eventId);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to events.php after successful update
        header("Location: events.php");
        exit();
    } else {
        echo "Error updating event.";
    }
}
?>
