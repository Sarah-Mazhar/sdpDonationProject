<?php
// models/donation/DonationTemplate.php

abstract class DonationTemplate {
    public function donate($userId) {
        try {
            $this->initializeDonation();
            $this->processDonation();
            $this->finalizeDonation($userId);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    }

    protected abstract function initializeDonation();
    protected abstract function processDonation();
    protected abstract function finalizeDonation($userId);
}
?>
