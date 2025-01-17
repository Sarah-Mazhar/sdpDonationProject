<?php
// views/view_events.php

// Database connection and query fetching
require_once __DIR__ . '/../config/Database.php';
$database = Database::getInstance();
$conn = $database->getConnection();

// Fetch all events ordered by ID
$query = "SELECT * FROM events ORDER BY id ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events</title>
    <style>
        /* Basic styling for the events page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 2em;
        }

        .events-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .event {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .event:last-child {
            border-bottom: none;
        }

        .event-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #4CAF50;
            text-decoration: none;
        }

        .event-title:hover {
            text-decoration: underline;
        }

        .event-description {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 10px;
        }

        .event-details {
            font-size: 0.9em;
            color: #888;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header>
    <h1>All Events</h1>
</header>

<main>
    <div class="events-container">
        <?php if (!empty($events)) : ?>
            <?php foreach ($events as $event) : ?>
                <div class="event">
                    <!-- Link each event title to the event details page -->
                    <a href="events_details.php?id=<?php echo $event['id']; ?>" class="event-title">
                        <?php echo htmlspecialchars($event['event_name']); ?>
                    </a>
                    <p class="event-description">
                        <?php echo nl2br(htmlspecialchars($event['event_description'])); ?>
                    </p>
                    <p class="event-details">
                        <strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?>
                    </p>
                    <p class="event-details">
                        <strong>Status:</strong> <?php echo htmlspecialchars($event['status']); ?>
                    </p>
                    <p class="event-details">
                        <strong>Max Volunteers:</strong> <?php echo htmlspecialchars($event['max_no_volunteers']); ?>
                    </p>
                    <p class="event-details">
                        <strong>Event Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($event['created_at'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p class="text-center">No events available at the moment.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Charity Event Management</p>
</footer>

</body>
</html>
