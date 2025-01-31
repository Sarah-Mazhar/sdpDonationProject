<?php
require_once __DIR__ . '/DonationState.php';
require_once __DIR__ . '/../../../controllers/DonationController.php';


class CompletedState implements DonationState {
    public function process(DonationController $context) {
        echo "Cannot process. Donation is already completed.\n";
    }

    public function pay(DonationController $context) {
        echo "Cannot pay. Donation is already completed.\n";
    }

    public function fail(DonationController $context) {
        echo "Cannot fail. Donation is already completed.\n";
    }

    public function complete(DonationController $context) {
        echo "Already in completed state.\n";
    }
}

?>
