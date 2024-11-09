<?php
// models/donation/FoodDonationDecorator.php

require_once 'FoodDonation.php';

abstract class FoodDonationDecorator extends FoodDonation {
    protected $donation;

    public function __construct(FoodDonation $donation) {
        $this->donation = $donation;
    }

    public function donate($userId, $amountOrItem = null, $quantity = 1) {
        $this->donation->donate($userId, $amountOrItem, $quantity);
    }
}
