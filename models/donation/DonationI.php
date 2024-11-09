<?php
// models/Donation.php

interface DonationI {
    public function donate($userId, $amountOrItem, $quantity);
}
?>
