<?php

require_once __DIR__ . '/DonationTemplate.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../donation/DonationI.php';

class MoneyDonation extends DonationTemplate implements DonationI {

    protected function initializeDonation($amountOrItem, $quantity) {
        echo "Initializing a money donation of {$amountOrItem}.<br>";
    }

    protected function processDonation($userId, $amountOrItem, $quantity) {
        $db = Database::getInstance()->getConnection();
        $type = 'money';
        $query = "INSERT INTO donations (type, amount, user_id) VALUES (:type, :amount, :user_id)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':amount', $amountOrItem);
        $stmt->bindParam(':user_id', $userId);

        $stmt->execute(); 

        echo "Processed money donation of {$amountOrItem} for User ID {$userId}.<br>";
    }

    protected function finalizeDonation() {
        echo "Money donation finalized successfully.<br>";
    }

    public function donate($userId, $amount, $quantity = null) {
        parent::donate($userId, $amount, $quantity);
    }

    public function generateReceipt($userId, $amount, $paymentMethod) {
        $receipt = "Receipt:\n";
        $receipt .= "User ID: {$userId}\n";
        $receipt .= "Amount Donated: {$amount}\n";
        $receipt .= "Payment Method: {$paymentMethod}\n";
        $receipt .= "Thank you for your generosity!\n";
    
        return $receipt;
    }
    
}
?>
