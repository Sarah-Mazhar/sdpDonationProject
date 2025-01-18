<?php

require_once 'FoodDonationDecorator.php';

class AddVegetables extends FoodDonationDecorator {
    public function addItemToDonation() {
        $this->donation->addItem("vegetables", 1);
    }
}
