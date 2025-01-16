<?php
// views/view_events.php

// Database connection and query fetching
require_once __DIR__ . '/../config/Database.php';
$database = Database::getInstance();
$conn = $database->getConnection();

// Fetch all events for each month
$query = "SELECT * FROM events ORDER BY MONTH(STR_TO_DATE(month, '%M'))";
$stmt = $conn->prepare($query);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group events by month
$groupedEvents = [];
foreach ($events as $event) {
    $groupedEvents[$event['month']][] = $event;
}

$months = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July', 
    'August', 'September', 'October', 'November', 'December'
];
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
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-items: center;
        }

        .month-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            width: 250px;
            transition: transform 0.3s ease-in-out;
        }

        .month-card:hover {
            transform: scale(1.05);
        }

        .month-title {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .event-list {
            margin-top: 10px;
        }

        .event-title {
            font-weight: bold;
            color: #4CAF50;
        }

        .event-description {
            font-size: 0.9em;
            color: #555;
            margin-bottom: 10px;
        }

        .event-date {
            font-size: 0.8em;
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
        <h1>All Charity Events</h1>
    </header>

    <main>
        <div class="events-container">
            <?php foreach ($months as $month): ?>
                <div class="month-card">
                    <h2 class="month-title"><?php echo htmlspecialchars($month); ?></h2>
                    <div class="event-list">
                        <?php if (isset($groupedEvents[$month])): ?>
                            <?php foreach ($groupedEvents[$month] as $event): ?>
                                <div class="event">
                                    <p class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></p>
                                    <p class="event-description"><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>
                                    <p class="event-date">Event Date: <?php echo date("F j, Y, g:i a", strtotime($event['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No events available for this month.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Charity Event Management</p>
    </footer>

</body>
</html>
