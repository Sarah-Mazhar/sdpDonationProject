<?php
class Event {
    public $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getEventsByMonth($month) {
        $query = "SELECT * FROM events WHERE month = :month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":month", $month);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMonths() {
        return [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
    }
}
