<?php
namespace Models\Donation\States;

use Controllers\DonationController;

// Include the DonationState interface
require_once __DIR__ . '/DonationState.php';

class ProcessingState implements DonationState {

    public function process(DonationController $context) {
        echo "Cannot process. Donation is already in the processing state.\n";
    }

    public function pay(DonationController $context) {
        echo "Cannot pay directly. Donation is still being processed.\n";
    }

    public function fail(DonationController $context) {
        echo "Cannot fail directly. Donation is still being processed.\n";
    }

    public function complete(DonationController $context) {
        echo "Cannot complete directly. Donation is still being processed.\n";
    }
}
?>
