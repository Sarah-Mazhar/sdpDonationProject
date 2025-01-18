<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/Database.php';

$database = Database::getInstance();
$conn = $database->getConnection();

$eventId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$eventId) {
    echo "No event ID provided.";
    die();
}

if (isset($_POST['save_event'])) {
    $eventName = $_POST['event_name'];
    $eventLocation = $_POST['event_location'];
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];
    $maxNoVolunteers = $_POST['max_no_volunteers'];
    $eventDescription = $_POST['event_description'];

    $query = "UPDATE events SET 
                event_name = :event_name,
                event_location = :event_location,
                event_date = :event_date,
                event_time = :event_time,
                max_no_volunteers = :max_no_volunteers,
                event_description = :event_description,
                updated_at = NOW()
              WHERE id = :id";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':event_location', $eventLocation);
    $stmt->bindParam(':event_date', $eventDate);
    $stmt->bindParam(':event_time', $eventTime);
    $stmt->bindParam(':max_no_volunteers', $maxNoVolunteers);
    $stmt->bindParam(':event_description', $eventDescription);
    $stmt->bindParam(':id', $eventId);

    if ($stmt->execute()) {
        header("Location: events.php");
        exit();
    } else {
        echo "Error updating event.";
    }
}
?>
