<?php
require_once __DIR__ . '/../models/donation/DonationFactory.php';
require_once __DIR__ . '/../models/donation/AddFruit.php';
require_once __DIR__ . '/../models/donation/AddVegetables.php';
require_once __DIR__ . '/../models/payment/CashPayment.php';
require_once __DIR__ . '/../models/payment/VisaPayment.php';
require_once __DIR__ . '/../models/payment/PaymentContext.php';
require_once __DIR__ . '/../models/donation/DonationSubject.php';
require_once __DIR__ . '/../models/observers/EmailObserver.php';
require_once __DIR__ . '/../models/observers/NotificationObserver.php';
require_once __DIR__ . '/../models/observers/LogObserver.php';
require_once __DIR__ . '/../models/donation/ProtectiveDonationProxy.php';

require_once __DIR__ . '/../models/donation/DonationAdminInterface.php';
require_once __DIR__ . '/../models/donation/RealDonationAdmin.php';

class DonationController {
    private $donationSubject;
    private $donationAdmin;

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
    }

    // Handle Money Donations
    public function donateMoney($amount, $paymentMethod) {
        $userId = $_SESSION['user_id'] ?? null;  // Get user ID from session

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
        } else {
            echo "Invalid payment method.";
            return;
        }

        // Execute the payment
        $paymentContext = new PaymentContext($paymentStrategy);
        $result = $paymentContext->executePayment($amount);

        if ($result['status']) {
            // Process the donation
            $moneyDonation->donate($userId, $amount);
            echo "Money donation of {$amount} processed successfully! {$result['message']} \n";

            // Notify observers
            $this->donationSubject->notifyObservers([
                'userId' => $userId,
                'amountOrItem' => $amount,
                'type' => 'money',
                'status' => 'success'
            ]);
        } else {
            echo "Money donation failed: {$result['message']}";
        }
    }

    // Handle Food Donations
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

        // Apply extras
        if (in_array('fruit', $extras)) {
            $fruitDecorator = new AddFruit($foodDonation);
            $fruitDecorator->addItemToDonation();
        }
        if (in_array('vegetables', $extras)) {
            $vegetablesDecorator = new AddVegetables($foodDonation);
            $vegetablesDecorator->addItemToDonation();
        }

        // Save donation
        $foodDonation->donate($userId);
        echo "Food donation processed successfully!";

        // Notify observers
        $this->donationSubject->notifyObservers([
            'userId' => $userId,
            'amountOrItem' => "{$quantity} {$foodItem}" . (empty($extras) ? '' : ' with extras: ' . implode(', ', $extras)),
            'type' => 'food',
            'status' => 'success'
        ]);
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
