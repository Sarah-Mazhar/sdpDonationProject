<?php
require_once __DIR__ . '/controllers/DonationController.php';

use Controllers\DonationController;

$controller = new DonationController();
$controller->process(); // Should trigger the `process` method in `PendingState`
?>
