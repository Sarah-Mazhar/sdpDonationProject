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
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .event {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
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
            margin: 5px 0;
        }

        .event-date {
            font-size: 0.8em;
            color: #888;
        }

        .add-event-container {
            margin: 20px auto;
            text-align: center;
        }

        .add-event-link {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease-in-out;
        }

        .add-event-link:hover {
            background-color: #45a049;
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
    <h1>All Charity Events</h1>
</header>

<div class="add-event-container">
    <a href="add_event.php" class="add-event-link">Add New Event</a>
</div>

<main>
    <div class="events-container">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <div class="event">
                    <!-- Link each event title to the event details page -->
                    <a href="events_details.php?id=<?php echo $event['id']; ?>" class="event-title">
                        <?php echo htmlspecialchars($event['event_name']); ?>
                    </a>
                    <p class="event-description"><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>
                    <p class="event-date">Event Date: <?php echo date("F j, Y, g:i a", strtotime($event['created_at'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No events found.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <p>&copy; 2024 Charity Event Management</p>
</footer>

</body>
</html>
