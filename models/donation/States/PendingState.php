<?php
namespace Models\Donation\States;

use Controllers\DonationController;

class PendingState implements DonationState {
    public function process(DonationController $context) {
        echo "Processing donation...\n";
        $context->changeState(new ProcessingState());
    }

    public function pay(DonationController $context) {
        echo "Cannot pay in pending state.\n";
    }

    public function fail(DonationController $context) {
        echo "Donation failed.\n";
        $context->changeState(new FailedState());
    }

    public function complete(DonationController $context) {
        echo "Cannot complete in pending state.\n";
    }
}
?>
