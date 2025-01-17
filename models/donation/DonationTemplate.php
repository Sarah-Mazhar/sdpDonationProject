<?php

// models/donation/DonationTemplate.php

abstract class DonationTemplate {
    public function donate($userId, $amountOrItem, $quantity = null) {
        $this->validateUser($userId);
        $this->initializeDonation($amountOrItem, $quantity);
        $this->processDonation($userId, $amountOrItem, $quantity);
        $this->finalizeDonation();
    }

    protected abstract function initializeDonation($amountOrItem, $quantity);
    protected abstract function processDonation($userId, $amountOrItem, $quantity);
    protected abstract function finalizeDonation();

    protected function validateUser($userId) {
        if (!$userId) {
            throw new Exception("User not logged in.");
        }
    }

    public function generateReceipt($userId, $amountOrItem, $quantity) {
        echo "Receipt: User ID {$userId} donated {$quantity} of {$amountOrItem}.<br>";
    }
}
?>
