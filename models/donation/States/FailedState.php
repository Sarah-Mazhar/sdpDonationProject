<?php
namespace Models\Donation\States;

use Controllers\DonationController;
// Include the DonationState interface
require_once __DIR__ . '/DonationState.php';

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
