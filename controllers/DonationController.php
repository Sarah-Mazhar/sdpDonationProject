<?php
// controllers/DonationController.php

require_once __DIR__ . '/../models/donation/DonationFactory.php';
require_once __DIR__ . '/../models/donation/AddFruit.php';
require_once __DIR__ . '/../models/donation/AddVegetables.php';
require_once __DIR__ . '/../models/donation/DonationSubject.php';
require_once __DIR__ .'/../models/payment/CashPayment.php';
require_once __DIR__ .'/../models/payment/VisaPayment.php';
require_once __DIR__ .'/../models/payment/PaymentContext.php';
require_once __DIR__ . '/../models/observers/EmailObserver.php';
require_once __DIR__ . '/../models/observers/NotificationObserver.php';
require_once __DIR__ . '/../models/observers/LogObserver.php';

class DonationController {

    private $donationSubject;

    public function __construct() {
        // Initialize the subject and attach observers
        $this->donationSubject = new DonationSubject();
        $this->donationSubject->attach(new EmailObserver());
        $this->donationSubject->attach(new NotificationObserver());
        $this->donationSubject->attach(new LogObserver());
    }

    // Handle Money Donations
    public function donateMoney($amount, $paymentMethod) {
        $userId = $_SESSION['user_id'] ?? null;  // Get user ID from session

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $donationFactory = new DonationFactory();
        $moneyDonation = $donationFactory->createDonation('money');

        // Select the appropriate payment strategy based on the payment method
        if ($paymentMethod === 'cash') {
            $paymentStrategy = new CashPayment();
        } elseif ($paymentMethod === 'visa') {
            $paymentStrategy = new VisaPayment();
        } else {
            echo "Invalid payment method.";
            return;
        }

        // Create a PaymentContext and execute the payment
        $paymentContext = new PaymentContext($paymentStrategy);
        $result = $paymentContext->executePayment($amount);

        if ($result['status']) {
            // Process the donation
            $moneyDonation->donate($userId, $amount);
            echo "Money donation of {$amount} processed successfully! {$result['message']} \n";

            // Notify all observers about the successful money donation
            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money'
            ]);
        } else {
            echo "Money donation of {$amount} failed";
        }
    }

    // Handle Food Donations
    public function donateFood($foodItem, $quantity, $extras = []) {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $donationFactory = new DonationFactory();
        $foodDonation = $donationFactory->createDonation('food');
        $foodDonation->addItem($foodItem, $quantity);

        // Apply decorators based on selected extras
        if (in_array('fruit', $extras)) {
            $fruitDecorator = new AddFruit($foodDonation);
            $fruitDecorator->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            $vegetablesDecorator = new AddVegetables($foodDonation);
            $vegetablesDecorator->addItemToDonation();
        }

        // Save all accumulated items in one go
        $foodDonation->donate($userId);
        echo "Food donation processed successfully! \n";

        // Notify all observers about the successful food donation
        $this->donationSubject->notifyObservers([
            'userId' => $userId,
            'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
            'type' => 'food'
        ]);
    }
}
?>
