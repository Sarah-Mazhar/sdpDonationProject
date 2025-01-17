<?php
class DonationFacade {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getDonationsByUser($userId) {
        $query = "SELECT user_id, type, 
                  CASE 
                    WHEN type = 'food' THEN CONCAT(food_item, ' (', quantity, ')')
                    WHEN type = 'money' THEN CONCAT('$', amount)
                    ELSE 'Unknown'
                  END AS details
                  FROM donations WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
