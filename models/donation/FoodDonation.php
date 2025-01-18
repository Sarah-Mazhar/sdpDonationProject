<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/DonationI.php';
require_once __DIR__ . '/DonationTemplate.php'; 

class FoodDonation extends DonationTemplate implements DonationI {
    private $items = [];

    public function addItem($item, $quantity) {
        $this->items[] = ["item" => $item, "quantity" => $quantity];
    }

    public function donate($userId, $amountOrItem = null, $quantity = 1) {
        // Call the template method
        parent::donate($userId, $amountOrItem, $quantity);
    }

    // Template Pattern: Initialize donation
    protected function initializeDonation($amountOrItem, $quantity) {
        echo "Preparing to donate {$quantity} of {$amountOrItem}.<br>";
        $this->addItem($amountOrItem, $quantity);
    }

    // Template Pattern: Process donation
    protected function processDonation($userId, $amountOrItem, $quantity) {
        // Create database connection
        $db = Database::getInstance()->getConnection();

        // Insert each item in the box as a separate donation entry
        foreach ($this->items as $entry) {
            $query = "INSERT INTO donations (type, food_item, quantity, user_id) VALUES ('food', :food_item, :quantity, :user_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':food_item', $entry["item"]);
            $stmt->bindParam(':quantity', $entry["quantity"]);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        }

        echo "Processed food donation of {$quantity} {$amountOrItem} for User ID {$userId}.<br>";
    }

    // Template Pattern: Finalize donation
    protected function finalizeDonation() {
        echo "Food donation finalized successfully.<br>";
    }

    public function generateReceipt($userId, $foodDetails, $extras = null) {
        $receipt = "Receipt:\n";
        $receipt .= "User ID: {$userId}\n";
        $receipt .= "Food Donation: {$foodDetails}\n";
        $receipt .= $extras ? "Extras: {$extras}\n" : "Extras: None\n";
        $receipt .= "Thank you for your generosity!\n";
    
        // Return the receipt for session storage or printing
        return $receipt;
    }
    
    
}
