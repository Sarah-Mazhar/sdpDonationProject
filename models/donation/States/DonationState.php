<?php
// namespace Models\Donation\States;

// use Controllers\DonationController;
// require_once __DIR__ . '/../../../../controllers/DonationController.php';
require_once __DIR__ . '/../../../controllers/DonationController.php';



interface DonationState {
    public function process(DonationController $context);
    public function pay(DonationController $context);
    public function fail(DonationController $context);
    public function complete(DonationController $context);
}
?>
