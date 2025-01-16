<?php
namespace Controllers;

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
    private $stateHistory = [];

    public function __construct() {
        // Initialize DonationSubject
        $this->donationSubject = new \DonationSubject();
        $this->donationSubject->attach(new \EmailObserver());
        $this->donationSubject->attach(new \NotificationObserver());
        $this->donationSubject->attach(new \LogObserver());

        // Set the initial state
        $this->changeState(new \Models\Donation\States\PendingState());
    }

    public function getStateHistory() {
        return $this->stateHistory;
    }

    private function changeState($newState) {
        $this->currentState = $newState;
        $this->stateHistory[] = (new \ReflectionClass($newState))->getShortName(); // Save state name
    }

    public function process() {
        $this->changeState(new \Models\Donation\States\ProcessingState());
    }

    public function complete() {
        $this->changeState(new \Models\Donation\States\CompletedState());
    }

    public function fail() {
        $this->changeState(new \Models\Donation\States\FailedState());
    }

    // public function donateMoney($amount, $paymentMethod) {
    //     $userId = $_SESSION['user_id'] ?? null;

    //     if (!$userId) {
    //         echo "Please log in to donate.";
    //         return;
    //     }

    //     $this->process(); // Transition to processing
    //     $donationFactory = new \DonationFactory();
    //     $moneyDonation = $donationFactory->createDonation('money');

    //     $paymentStrategy = $paymentMethod === 'cash' ? new \CashPayment() :
    //                       ($paymentMethod === 'visa' ? new \VisaPayment() : null);

    //     if (!$paymentStrategy) {
    //         echo "Invalid payment method.";
    //         $this->fail(); // Transition to failed
    //         return;
    //     }

    //     $paymentContext = new \PaymentContext($paymentStrategy);
    //     $result = $paymentContext->executePayment($amount);

    //     if ($result['status']) {
    //         $moneyDonation->donate($userId, $amount);
    //         $this->complete(); // Transition to completed
    //         $this->donationSubject->notifyObservers([
    //             'userId' => $userId,
    //             'amountOrItem' => $amount,
    //             'type' => 'money',
    //         ]);
    //     } else {
    //         $this->fail(); // Transition to failed
    //     }
    // }

    // public function donateFood($foodItem, $quantity, $extras = []) {
    //     $userId = $_SESSION['user_id'] ?? null;

    //     if (!$userId) {
    //         echo "Please log in to donate.";
    //         return;
    //     }

    //     $this->process(); // Transition to processing
    //     $donationFactory = new \DonationFactory();
    //     $foodDonation = $donationFactory->createDonation('food');
    //     $foodDonation->addItem($foodItem, $quantity);

    //     foreach ($extras as $extra) {
    //         if ($extra === 'fruit') {
    //             $foodDonation = new \AddFruit($foodDonation);
    //         } elseif ($extra === 'vegetables') {
    //             $foodDonation = new \AddVegetables($foodDonation);
    //         }
    //     }

    //     try {
    //         $foodDonation->donate($userId);
    //         $this->complete(); // Transition to completed
    //         $this->donationSubject->notifyObservers([
    //             'userId' => $userId,
    //             'amountOrItem' => "{$quantity} {$foodItem} with extras: " . implode(', ', $extras),
    //             'type' => 'food',
    //         ]);
    //     } catch (\Exception $e) {
    //         echo "Donation failed: " . $e->getMessage();
    //         $this->fail(); // Transition to failed
    //     }
    // }


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
    
        echo "<p>Pending State...</p>"; // Show pending state
        if ($result['status']) {
            $this->process();
            echo "<p>Processing State...</p>"; // Show processing state
    
            // Simulate some processing time
            sleep(1);
    
            $moneyDonation->donate($userId, $amount);
    
            $this->complete();
            echo "<p>Completed State!</p>"; // Show completed state
    
            // Simulate some time before sending the final message
            sleep(1);
    
            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money'
            ]);
            echo "Money donation of {$amount} processed successfully! {$result['message']} <br>";
        } else {
            $this->fail();
            echo "<p>Failed State!</p>";
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
    
        echo "<p>Pending State...</p>"; // Show pending state
    
        if (in_array('fruit', $extras)) {
            $fruitDecorator = new \AddFruit($foodDonation);
            $fruitDecorator->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            $vegetablesDecorator = new \AddVegetables($foodDonation);
            $vegetablesDecorator->addItemToDonation();
        }
    
        $this->process();
        echo "<p>Processing State...</p>"; // Show processing state
    
        // Simulate some processing time
        sleep(1);
    
        $foodDonation->donate($userId);
    
        $this->complete();
        echo "<p>Completed State!</p>"; // Show completed state
    
        // Simulate some time before sending the final message
        sleep(1);
    
        $this->donationSubject->notifyObservers([
            'userId' => $userId,
            'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
            'type' => 'food'
        ]);
        echo "Food donation processed successfully! <br>";
    }
    
}
?>
