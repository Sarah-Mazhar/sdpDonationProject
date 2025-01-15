<?php
namespace Models\Donation\States;

use Controllers\DonationController;

class ProcessingState implements DonationState {
    public function process(DonationController $context) {
        echo "Already in processing state.\n";
    }

    public function pay(DonationController $context) {
        echo "Payment completed successfully. Moving to completed state.\n";
        $context->changeState(new CompletedState());
    }

    public function fail(DonationController $context) {
        echo "Processing failed. Moving to failed state.\n";
        $context->changeState(new FailedState());
    }

    public function complete(DonationController $context) {
        echo "Cannot complete directly from processing. Payment required.\n";
    }
}
?>
