<?php
// Controllers
require_once __DIR__ . '/controllers/DonationController.php';

// Models
require_once __DIR__ . '/models/donation/AddFruit.php';
require_once __DIR__ . '/models/donation/AddVegetables.php';
require_once __DIR__ . '/models/donation/DonationFactory.php';
require_once __DIR__ . '/models/donation/DonationSubject.php';

// States
require_once __DIR__ . '/models/donation/States/DonationState.php';
require_once __DIR__ . '/models/donation/States/PendingState.php';
require_once __DIR__ . '/models/donation/States/ProcessingState.php';
require_once __DIR__ . '/models/donation/States/CompletedState.php';
require_once __DIR__ . '/models/donation/States/FailedState.php';

// Observers
require_once __DIR__ . '/models/observers/EmailObserver.php';
require_once __DIR__ . '/models/observers/NotificationObserver.php';
require_once __DIR__ . '/models/observers/LogObserver.php';

// Payments
require_once __DIR__ . '/models/payment/CashPayment.php';
require_once __DIR__ . '/models/payment/VisaPayment.php';
require_once __DIR__ . '/models/payment/PaymentContext.php';
?>
