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

if ($event):
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event Details</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 20px;
        }

        footer {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .event-details-container {
            max-width: 900px;
            margin: 40px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            font-size: 24px;
            color: #333;
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #555;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <header>
        <h1>Edit Event Details</h1>
    </header>

    <main>
        <div class="event-details-container">
            <h2>Update Event Information</h2>
            <form action="edit_event.php?id=<?php echo $eventId; ?>" method="POST">

                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>

                <label for="event_location">Event Location:</label>
                <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>

                <label for="event_date">Event Date:</label>
                <input type="date" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

                <label for="event_time">Event Time:</label>
                <input type="time" id="event_time" name="event_time" value="<?php echo htmlspecialchars($event['event_time']); ?>" required>

                <label for="max_no_volunteers">Max Number of Volunteers:</label>
                <input type="number" id="max_no_volunteers" name="max_no_volunteers" value="<?php echo htmlspecialchars($event['max_no_volunteers']); ?>" required>

                <label for="event_description">Event Description:</label>
                <textarea id="event_description" name="event_description" rows="5" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>

                <!-- Save Changes Button -->
                <button type="submit" name="save_event">Save Changes</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Charity Event Management</p>
    </footer>

</body>
</html>

<?php
else:
    echo "Event not found.";
endif;
?>
