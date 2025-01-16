<?php

require_once __DIR__ . '/../models/donation/DonationFactory.php';
require_once __DIR__ . '/../models/donation/AddFruit.php';
require_once __DIR__ . '/../models/donation/AddVegetables.php';
require_once __DIR__ . '/../models/payment/CashPayment.php';
require_once __DIR__ . '/../models/payment/VisaPayment.php';
require_once __DIR__ . '/../models/payment/PaymentContext.php';
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentGateway.php';
require_once __DIR__ . '/../models/payment/ThirdPartyPaymentAdapter.php';
require_once __DIR__ . '/../models/donation/DonationSubject.php';
require_once __DIR__ . '/../models/observers/EmailObserver.php';
require_once __DIR__ . '/../models/observers/NotificationObserver.php';
require_once __DIR__ . '/../models/observers/LogObserver.php';
require_once __DIR__ . '/../models/donation/ProtectiveDonationProxy.php';
require_once __DIR__ . '/../models/donation/DonationAdminInterface.php';
require_once __DIR__ . '/../models/donation/RealDonationAdmin.php';

// State-related includes
require_once __DIR__ . '/../models/donation/States/PendingState.php';
require_once __DIR__ . '/../models/donation/States/ProcessingState.php';
require_once __DIR__ . '/../models/donation/States/CompletedState.php';
require_once __DIR__ . '/../models/donation/States/FailedState.php';

// use Models\Donation\States\PendingState;
// use Models\Donation\States\ProcessingState;
// use Models\Donation\States\CompletedState;
// use Models\Donation\States\FailedState;


class DonationController {
    private $donationSubject;
    private $donationAdmin;
    private $currentState; // Current state of the donation process
    private $stateHistory = []; // History of state transitions

    public function __construct() {
        // Initialize the subject and attach observers
        $this->donationSubject = new DonationSubject();
        $this->donationSubject->attach(new EmailObserver());
        $this->donationSubject->attach(new NotificationObserver());
        $this->donationSubject->attach(new LogObserver());

        // Determine user role (fetch from session)
        $userRole = $_SESSION['user_role'] ?? 'guest';

        // Initialize the proxy for donation administration
        $this->donationAdmin = new ProtectiveDonationProxy($userRole);

        // Initialize the state to PendingState
        $this->changeState(new PendingState());
    }

    // Change the state and notify observers
    private function changeState($newState) {
        $this->currentState = $newState;
        $this->stateHistory[] = (new \ReflectionClass($newState))->getShortName(); // Save state name
        echo "State changed to: " . end($this->stateHistory) . "<br>";

        // // Notify observers about the state change
        // $this->donationSubject->notifyObservers([
        //     'userId' => $_SESSION['user_id'] ?? 'Unknown User',
        //     'state' => end($this->stateHistory),
        // ]);
    }

    // Get state history
    public function getStateHistory() {
        return $this->stateHistory;
    }

    // Handle Money Donations with State Transitions
    public function donateMoney($amount, $paymentMethod) {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $amount = floatval($amount);
        if ($amount <= 0) {
            echo "Invalid donation amount.";
            return;
        }

        $donationFactory = new DonationFactory();
        $moneyDonation = $donationFactory->createDonation('money');

        // Select the appropriate payment strategy
        $paymentStrategy = null;
        if ($paymentMethod === 'cash') {
            $paymentStrategy = new CashPayment();
        } elseif ($paymentMethod === 'visa') {
            $paymentStrategy = new VisaPayment();
        } elseif ($paymentMethod === 'third_party') {
            $thirdPartyGateway = new ThirdPartyPaymentGateway();
            $paymentStrategy = new ThirdPartyPaymentAdapter($thirdPartyGateway);
        } else {
            echo "Invalid payment method.";
            return;
        }

        $paymentContext = new PaymentContext($paymentStrategy);
        $result = $paymentContext->executePayment($amount);

        // State transitions based on payment success
        $this->changeState(new ProcessingState());
        if ($result['status']) {
            $moneyDonation->donate($userId, $amount);
            $this->changeState(new CompletedState());
            echo "Money donation of {$amount} processed successfully! {$result['message']}<br>";

            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money',
                'status' => 'success'
            ]);
        } else {
            $this->changeState(new FailedState());
            echo "Money donation failed: {$result['message']}<br>";
        }
    }

    // Handle Food Donations with State Transitions
    public function donateFood($foodItem, $quantity, $extras = []) {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $quantity = intval($quantity);
        if ($quantity <= 0 || empty($foodItem)) {
            echo "Invalid food item or quantity.";
            return;
        }

        // Set amountOrItem in session for state notifications
        $_SESSION['amountOrItem'] = "{$quantity} {$foodItem}";

        $donationFactory = new DonationFactory();
        $foodDonation = $donationFactory->createDonation('food');
        $foodDonation->addItem($foodItem, $quantity);

        // Apply extras
        if (in_array('fruit', $extras)) {
            $fruitDecorator = new AddFruit($foodDonation);
            $fruitDecorator->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            $vegetablesDecorator = new AddVegetables($foodDonation);
            $vegetablesDecorator->addItemToDonation();
        }

        // State transitions
        $this->changeState(new ProcessingState());
        try {
            $foodDonation->donate($userId, $foodItem, $quantity);
            $this->changeState(new CompletedState());
            echo "Food donation processed successfully!<br>";

            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
                'type' => 'food',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->changeState(new FailedState());
            echo "Error during food donation: " . $e->getMessage() . "<br>";
        }
    }

    // View Donations (Admin-only)
    public function viewDonations() {
        if ($_SESSION['user_type'] === 'donation_admin' || $_SESSION['user_type'] === 'super_admin') {
            $this->donationAdmin->viewDonations();
        } else {
            echo "Access denied: You are not authorized to view donations.";
        }
    }

    // Delete a Donation (Admin-only)
    public function deleteDonation($donationId) {
        if ($_SESSION['user_type'] === 'donation_admin' || $_SESSION['user_type'] === 'super_admin') {
            $this->donationAdmin->deleteDonation($donationId);
        } else {
            echo "Access denied: You are not authorized to delete donations.";
        }
    }
}
?>
