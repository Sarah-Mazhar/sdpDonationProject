<?php
namespace Controllers;

// Include the required files for your project
require_once __DIR__ . '/../models/donation/DonationSubject.php';
require_once __DIR__ . '/../models/observers/EmailObserver.php';
require_once __DIR__ . '/../models/observers/NotificationObserver.php';
require_once __DIR__ . '/../models/observers/LogObserver.php';
require_once __DIR__ . '/../models/donation/States/PendingState.php';
require_once __DIR__ . '/../models/donation/States/ProcessingState.php';
require_once __DIR__ . '/../models/donation/States/CompletedState.php';
require_once __DIR__ . '/../models/donation/States/FailedState.php';

class DonationController {

    private $donationSubject;
    private $currentState;

    public function __construct() {
        // Initialize DonationSubject
        $this->donationSubject = new \DonationSubject();
        $this->donationSubject->attach(new \EmailObserver());
        $this->donationSubject->attach(new \NotificationObserver());
        $this->donationSubject->attach(new \LogObserver());

        // Set the initial state
        $this->currentState = new \Models\Donation\States\PendingState();

    }

    public function changeState($newState) {
        $this->currentState = $newState;
    }

    public function process() {
        $this->currentState->process($this);
    }

    public function pay() {
        $this->currentState->pay($this);
    }

    public function fail() {
        $this->currentState->fail($this);
    }

    public function complete() {
        $this->currentState->complete($this);
    }

    public function donateMoney($amount, $paymentMethod) {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $donationFactory = new \DonationFactory();
        $moneyDonation = $donationFactory->createDonation('money');

        $paymentStrategy = $paymentMethod === 'cash' ? new \CashPayment() :
                          ($paymentMethod === 'visa' ? new \VisaPayment() : null);

        if (!$paymentStrategy) {
            echo "Invalid payment method.";
            return;
        }

        $paymentContext = new \PaymentContext($paymentStrategy);
        $result = $paymentContext->executePayment($amount);

        if ($result['status']) {
            $this->process();
            $moneyDonation->donate($userId, $amount);
            echo "Money donation of {$amount} processed successfully! {$result['message']} \n";
            $this->complete();
            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money'
            ]);
        } else {
            $this->fail();
            echo "Money donation of {$amount} failed.";
        }
    }

    public function donateFood($foodItem, $quantity, $extras = []) {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo "Please log in to donate.";
            return;
        }

        $donationFactory = new \DonationFactory();
        $foodDonation = $donationFactory->createDonation('food');
        $foodDonation->addItem($foodItem, $quantity);

        if (in_array('fruit', $extras)) {
            $fruitDecorator = new \AddFruit($foodDonation);
            $fruitDecorator->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            $vegetablesDecorator = new \AddVegetables($foodDonation);
            $vegetablesDecorator->addItemToDonation();
        }

        $this->process();
        $foodDonation->donate($userId);
        echo "Food donation processed successfully! \n";
        $this->complete();
        $this->donationSubject->notifyObservers([
            'userId' => $userId,
            'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
            'type' => 'food'
        ]);
    }
}
?>
