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

// Fetch event details
$query = "SELECT * FROM events WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $eventId);
$stmt->execute();

// Check for any SQL errors
if ($stmt->errorCode() != '00000') {
    var_dump($stmt->errorInfo());
    die();
}

// Fetch the event
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_event'])) {
    // Get form data
    $eventName = $_POST['event_name'];
    $month = $_POST['month'];
    $eventDescription = $_POST['event_description'];
    $eventDate = $_POST['event_date'];
    $eventLocation = $_POST['event_location'];
    $noOfApplicants = $_POST['no_of_applicants'];

    // Update query
    $updateQuery = "UPDATE events SET 
                    event_name = :event_name,
                    month = :month,
                    event_description = :event_description,
                    event_date = :event_date,
                    event_location = :event_location,
                    no_of_applicants = :no_of_applicants,
                    updated_at = NOW()
                    WHERE id = :id";

    // Prepare the update query
    $stmt = $conn->prepare($updateQuery);

    // Bind parameters
    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':month', $month);
    $stmt->bindParam(':event_description', $eventDescription);
    $stmt->bindParam(':event_date', $eventDate);
    $stmt->bindParam(':event_location', $eventLocation);
    $stmt->bindParam(':no_of_applicants', $noOfApplicants);
    $stmt->bindParam(':id', $eventId);

    // Execute the query
    if ($stmt->execute()) {
        header("Location: events.php"); // Redirect after successful update
        exit();
    } else {
        echo "Error updating event.";
    }
}
?>