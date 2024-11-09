<?php
// models/donation/AddFruit.php

require_once 'FoodDonationDecorator.php';

class AddFruit extends FoodDonationDecorator {
    public function addItemToDonation() {
        $this->donation->addItem("fruit", 1);  // Adds fruit item to the list
    }
}
