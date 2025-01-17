<?php
class Event {
    public $conn;

    // Constructor that accepts a database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch events by the selected month
    public function getEventsByMonth($month) {
        // Prepare SQL query to fetch events for the given month
        $query = "SELECT * FROM events WHERE month = :month";
        $stmt = $this->conn->prepare($query);
        // Bind the month parameter to prevent SQL injection
        $stmt->bindParam(":month", $month);
        $stmt->execute();
        // Return the fetched results as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all available months - Change visibility to public
    public function getAllMonths() {
        // Return an array of month names
        return [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
    }
}
