<?php
// models/donation/AddVegetables.php

require_once 'FoodDonationDecorator.php';

class AddVegetables extends FoodDonationDecorator {
    public function addItemToDonation() {
        $this->donation->addItem("vegetables", 1);  // Adds vegetables item to the list
    }
}
