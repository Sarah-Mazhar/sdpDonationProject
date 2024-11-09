<?php

// models/MoneyDonation.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../donation/DonationI.php';


class MoneyDonation implements DonationI {

    public function donate($userId, $amount, $quantity=null) {
        // Create database connection
        $db = Database::getInstance()->getConnection();

        // Prepare SQL query to insert donation
        $query = "INSERT INTO donations (type, amount, user_id) VALUES (:type, :amount, :user_id)";
        $stmt = $db->prepare($query);

        // Bind parameters and execute query
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':user_id', $userId);
        
        $type = 'money'; // Donation type is money

        $stmt->execute(); // Execute the query to insert donation record
    }
}
?>
