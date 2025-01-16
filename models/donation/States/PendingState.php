<?php
// namespace Models\Donation\States;

// use Controllers\DonationController;

// Include the DonationState interface
require_once __DIR__ . '/DonationState.php';
// require_once __DIR__ . '/../../../../controllers/DonationController.php';
require_once __DIR__ . '/../../../controllers/DonationController.php';


class PendingState implements DonationState {

    public function process(DonationController $context) {
        echo "Cannot process. Donation is already in the pending state.\n";
    }

    public function pay(DonationController $context) {
        echo "Cannot pay. Donation is still in the pending state.\n";
    }

    public function fail(DonationController $context) {
        echo "Cannot fail directly. Donation is still in the pending state.\n";
    }

    public function complete(DonationController $context) {
        echo "Cannot complete directly. Donation is still in the pending state.\n";
    }
}
?>
