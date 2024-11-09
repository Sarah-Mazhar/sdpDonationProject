<?php
// models/DonationFactory.php

require_once __DIR__ . '/MoneyDonation.php'; // Money Donation class
require_once __DIR__ . '/FoodDonation.php'; // Food Donation class

class DonationFactory {

    public function createDonation($type) {
        if ($type === 'money') {
            return new MoneyDonation();
        } elseif ($type === 'food') {
            return new FoodDonation();
        }
        throw new Exception("Donation type {$type} not supported.");
    }
}
?>
