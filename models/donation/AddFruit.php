<?php

require_once 'FoodDonationDecorator.php';

class AddFruit extends FoodDonationDecorator {
    public function addItemToDonation() {
        $this->donation->addItem("fruit", 1); 
    }
}
