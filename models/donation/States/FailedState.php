<?php

require_once __DIR__ . '/DonationState.php';
require_once __DIR__ . '/../../../controllers/DonationController.php';


class FailedState implements DonationState {
    public function process(DonationController $context) {
        echo "Cannot process. Donation is already failed.\n";
    }

    public function pay(DonationController $context) {
        echo "Cannot pay. Donation is already failed.\n";
    }

    public function fail(DonationController $context) {
        echo "Already in failed state.\n";
    }

    public function complete(DonationController $context) {
        echo "Cannot complete. Donation is in a failed state.\n";
    }
}
?>
