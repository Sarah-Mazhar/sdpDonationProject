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
require_once __DIR__ . '/../models/Iterator/DonationIterator.php';
require_once __DIR__ . '/../models/donation/States/PendingState.php';
require_once __DIR__ . '/../models/donation/States/ProcessingState.php';
require_once __DIR__ . '/../models/donation/States/CompletedState.php';
require_once __DIR__ . '/../models/donation/States/FailedState.php';

class DonationController {
    private $donationSubject; 
    private $donationAdmin; 
    private $currentState;  
    private $stateHistory = [];

    public function __construct() {
        $this->donationSubject = new DonationSubject();
        $this->donationSubject->attach(new EmailObserver());
        $this->donationSubject->attach(new NotificationObserver());
        $this->donationSubject->attach(new LogObserver());

        $userRole = $_SESSION['user_role'] ?? 'guest';
        $this->donationAdmin = new ProtectiveDonationProxy($userRole);

        $this->changeState(new PendingState());
    }

    private function changeState($newState) {
        $this->currentState = $newState;
        $this->stateHistory[] = (new \ReflectionClass($newState))->getShortName();
        echo "State changed to: " . end($this->stateHistory) . "<br>";
    }

    public function getStateHistory() {
        return $this->stateHistory;
    }

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

        $paymentStrategy = match ($paymentMethod) {
            'cash' => new CashPayment(),
            'visa' => new VisaPayment(),
            'third_party' => new ThirdPartyPaymentAdapter(new ThirdPartyPaymentGateway()),
            default => null
        };

        if (!$paymentStrategy) {
            echo "Invalid payment method.";
            return;
        }

        $paymentContext = new PaymentContext($paymentStrategy);
        $result = $paymentContext->executePayment($amount);

        $this->changeState(new ProcessingState());
        if ($result['status']) {
            $moneyDonation->donate($userId, $amount);
            $this->changeState(new CompletedState());
            echo "Money donation of {$amount} done successfully! {$result['message']}<br>";

            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money',
                'status' => 'success'
            ]);

            $receipt = $moneyDonation->generateReceipt($userId, $amount, $paymentMethod);
            $_SESSION['money_receipt'] = $receipt;
        } else {
            $this->changeState(new FailedState());
            echo "Money donation failed: {$result['message']}<br>";
        }
    }

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

        $donationFactory = new DonationFactory();
        $foodDonation = $donationFactory->createDonation('food');
        $foodDonation->addItem($foodItem, $quantity);

        if (in_array('fruit', $extras)) {
            (new AddFruit($foodDonation))->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            (new AddVegetables($foodDonation))->addItemToDonation();
        }

        $this->changeState(new ProcessingState());
        try {
            $foodDonation->donate($userId, $foodItem, $quantity);
            $this->changeState(new CompletedState());
            echo "Food donation done successfully!<br>";

            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
                'type' => 'food',
                'status' => 'success'
            ]);

            $extrasText = !empty($extras) ? implode(', ', $extras) : 'None';
            $receipt = $foodDonation->generateReceipt($userId, "{$quantity} {$foodItem}", $extrasText);
            $_SESSION['food_receipt'] = $receipt;
        } catch (\Exception $e) {
            $this->changeState(new FailedState());
            echo "Error during food donation: " . $e->getMessage() . "<br>";
        }
    }

    public function viewDonations() {
        if (in_array($_SESSION['user_type'], ['donation_admin', 'super_admin'])) {
            $this->donationAdmin->viewDonations();
        } else {
            echo "Access denied: You are not authorized to view donations.";
        }
    }

    public function deleteDonation($donationId) {
        if (in_array($_SESSION['user_type'], ['donation_admin', 'super_admin'])) {
            $this->donationAdmin->deleteDonation($donationId);
        } else {
            echo "Access denied: You are not authorized to delete donations.";
        }
    }

    public function listAllDonations() {
        if (in_array($_SESSION['user_type'], ['donation_admin', 'super_admin'])) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->query("SELECT * FROM donations");
            $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $donationIterator = new DonationIterator($donations);
            $donationsList = [];
            while ($donationIterator->hasNext()) {
                $donationsList[] = $donationIterator->next();
            }
            return $donationsList;
        } else {
            echo "Access denied: You are not authorized to view donations.";
            return [];
        }
    }
}
?>
