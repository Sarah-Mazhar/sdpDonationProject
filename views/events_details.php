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
        /* General body styling */
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

        textarea {
            resize: vertical;
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .disabled-input {
            background-color: #f1f1f1;
            cursor: not-allowed;
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

                <div class="form-group">
                    <label for="event_name">Event Name:</label>
                    <input type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="month">Month:</label>
                    <input type="text" id="month" name="month" value="<?php echo htmlspecialchars($event['month']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_description">Event Description:</label>
                    <textarea id="event_description" name="event_description" rows="5" required><?php echo htmlspecialchars($event['event_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="event_date">Event Date:</label>
                    <input type="text" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_location">Event Location:</label>
                    <input type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="no_of_applicants">Number of Applicants:</label>
                    <input type="number" id="no_of_applicants" name="no_of_applicants" value="<?php echo htmlspecialchars($event['no_of_applicants']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="created_at">Created At:</label>
                    <input type="text" id="created_at" name="created_at" value="<?php echo htmlspecialchars($event['created_at']); ?>" disabled class="disabled-input">
                </div>

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
