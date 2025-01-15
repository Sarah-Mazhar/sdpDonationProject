<?php
namespace Models\Donation\States;

use Controllers\DonationController;

interface DonationState {
    public function process(DonationController $context);
    public function pay(DonationController $context);
    public function fail(DonationController $context);
    public function complete(DonationController $context);
}
?>
