<?php
class Event {
    private $conn;

    // Constructor that accepts a database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Fetch events by the selected month
    public function getEventsByMonth($month) {
        $query = "SELECT * FROM events WHERE month = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":month", $month);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all available months
    public function getAllMonths() {
        return [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
    }
}
?>
