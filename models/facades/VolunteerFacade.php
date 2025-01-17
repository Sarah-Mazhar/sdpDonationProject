<?php
class VolunteerFacade {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUserDetails($userId) {
        $query = "SELECT id, email, mobile FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getEventDetails($eventId) {
        $query = "SELECT * FROM events WHERE id = :event_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementApplicants($eventId) {
        $query = "UPDATE events SET no_of_applicants = no_of_applicants + 1 WHERE id = :event_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
