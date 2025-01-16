<?php
// EventController.php

require_once __DIR__ . '/../config/Database.php';

class EventController {
    private $db;

    // Constructor initializes the database connection
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();  // Get the database connection from the Database class
    }

    // Method to display the months dropdown
    public function displayMonths() {
        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        echo '<form method="GET" action="index.php">';
        echo '<select name="month" required>';
        foreach ($months as $month) {
            echo "<option value='$month'>$month</option>";
        }
        echo '</select>';
        echo '<button type="submit" name="action" value="view_events">View Events</button>';
        echo '</form>';
    }

    // Method to display events for the selected month
    public function displayEvents($month) {
        // Prepare the SQL query to fetch events for the selected month
        $stmt = $this->db->prepare("SELECT * FROM events WHERE month = :month ORDER BY created_at ASC");
        $stmt->bindParam(':month', $month, PDO::PARAM_STR);  // Bind the month parameter to prevent SQL injection
        $stmt->execute();  // Execute the query
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Corrected to use PDO::FETCH_ASSOC for associative array

        if ($events) {
            include 'views/view_events.php';  // Include the view to display the events
        } else {
            echo "No events found for this month.";  // If no events are found, show a message
        }
    }
}
?>
