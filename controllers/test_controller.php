<?php
require_once __DIR__ . '/DonationController.php'; // Correct path

use Controllers\DonationController;

$controller = new DonationController();
$controller->process(); // Should trigger the `process` method in `PendingState`
?>
