<?php

// models/MoneyDonation.php

require_once __DIR__ . '/DonationTemplate.php'; // Template Pattern base class
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../donation/DonationI.php';

class MoneyDonation extends DonationTemplate implements DonationI {

    protected function initializeDonation($amountOrItem, $quantity) {
        echo "Initializing a money donation of {$amountOrItem}.<br>";
    }

    protected function processDonation($userId, $amountOrItem, $quantity) {
        // Create database connection
        $db = Database::getInstance()->getConnection();

        $type = 'money'; // Donation type is money

        // Prepare SQL query to insert donation
        $query = "INSERT INTO donations (type, amount, user_id) VALUES (:type, :amount, :user_id)";
        $stmt = $db->prepare($query);

        // Bind parameters and execute query
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':amount', $amountOrItem);
        $stmt->bindParam(':user_id', $userId);

       

        $stmt->execute(); // Execute the query to insert donation record

        echo "Processed money donation of {$amountOrItem} for User ID {$userId}.<br>";
    }

    protected function finalizeDonation() {
        echo "Money donation finalized successfully.<br>";
    }

    public function donate($userId, $amount, $quantity = null) {
        parent::donate($userId, $amount, $quantity); // Calls the template's donation method
    }

    public function generateReceipt($userId, $amount, $paymentMethod) {
        $receipt = "Receipt:\n";
        $receipt .= "User ID: {$userId}\n";
        $receipt .= "Amount Donated: {$amount}\n";
        $receipt .= "Payment Method: {$paymentMethod}\n";
        $receipt .= "Thank you for your generosity!\n";
    
        // Return the receipt for session storage or printing
        return $receipt;
    }
    
}
?>
