<?php
// models/donation/FoodDonation.php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/./DonationI.php';

class FoodDonation implements DonationI {
    private $items = []; // Start with an empty box

    public function addItem($item, $quantity) {
        $this->items[] = ["item" => $item, "quantity" => $quantity];
    }

    public function donate($userId, $amountOrItem = null, $quantity = 1) {
        // Create database connection
        $db = Database::getInstance()->getConnection();

        // If specific items are provided, add them to the box
        if ($amountOrItem) {
            $this->addItem($amountOrItem, $quantity);
        }

        // Insert each item in the box as a separate donation entry
        foreach ($this->items as $entry) {
            $query = "INSERT INTO donations (type, food_item, quantity, user_id) VALUES ('food', :food_item, :quantity, :user_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':food_item', $entry["item"]);
            $stmt->bindParam(':quantity', $entry["quantity"]);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
        }

        // Clear the items after the donation process is completed
        $this->items = [];
    }
}
